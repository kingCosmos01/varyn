<?php
    /**
     * Enginesis service object for PHP. Support for each Enginesis API and additional helper functions.
     * User: jf
     * Date: 2/13/16
     */

    require_once('errors.php');
    define('SESSION_COOKIE', 'engsession');
    define('SESSION_DAYSTAMP_HOURS', 48);
    define('SESSION_USERID_CACHE', 'engsession_uid');

    class Enginesis
    {
        private $m_server;
        private $m_serviceEndPoint;
        private $m_lastError;
        private $m_siteId;
        private $m_isLoggedIn;
        private $m_userId;
        private $m_siteUserId;
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
            $this->m_userAccessLevel = 0;
            $this->m_isLoggedIn = false;
            $this->m_syncId = 0;
            $this->m_serviceProtocol = $this->getServiceProtocol();
            $this->m_responseFormat = 'json';
            $this->m_debugFunction = null;
            $this->m_developerKey = $developerKey;
            $this->m_languageCode = 'en';
            $this->m_authToken = null;
            $this->m_authTokenWasValidated = false;
            if (empty($enginesisServer)) {
                // Caller doesn't know which stage, converse with the one that matches the stage we are on
                $this->m_serviceEndPoint = $this->m_serviceProtocol . '://enginesis' . $this->m_stage . '.com/index.php';
            } elseif (strlen($enginesisServer) == 2) {
                // Caller may provide a stage we should converse with
                $this->m_serviceEndPoint = $this->m_serviceProtocol . '://enginesis' . $enginesisServer . '.com/index.php';
            } else {
                // Caller can provide a specific server we should converse with
                $this->m_serviceEndPoint = $this->m_serviceProtocol . '://' . $enginesisServer . '/index.php';
            }
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

        /**
         * Return the domain name and TLD only (remove server name, protocol, anything else) e.g. games.com or games-q.com
         * @param null $serverName
         * @return null|string
         */
        private function serverTail ($serverName = null) {
            if (strlen($serverName) == 0) {
                $serverName = $this->getServerName();
            }
            if ($serverName != 'localhost') {
                $pos1 = strpos($serverName, '.');
                $pos2 = false;
                if ($pos1 !== false) {
                    $pos2 = strpos($serverName, '.', $pos1 + 1);
                }
                if ($pos2 !== false) {
                    $serverName = substr($serverName, strpos($serverName, '.') + 1);
                } else {
                    $serverName = substr($serverName, strpos($serverName, '://') + 3);
                }
            }
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
         * @param $enginesisResponse A response object from an Enginesis API call.
         * @return null|object If the API call returned an error this function sets the lastError object.
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
            if ($authenticationToken == '') {
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
                $sessionAccessLevel = 0;
                $this->sessionValidate($authToken, $sessionSiteId, $sessionUserId, $sessionSiteUserId, $sessionUserName, $sessionAccessLevel);
                if ($sessionUserId != 0) {
                    $this->m_siteId = $sessionSiteId;
                    $this->m_userId = $sessionUserId;
                    $this->m_userName = $sessionUserName;
                    $this->m_siteUserId = $sessionSiteUserId;
                    $this->m_userAccessLevel = $sessionAccessLevel;
                    $this->m_authToken = $this->authTokenMake($sessionSiteId, $sessionUserId, $sessionSiteUserId, $sessionUserName, $sessionAccessLevel);
                    $this->m_authTokenWasValidated = true;
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
         * @return encrypted user authentication token
         */
        private function authTokenMake ($siteId, $userId, $siteUserId, $userName, $accessLevel) {
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
            $decryptedData = 'siteid=' . $siteId . '&userid=' . $userId . '&siteuserid=' . $siteUserId . '&username=' . $userName . '&accesslevel=' . $accessLevel . '&daystamp=' . $this->sessionDayStamp();
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
         * @return string
         */
        private function sessionValidate ($authToken, & $site_id, & $user_id,  & $site_user_id, & $user_name,& $access_level) {
            $rc = '';
            if (strlen($authToken) > 0) {
                $dataArray = $this->authTokenDecrypt($authToken);
                if (isset($dataArray['daystamp'])) {
                    $dayStamp = $dataArray['daystamp'];
                    $dayStampCurrent = $this->sessionDayStamp();
                    if ($dayStamp < $dayStampCurrent - (SESSION_DAYSTAMP_HOURS / 24) || $dayStamp > $dayStampCurrent) {
                        $rc = 'TOKEN_EXPIRED';
                    } else {
                        $site_id = $dataArray['siteid'];
                        $user_id = $dataArray['userid'];
                        $user_name = $dataArray['username'];
                        $site_user_id = $dataArray['siteuserid'];
                        $access_level = $dataArray['accesslevel'];
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
         * @param $authToken string the encrypted authentication token generated by sessionMakeAuthenticationTokenEncrypted.
         * @param $user_id
         * @param $site_user_id
         * @param $user_name
         * @param $access_level
         * @return string An error code, '' if successful.
         */
        private function sessionSave ($authToken, $user_id, $site_user_id, $user_name, $access_level) {
            $rc = '';
            if (strlen($authToken) > 0) {
                $this->m_authToken = $authToken;
                $this->m_authTokenWasValidated = true;
                $this->m_userName = $user_name;
                $this->m_userId = $user_id;
                $this->m_siteUserId = $site_user_id;
                $this->m_userAccessLevel = $access_level;
                $this->m_isLoggedIn = true;
                $_COOKIE[SESSION_COOKIE] = $authToken;
                $GLOBALS[SESSION_USERID_CACHE] = $user_id;
                if (setcookie(SESSION_COOKIE, $authToken, time() + (SESSION_DAYSTAMP_HOURS * 60 * 60), '/', $this->sessionCookieDomain()) === false) {
                    $rc = 'CANNOT_SET_SESSION';
                }
            } else {
                $rc = 'INVALID_TOKEN';
            }
            return $rc;
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
            $this->m_userAccessLevel = 0;
            $this->m_isLoggedIn = false;
            $rc = '';
            if (setcookie(SESSION_COOKIE, null, time() - 86400, '/', $this->sessionCookieDomain()) === false) {
                $rc = 'CANNOT_SET_SESSION';
            }
            $_COOKIE[SESSION_COOKIE] = null;
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
         * Make sure we call the API with all the necessary parameters.
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
            $serverParams['state_seq'] = ++ $this->m_syncId;
            if ( ! isset($parameters['response'])) {
                $serverParams['response'] = $this->m_responseFormat;
            }
            foreach ($parameters as $key => $value) {
                $serverParams[$key] = $value; // urlencode($value); // I'm not sure we should urlencode the data as it is going into the database encoded.
            }
            if ( ! isset($parameters['language_code'])) {
                $serverParams['language_code'] = $this->m_languageCode;
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
         * @return object: null if login fails, otherwise returns the user info object.
         */
        public function userLogin ($userName, $password) {
            $user = null;
            $enginesisResponse = $this->callServerAPI('UserLogin', array('user_name' => $userName, 'password' => $password));
            $results = $this->setLastErrorFromResponse($enginesisResponse);
            if ($results != null && isset($results->row)) {
                $user = $results->row;
                if ($user) {
                    $this->sessionSave($user->authtok, $user->user_id, $user->site_user_id, $user->user_name, $user->access_level);
                }
            }
            return $user;
        }

        /* @function userLogout
         * @description
         *   Logout the user clearing all internal cookies and data structures.
         * @return bool: true if successful. If false there was an internal error (logout should never really fail.)
         */
        public function userLogout () {
            $enginesisResponse = $this->callServerAPI('UserLogout', array());
            $results = $this->setLastErrorFromResponse($enginesisResponse);
            echo("<h3>User logged out!</h3>");
            print_r($results);
            $this->sessionClear();
            return $results != null;
        }

        /* @function userRegistrationValidation
         * @description
         *   Determine if user registration parameters are valid. If not, indicate the first parameter that is invalid.
         * @param $user_id: id of existing user to validate, or 0/null if a new registration.
         * @param $parameters: key/value object of registration data.
         * @return object: key/value pairs that we think are unacceptable. null if acceptable. key is the field in error, value is the error message.
         * TODO: this is not implemented, returns null (OK) as a placeholder.
         */
        public function userRegistrationValidation ($user_id, $parameters) {
//        $parameters = array(
//            'user_name' => $userName,
//            'password' => $password,
//            'email_address' => $email_address,
//            'full_name' => $fullname,
//            'location' => $location,
//            'tagline' => $tagline,
//            'dob' => $dateOfBirth,
//            'gender' => $gender,
//            'captcha' => $captcha,
//            'agreement' => $agreement
//        );
            // Mandatory: user_name, password, email, agreement
            // optional - must be valid if provided: full_name, dob, gender
            return null;
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
                'real_name' => $this->arrayValueOrDefault($parameters, 'real_name', $parameters['user_name']),
                'city' => $parameters['location'],
                'state' => '',
                'zipcode' => '',
                'country_code' => '',
                'tagline' => $parameters['tagline'],
                'dob' => $parameters['dob'],
                'gender' => $parameters['gender'],
                'mobile_number' => '',
                'im_id' => '',
                'img_url' => '',
                'about_me' => '',
                'additional_info' => '',
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
                    'location' => $parameters->location,
                    'tagline' => $parameters->tagline,
                    'age' => $parameters->age,
                    'gender' => $parameters->gender
                );
            }
            return $userInfo;
        }

        /* @function userRegistrationUpdate
         * @description
         *   Update and existing user's registration information.
         * @param $userId: int the user's internal id
         * @param $parameters: key/value object of registration data. Only changed keys may be provided.
         * @return object: null if registration fails, otherwise returns the user info object.
         */
        public function userRegistrationUpdate ($userId, $parameters) {
            $service = 'RegisteredUserUpdate';
            return null;
        }

        /* @function userForgotPassword
         * @description
         *   Trigger the forgot password procedure. The server will reset the user's password and
         *   send an email to the email address on record to follow a link to reset the password.
         * @param $userName: string the user's name
         * @param $email: string the user's email address
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

        public function newsletterTrackingRecord ($userId, $newsletterId, $event, $eventDetails, $referrer) {
            $enginesisResponse = $this->callServerAPI('NewsletterTrackingRecord', array('u_id' => $userId, 'newsletter_id' => $newsletterId, 'event_id' => $event, 'event_details' => $eventDetails, 'referrer' => $referrer));
            $results = $this->setLastErrorFromResponse($enginesisResponse);
            return $results != null;
        }
    }