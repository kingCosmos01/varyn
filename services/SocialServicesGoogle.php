<?php
    /**
     * The Google Plus implementation of our SocialServices base class.
     * @author: jf
     * @date: 7/16/2017
     */

    require_once 'lib/vendor/google/vendor/autoload.php';

    define ('GOOGLEAPI_SESSION_KEY', 'enggapisession');
    define ('GOOGLEAPI_COOKIE_KEY', 'enggapicode');
    define ('GOOGLEAPI_API_KEY', 'AIzaSyBVtePrW-dW6uRfWvWcHp5fVaWITVfMsOo'); // TODO: Move to config file

/*
 *     {"web":
    {"client_id":"1065156255426-al1fbn6kk4enqfq1f9drn8q1111optvt.apps.googleusercontent.com",
    "client_secret":"10xMn5CfHOVSpH8FWyOqyB5a",
    "project_id":"varyn-website",
    "auth_uri":"https://accounts.google.com/o/oauth2/auth",
    "token_uri":"https://accounts.google.com/o/oauth2/token",
    "auth_provider_x509_cert_url":"https://www.googleapis.com/oauth2/v1/certs",
    "redirect_uris":["https://www.varyn.com/procs/oauth.php"],
    "javascript_origins":["https://www.varyn.com","http://varyn-l.com","http://varyn-q.com","http://www.varyn-l.com","http://www.varyn-q.com","https://varyn.com","http://varyn-d.com","http://www.varyn-d.com"]}}

 */

    class SocialServicesGoogle extends SocialServices
    {
        private $googleAPI = null;
        private $appId = '';
        private $appSecret = '';
        private $appAccessCode = '';
        private $isLoggedIn = false;
        private $userInfo = null;
        private $idToken = null;

        public function __construct () {
            global $socialServiceKeys; // from serverConfig.
            $this->appId = $socialServiceKeys[EnginesisNetworks::Google]['app_id'];
            $this->appSecret = $socialServiceKeys[EnginesisNetworks::Google]['app_secret'];
            $this->setNetworkId(EnginesisNetworks::Google);
            $this->m_networkName = 'Google';
            $this->googleAPI = new Google_Client(['client_id' => $this->appId]);
            $this->googleAPI->setDeveloperKey(GOOGLEAPI_API_KEY);
            if (isset($_SESSION[GOOGLEAPI_SESSION_KEY])) {
                $accessToken = $_SESSION[GOOGLEAPI_SESSION_KEY];
                $this->m_accessToken = (string) $accessToken;
            }
        }

        /**
         * Perform all necessary steps to log user in and get their basic info.
         * TODO: extendToken?
         * @return {object} User info object
         */
        public function connectSSO () {
            $userInfo = null;
            if ($this->googleAPI) {
                if ($this->isLoggedIn) {
                    $userInfo = $this->currentUserInfo();
                } else {
                    $this->login();
                    if ($this->isLoggedIn) {
                        $userInfo = $this->currentUserInfo();
                    } else {
                        debugLog("SocialServicesGoogle connectSSO but no user is logged in");
                        $this->setLastError('INVALID_LOGIN', 'No user is logged in with Google.');
                    }
                }
            } else {
                debugLog("GoogleAPI is not loaded");
                $this->setLastError('INVALID_LOGIN', 'GoogleAPI is not loaded.');
            }
            return $userInfo;
        }

        private function exchangeToken($idToken) {
            $userInfo = null;
            if ( ! empty($idToken)) {
                $payload = $this->googleAPI->verifyIdToken($idToken);
                if ($payload) {
                    /*
                     * TODO: id is verified, user is fully logged in. OK, now what???
                     array(14) {
                    ["azp"]=> string(73) "1065156255426-al1fbn6kk4enqfq1f9drn8q1111optvt.apps.googleusercontent.com"
                    ["aud"]=> string(73) "1065156255426-al1fbn6kk4enqfq1f9drn8q1111optvt.apps.googleusercontent.com"
                    ["sub"]=> string(21) "112965556851421305464"
                    ["email"]=> string(16) "jlf990@gmail.com"
                    ["email_verified"]=> bool(true)
                    ["at_hash"]=> string(22) "BmZKhofpJKcS-alNcHQuSw"
                    ["iss"]=> string(19) "accounts.google.com"
                    ["iat"]=> int(1504567895)
                    ["exp"]=> int(1504571495)
                    ["name"]=> string(11) "John Foster"
                    ["picture"]=> string(98) "https://lh6.googleusercontent.com/-HaxvOzElL0A/AAAAAAAAAAI/AAAAAAAAAFA/zK7jsxGMwic/s96-c/photo.jpg"
                    ["given_name"]=> string(4) "John"
                    ["family_name"]=> string(6) "Foster"
                    ["locale"]=> string(2) "en" }
                     */
                    $userInfo = array(
                        'network_id' => EnginesisNetworks::Google,
                        'site_user_id' => $payload['sub'],
                        'real_name' => $payload['name'] . ' ' . $payload['family_name'],
                        'user_name' => $payload['name'],
                        'email_address' => $payload['email'],
                        'gender' => 'U',
                        'dob' => '',
                        'agreement' => '1',
                        'scope' => '',
                        'avatar_url' => $payload['picture'],
                        'id_token' => $idToken);
                    $expireDate = $payload['exp'];
                    $this->clearError();
                    $this->isLoggedIn = true;
                    $this->m_site_user_id = $userInfo['site_user_id'];
                    $this->m_accessToken = $this->googleAPI->getAccessToken();
                    $this->idToken = $idToken;
                } else {
                    debugLog("GoogleAPI token is not valid.");
                    $this->setLastError('INVALID_TOKEN', 'GoogleAPI token is not valid.');
                }
            }
            return $userInfo;
        }
        /**
         * For Google, the login is performed client-side with JavaScript and if that succeeds the client
         * drops a cookie with Google's authorization code. We take the code server side and exchange it for the
         * authentication token.
         */
        public function login () {
            $this->readGoogleCodeCookie();
            $idToken = $this->appAccessCode;
            if ( ! empty($idToken)) {
                $this->userInfo = $this->exchangeToken($idToken);
            } elseif ( ! empty($this->m_accessToken)) {
                debugLog("SocialServicesGoogle reusing token");
            } else {
                debugLog("SocialServicesGoogle no cookie dropped so cannot log the user in from server");
                $this->setLastError('INVALID_LOGIN', 'Token not provided.');
            }
            return $this->isLoggedIn;
        }

        /**
         * For Facebook we need to get an extended token after a first-time login.
         */
        public function loginPostProcess () {
            return $this->extendToken();
        }

        /**
         * When we initially login we get a token only valid for a few hours. However we can exchange that token
         * for one that lasts 60 days. The trick just may be knowing when to do that.
         * @return bool
         */
        private function extendToken () {
            return false;
        }

        /**
         * Log any current user out of the current network.
         */
        public function logout () {
            return false;
        }

        /**
         * Return a $userInfo object representing the current logged in user.
         * Enginesis: array('user_name' => $userName, 'email_address' => $email, 'real_name' => $realName, 'gender' => $gender, 'agreement' => $agreement);
         */
        public function currentUserInfo () {
            $userInfo = $this->userInfo;
            if ($userInfo == null) {
                if ( ! empty($this->idToken)) {
                    $this->userInfo = $this->exchangeToken($this->idToken);
                } elseif ( ! empty($this->m_site_user_id)) {
                    $idToken = $this->getNetworkUserDataToken(EnginesisNetworks::Google, $this->m_site_user_id);
                    if ( ! empty($idToken)) {
                        $this->userInfo = $this->exchangeToken($this->idToken);
                    }
                }
            }
            return $userInfo;
        }

        public function getFriends () {
            return null;
        }

        /**
         * Return a URL to the image of the user on the network signed in to.
         * @param int $size image size, abiding by the Enginesis values (0=small, 1=medium, 2=large)
         * @returns {string} URL to an image.
         */
        public function currentUserProfileImage ($size = 0) {
            return '';
        }

        public function deleteApp () {
            return false;
        }

        /**
         * Google sends ID tokens that require unpacking to determine the proper user id on their network.
         * @param $userIdToken - a user id token handed out by Google's client API.
         * @returns $site_user_id the real user id to identify the user (securely)
         */
        public function validateIdToken($userIdToken) {
            $payload = $this->googleAPI->verifyIdToken($userIdToken);
            if ($payload) {
                $site_user_id = $payload['sub'];
            } else {
                // Invalid ID token
                $site_user_id = null;
            }
            return $site_user_id;
        }

        /**
         * Read and clear the login access code set by the client during the initial login process.
         */
        private function readGoogleCodeCookie () {
            $clearCookie = true;
            if (isset($_SESSION[GOOGLEAPI_COOKIE_KEY])) {
                $this->appAccessCode = (string) $_SESSION[GOOGLEAPI_COOKIE_KEY];
                if ($clearCookie) {
                    $_SESSION[GOOGLEAPI_COOKIE_KEY] = null;
                    unset($_SESSION[GOOGLEAPI_COOKIE_KEY]);
                }
            } elseif (isset($_COOKIE[GOOGLEAPI_COOKIE_KEY])) {
                $this->appAccessCode = (string)$_COOKIE[GOOGLEAPI_COOKIE_KEY];
                if ($clearCookie) {
                    $_COOKIE[GOOGLEAPI_COOKIE_KEY] = null;
                    unset($_COOKIE[GOOGLEAPI_COOKIE_KEY]);
                    setcookie(GOOGLEAPI_COOKIE_KEY, null, -1, '/');
                }
            }
            return $this->appAccessCode;
        }

        private function fakeUser() {
            return (object) array(
                'network_id' => EnginesisNetworks::Google,
                'site_user_id' => '726468316',
                'user_name' => 'FakeGoogleUser',
                'real_name' => 'Fake Google User',
                'email_address' => 'info@varyn.com',
                'dob' => null,
                'gender' => 'U',
                'agreement' => '1',
                'scope' => 'profile email',
                'avatar_url' => '',
                'id_token' => ''
            );
        }
    }
