<?php

/**
 * The Apple implementation of our SocialServices base class.
 * @author: jf
 * @date: 7/16/2017
 */

require_once 'lib/vendor/google/vendor/autoload.php';

class SocialServicesApple extends SocialServices {
    private $isLoggedIn = false;
    private $userInfo = null;

    public function __construct() {
        global $socialServiceKeys; // from serverConfig.
        $this->setNetworkId(EnginesisNetworks::Apple);
        $this->m_networkName = 'Apple';
    }

    /**
     * Perform all necessary steps to log user in and get their basic info.
     * TODO: extendToken?
     * @return {object} User info object
     */
    public function connectSSO() {
        $userInfo = null;
        return $userInfo;
    }

    private function exchangeToken($idToken) {
        $userInfo = null;
        return $userInfo;
    }

    public function login() {
        return $this->isLoggedIn;
    }

    public function loginPostProcess() {
        return $this->extendToken();
    }

    private function extendToken() {
        return false;
    }

    public function logout() {
        return false;
    }

    public function currentUserInfo() {
        $userInfo = $this->userInfo;
        return $userInfo;
    }

    public function getFriends() {
        return null;
    }

    public function currentUserProfileImage($size = 0) {
        return '';
    }

    public function deleteApp() {
        return false;
    }

    public function validateIdToken($userIdToken) {
        $site_user_id = '';
        return $site_user_id;
    }

    private function fetchSigningKeys() {
        // For https://developer.apple.com/documentation/sign_in_with_apple/fetch_apple_s_public_key_for_verifying_token_signature
        //  select the key with the matching key identifier (kid) to verify the signature of any JSON Web Token (JWT) issued by Apple
        $endpoint = 'https://appleid.apple.com/auth/keys';
    }

    private function generateToken() {
        // For https://developer.apple.com/documentation/sign_in_with_apple/generate_and_validate_tokens

        $endpoint = 'https://appleid.apple.com/auth/token';

        /*
        curl -v POST "https://appleid.apple.com/auth/token" \
        -H 'content-type: application/x-www-form-urlencoded' \
        -d 'client_id=CLIENT_ID' \
        -d 'client_secret=CLIENT_SECRET' \
        -d 'code=CODE' \
        -d 'grant_type=authorization_code' \
        -d 'redirect_uri=REDIRECT_URI'
        */
    }

    private function fakeUser() {
        return (object) [
            'network_id' => EnginesisNetworks::Apple,
            'site_user_id' => '726468316',
            'user_name' => 'FakeAppleUser',
            'real_name' => 'Fake Apple User',
            'email_address' => 'info@varyn.com',
            'dob' => null,
            'gender' => 'U',
            'agreement' => '1',
            'scope' => 'profile email',
            'avatar_url' => '',
            'id_token' => ''
        ];
    }
}
