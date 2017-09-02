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

        public function __construct () {
            global $socialServiceKeys; // from serverConfig.
            $this->appId = $socialServiceKeys[EnginesisNetworks::Google]['app_id'];
            $this->appSecret = $socialServiceKeys[EnginesisNetworks::Google]['app_secret'];
            $this->setNetworkId(EnginesisNetworks::Google);
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
                    echo("<p>GoogleAPI trying login</p>");
                    $this->login();
                    if ($this->isLoggedIn) {
                        echo("<p>GoogleAPI user is logged in</p>");
                        $userInfo = $this->currentUserInfo();
                        var_dump($userInfo);
                    } else {
                        echo("<p>GoogleAPI no user is logged in</p>");
                    }
                }
            } else {
                echo("<p>GoogleAPI is not loaded</p>");
                debugLog("GoogleAPI is not loaded");
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
            if ( ! empty($this->appAccessCode)) {
                echo("<p>SSO tryng exchange</p>");
                // exchange the auth code for access and refresh tokens
                $creds = $this->googleAPI->fetchAccessTokenWithAuthCode($this->appAccessCode);
                var_dump($creds);
            } elseif ( ! empty($this->m_accessToken)) {
                echo("<p>SSO reusing token</p>");

            } else {
                echo("<p>SSO no cookie dropped so cannot log the user in from server</p>");
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
         * Facebook: [items:protected] => Array ( [id] => 726468316 [name] => John Foster [email] => jfoster@acm.org [gender] => male
         * Enginesis: array('user_name' => $userName, 'email_address' => $email, 'real_name' => $realName, 'gender' => $gender, 'agreement' => $agreement);
         */
        public function currentUserInfo () {
            $userInfo = null;
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
            if (isset($_SESSION[GOOGLEAPI_COOKIE_KEY])) {
                $this->appAccessCode = (string) $_SESSION[GOOGLEAPI_COOKIE_KEY];
                $_SESSION[GOOGLEAPI_COOKIE_KEY] = null;
                unset($_SESSION[GOOGLEAPI_COOKIE_KEY]);
            } elseif (isset($_COOKIE[GOOGLEAPI_COOKIE_KEY])) {
                $this->appAccessCode = (string)$_COOKIE[GOOGLEAPI_COOKIE_KEY];
                $_COOKIE[GOOGLEAPI_COOKIE_KEY] = null;
                unset($_COOKIE[GOOGLEAPI_COOKIE_KEY]);
                setcookie(GOOGLEAPI_COOKIE_KEY, null, -1, '/');
            }
            return $this->appAccessCode;
        }

        private function fakeUser() {
            return (object) array(
                'user_name' => 'FakeGoogleUser',
                'full_name' => 'Fake Google User',
                'user_id' => 726468316,
                'email_address' => 'info@varyn.com',
                'network_id' => EnginesisNetworks::Google,
                'site_user_id' => '726468316',
                'dob' => null,
                'gender' => 'U',
                'avatarURL' => ''
            );
        }
    }
