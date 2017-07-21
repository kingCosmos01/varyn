<?php
    /**
     * The Twitter implementation of our SocialServices base class.
     * @author: jf
     * @date: 7/14/2017
     */

    define ('TWITTER_SESSION_KEY', 'engtwsession');


    class SocialServicesTwitter extends SocialServices
    {
        private $appId = '';
        private $appSecret = '';
        private $isLoggedIn = false;
        private $profileImgUrl = null;

        public function __construct () {
            global $socialServiceKeys; // from serverConfig.
            $this->setNetworkId(EnginesisNetworks::Twitter);
            $this->appId = $socialServiceKeys[EnginesisNetworks::Twitter]['app_id'];
            $this->appSecret = $socialServiceKeys[EnginesisNetworks::Twitter]['app_secret'];
            if (isset($_SESSION[TWITTER_SESSION_KEY])) {
                $accessToken = $_SESSION[TWITTER_SESSION_KEY];
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
         * For Twitter we need to get an extended token after a first-time login.
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
         * Twitter:
         * Enginesis: array('user_name' => $userName, 'email_address' => $email, 'real_name' => $realName, 'gender' => $gender, 'agreement' => $agreement);
         */
        public function currentUserInfo () {
            /*
             * object(stdClass)#8 (43)
             * { ["id"]=> int(1184539699)
             * ["id_str"]=> string(10) "1184539699"
             * ["name"]=> string(9) "Varyn Dev"
             * ["screen_name"]=> string(8) "VarynDev"
             * ["location"]=> string(12) "New York, NY"
             * ["description"]=> string(84) "Game developer, now with HTML5 and Unity, previously Flash, Director, C++, iOS, Java"
             * ["url"]=> string(22) "http://t.co/0r4ZbHNUfg"
             * ["entities"]=> object(stdClass)#9 (2) {
             *     ["url"]=> object(stdClass)#10 (1) {
             *         ["urls"]=> array(1) {
             *             [0]=> object(stdClass)#11 (4)
             *                 { ["url"]=> string(22) "http://t.co/0r4ZbHNUfg"
             *                   ["expanded_url"]=> string(20) "http://www.varyn.com"
             *                   ["display_url"]=> string(9) "varyn.com"
             *                   ["indices"]=> array(2) {
             *                        [0]=> int(0)
             *                        [1]=> int(22)
             *     } } } }
             *     ["description"]=> object(stdClass)#12 (1) {
             *         ["urls"]=> array(0) { } } }
             * ["protected"]=> bool(false)
             * ["followers_count"]=> int(35)
             * ["friends_count"]=> int(87)
             * ["listed_count"]=> int(7)
             * ["created_at"]=> string(30) "Sat Feb 16 03:36:55 +0000 2013"
             * ["favourites_count"]=> int(10)
             * ["utc_offset"]=> int(-14400)
             * ["time_zone"]=> string(26) "Eastern Time (US & Canada)"
             * ["geo_enabled"]=> bool(false)
             * ["verified"]=> bool(false)
             * ["statuses_count"]=> int(90)
             * ["lang"]=> string(2) "en"
             * ["contributors_enabled"]=> bool(false)
             * ["is_translator"]=> bool(false)
             * ["is_translation_enabled"]=> bool(false)
             * ["profile_background_color"]=> string(6) "C0DEED"
             * ["profile_background_image_url"]=> string(48) "http://abs.twimg.com/images/themes/theme1/bg.png"
             * ["profile_background_image_url_https"]=> string(49) "https://abs.twimg.com/images/themes/theme1/bg.png"
             * ["profile_background_tile"]=> bool(false)
             * ["profile_image_url"]=> string(98) "http://pbs.twimg.com/profile_images/378800000151596725/701e4e6e6e0e9b957c8fe1b4498805ba_normal.png"
             * ["profile_image_url_https"]=> string(99) "https://pbs.twimg.com/profile_images/378800000151596725/701e4e6e6e0e9b957c8fe1b4498805ba_normal.png"
             * ["profile_banner_url"]=> string(59) "https://pbs.twimg.com/profile_banners/1184539699/1449962932"
             * ["profile_link_color"]=> string(6) "1DA1F2"
             * ["profile_sidebar_border_color"]=> string(6) "C0DEED"
             * ["profile_sidebar_fill_color"]=> string(6) "DDEEF6"
             * ["profile_text_color"]=> string(6) "333333"
             * ["profile_use_background_image"]=> bool(true)
             * ["has_extended_profile"]=> bool(false)
             * ["default_profile"]=> bool(true)
             * ["default_profile_image"]=> bool(false)
             * ["following"]=> bool(false)
             * ["follow_request_sent"]=> bool(false)
             * ["notifications"]=> bool(false)
             * ["translator_type"]=> string(4) "none"
             * ["email"]=> string(19) "varyn.dev@gmail.com" }
             */
            /**
             *         _userInfo = {
            userName: '',
            fullName: '',
            userId: '',
            networkId: 0,
            siteUserId: '',
            dob: null,
            gender: 'U',
            avatarURL: ''
            };
             */
            $twitterUserInfo = $this->fakeUser();
            $userInfo = array('network_id' => EnginesisNetworks::Twitter, 'site_user_id' => $this->m_site_user_id, 'real_name' => $user['name'], 'user_name' => '', 'email_address' => $user['email'], 'gender' => strtoupper($user['gender'][0]), 'dob' => '', 'scope' => '', 'agreement' => '1');
            $userInfo = array(
                'userName' => $twitterUserInfo->screen_name,
                'fullName' => $twitterUserInfo->name,
                'userId' => $twitterUserInfo->id,
                'email' => $twitterUserInfo->email,
                'networkId' => EnginesisNetworks::Twitter,
                'siteUserId' => $twitterUserInfo->id_str,
                'dob' => null,
                'gender' => 'U',
                'avatarURL' => $twitterUserInfo->profile_image_url_https
            );

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
            return $this->profileImgUrl;
        }

        public function deleteApp () {
            return false;
        }

        private function fakeUser() {
            return (object) array(
                'userName' => 'FakeTwUser',
                'fullName' => 'Fake Tw User',
                'userId' => 1184539699,
                'email' => 'info@varyn.com',
                'networkId' => EnginesisNetworks::Twitter,
                'siteUserId' => '1184539699',
                'dob' => null,
                'gender' => 'U',
                'avatarURL' => 'https://pbs.twimg.com/profile_images/378800000151596725/701e4e6e6e0e9b957c8fe1b4498805ba_normal.png'
            );
        }
    }
