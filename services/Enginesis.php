<?php
    /**
     * Enginesis service object for PHP. Support for each Enginesis API and additional helper functions.
     * User: jf
     * Date: 2/13/16
     */

    require_once('errors.php');
    define('SESSION_COOKIE', 'engsession');
    define('SESSION_USERINFO', 'engsession_user');
    define('SESSION_DAYSTAMP_HOURS', 48);
    define('SESSION_USERID_CACHE', 'engsession_uid');
    if ( ! defined('ENGINESIS_VERSION')) {
        define('ENGINESIS_VERSION', '2.3.26');
    }

    abstract class EnginesisNetworks {
        const Enginesis = 1;
        const Facebook = 2;
        const OpenSocial = 3;
        const Flux = 4;
        const AddictingGames = 5;
        const Google = 7;
        const BeBo = 8;
        const Friendster = 9;
        const MySpace = 10;
        const Twitter = 11;
        const Hi5 = 11;
        const AOL = 12;
    }

    class Enginesis
    {
        private $m_server;
        private $m_serviceRoot;
        private $m_serviceEndPoint;
        private $m_avatarEndPoint;
        private $m_lastError;
        private $m_siteId;
        private $m_gameId;
        private $m_gameGroupId;
        private $m_isLoggedIn;
        private $m_userId;
        private $m_siteUserId;
        private $m_networkId;
        private $m_userName;
        private $m_userAccessLevel;
        private $m_stage;
        private $m_syncId;
        private $m_serviceProtocol;
        private $m_responseFormat;
        private $m_debugFunction;
        private $m_developerKey;
        private $m_languageCode;
        private $m_authToken;
        private $m_authTokenWasValidated;

        /**
         * @method constructor
         * @purpose: Set up the Enginesis environment so it is able to easily make service requests with the server.
         * TODO: set m_debugFunction as a function to call whena debug statment is called
         * TODO: validate the developer key on site_id.
         */
        public function __construct ($siteId, $enginesisServer, $developerKey) {
            $this->m_server = $this->serverName();
            $this->m_stage = $this->serverStage($this->m_server);
            $this->m_lastError = null;
            $this->m_siteId = $siteId;
            $this->m_userId = 0;
            $this->m_userName = '';
            $this->m_siteUserId = null;
            $this->m_networkId = 1;
            $this->m_userAccessLevel = 0;
            $this->m_isLoggedIn = false;
            $this->m_syncId = 0;
            $this->m_gameId = 0;
            $this->m_gameGroupId = 0;
            $this->m_serviceProtocol = $this->getServiceProtocol();
            $this->m_responseFormat = 'json';
            $this->m_debugFunction = null;
            $this->m_developerKey = $developerKey;
            $this->m_languageCode = 'en';
            $this->m_authToken = null;
            $this->m_authTokenWasValidated = false;
            if (empty($enginesisServer)) {
                // Caller doesn't know which stage, converse with the one that matches the stage we are on
                $enginesisServiceRoot = $this->m_serviceProtocol . '://enginesis' . $this->m_stage . '.com/';
            } elseif (strlen($enginesisServer) == 2) {
                // Caller may provide a stage we should converse with
                $enginesisServiceRoot = $this->m_serviceProtocol . '://enginesis' . $enginesisServer . '.com/';
            } else {
                // Caller can provide a specific server we should converse with
                $enginesisServiceRoot = $this->m_serviceProtocol . '://' . $enginesisServer . '/';
            }
            $this->m_serviceRoot = $enginesisServiceRoot;
            $this->m_serviceEndPoint = $enginesisServiceRoot . '/index.php';
            $this->m_avatarEndPoint = $enginesisServiceRoot . '/avatar.php';
            $this->restoreUserFromAuthToken(null);
        }

        /**
         * @method destructor
         * @purpose: free any references before destructing the object
         */
        public function __destruct () {
            $this->reset();
        }

        private function reset () {
            $this->m_server = $this->serverName();
            $this->m_stage = $this->serverStage($this->m_server);
            $this->m_lastError = null;
            $this->m_userId = 0;
            $this->m_isLoggedIn = false;
            $this->m_syncId = 0;
            $this->m_serviceProtocol = $this->getServiceProtocol();
        }

        /**
         * Return the version of this Enginesis library.
         * @return string version identifier
         */
        public function version() {
            return ENGINESIS_VERSION;
        }

        /**
         * Determine if the $id is valid.
         * @param $id
         * @return bool
         */
        public function isValidId ($id) {
            return $id != null && $id > 0;
        }

        /**
         * Determine if the $id is not valid.
         * @param $id
         * @return bool
         */
        public function isInvalidId ($id) {
            return ! $this->isValidId($id);
        }

        /**
         * Determine if the string is valid.
         * @param $string string to test
         * @param $minLength int The minimum length allowed. 0 will allow both null and empty string.
         * @param $maxLength int The maximum length allowed.
         * @param $allowEmpty bool true then allow empty/non-existing string.
         * @param $allowTags bool true then make sure string does not contain HTML.
         * @return bool
         */
        public function isValidString ($string, $minLength, $maxLength, $allowEmpty, $allowTags) {
            if ($allowEmpty && strlen($string) == 0) {
                return true;
            } else {
                if ( ! $allowTags && $string != strip_tags($string)) {
                    return false;
                } else {
                    $len = strlen($string);
                    return  $len >= $minLength && $len <= $maxLength;
                }
            }
        }

        /**
         * Determine if a user name passes basic validity checks.
         * @param $userName
         * @return bool
         */
        public function isValidUserName ($userName) {
            $badNames = array('null', 'undefined', 'xxx', 'shit', 'fuck', 'dick');
            return strlen(trim($userName)) > 2 && ! in_array($userName, $badNames);
        }

        /**
         * Determine if a password passes basic validity checks.
         * @param $password
         * @return bool
         */
        public function isValidPassword ($password) {
            return strlen(trim($password)) > 3;
        }

        /**
         * Determine if we have a valid gender setting.
         * @param $gender
         * @return bool
         */
        public function isValidGender ($gender) {
            $acceptableGenders = array('M', 'Male', 'F', 'Female', 'X', 'undefined');
            return in_array($gender, $acceptableGenders);
        }

        /**
         * Determine if the date is acceptable.
         * @param $date
         * @return bool
         */
        public function isValidDate ($date) {
            $dateParts = explode('-', $date);
            return count($dateParts) == 3 && checkdate($dateParts[1], $dateParts[2], $dateParts[0]);
        }

        /**
         * Determine the site-id.
         * @return int
         */
        public function getSiteId () {
            return $this->m_siteId;
        }

        /**
         * Determine the user-id.
         * @return int
         */
        public function getUserId () {
            if ( ! $this->m_authTokenWasValidated) {
                $this->restoreUserFromAuthToken();
            }
            return $this->m_userId;
        }

        /**
         * Determine the user-name.
         * @return string
         */
        public function getUserName () {
            if ( ! $this->m_authTokenWasValidated) {
                $this->restoreUserFromAuthToken();
            }
            return $this->m_userName;
        }

        /**
         * Determine the user access level.
         * @return int
         */
        public function getUserAccessLevel () {
            if ( ! $this->m_authTokenWasValidated) {
                $this->restoreUserFromAuthToken();
            }
            return $this->m_userAccessLevel;
        }

        /**
         * Determine the network used to validate this user.
         * @return int
         */
        public function getNetworkId () {
            if ( ! $this->m_authTokenWasValidated) {
                $this->restoreUserFromAuthToken();
            }
            return $this->m_networkId;
        }

        /**
         * Determine the user-id on the SSO network.
         * @return string
         */
        public function getSiteUserId () {
            if ( ! $this->m_authTokenWasValidated) {
                $this->restoreUserFromAuthToken();
            }
            return $this->m_siteUserId;
        }

        /**
         * Return the language code.
         * @return string
         */
        public function getLanguageCode () {
            return $this->m_languageCode;
        }

        /**
         * @method serverName
         * @purpose: determine the full domain name of the server we are currently running on.
         * @return: {string} server host name only, e.g. www.enginesis.com.
         */
        private function serverName () {
            if (strpos($_SERVER['HTTP_HOST'], ':' ) !== false) {
                $host_name = isset($_SERVER['HTTP_X_FORWARDED_HOST'] ) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : $_SERVER['HTTP_HOST'];
                $server = substr($host_name, 0, strpos( $host_name, ':' ) );
            } else {
                $server = isset($_SERVER['HTTP_X_FORWARDED_HOST'] ) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : $_SERVER['HTTP_HOST'];
            }
            return $server;
        }

        public function getServerName () {
            return $this->m_server;
        }

        public function getServiceRoot () {
            return $this->m_serviceRoot;
        }

        /**
         * Return the domain name and TLD only (remove server name, protocol, anything else) e.g. this function
         * converts http://www.games.com into games.com or http://www.games-q.com into games-q.com
         * @param null $serverName
         * @return null|string
         */
        private function serverTail ($serverName = null) {
            if (strlen($serverName) == 0) {
                $serverName = $this->getServerName();
            }
            if ($serverName != 'localhost') {
                $urlParts = explode('.', $serverName);
                $numParts = count($urlParts);
                if ($numParts > 1) {
                    $tld = '.' . $urlParts[$numParts - 1];
                    $domain = $urlParts[$numParts - 2];
                } else {
                    $domain = $urlParts[0];
                    $tld = '';
                }
                if (strpos($domain, '://') > 0) {
                    $domain = substr($domain, strpos($domain, '://') + 3);
                }
            }
            $serverName = $domain . $tld;
            return $serverName;
        }

        /**
         * @method serverStage
         * @purpose Parse the given host name to determine which stage we are currently running on.
         * @return string: server host name only, e.g. www.enginesis.com.
         */
        private function serverStage ($hostName = null) {
            // return just the -l, -d, -q, -x part, or '' for live.
            $targetPlatform = ''; // assume live until we prove otherwise
            if (strlen($hostName) == 0) {
                $hostName = $this->serverName();
            }
            if (strpos($hostName, '-l.') >= 2) {
                $targetPlatform = '-l';
            } elseif (strpos($hostName, '-d.') >= 2) {
                $targetPlatform = '-d';
            } elseif (strpos($hostName, '-q.') >= 2) {
                $targetPlatform = '-q';
            } elseif (strpos($hostName, '-x.') >= 2) {
                $targetPlatform = '-x';
            }
            return $targetPlatform;
        }

        public function getServerStage () {
            return $this->m_stage;
        }

        /**
         * @method getServiceProtocol
         * @purpose Determine if we are runing on http or https.
         * @return string: HTTP protocol, either http or https.
         */
        public function getServiceProtocol () {
            // return http or https. you should use the result of this and never hard-code http:// into any URLs.
            if ($this->m_serviceProtocol) {
                $protocol = $this->m_serviceProtocol;
            } elseif (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
                $protocol = 'https';
            } else {
                $protocol = 'http';
            }
            return $protocol;
        }

        /**
         * When looking at a key => value array return a default value if the given key is not found.
         * @param $kvArray
         * @param $key
         * @param $default
         * @return mixed
         */
        private function arrayValueOrDefault ($kvArray, $key, $default) {
            if ( ! is_array($kvArray)) {
                return $default;
            } else {
                return isset($kvArray[$key]) ? $kvArray[$key] : $default;
            }
        }

        /**
         * If the API call returned an error this function sets the lastError object. If there was no error then
         * lastError is null.
         * @param $enginesisResponse A response object from an Enginesis API call.
         * @return null|object Returns null if an error occurred (weird, right? but other logic already depends on this.)
         */
        private function setLastErrorFromResponse ($enginesisResponse) {
            $results = null;
            if ($enginesisResponse) {
                $success = 0;
                $statusMessage = '';
                $extendedInfo = '';
                $results = $this->getResponseStatus($enginesisResponse, $success, $statusMessage, $extendedInfo);
                if ($results == null) {
                    $this->m_lastError = array('success' => $success, 'message' => $statusMessage, 'extended_info' => $extendedInfo);
                } else {
                    $this->m_lastError = null;
                }
            }
            return $results;
        }

        /**
         * Force-set the last error message for internal (non-API) errors.
         * @param $errorCode string A typical Enginesis error code.
         * @param $errorMessage string An optional error message.
         * @return null|object Returns the lastError object, null if the last error was successful.
         */
        private function setLastError ($errorCode, $errorMessage) {
            $success = $errorCode == '';
            if ($success) {
                $this->m_lastError = null;
            } else {
                $this->m_lastError = array('success' => $success, 'message' => $errorCode, 'extended_info' => $errorMessage);
            }
            return $this->m_lastError;
        }

        /**
         * Figure out which domain we want to save the cookie under.
         * @param null $serverName
         * @return null|string
         */
        private function sessionCookieDomain ($serverName = null) {
            $newDomain = null;
            $domain = $this->serverTail($serverName);
            if (strlen($domain) > 0) {
                $newDomain = '.' . $domain;
            }
            return $newDomain;
        }

        /**
         * Get the authentication token, either it was provided in the http get/post, or it is in the cookie. POST overrides cookie.
         * This function does not determine if the authentication token is actually valid (use sessionValidateAuthenticationToken for that.)
         * @return string token, null if no token could be found.
         */
        public function sessionGetAuthenticationToken () {
            $authenticationToken = getPostOrRequestVar('authtok', '');
            if (empty($authenticationToken)) {
                if (isset($_COOKIE[SESSION_COOKIE])) {
                    $authenticationToken = $_COOKIE[SESSION_COOKIE];
                } else {
                    $authenticationToken = null;
                }
            }
            return $authenticationToken;
        }

        /**
         * Restore the user's session from the provided authentication token.
         * @param null $authToken
         */
        private function restoreUserFromAuthToken ($authToken = null) {
            $this->m_authTokenWasValidated = false;
            if (empty($authToken)) {
                $authToken = $this->sessionGetAuthenticationToken();
            }
            if ( ! empty($authToken)) {
                $sessionSiteId = 0;
                $sessionUserId = 0;
                $sessionUserName = '';
                $sessionSiteUserId = '';
                $sessionNetworkId = 1;
                $sessionAccessLevel = 0;
                $errorCode = $this->sessionValidate($authToken, $sessionSiteId, $sessionUserId, $sessionUserName, $sessionSiteUserId, $sessionAccessLevel, $sessionNetworkId);
                if ($errorCode == '' && $sessionUserId != 0) {
                    $this->m_siteId = $sessionSiteId;
                    $this->m_userId = $sessionUserId;
                    $this->m_userName = $sessionUserName;
                    $this->m_siteUserId = $sessionSiteUserId;
                    $this->m_networkId = $sessionNetworkId;
                    $this->m_userAccessLevel = $sessionAccessLevel;
                    $this->m_authToken = $this->authTokenMake($sessionSiteId, $sessionUserId, $sessionUserName, $sessionSiteUserId, $sessionAccessLevel, $sessionNetworkId);
                    $this->m_authTokenWasValidated = true;
                // } else {
                    // echo("<h3>restoreUserFromAuthToken FAILED</h3>");
                    // exit(0);
                }
            }
            if ( ! $this->m_authTokenWasValidated) {
                // TODO: should we clear the cookie and user info?
                $this->sessionClear();
            }
        }

        /**
         * Create an authentication token using the given parameters, or defaults from settings in the current object.
         * @param $siteId
         * @param $userId
         * @param $siteUserId
         * @param $userName
         * @param $accessLevel
         * @param $networkId
         * @returns {string} encrypted user authentication token
         */
        private function authTokenMake ($siteId, $userId, $userName, $siteUserId, $accessLevel, $networkId) {
            if ($this->m_authTokenWasValidated) {
                return $this->m_authToken;
            }
            if ($userId == null || $userId < 1) {
                $userId = $this->m_userId;
            }
            if ($siteUserId == null || $siteUserId == '') {
                $siteUserId = $this->m_siteUserId;
            }
            if ($userName == null || $userName == '') {
                $userName = $this->m_userName;
            }
            $decryptedData = 'siteid=' . $siteId . '&userid=' . $userId . '&siteuserid=' . $siteUserId . '&networkid=' . $networkId . '&username=' . $userName . '&accesslevel=' . $accessLevel . '&daystamp=' . $this->sessionDayStamp();
            $tokenDataBase64 = base64_encode(mcrypt_encrypt(MCRYPT_BLOWFISH, pack('H*', $this->m_developerKey), $this->blowfishPad($decryptedData), MCRYPT_MODE_ECB, pack('H*', '000000000000000')));
            $tokenDataBase64Clean = str_replace('+', ' ', $tokenDataBase64);
            return $tokenDataBase64Clean;
        }

        /**
         * Decrypt an authentication token and return an array of items contained in it. This function is designed to undo
         * what sessionMakeAuthenticationTokenEncrypted did but returning an array of the input parameters.
         * @param $authenticationToken {string} the encrypted token.
         * @return array|null Returns null if the token could not be decrypted, when successful returns an array matching the
         *     input parameters of sessionMakeAuthenticationTokenEncrypted.
         */
        private function authTokenDecrypt ($authenticationToken) {
            $dataArray = null;
            $tokenData = $this->blowfishUnpad(mcrypt_decrypt(MCRYPT_BLOWFISH, pack('H*', $this->m_developerKey), base64_decode(str_replace(' ', '+', $authenticationToken)), MCRYPT_MODE_ECB, pack('H*', '000000000000000')));
            if ($tokenData != null && $tokenData != '') {
                $dataArray = $this->decodeURLParams($tokenData);
            }
            return $dataArray;
        }

        /**
         * Generate a time stamp for the current time rounded to the nearest SESSION_DAYSTAMP_HOURS hour.
         * @return int
         */
        private function sessionDayStamp () {
            return floor(time() / (SESSION_DAYSTAMP_HOURS * 60 * 60)); // good for SESSION_DAYSTAMP_HOURS hours
        }

        /**
         * Determine if a day stamp is currently valid. Day stamps expire after SESSION_DAYSTAMP_HOURS.
         * @param $dayStamp
         * @return bool
         */
        private function sessionIsValidDayStamp ($dayStamp) {
            $day_stamp_current = $this->sessionDayStamp();
            return ! ($dayStamp < ($day_stamp_current - (SESSION_DAYSTAMP_HOURS / 24)) || $dayStamp > $day_stamp_current);
        }

        /**
         * Create a user-based and time-sensitive session identifier. Typically used to identify a unique game session
         * for a specific user so another user can't spoof that user.
         * @return string
         */
        private function sessionMakeId () {
            return md5($this->m_developerKey . '' . $this->sessionDayStamp() . '' . $this->m_userId);
        }

        /**
         * This function validates a token used to communicate to the client who is logged in user_id, user_name and site_user_id
         * @param $authToken
         * @param $site_id
         * @param $user_id
         * @param $user_name
         * @param $site_user_id
         * @param $access_level
         * @param $network_id
         * @return string
         */
        private function sessionValidate ($authToken, & $site_id, & $user_id, & $user_name, & $site_user_id, & $access_level, & $network_id) {
            $rc = '';
            if (empty($authToken)) {
                $authToken = $this->sessionGetAuthenticationToken();
            }
            if (strlen($authToken) > 0) {
                $dataArray = $this->authTokenDecrypt($authToken);
                if (isset($dataArray['daystamp'])) {
                    if ($this->sessionIsValidDayStamp($dataArray['daystamp'])) {
                        $site_id = $dataArray['siteid'];
                        $user_id = $dataArray['userid'];
                        $user_name = $dataArray['username'];
                        $site_user_id = $dataArray['siteuserid'];
                        $access_level = $dataArray['accesslevel'];
                        $network_id = $dataArray['networkid'];
                    } else {
                        $rc = 'TOKEN_EXPIRED';
                    }
                } else {
                    $rc = 'INVALID_TOKEN';
                }
            } else {
                $rc = 'INVALID_TOKEN';
            }
            return $rc;
        }

        /**
         * Save the authenticated session to cookie so we can retrieve it next time this user returns.
         * @param $authenticationToken string the encrypted authentication token generated by sessionMakeAuthenticationTokenEncrypted.
         * @param $user_id
         * @param $user_name
         * @param $site_user_id
         * @param $access_level
         * @param $network_id
         * @return string An error code, '' if successful.
         */
        private function sessionSave ($authenticationToken, $user_id, $user_name, $site_user_id, $access_level, $network_id) {
            $rc = '';
            $errorLevel = error_reporting(); // turn off warnings so we don't generate crap in the output stream. If we don't do this fucking php writes an error and screws up the output stream. (I cant get the try/catch to work without it)
            error_reporting($errorLevel & ~E_WARNING);
            try {
                if (setcookie(SESSION_COOKIE, $authenticationToken, time() + (SESSION_DAYSTAMP_HOURS * 60 * 60), '/', $this->sessionCookieDomain()) === false) {
                    $rc = 'CANNOT_SET_SESSION';
                    $this->setLastError($rc, 'sessionSave setcookie failed');
                }
            } catch (Exception $e) {
                $rc = 'CANNOT_SET_SESSION';
                $this->setLastError($rc, 'sessionSave could not set cookie: ' . $e->getMessage());
            }
            error_reporting($errorLevel); // put error level back to where it was
            if ($rc == '') {
                $this->m_authTokenWasValidated = true;
                $this->m_authToken = $authenticationToken;
                $this->m_userName = $user_name;
                $this->m_userId = $user_id;
                $this->m_siteUserId = $site_user_id;
                $this->m_networkId = $network_id;
                $this->m_userAccessLevel = $access_level;
                $this->m_isLoggedIn = true;
                $_COOKIE[SESSION_COOKIE] = $authenticationToken;
                $GLOBALS[SESSION_USERID_CACHE] = $user_id;
                // $_POST['authtok'] = $authenticationToken; // not sure about this
            }
            return $rc;
        }

        /**
         * Save the authenticated user info to cookie so we can retrieve it next time this user returns. We do this
         * to allow clients to access the logged in user info easily after a login. Always verify the token is
         * valid before relying on this data.
         * @param $userInfo
         * @return string An error code, '' if successful.
         */
        private function sessionUserInfoSave ($userInfo) {
            $rc = '';
            $errorLevel = error_reporting(); // turn off warnings so we don't generate crap in the output stream. If we don't do this fucking php writes an error and screws up the output stream. (I cant get the try/catch to work without it)
            error_reporting($errorLevel & ~E_WARNING);
            try {
                $userInfoJSON = json_encode($userInfo);
                if (setcookie(SESSION_USERINFO, $userInfoJSON, time() + (SESSION_DAYSTAMP_HOURS * 60 * 60), '/', $this->sessionCookieDomain()) === false) {
                    $rc = 'CANNOT_SAVE_USERINFO';
                    $this->setLastError($rc, 'sessionUserInfoSave setcookie failed');
                }
            } catch (Exception $e) {
                $rc = 'CANNOT_SAVE_USERINFO';
                $this->setLastError($rc, 'sessionUserInfoSave could not set cookie: ' . $e->getMessage());
            }
            error_reporting($errorLevel); // put error level back to where it was
            return $rc;
        }

        /**
         * Restore the user info data from cookie
         * @return null|object
         */
        private function sessionUserInfoGet () {
            $userInfo = null;
            if (isset($_COOKIE[SESSION_USERINFO])) {
                try {
                    $userInfo = json_decode($_COOKIE[SESSION_USERINFO]);
                } catch (Exception $e) {
                    $this->setLastError('CANNOT_GET_USERINFO', 'sessionUserInfoGet could not get cookie: ' . $e->getMessage());
                }
            }
            return $userInfo;
        }

        /**
         * Clear any session data and forget any logged in user.
         * @return string An error code if the function fails to clear the cookies, or an empty string if successful.
         */
        private function sessionClear () {
            $this->m_authToken = null;
            $this->m_authTokenWasValidated = false;
            $this->m_userName = '';
            $this->m_userId = 0;
            $this->m_siteUserId = '';
            $this->m_userAccessLevel = 0;
            $this->m_isLoggedIn = false;
            $rc = '';
            if (setcookie(SESSION_COOKIE, null, time() - 86400, '/', $this->sessionCookieDomain()) === false) {
                $rc = 'CANNOT_SET_SESSION';
            }
            setcookie(SESSION_USERINFO, null, time() - 86400, '/', $this->sessionCookieDomain());
            $_COOKIE[SESSION_COOKIE] = null;
            $_COOKIE[SESSION_USERINFO] = null;
            $GLOBALS[SESSION_USERID_CACHE] = null;
            return $rc;
        }

        private function blowfishPad ($text) {
            $imod = 8 - (strlen($text) % 8);
            for ($i = 0; $i < $imod; $i++) {
                $text .= chr($imod);
            }
            return $text;
        }

        private function blowfishUnpad ($text) {
            $textLen = strlen($text);
            if ($textLen > 0) {
                $padLen = ord($text[$textLen-1]);
                if ($padLen > 0 && $padLen <= 8) {
                    return substr($text, 0, $textLen - $padLen);
                } else {
                    return $text;
                }
            }
            return null;
        }

        private function base64URLDecode($input) {
            // replaces base64 chars that are not URL safe
            return base64_decode(strtr($input, '-_', '+/'));
        }

        private function base64URLEncode($input) {
            // replaces base64 chars that are not URL safe
            return strtr(base64_encode($input), '+/', '-_');
        }

        /**
         * User can set their own debug callback function we will call when we have a debug statement.
         * @param $debugFunction
         */
        public function setDebugFunction ($debugFunction) {

        }

        public function debugDump () {
            echo("<h3>Enginesis object state</h3>");
            echo("<p>Server: $this->m_server</p>");
            echo("<p>End point: $this->m_serviceEndPoint</p>");
            echo("<p>Stage: $this->m_stage</p>");
            echo("<p>Site-id: $this->m_siteId</p>");
            echo("<p>Sync-id: $this->m_syncId</p>");
            echo("<p>Protocol: $this->m_serviceProtocol</p>");
            echo("<p>Format: $this->m_responseFormat</p>");
            echo("<p>User-id: $this->m_userId</p>");
            echo("<p>Site-user-id: $this->m_siteUserId</p>");
            echo("<p>Network-id: $this->m_networkId</p>");
            echo("<p>User logged in: " . ($this->m_isLoggedIn ? 'YES' : 'NO') . "</p>");
            echo("<p>Last error: " . ($this->m_lastError ? implode(', ', $this->m_lastError) : 'null') . "</p>");
        }

        public function encodeURLParams ($data) {
            $encodedURLParams = '';
            foreach ($data as $key => $value) {
                if ($encodedURLParams != '') {
                    $encodedURLParams .= '&';
                }
                $encodedURLParams .= urlencode($key) . '=' . urlencode($value);
            }
            return $encodedURLParams;
        }

        public function decodeURLParams ($encodedURLParams) {
            $data = array();
            $arrayOfParameters = explode('&', $encodedURLParams);
            $i = 0;
            $numParameters = count($arrayOfParameters);
            while ($i < $numParameters)  {
                $parameter = explode('=', $arrayOfParameters[$i]);
                if (count($parameter) > 1) {
                    $data[urldecode($parameter[0])] = urldecode($parameter[1]);
                }
                $i ++;
            }
            return $data;
        }

        /** @method isLoggedInUser
         * Check we have the Enginesis authtoken in engsession cookie and it is valid.
         * @return bool true if the cookie is valid and the user is logged in.
         */
        public function isLoggedInUser () {
            if ($this->m_authTokenWasValidated && $this->m_userId > 0) {
                return true;
            } else {
                $this->restoreUserFromAuthToken(null);
                if ($this->m_authTokenWasValidated && $this->m_userId > 0) {
                    return true;
                }
            }
            return false;
        }

        /**
         * Make sure we call the API with all the necessary parameters. We can assume some defaults from the
         * current session but they only are used when the caller does not provide a required parameter.
         * @param $fn string: The API function to call
         * @param $parameters array: The API parameters as a key-value array
         * @return array The cleansed parameter array ready to call the requested API.
         */
        public function serverParamObjectMake ($fn, $parameters) {
            $serverParams = array();
            $serverParams['fn'] = $fn;
            if ( ! isset($parameters['site_id'])) {
                $serverParams['site_id'] = $this->m_siteId;
            }
            if ( ! isset($parameters['user_id'])) {
                $serverParams['user_id'] = $this->m_userId;
            }
            if ( ! isset($parameters['response'])) {
                $serverParams['response'] = $this->m_responseFormat;
            }
            foreach ($parameters as $key => $value) {
                $serverParams[$key] = $value; // urlencode($value); // TODO: I'm not sure we should urlencode the data as it is going into the database encoded.
            }
            $serverParams['state_seq'] = ++ $this->m_syncId;
            if ( ! isset($parameters['language_code'])) {
                $serverParams['language_code'] = $this->m_languageCode;
            }
            if ($this->m_authTokenWasValidated && ! isset($parameters['authtok'])) {
                $serverParams['authtok'] = $this->m_authToken;
            }
            return $serverParams;
        }

        /**
         * callServerAPI: Make an Enginesis API request over the WWW
         * @param $fn string is the API service to call.
         * @param $paramArray array key => value array of parameters e.g. array('site_id' => 100);
         * @param $debug boolean true to log debug info regarding this API call.
         * @return object response from server based on requested response format.
         */
        private function callServerAPI ($fn, $paramArray, $debug = false) {
            $parameters = $this->serverParamObjectMake($fn, $paramArray);
            $response = $parameters['response'];
            if ($debug) {
                print_r(array('Before callServerAPI', $fn, $this->m_server, $paramArray));
            }
            if ($debug) {
                echo("<h3>Params for $fn:</h3><p>" . $this->encodeURLParams($parameters) . "</p>");
            }
            $ch = curl_init();
            if ($ch) {
                curl_setopt($ch, CURLOPT_URL, $this->m_serviceEndPoint);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
                curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie.txt');
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $this->encodeURLParams($parameters));
                $contents = curl_exec($ch);
                curl_close($ch);
                $succeeded = strlen($contents) > 0;
                // TODO: We should verify the response is a valid EnginesisReponse object
                if ( ! $succeeded) {
                    $contents = '{"results":{"status":{"success":"0","message":"SYSTEM_ERR","extended_info":"System error: ' . $this->m_serviceEndPoint . ' replied with no data."},"passthru":{"fn":"' . $fn . '","state_seq":0}}}';
                }
            } else {
                $contents = '{"results":{"status":{"success":"0","message":"SYSTEM_ERR","extended_info":"System error: unable to contact ' . $this->m_serviceEndPoint . ' or the server did not respond."},"passthru":{"fn":"' . $fn . '","state_seq":0}}}';
            }
            if ($debug) {
                echo("<h3>Response from $fn:</h3><p>$contents</p>");
            }
            if ($response == 'json') {
                $contentsObject = json_decode($contents);
                if ($contentsObject == null) {
                    debugLog("callServerAPI could not parse JSON into an object: $contents");
                }
            }
            return $contentsObject;
        }

        /**
         * @return object: the last error, null if the most recent operation succeeded.
         */
        public function getLastError () {
            return $this->m_lastError;
        }

        /**
         * @return object: the last error, null if the most recent operation succeeded.
         */
        public function getLastErrorCode () {
            return ($this->m_lastError != null) ? $this->m_lastError['message'] : '';
        }

        /**
         * Return the last error information as a string.
         * @return string
         */
        public function getLastErrorDescription () {
            return ($this->m_lastError != null) ? $this->m_lastError['message'] . ': ' . $this->m_lastError['extended_info'] : '';
        }

        /**
         * Given an Enginesis response object, pull out the status message information.
         * This is a helper function because doing this for every API call is a bit tedious.
         * @param $enginesisResponse {object} the enginesis response object returned from callServerAPI
         * @param $success {bool} true if API succeeded, false when failed
         * @param $statusMessage {string} error message, empty if success is true
         * @param $extendedInfo {string} optional additional information about the error
         * @return {object} array or rows if succeeded, null when failed
         */
        public function getResponseStatus ($enginesisResponse, & $success, & $statusMessage, & $extendedInfo) {
            $success = false;
            $statusMessage = EnginesisErrors::INVALID_PARAM;
            $extendedInfo = '';
            $resultSet = null;
            if ($enginesisResponse != null) {
                $results = $enginesisResponse->results;
                if ($results) {
                    $status = $results->status;
                    if ($status) {
                        $success = $status->success;
                        $statusMessage = $status->message;
                        if (isset($status->extended_info)) {
                            $extendedInfo = $status->extended_info;
                        }
                        if ($success) {
                            if (isset($results->results)) {
                                $resultSet = $results->results;
                            } elseif (isset($results->result)) {
                                $resultSet = $results->result;
                            }
                        }
                    } else {
                        $statusMessage = EnginesisErrors::SERVER_RESPONSE_NOT_VALID;
                    }
                } else {
                    $statusMessage = EnginesisErrors::SERVER_SYSTEM_ERROR;
                }
            } else {
                $statusMessage = EnginesisErrors::SERVER_DID_NOT_REPLY;
            }
            return $resultSet;
        }

        /**
         * @method userLogin
         * @description
         *   Login a user by calling the Enginesis function and wait for the response. If the user is successfully
         *   logged in then save the session cookie that allows us to converse with the server without logging in each time.
         * @param $userName: string the user's name or email address
         * @param $password: string the user's password
         * @param $saveSession boolean true to save this session in a cookie for the next page load to read back. Typically linked to a Remember Me checkbox.
         * @return object: null if login fails, otherwise returns the user info object.
         */
        public function userLogin ($userName, $password, $saveSession) {
            $userInfo = null;
            $enginesisResponse = $this->callServerAPI('UserLogin', array('user_name' => $userName, 'password' => $password));
            $results = $this->setLastErrorFromResponse($enginesisResponse);
            if ($results != null && isset($results->row)) {
                $userInfo = $results->row;
                if ($userInfo && $saveSession) {
                    $this->sessionSave($userInfo->authtok, $userInfo->user_id, $userInfo->user_name, $userInfo->site_user_id, $userInfo->access_level, EnginesisNetworks::Enginesis);
                    $this->sessionUserInfoSave($userInfo);
                }
            }
            return $userInfo;
        }

        /**
         * For Co-registration/SSO, we take information provided by the hosting network and either setup a new user or update
         * an existing user. The unique key is $site_user_id and that plus one of $real_name or $user_name are required.
         * @param $parameters {object} array of key values of user information. Keys site_user_id, network_id, and one
         *   of real_name or user_name are required. dob, gender, scope, email_address are optional.
         * @param $saveSession {boolean} true to save this session for next page refresh.
         * @return {object} an $userInfo object. Same result as UserLogin.
         */
        public function userLoginCoreg ($parameters, $saveSession) {
            $userInfo = null;
            // Convert parameters or use logical defaults
            $site_user_id = $parameters['site_user_id'];
            $network_id = $parameters['network_id'];
            $real_name = isset($parameters['real_name']) ? $parameters['real_name'] : '';
            $user_name = isset($parameters['user_name']) ? $parameters['user_name'] : '';
            $email_address = isset($parameters['email_address']) ? $parameters['email_address'] : '';
            $gender = isset($parameters['gender']) ? $parameters['gender'] : 'F';
            $dob = isset($parameters['dob']) ? $parameters['dob'] : '';
            $scope = isset($parameters['scope']) ? $parameters['scope'] : '';
            $enginesisResponse = $this->callServerAPI('UserLoginCoreg', array('site_user_id' => $site_user_id, 'user_name' => $user_name, 'real_name' => $real_name, 'email_address' => $email_address, 'gender' => $gender, 'dob' => $dob, 'network_id' => $network_id, 'scope' => $scope));
            $results = $this->setLastErrorFromResponse($enginesisResponse);
            if ($results != null && isset($results->row)) {
                $userInfo = $results->row;
                if ($userInfo && $saveSession) {
                    $this->sessionSave($userInfo->authtok, $userInfo->user_id, $userInfo->user_name, $userInfo->site_user_id, $userInfo->access_level, $network_id);
                    $this->sessionUserInfoSave($userInfo);
                }
            }
            return $userInfo;
        }

        /* @function userLogout
         * @description
         * Logout the user clearing all internal cookies and data structures.
         * @return bool: true if successful. If false there was an internal error (logout should never really fail.)
         */
        public function userLogout () {
            $enginesisResponse = $this->callServerAPI('UserLogout', array());
            $results = $this->setLastErrorFromResponse($enginesisResponse);
            $this->sessionClear();
            return $results != null;
        }

        /* @function userRegistrationValidation
         * @description
         *   Determine if user registration parameters are valid. If not, indicate the first parameter that is invalid.
         * @param $user_id: id of existing user to validate, or 0/null if a new registration.
         * @param $parameters: key/value object of registration data.
         * @return array: keys that we think are unacceptable. null if acceptable.
         * TODO: this is not implemented, returns null (OK) as a placeholder.
         */
        public function userRegistrationValidation ($user_id, $parameters) {
            $errors = array();

            $key = 'user_name';
            if ( ! isset($parameters[$key]) || ! $this->isValidUserName($parameters[$key])) {
                array_push($errors, $key);
            }
            $key = 'email_address';
            if ( ! isset($parameters[$key]) || ! checkEmailAddress($parameters[$key])) {
                array_push($errors, $key);
            }
            if ($user_id == 0) {
                // This is a new registration
                $key = 'password';
                if ( ! isset($parameters[$key]) || ! $this->isValidPassword($parameters[$key])) {
                    array_push($errors, $key);
                }
            }
            $key = 'dob';
            if (isset($parameters[$key]) && ! $this->isValidDate($parameters[$key])) {
                array_push($errors, $key);
            }
            $key = 'real_name';
            if (isset($parameters[$key]) && ! $this->isValidString($parameters[$key], 0, 50, true, false)) {
                array_push($errors, $key);
            }
            $key = 'city';
            if (isset($parameters[$key]) && ! $this->isValidString($parameters[$key], 0, 80, true, false)) {
                array_push($errors, $key);
            }
            $key = 'state';
            if (isset($parameters[$key]) && ! $this->isValidString($parameters[$key], 2, 2, true, false)) {
                array_push($errors, $key);
            }
            $key = 'country_code';
            if (isset($parameters[$key]) && ! $this->isValidString($parameters[$key], 2, 2, true, false)) {
                array_push($errors, $key);
            }
            $key = 'zipcode';
            if (isset($parameters[$key]) && ! $this->isValidString($parameters[$key], 5, 10, true, false)) {
                array_push($errors, $key);
            }
            $key = 'gender';
            if (isset($parameters[$key]) && ! $this->isValidGender($parameters[$key])) {
                array_push($errors, $key);
            }
            $key = 'tagline';
            if (isset($parameters[$key]) && ! $this->isValidString($parameters[$key], 0, 255, true, true)) {
                array_push($errors, $key);
            }
            $key = 'about_me';
            if (isset($parameters[$key]) && ! $this->isValidString($parameters[$key], 0, 500, true, true)) {
                array_push($errors, $key);
            }
            $key = 'additional_info';
            if (isset($parameters[$key]) && ! $this->isValidString($parameters[$key], 0, 1000, true, true)) {
                array_push($errors, $key);
            }
            $key = 'mobile_number';
            if (isset($parameters[$key]) && ! $this->isValidString($parameters[$key], 0, 20, true, false)) {
                array_push($errors, $key);
            }
            $key = 'im_id';
            if (isset($parameters[$key]) && ! $this->isValidString($parameters[$key], 0, 50, true, false)) {
                array_push($errors, $key);
            }
            return count($errors) > 0 ? $errors : null;
        }

        /* @function userRegistration
         * @description
         *   Register a new user by calling the Enginesis function and wait for the response. We must convert and field data
         *   from our version to the Enginesis version since we have multiple different ways to collect it.
         * @param $parameters: key/value object of registration data.
         * @return object: null if registration fails, otherwise returns the user info object and logs the user in.
         */
        public function userRegistration ($parameters) {
            $userInfo = array(
                'user_name' => $parameters['user_name'],
                'password' => $parameters['password'],
                'security_question_id' => $this->arrayValueOrDefault($parameters, 'security_question_id', '3'),
                'security_answer' => $this->arrayValueOrDefault($parameters, 'security_answer', ''),
                'email_address' => $parameters['email_address'],
                'dob' => $parameters['dob'],
                'real_name' => $this->arrayValueOrDefault($parameters, 'real_name', $parameters['user_name']),
                'city' => $this->arrayValueOrDefault($parameters, 'city', ''),
                'state' => $this->arrayValueOrDefault($parameters, 'state', ''),
                'zipcode' => $this->arrayValueOrDefault($parameters, 'zipcode', ''),
                'country_code' => $this->arrayValueOrDefault($parameters, 'country_code', 'US'),
                'tagline' => $this->arrayValueOrDefault($parameters, 'tagline', ''),
                'gender' => $this->arrayValueOrDefault($parameters, 'gender', 'F'),
                'mobile_number' => $this->arrayValueOrDefault($parameters, 'mobile_number', ''),
                'im_id' => $this->arrayValueOrDefault($parameters, 'im_id', ''),
                'img_url' => $this->arrayValueOrDefault($parameters, 'img_url', ''),
                'about_me' => $this->arrayValueOrDefault($parameters, 'about_me', ''),
                'additional_info' => $this->arrayValueOrDefault($parameters, 'additional_info', ''),
                'agreement' => '1',
                'captcha_id' => '99999',
                'captcha_response' => 'DEADMAN',
                'site_user_id' => '',
                'network_id' => '1',
                'source_site_id' => $this->m_siteId
            );
            $enginesisResponse = $this->callServerAPI('RegisteredUserCreate', $userInfo);
            $results = $this->setLastErrorFromResponse($enginesisResponse);
            if ($results != null) {
                $parameters = $results[0];
                $userInfo = array(
                    'user_name' => $parameters->user_name,
                    'email_address' => $parameters->email_address,
                    'full_name' => $parameters->fullname,
                    'city' => $parameters->city,
                    'state' => $parameters->state,
                    'zipcode' => $parameters->zipcode,
                    'country_code' => $parameters->country_code,
                    'tagline' => $parameters->tagline,
                    'about_me' => $parameters->about_me,
                    'additional_info' => $parameters->additional_info,
                    'age' => $parameters->age,
                    'gender' => $parameters->gender,
                    'dob' => $parameters->dob,
                    'mobile_number' => $parameters->mobile_number,
                    'im_id' => $parameters->im_id
                );
            }
            return $userInfo;
        }

        /* @function registeredUserUpdate
         * @description
         *   Update and existing user's registration information.
         * @param $parameters: key/value object of registration data. Only changed keys may be provided.
         * @return object: null if registration fails, otherwise returns the user info object.
         */
        public function registeredUserUpdate ($parameters) {
            $service = 'RegisteredUserUpdate';
            $userInfo = array(
                'user_name' => $parameters['user_name'],
                'email_address' => $parameters['email_address'],
                'dob' => $parameters['dob'],
                'real_name' => $this->arrayValueOrDefault($parameters, 'real_name', $parameters['user_name']),
                'city' => $this->arrayValueOrDefault($parameters, 'city', ''),
                'state' => $this->arrayValueOrDefault($parameters, 'state', ''),
                'zipcode' => $this->arrayValueOrDefault($parameters, 'zipcode', ''),
                'country_code' => $this->arrayValueOrDefault($parameters, 'country_code', 'US'),
                'tagline' => $this->arrayValueOrDefault($parameters, 'tagline', ''),
                'gender' => $this->arrayValueOrDefault($parameters, 'gender', 'F'),
                'mobile_number' => $this->arrayValueOrDefault($parameters, 'mobile_number', ''),
                'im_id' => $this->arrayValueOrDefault($parameters, 'im_id', ''),
                'img_url' => $this->arrayValueOrDefault($parameters, 'img_url', ''),
                'about_me' => $this->arrayValueOrDefault($parameters, 'about_me', ''),
                'additional_info' => $this->arrayValueOrDefault($parameters, 'additional_info', ''),
                'captcha_id' => '99999',
                'captcha_response' => 'DEADMAN'
            );
            $enginesisResponse = $this->callServerAPI($service, $userInfo);
            $results = $this->setLastErrorFromResponse($enginesisResponse);
            if ($results != null) {
                $results = $results[0];
            }
            return $results;
        }

        /**
         * Confirm a new user registration given the user-id and the token. These are supplied in the email sent when
         * a new registration is created with RegisteredUserCreate. If successful the user is logged in and a login
         * token (authtok) is sent back from the server.
         * @param $userId
         * @param $secondaryPassword
         * @return null|object
         */
        public function registeredUserConfirm ($userId, $secondaryPassword) {
            $service = 'RegisteredUserConfirm';

            $userInfo = array(
                'user_id' => $userId,
                'secondary_password' => $secondaryPassword
            );
            $enginesisResponse = $this->callServerAPI($service, $userInfo);
            $results = $this->setLastErrorFromResponse($enginesisResponse);
            if ($results != null) {
                $results = $results->row;
            }
            return $results;
        }

        /**
         * Get the security info for the current logged in user. Returns {mobile_number, security_question_id, security_question, security_answer}
         * @return null|object
         */
        public function registeredUserSecurityGet () {
            $service = 'RegisteredUserSecurityGet';

            $userInfo = array(
                'site_user_id' => '',
            );
            $enginesisResponse = $this->callServerAPI($service, $userInfo);
            $results = $this->setLastErrorFromResponse($enginesisResponse);
            if ($results != null) {
                $results = $results[0];
            }
            return $results;
        }

        /* @function registeredUserSecurityValidation
         * @description
         *   Determine if user security parameters are valid. If not, indicate the first parameter that is invalid.
         * @param $user_id int
         * @param $mobile_number string
         * @param $security_question_id int
         * @param $security_question string
         * @param $security_answer string
         * @return object: null if all acceptable. key/value pairs that we think are unacceptable. key is the field in error, value is the error message.
         */
        public function registeredUserSecurityValidation ($user_id, $mobile_number, $security_question_id, $security_question, $security_answer) {
            $errors = array();
            $key = 'mobile_number';
            $questionOK = true;
            if ( ! $this->isValidString($mobile_number, 7, 20, true, false)) {
                array_push($errors, $key);
            }
            $key = 'security_question_id';
            if ( ! $this->isValidId($security_question_id)) {
                array_push($errors, $key);
            }
            $key = 'security_answer';
            if ( ! $this->isValidString($security_answer, 3, 50, true, false)) {
                array_push($errors, $key);
                $questionOK = false;
            }
            $key = 'security_question';
            if ( ! $this->isValidString($security_question, 4, 80, true, false)) {
                array_push($errors, $key);
                $questionOK = false;
            }
            $q = strlen($security_question);
            $a = strlen($security_answer);
            if ($questionOK && ($q xor $a)) {
                // both question and answer must be empty or not empty
                array_push($errors, $key);
            }
            return count($errors) > 0 ? $errors : null;
        }

        /**
         * Set the security info for the current logged in user.
         * @param $mobile_number - this is optional, pass either null or empty string to skip.
         * @param $security_question_id
         * @param $security_question
         * @param $security_answer
         * @return null|object - if null then check getLastError(), otherwise the current user-id.
         */
        public function registeredUserSecurityUpdate ($mobile_number, $security_question_id, $security_question, $security_answer) {
            $service = 'RegisteredUserSecurityUpdate';

            $userInfo = array(
                'mobile_number' => $mobile_number,
                'security_question_id' => $security_question_id,
                'security_question' => $security_question,
                'security_answer' => $security_answer,
                'captcha_id' => '99999',
                'captcha_response' => 'DEADMAN'
            );
            $enginesisResponse = $this->callServerAPI($service, $userInfo);
            $results = $this->setLastErrorFromResponse($enginesisResponse);
            if ($results != null) {
                $results = $results[0];
            }
            return $results;
        }

        /**
         * User requests to change the password. Requires a secondary password token that is returned from this service.
         * You need to supply that token when calling registeredUserPasswordChange.
         * @return null|object
         */
        public function registeredUserRequestPasswordChange () {
            $service = 'RegisteredUserRequestPasswordChange';

            $enginesisResponse = $this->callServerAPI($service, array());
            $results = $this->setLastErrorFromResponse($enginesisResponse);
            if ($results != null) {
                $results = $results[0];
            }
            return $results;
        }

        /**
         * Complete the setting of a new password for the user who is logged in. Requires a authenticated user, a
         * new password and the secondary password token that was given with RegisteredUserRequestPasswordChange.
         * The token expires in 24 hours.
         * @param $newPassword
         * @param $secondaryPassword
         * @return null|object
         */
        public function registeredUserPasswordChange ($newPassword, $secondaryPassword) {
            $service = 'RegisteredUserPasswordChange';

            $userInfo = array(
                'password' => $newPassword,
                'secondary_password' => $secondaryPassword,
                'captcha_id' => '99999',
                'captcha_response' => 'DEADMAN'
            );
            $enginesisResponse = $this->callServerAPI($service, $userInfo);
            $results = $this->setLastErrorFromResponse($enginesisResponse);
            if ($results != null) {
                $results = $results[0];
            }
            return $results;
        }

        /**
         * Complete the setting of a new password for a user who is not currently logged in. Requires the user-id,
         * a new password and the secondary password token that was given with RegisteredUserRequestPasswordChange.
         * The token expires in 24 hours.
         * @param $userId
         * @param $newPassword
         * @param $secondaryPassword
         * @return null|object
         */
        public function registeredUserPasswordChangeUnauth ($userId, $newPassword, $secondaryPassword) {
            $service = 'RegisteredUserPasswordChangeUnauth';

            $userInfo = array(
                'user_id' => $userId,
                'password' => $newPassword,
                'secondary_password' => $secondaryPassword,
                'captcha_id' => '99999',
                'captcha_response' => 'DEADMAN'
            );
            $enginesisResponse = $this->callServerAPI($service, $userInfo);
            $results = $this->setLastErrorFromResponse($enginesisResponse);
            if ($results != null) {
                $results = $results[0];
            }
            return $results;
        }

        /* @function userForgotPassword
         * @description
         *   Trigger the forgot password procedure. The server will reset the user's password and
         *   send an email to the email address on record to follow a link to reset the password.
         * @param $userName: string the user's name
         * @param $email_address: string the user's email address
         * @return bool: true if the process was started, false if there was an error.
         */
        public function userForgotPassword ($userName, $email_address) {
            $enginesisResponse = $this->callServerAPI('RegisteredUserForgotPassword', array('user_name' => $userName, 'email_address' => $email_address));
            $results = $this->setLastErrorFromResponse($enginesisResponse);
            if ($results == null) {
                debugLog('userForgotPassword failed: ' . $this->m_lastError['message'] . ' / ' . $this->m_lastError['extended_info']);
            }
            return $results;
        }

        /* @function userResetPassword
         * @description
         *   Trigger the reset password procedure. The server will reset the user's password and
         *   send an email to the email address on record to follow a link to reset the password.
         * @return bool: true if the process was started, false if there was an error.
         */
        public function userResetPassword () {
            $enginesisResponse = $this->callServerAPI('RegisteredUserResetPassword', array());
            $results = $this->setLastErrorFromResponse($enginesisResponse);
            if ($results == null) {
                debugLog('userResetPassword failed: ' . $this->m_lastError['message'] . ' / ' . $this->m_lastError['extended_info']);
            }
            return $results;
        }

        /* @function userVerifyForgotPassword
         * @description
         *   When the user comes back to reset the password.
         * @param $userId: int the user's internal user id.
         * @param $newPassword: string the user's replacement password.
         * @param $token: string the user's granted token allowing the reset from an authorized source.
         * @return object: null if reset fails, otherwise returns the user info object and logs the user in.
         */
        public function userVerifyForgotPassword ($userId, $newPassword, $token) {
            $enginesisResponse = $this->callServerAPI('RegisteredUserVerifyForgotPassword', array('user_id' => $userId, 'password' => $newPassword, 'token' => $token));
            $results = $this->setLastErrorFromResponse($enginesisResponse);
            return $results != null;
        }

        /**
         * The general public user get - returns a minimum set of public attributes about a user.
         * @param $userId - may be either an int indicating a user_id or a string indicating a user_name.
         * @return object A user info object containing only the public attributes.
         */
        public function userGet ($userId) {
            $user = null;
            if (is_numeric ($userId)) {
                if ($userId < 9999) {
                    $userId = $this->m_userId;
                }
                $enginesisResponse = $this->callServerAPI('UserGet', array('get_user_id' => $userId));
            } elseif ($this->isValidUserName($userId)) {
                $enginesisResponse = $this->callServerAPI('UserGetByName', array('user_name' => $userId));
            }
            $results = $this->setLastErrorFromResponse($enginesisResponse);
            if ($results != null && isset($results[0])) {
                $user = $results[0];
            }
            return $user;
        }

        /**
         * The general public user get by a user name - returns a minimum set of public attributes about a user.
         * @param $userName - may be either an int indicating a user_id or a string indicating a user_name.
         * @return object A user info object containing only the public attributes.
         */
        public function userGetByName ($userName) {
            $user = null;
            if ($this->isValidUserName($userName)) {
                $enginesisResponse = $this->callServerAPI('UserGetByName', array('user_name' => $userName));
                $results = $this->setLastErrorFromResponse($enginesisResponse);
                if ($results != null && isset($results[0])) {
                    $user = $results[0];
                }
            }
            return $user;
        }

        /**
         * Get information about a given user. If no user is provided and there is a logged in user, then
         * returns the info about the logged in user. Note if getting info on the current logged in user
         * then there could be more attributes that are intended to be visible only by that user (not public info).
         * @param int $userId optional user id to get info on.
         * @param string $siteUserId optional site user id to get info on.
         * @return object the attributes of the requested user, null if no such user or error.
         */
        public function registeredUserGet ($userId = 0, $siteUserId = '') {
            $user = null;
            $parameters = array();
            if ($userId != 0) {
                if ($userId < 9999) {
                    $userId = $this->m_userId;
                }
                $parameters['get_user_id'] = $userId;
            }
            if ($siteUserId != '') {
                $parameters['site_user_id'] = $siteUserId;
            }
            $enginesisResponse = $this->callServerAPI('RegisteredUserGet', $parameters);
            $results = $this->setLastErrorFromResponse($enginesisResponse);
            if ($results != null && isset($results[0])) {
                $results = $results[0];
            }
            return $results;
        }

        /**
         * Get extended information about a given user. If no user is provided and there is a logged in user, then
         * returns the info about the logged in user. Note if getting info on the current logged in user
         * then there could be more attributes that are intended to be visible only by that user (not public info).
         * @param int $userId optional user id to get info on.
         * @param string $siteUserId optional site user id to get info on.
         * @return object the attributes of the requested user, null if no such user or error.
         */
        public function registeredUserGetEx ($userId = 0, $siteUserId = '')
        {
            $user = null;
            $parameters = array();
            if ($userId != 0) {
                if ($userId < 9999) {
                    $userId = $this->m_userId;
                }
                $parameters['get_user_id'] = $userId;
            }
            if ($siteUserId != '') {
                $parameters['site_user_id'] = $siteUserId;
            }
            $enginesisResponse = $this->callServerAPI('RegisteredUserGetEx', $parameters);
            $results = $this->setLastErrorFromResponse($enginesisResponse);
            if ($results != null && isset($results[0])) {
                $results = $results[0];
            }
            return $results;
        }

        /**
         * Find users that match a given search criteria. The search is performed against certain publis attributes,
         * such as user-name, tag-line, additional-info, about-me.
         * @param $searchString
         * @return array a list of matching users an a subset of user attributes - {user_id, user_name, date_created, site_currency_value, site_experience_points}
         */
        public function registeredUserFind ($searchString) {
            $enginesisResponse = $this->callServerAPI('RegisteredUserGetEx', array('search_str' => $searchString));
            $results = $this->setLastErrorFromResponse($enginesisResponse);
            return $results;
        }

        /**
         * Track a hit on the newsletter.
         * @param $userId
         * @param $newsletterId
         * @param $event
         * @param $eventDetails
         * @param $referrer
         * @return bool
         */
        public function newsletterTrackingRecord ($userId, $newsletterId, $event, $eventDetails, $referrer) {
            $enginesisResponse = $this->callServerAPI('NewsletterTrackingRecord', array('u_id' => $userId, 'newsletter_id' => $newsletterId, 'event_id' => $event, 'event_details' => $eventDetails, 'referrer' => $referrer));
            $results = $this->setLastErrorFromResponse($enginesisResponse);
            return $results != null;
        }

        /**
         * Return the proper URL to use to show an avatar for a given user. The default is the default size and the current user.
         * This URL should always return an image.
         * @param int $size
         * @param int $userId
         * @return string
         */
        public function avatarURL ($size = 0, $userId = 0) {
            if ($this->isInvalidId($userId)) {
                $userId = $this->m_userId;
            }
            return $this->m_avatarEndPoint . '?site_id=' . $this->m_siteId . '&user_id=' . $userId . '&size=' . $size;
        }

        public function gameGet ($gameId) {
            if ($this->isInvalidId($gameId)) {
                $gameId = $this->gameId;
            }
            $enginesisResponse = $this->callServerAPI('GameGet', array('game_id' => $gameId));
            $results = $this->setLastErrorFromResponse($enginesisResponse);
            if ($results != null && isset($results[0])) {
                $gameInfo = $results[0];
            } else {
                $gameInfo = null;
            }
            return $gameInfo;
        }

        public function gameGetByName ($gameName) {
            $enginesisResponse = $this->callServerAPI('GameGetByName', array('game_name' => $gameName));
            $results = $this->setLastErrorFromResponse($enginesisResponse);
            if ($results != null && isset($results[0])) {
                $gameInfo = $results[0];
            } else {
                $gameInfo = null;
            }
            return $gameInfo;
        }

        public function gameRatingUpdate ($rating, $gameId) {
            if ($rating < 0 || $rating > 100) {
                $rating = 5;
            }
            if ($this->isInvalidId($gameId)) {
                $gameId = $this->gameId;
            }
            $enginesisResponse = $this->callServerAPI('GameRatingUpdate', array('game_id' => $gameId, 'rating' => $rating));
            $results = $this->setLastErrorFromResponse($enginesisResponse);
            if ($results != null && isset($results[0])) {
                $gameInfo = $results[0];
            } else {
                $gameInfo = null;
            }
            return $gameInfo;
        }

        public function gameRatingGet ($gameId) {
            if ($this->isInvalidId($gameId)) {
                $gameId = $this->gameId;
            }
            $enginesisResponse = $this->callServerAPI('GameRatingGet', array('game_id' => $gameId));
            $results = $this->setLastErrorFromResponse($enginesisResponse);
            if ($results != null && isset($results[0])) {
                $gameInfo = $results[0];
            } else {
                $gameInfo = null;
            }
            return $gameInfo;
        }

        public function gameRatingList ($numberOfGames) {
            if ($numberOfGames < 1 || $numberOfGames > 100) {
                $numberOfGames = 5;
            }
            $enginesisResponse = $this->callServerAPI('GameRatingList', array('num_items' => $numberOfGames));
            $results = $this->setLastErrorFromResponse($enginesisResponse);
            if ($results != null && isset($results[0])) {
                $gameList = $results[0];
            } else {
                $gameList = null;
            }
            return $gameList;
        }
    }