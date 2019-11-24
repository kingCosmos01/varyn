<?php
/**
 * The Facebook implementation of our SocialServices base class.
 * @author: jf
 * @date: 5/10/2016
 */

require_once 'lib/vendor/facebook/autoload.php';

define ('FACEBOOK_SESSION_KEY', 'engfbsession');


class SocialServicesFacebook extends SocialServices
{
    private $fb = null;
    private $appId = '';
    private $appSecret = '';
    private $isLoggedIn = false;

    public function __construct () {
        global $socialServiceKeys; // from serverConfig.
        $this->appId = $socialServiceKeys[EnginesisNetworks::Facebook]['app_id'];
        $this->appSecret = $socialServiceKeys[EnginesisNetworks::Facebook]['app_secret'];
        $this->setNetworkId(EnginesisNetworks::Facebook);
        $this->fb = new Facebook\Facebook([
            'app_id' => $this->appId,
            'app_secret' => $this->appSecret,
            'default_graph_version' => 'v5.0'
        ]);
        if (isset($_SESSION[FACEBOOK_SESSION_KEY])) {
            $accessToken = $_SESSION[FACEBOOK_SESSION_KEY];
            $this->m_accessToken = (string) $accessToken;
            $this->fb->setDefaultAccessToken($this->m_accessToken);
        }
    }

    /**
     * Perform all necessary steps to log user in and get their basic info.
     * TODO: extendToken?
     * @return {object} User info object
     */
    public function connectSSO () {
        $userInfo = null;
        if ($this->fb) {
            if ($this->isLoggedIn) {
                $userInfo = $this->currentUserInfo();
            } else {
                $this->login();
                if ($this->isLoggedIn) {
                    $userInfo = $this->currentUserInfo();
                }
            }
        }
        return $userInfo;
    }

    /**
     * Invoke the network's login procedure.
     */
    public function login () {
        if ( ! $this->fb) {
            return false;
        }
        if ( ! empty($this->m_accessToken)) {
            $this->isLoggedIn = true;
            return true;
        }
        $error = '';
        $errorMessage = '';
        $this->isLoggedIn = false;
        $fbHelper = $this->fb->getJavaScriptHelper();
        try {
            $accessToken = $fbHelper->getAccessToken();
        } catch (Facebook\Exceptions\FacebookResponseException $e) {
            // When Graph returns an error
            $error = EnginesisErrors::INVALID_LOGIN;
            $errorMessage = 'Facebook login error ' . $e->getMessage();
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
            // When validation fails or other local issues
            $error = EnginesisErrors::INVALID_LOGIN;
            $errorMessage = 'Facebook login failed ' . $e->getMessage();
        }
        if ( ! isset($accessToken)) {
            $this->setLastError($error, $errorMessage);
        } else {
            $this->clearError();
            $this->isLoggedIn = true;
            $this->m_accessToken = (string) $accessToken;
            $this->extendToken();
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
        if ( ! $this->fb) {
            return false;
        }
        if ($this->isLoggedIn) {
            $oAuth2Client = $this->fb->getOAuth2Client();
            $longLivedAccessToken = $oAuth2Client->getLongLivedAccessToken($this->m_accessToken);
            $this->m_accessToken = (string) $longLivedAccessToken->getValue();
            $expireDate = $longLivedAccessToken->getExpiresAt();  //  TODO: We should also save the expire so we know when to check again
            $this->fb->setDefaultAccessToken($this->m_accessToken);
            $_SESSION[FACEBOOK_SESSION_KEY] = $this->m_accessToken;
            return true;
        } else {
            return false;
        }
    }

    /**
     * Log any current user out of the current network.
     */
    public function logout () {
        if ( ! $this->fb) {
            return false;
        }
        // TODO:
        return false;
    }

    /**
     * Return a $userInfo object representing the current logged in user.
     * Facebook: [items:protected] => Array ( [id] => 726468316 [name] => John Foster [email] => jfoster@acm.org [gender] => male
     * Enginesis: array('user_name' => $userName, 'email_address' => $email, 'real_name' => $realName, 'gender' => $gender, 'agreement' => $agreement);
     */
    public function currentUserInfo () {
        if ( ! $this->fb) {
            return false;
        }
        $error = '';
        $errorMessage = '';
        $userInfo = null;
        try {
            // Returns a `Facebook\FacebookResponse` object
            $response = $this->fb->get('/me?fields=id,name,email,gender');
        } catch(Facebook\Exceptions\FacebookResponseException $e) {
            $error = EnginesisErrors::INVALID_LOGIN;
            $errorMessage = 'Facebook graph error ' . $e->getMessage();
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
            $error = EnginesisErrors::INVALID_LOGIN;
            $errorMessage = 'Facebook SDK error ' . $e->getMessage();
        }
        $this->setLastError($error, $errorMessage);
        if ($error == '') {
            $user = $response->getGraphUser();
            if ($user != null && isset($user['id'])) {
                // Convert Facebook's $user into Enginesis $userInfo
                $this->m_site_user_id = $user['id'];
                if ($user->offsetExists('gender')) {
                    $gender = strtoupper($user['gender'][0]);
                } else {
                    $gender = 'U';
                }
                $userInfo = [
                    'network_id' => EnginesisNetworks::Facebook,
                    'site_user_id' => $this->m_site_user_id,
                    'real_name' => $user['name'],
                    'user_name' => $user['name'],
                    'email_address' => $user['email'],
                    'gender' => $gender,
                    'dob' => '',
                    'agreement' => '1',
                    'scope' => '',
                    'avatar_url' => 'https://graph.facebook.com/' . $user['id'] . '/picture?type=square&width=120&height=120',
                    'id_token' => ''
                ];
            } else {
                $this->setLastError('INVALID_PARAM', 'User is not properly logged in via Facebook SDK');
                $userInfo = null;
            }
        }
        return $userInfo;
    }

    public function getFriends () {
        if ( ! $this->fb) {
            return null;
        }
        // TODO:
        return null;
    }

    /**
     * Return a URL to the image of the user on the network signed in to.
     * @param int $size image size, abiding by the Enginesis values (0=small, 1=medium, 2=large)
     * @returns {string} URL to an image.
     */
    public function currentUserProfileImage ($size = 0) {
        $type = 'square';
        switch ($size) {
            case 1: // medium
                $width = 120;
                break;
            case 2: // large
                $width = 512;
                break;
            default: // small
                $width = 90;
                break;
        }
        return 'https://graph.facebook.com/' . $this->m_site_user_id . '/picture?type=' . $type . '&width=' . $width . '&height=‌​' . $width;
    }

    public function deleteApp () {
        // TODO:
        return false;
    }

    private function fakeUser() {
        return (object) [
            'network_id' => EnginesisNetworks::Facebook,
            'site_user_id' => '726468316',
            'user_name' => 'FakeFbUser',
            'real_name' => 'Fake Fb User',
            'email_address' => 'info@varyn.com',
            'dob' => null,
            'gender' => 'U',
            'agreement' => '1',
            'scope' => 'email',
            'avatar_url' => 'https://graph.facebook.com/726468316/picture?type=square&width=120&height=120',
            'id_token' => ''
        ];
    }
}
