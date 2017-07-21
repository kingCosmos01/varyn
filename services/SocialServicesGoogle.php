<?php
    /**
     * The Google Plus implementation of our SocialServices base class.
     * @author: jf
     * @date: 7/16/2017
     */

    define ('GOOGLEAPI_SESSION_KEY', 'enggapisession');


    class SocialServicesGoogle extends SocialServices
    {
        private $appId = '';
        private $appSecret = '';
        private $isLoggedIn = false;

        public function __construct () {
            global $socialServiceKeys; // from serverConfig.
            $this->appId = $socialServiceKeys[EnginesisNetworks::Google]['app_id'];
            $this->appSecret = $socialServiceKeys[EnginesisNetworks::Google]['app_secret'];
            $this->setNetworkId(EnginesisNetworks::Google);
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
            return $userInfo;
        }

        /**
         * Invoke the network's login procedure.
         */
        public function login () {
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
