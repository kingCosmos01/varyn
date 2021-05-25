<?php
/**
 * SocialServices acts as an interface to all the social platforms we integrate with to do things like SSO
 * and get info about a given user on that network. The default implementation uses Enginesis.
 * @author: jf
 * @date: 5/10/2016
 */

require_once 'Enginesis.php';
require_once 'SocialServicesFacebook.php';
require_once 'SocialServicesTwitter.php';
require_once 'SocialServicesGoogle.php';
require_once 'SocialServicesApple.php';


class SocialServices
{
    // All property access is private, use accessor functions.
    protected $m_networkId = EnginesisNetworks::Enginesis;
    protected $m_networkName = 'Enginesis';
    protected $m_lastErrorCode = '';
    protected $m_lastErrorMessage = '';
    protected $m_accessToken = null;
    protected $m_site_user_id = '';

    /**
     * Factory function to create a proper SocialServices object based on a network id.
     * @param $networkId
     * @return SocialServices|SocialServicesFacebook
     */
    public static function create($networkId) {
        switch ($networkId) {
            case EnginesisNetworks::Enginesis:
                return new SocialServices($networkId);
                break;
            case EnginesisNetworks::Facebook:
                return new SocialServicesFacebook($networkId);
                break;
            case EnginesisNetworks::Google:
                return new SocialServicesGoogle($networkId);
                break;
            case EnginesisNetworks::Twitter:
                return new SocialServicesTwitter($networkId);
                break;
            case EnginesisNetworks::Apple:
                return new SocialServicesApple($networkId);
                break;
            default:
                return null;
                break;
        }
    }

    /**
     * SocialServices constructor for Enginesis default implementation.
     * @param int $networkId
     */
    public function __construct ($networkId = EnginesisNetworks::Enginesis) {
        $this->setNetworkId($networkId);
    }

    public function setNetworkId ($networkId) {
        $this->m_networkId = $networkId;
    }

    public function getNetworkId () {
        return $this->m_networkId;
    }

    public function getNetworkName () {
        return $this->m_networkName;
    }

    public function getSiteUserId () {
        return $this->m_site_user_id;
    }

    public function getLastError () {
        if ($this->m_lastErrorCode == '') {
            return null;
        } else {
            return array('code' => $this->m_lastErrorCode, 'message' => $this->m_lastErrorMessage);
        }
    }

    public function getAccessToken () {
        return $this->m_accessToken;
    }

    protected function clearError () {
        $this->m_lastErrorCode = '';
        $this->m_lastErrorMessage = '';
    }

    protected function setLastError ($code, $message) {
        $this->m_lastErrorCode = $code;
        $this->m_lastErrorMessage = $message;
    }

    /**
     * Perform all necessary steps to log user in and get their basic info.
     * @return {object} User info object
     */
    public function connectSSO () {
        return null;
    }

    /**
     * Invoke the network's login procedure. Returns true is logged in.
     */
    public function login () {
        // $enginesis->userLogin($userName, $password, $saveSession);
        return false;
    }

    /**
     * Perform any network-specific requirements after a first time login is successful.
     * For example, usually you need to get an extended token
     */
    public function loginPostProcess () {
        return false;
    }

    /**
     * Log any current user out of the current network. Returns true if logged out.
     */
    public function logout () {
        // $enginesis->userLogout();
        return false;
    }

    /**
     * Return a $userInfo object representing the current logged in user. User info looks like this template:
     *  array(
     * 'network_id' => EnginesisNetworks::networkId,
     * 'site_user_id' => '',
     * 'real_name' => '',
     * 'user_name' => '',
     * 'email_address' => '',
     * 'gender' => 'U',
     * 'dob' => '',
     * 'agreement' => '1',
     * 'scope' => '',
     * 'avatar_url' => 'url',
     * 'id_token' => 'token');
     * @return {object} $userInfo
     */
    public function currentUserInfo () {
        // $enginesis->registeredUserGet();
        $userInfo = null;
        return $userInfo;
    }

    /**
     * Return a URL to the image of the user on the network signed in to.
     * @param int $size image size, abiding by the Enginesis values (0=small, 1=medium, 2=large)
     * @returns {string} URL to an image, '' if we cannot figure it out.
     */
    public function currentUserProfileImage ($size = 0) {
        // $enginesis->avatarURL($size);
        return '';
    }

    /**
     * Return an array of friends for the current user.
     */
    public function getFriends () {
        // $enginesis->friendList();
        return null;
    }

    /**
     * User requests app deletion, we should remove any association to this account. Returns true if deleted.
     */
    public function deleteApp () {
        // $enginesis->friendList();
        return false;
    }
}
