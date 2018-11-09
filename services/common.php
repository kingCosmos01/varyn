<?php
/**
 * Common utility PHP functions for Varyn.com
 * This file defines the following globals:
 *   ROOTPATH is the file path to the root of the web site
 *   $stage = -l, -q, -d,or '' for Live
 *   $server = which enginesis server to converse with, full protocol/domain/url e.g. https://www.enginesis.com
 *   $siteId = enginesis site_id for this website.
 *   $enginesisServer = location/root URL of the enginesis server we are conversing with
 *   $webServer = our (this) web server
 *   $stage = the server stage we think we are
 *   $isLoggedIn = true if the user is logged in
 *
 */
session_start();
require_once('serverConfig.php');
require_once('Enginesis.php');
date_default_timezone_set('America/New_York');
setErrorReporting(true);
define('VARYN_VERSION', '2.1.3');
define('LOGFILE_PREFIX', 'varyn_php_');
define('VARYN_SESSION_COOKIE', 'varynuser');
if (isset($_SERVER['DOCUMENT_ROOT']) && strlen($_SERVER['DOCUMENT_ROOT']) > 0) {
    $varynServerRootPath = $_SERVER['DOCUMENT_ROOT'] . '/../';
} else {
    $varynServerRootPath = '../';
}
define('SERVER_ROOT', $varynServerRootPath);
define('SERVER_DATA_PATH', $varynServerRootPath . 'data/');
define('SERVICE_ROOT', $varynServerRootPath . 'services/');
define('VIEWS_ROOT', $varynServerRootPath . 'services/views/');

/**
 * @description
 *   Turn on or off all error reporting. Typically we want this on for development, off for production.
 * @param {bool} true to turn on error reporting, false to turn it off.
 * @return {bool} just echos back the flag.
 */
function setErrorReporting ($reportingFlag) {
    if ($reportingFlag) {
        ini_set('error_reporting', E_ALL);
        ini_set('display_errors', 1);
        ini_set('html_errors', 'On');
        error_reporting(E_ALL);
    } else {
        ini_set('error_reporting', E_ERROR);
        ini_set('display_errors', 0);
        ini_set('html_errors', 'Off');
        error_reporting(E_ERROR);
    }
    return $reportingFlag;
}

/**
 * Write a debug log message to the server log.
 * @param $msg string The message to log.
 */
function debugLog ($msg) {
    $filename = SERVER_DATA_PATH . LOGFILE_PREFIX . date('ymd') . '.log';
    try {
        $logfile = fopen($filename, 'a');
        if ($logfile) {
            fwrite($logfile, "$msg\r\n");
            fclose($logfile);
        } else {
            error_log("Varyn debugLog file system error on $filename: $msg\n");
        }
    } catch (Exception $e) {
        error_log("Varyn debugLog: $msg\n");
    }
}

/**
 * Debug a variable by echoing information to the output stream.
 * @param $variable
 * @param null $message
 */
function debugVar($variable, $message = null, $show = true) {
    if ( ! isset($message) || $message == null) {
        $caller = debug_backtrace()[0];
        $message = 'From ' . basename($caller['file']) . ':' . $caller['line'];
    }
    if ($show) {
        echo("<h3>$message</h3>");
        echo '<pre>';
        var_dump($variable);
        echo '</pre>';
    }
    debugLog($message . ' || ' . var_export($variable, true));
}

/**
 * Debug a variable by returning it as a string.
 * @param $variable
 * @return string
 */
function debugToString($variable) {
    return var_export($variable, true);
}

/**
 * Convert a boolean value to a string.
 * @param $variable
 * @return string
 */
function boolToString($variable) {
    return $variable ? 'true' : 'false';
}

/**
 * Convert a value to its boolean representation.
 * @param $variable - any type will be coerced to a boolean value.
 * @return boolean
 */
function valueToBoolean($variable) {
    if (is_string($variable)) {
        $variable = strtoupper($variable);
        $result =  $variable == '1' || $variable == 'Y' || $variable == 'T' || $variable == 'YES' || $variable == 'TRUE' || $variable == 'CHECKED';
    } elseif (is_numeric($variable)) {
        $result = ! ! $variable;
    } else {
        $result = $variable != null;
    }
    return $result;
}

/**
 * Return a variable that was posted from a form, or in the REQUEST object (GET or COOKIES), or a default if not found.
 * This way POST is the primary concern but if not found will fallback to the other methods.
 * @param $varName
 * @param null $defaultValue
 * @return null
 */
function getPostOrRequestVar ($varName, $defaultValue = NULL) {
    $value = null;
    if (is_array($varName)) {
        for ($i = 0; $i < count($varName); $i ++) {
            $value = getPostOrRequestVar($varName[$i], null);
            if ($value != null) {
                break;
            }
        }
        if ($value == null) {
            $value = $defaultValue;
        }
    } else {
        if (isset($_POST[$varName])) {
            $value = $_POST[$varName];
        } elseif (isset($_GET[$varName])) {
            $value = $_GET[$varName];
        } elseif (isset($_REQUEST[$varName])) {
            $value = $_REQUEST[$varName];
        } else {
            $value = $defaultValue;
        }
    }
    return $value;
}

function startsWith($haystack, $needle) {
    if (is_array($needle)) {
        for ($i = 0; $i < count($needle); $i ++) {
            if (startsWith($haystack, $needle[$i])) {
                return true;
            }
        }
        return false;
    } else {
        return (substr($haystack, 0, strlen($needle)) === $needle);
    }
}

    /**
     * Return a variable that was posted from a form, or a default if not found.
     * @param $varName
     * @param null $defaultValue
     * @return null
     */
    function getPostVar ($varName, $defaultValue = NULL) {
        if (isset($_POST[$varName])) {
            return($_POST[$varName]);
        } else {
            return $defaultValue;
        }
    }

    function getDatabaseConnectionInfo ($serverStage) {
        global $_DB_CONNECTIONS;

        $dbConnectInfo = $_DB_CONNECTIONS[$serverStage];
        if ($dbConnectInfo != null) {
            $sqlDatabaseConnectionInfo = array(
                'host' => $dbConnectInfo['host'],
                'port' => $dbConnectInfo['port'],
                'user' => $dbConnectInfo['user'],
                'password' => $dbConnectInfo['password'],
                'db' => $dbConnectInfo['db']);
        } else {
            $sqlDatabaseConnectionInfo = null;
        }
        return $sqlDatabaseConnectionInfo;
    }

    function setMailHostsTable ($serverStage) {
        global $_MAIL_HOSTS;
        if (isset($_MAIL_HOSTS) && isset($_MAIL_HOSTS[$serverStage])) {
            ini_set('SMTP', $_MAIL_HOSTS[$serverStage]['host']);
        }
    }

    /** @function hashPassword
     * @description
     *   Call this function to generate a password hash to save in the database instead of the password.
     *   Generate random salt, can only be used with the exact password match.
     *   This calls PHP's crypt function with the specific setup for blowfish. mcrypt is a required PHP module.
     * @param string the user's password
     * @return string the hashed password.
     */
    function hashPassword ($password) {
        $chars = './ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $salt = '$2a$10$';
        for ($i = 0; $i < 22; $i ++) {
            $salt .= $chars[mt_rand(0, 63)];
        }
        return crypt($password, $salt);
    }

    /** @function verifyHashPassword
     * @param $pass string the user's password
     * @param $hashStoredInDatabase string the password we looked up in the database
     * @return bool true if the password is a match. false if password does not match.
     */
    function verifyHashPassword ($pass, $hashStoredInDatabase) {
        // Test a password and the user's stored hash of that password
        return $hashStoredInDatabase == crypt($pass, $hashStoredInDatabase);
    }

    /**
     * Database functions to abstract access to MySQL over PDO. This should be considered to be moved
     * into a separate class.
     * @return null|PDO
     */
    function dbConnect () {
        global $sqlDatabaseConnectionInfo;

        $dbConnection = null;
        try {
            $dbConnection = new PDO('mysql:host=' . $sqlDatabaseConnectionInfo['host'] . ';dbname=' . $sqlDatabaseConnectionInfo['db'] . ';port=' . $sqlDatabaseConnectionInfo['port'], $sqlDatabaseConnectionInfo['user'], $sqlDatabaseConnectionInfo['password']);
            $dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        } catch(PDOException $e) {
            echo('Error connecting to server ' . $sqlDatabaseConnectionInfo['host'] . ' ' . $sqlDatabaseConnectionInfo['db'] . ' user=' . $sqlDatabaseConnectionInfo['user'] . ' ' . $e->getMessage());
        }
        return $dbConnection;
    }

    function dbQuery ($db, $sqlCommand, $parametersArray) {
        if ($parametersArray == null) {
            $parametersArray = array();
        }
        $sql = $db->prepare($sqlCommand);
        $sql->execute($parametersArray);
        $sql->setFetchMode(PDO::FETCH_ASSOC);
        return $sql;
    }

    function dbExec ($db, $sqlCommand, $parametersArray) {
        $sql = $db->prepare($sqlCommand);
        $sql->execute($parametersArray);
        return $sql;
    }

    function dbError ($db) {
        // $db can be either a database handle or a results object
        $errorCode = null; // no error
        if ($db != null) {
            $errorInfo = $db->errorInfo();
            if ($errorInfo != null && count($errorInfo) > 1 && $errorInfo[1] != 0) {
                $errorCode = $errorInfo[2];
            }
        }
        return $errorCode;
    }

    function dbFetch ($result) {
        return $result->fetch();
    }

    function dbRowCount ($result) {
        return $result->rowCount();
    }

    function dbLastInsertId ($db) {
        $lastId = 0; // error
        if ($db != null) {
            $lastId = $db->lastInsertId();
        }
        return $lastId;
    }

    function dbClearResults ($results) {
        // Clear any query results still pending on the connection
        if ($results != null) {
            $results->closeCursor();
        }
    }

    /**
     * Given a MySQL date string return a human readable date string.
     * @param $date
     * @return bool|string
     */
    function mysqlDateToHumanDate($date) {
        if ( ! empty($date)) {
            $defaultUserDateFormat = 'D j-M Y g:i A';
            return date($defaultUserDateFormat, strtotime($date));
        } else {
            return 'unknown';
        }
    }

    /**
     * Convert php Date or a date string to MySQL date
     * @param $phpDate
     * @return bool|string
     */
    function dateToMysqlDate ($phpDate) {
        if (is_null($phpDate)) {
            return date('Y-m-d H:i:s', time()); // no date given, use now
        } elseif (is_string($phpDate)) {
            return date('Y-m-d H:i:s', strtotime($phpDate));
        } else {
            return date('Y-m-d H:i:s', $phpDate);
        }
    }

    /**
     * @function: checkEmailAddress: process a possible track back request when a page loads.
     * @param {string} email address to validate
     * @return bool true if possibly valid
     */
    function checkEmailAddress ($email) {
        //
        // Email address validator. Given a single email address returns true if format acceptable or false.
        //
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * @function: ageFromDate: Determine age (number of years) since date.
     * @param {date} Date to calculate age from.
     * @param {date} Date to calculate age to, default is today.
     * @return int number of years from date to today.
     */
    function ageFromDate ($checkDate, $referenceDate = null) {
        $timestamp = strtotime($checkDate);
        if ($referenceDate == null) {
            $referenceDateTime = time();
        } else {
            $referenceDateTime = strtotime($referenceDate);
        }
        $years = date("Y", $referenceDateTime) - date("Y", $timestamp);
        if (date("md", $timestamp) > date("md", $referenceDateTime)) {
            $years --;
        }
        return $years;
    }

    /**
     * processTrackBack: process a possible track back request when a page loads.
     * @param e: the event we are tracking, such as "Clicked Logo". While these are arbitrary, we should try to use
     *     the same value for the same event across all pages. Where are these id's documented?
     * @param u: the anonymous userId who generated the event.
     * @param: i: which newsletter this event came from.
     *
     * This data gets recorded in the database to be processed later.
     *
     */
    function processTrackBack () {
        global $enginesis;
        $event = getPostOrRequestVar('e', '');
        $userId = getPostOrRequestVar('u', '');
        $newsletterId = getPostOrRequestVar('i', '');
        if ($newsletterId == '') {
            $newsletterId = getPostOrRequestVar('id', '');
        }
        if ($event != '' && $userId != '' && $newsletterId != '') {
            if (isset($_SERVER['HTTP_REFERER'])) {
                $url = parse_url($_SERVER['HTTP_REFERER']);
                $referrer = $url['host'];
            } else {
                $referrer = 'varyn.com';
            }
            $enginesis->newsletterTrackingRecord($userId, $newsletterId, $event, '', $referrer);
        }
    }

    /**
     * Verify the sever stage we are running on is sufficient to run Enginesis. There are a set of required
     * modules we need in order for the platform to operate. This function returns an array of either only
     * the failed tests, or the status of all tests.
     * @param $includePassedTests boolean set to false to return only failed tests, set to true to return
     *        both failed tests and passed tests. default is false.
     * @return array a key value array where the key is the test performed and the value is a boolean
     *        indicating the test passed (true) or the test failed (false).
     */
    function verifyStage($includePassedTests = false) {
        global $enginesisLogger;
        $testStatus = [];

        // Test for required PHP version
        $test = 'php-version';
        $isValid = version_compare(phpversion(), '7.2.0', '>=');
        if ( ! $isValid || ($isValid && $includePassedTests)) {
            $testStatus[$test] = $isValid;
        }

        // Test for required modules/extensions
        $requiredExtensions = ['openssl', 'curl', 'json', 'gd', 'PDO', 'pdo_mysql'];
        $extensions = get_loaded_extensions();
        foreach($requiredExtensions as $i => $test) {
            $isValid = in_array($test, $extensions);
            if ( ! $isValid || ($isValid && $includePassedTests)) {
                $testStatus[$test] = $isValid;
            }
        }

        // Test for required gd support
        $test = 'gd';
        $isValid = function_exists('gd_info');
        if ($isValid) {
            $gdInfo = gd_info();
            $test = 'gd-jpg';
            $isValid = $gdInfo['JPEG Support'];
            if ( ! $isValid || ($isValid && $includePassedTests)) {
                $testStatus[$test] = $isValid;
            }
            $test = 'gd-png';
            $isValid = $gdInfo['PNG Support'];
            if ( ! $isValid || ($isValid && $includePassedTests)) {
                $testStatus[$test] = $isValid;
            }
        } else {
            $testStatus[$test] = $isValid;
        }

        // test for required openssl support
        $test = 'openssl';
        $isValid = function_exists('openssl_encrypt') && function_exists('openssl_get_cipher_methods');
        if ( ! $isValid || ($isValid && $includePassedTests)) {
            $testStatus[$test] = $isValid;
        }

        // Verify we have the right version of openssl
        $test = 'openssl-version';
        $openSSLMinVersion = 9470367;
        $isValid = OPENSSL_VERSION_NUMBER >= $openSSLMinVersion;
        if ( ! $isValid || ($isValid && $includePassedTests)) {
            $testStatus[$test] = $isValid;
        }
        return $testStatus;
    }

    /**
     * @method serverName
     * @purpose: determine the full domain name of the server we are currently running on.
     * @return: {string} server host name only, e.g. www.enginesis.com.
     */
    function serverName () {
        if (strpos($_SERVER['HTTP_HOST'], ':' ) !== false) {
            $host_name = isset($_SERVER['HTTP_X_FORWARDED_HOST'] ) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : $_SERVER['HTTP_HOST'];
            $server = substr($host_name, 0, strpos($host_name, ':' ) );
        } else {
            $server = isset($_SERVER['HTTP_X_FORWARDED_HOST'] ) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : $_SERVER['HTTP_HOST'];
        }
        return $server;
    }

    /**
     * Return the domain name and TLD only (remove server name, protocol, anything else) e.g. this function
     * converts http://www.games.com into games.com or http://www.games-q.com into games-q.com
     * @param null $serverName
     * @return null|string
     */
    function serverTail ($serverName = null) {
        if (strlen($serverName) == 0) {
            $serverName = serverName();
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
     * @purpose Parse the given host name to determine which stage we are currently running on. Return just
     *   the -l, -d, -q, -x part, or '' for live.
     * @param $hostName string - host name or domain name to parase. If null we try the current serverName().
     * @return string: server host name only, e.g. www.enginesis.com.
     */
    function serverStage ($hostName = null) {
        $targetPlatform = ''; // assume live until we prove otherwise
        if (strlen($hostName) == 0) {
            $hostName = serverName();
        }
        if (preg_match('/-[dlqx]\./i', $hostName, $matchedStage)) {
            $targetPlatform = substr($matchedStage[0], 0, 2);
        }
        return $targetPlatform;
    }

    /**
     * Create a token that is good on this server for 30 minutes. We use this token in sensitive input forms
     * to not accept input after this expiration time.
     * @return string the token the form should return.
     */
    function makeInputFormHackerToken () {
        global $enginesis;
        $expirationTime = 30;
        $hackerToken = md5($enginesis->getServerName()) . '' . floor(time() / ($expirationTime * 60));
        return $hackerToken;
    }

    /**
     * Given a token from an input form check to verify it has not yet expired.
     * @param $token generated with makeInputFormHackerToken.
     * @return boolean true when the token is good.
     */
    function validateInputFormHackerToken ($token) {
        return makeInputFormHackerToken() == $token;
    }

    /**
     * Transform a string into a safe to show inside HTML string. Unsafe HTML chars are converted to their escape equivalents.
     * @param $string a string to transform.
     * @return string the transformed string.
     */
    function safeForHTML ($string) {
        $htmlEscapeMap = array(
            '&' => '&amp;',
            '<' => '&lt;',
            '>' => '&gt;',
            '"' => '&quot;',
            "'" => '&#x27;',
            '/' => '&#x2F;'
        );
        $htmlEscapePattern = array(
            '/&/',
            '/</',
            '/>/',
            '/"/',
            '/\'/',
            '/\//'
        );
        return preg_replace($htmlEscapePattern, $htmlEscapeMap, $string);
    }

    /**
     * We cache the logged in user object locally so we have the user data at our disposal without going back to the server.
     * @param $userInfo
     * @param $domain
     */
    function setVarynUserCookie ($userInfo, $domain) {
        // $userInfo Object ( [user_id] => 10239 [site_id] => 106 [user_name] => Varyn [real_name] => Varyn [site_user_id] => [network_id] => 1 [dob] => 2004-02-16 [gender] => F [city] => [state] => [zipcode] => [country_code] => [email_address] => john@varyn.com [mobile_number] => [im_id] => [agreement] => 1 [img_url] => [about_me] => [date_created] => 2016-02-16 20:47:45 [date_updated] => [source_site_id] => 106 [last_login] => 2016-02-20 22:27:38 [login_count] => 34 [tagline] => [additional_info] => [reg_confirmed] => 1 [user_status_id] => 1 [site_currency_value] => 0 [site_experience_points] => 0 [view_count] => 0 [access_level] => 10 [role_name] => [user_rank] => 10001 [session_id] => cecfe3b4b5dac00d464eff98ba5c75c3 [cr] => d2a1bae6ef968501b648ccf253451a1a [authtok] => Dk39dEasNBgO79Mp0gjXnvGYBEPP06d5Pd KmpdvCnVEehliQpl5eezAdVfc9t9xsE7RDp5i9rPDjj73TXxaW1XOrVjWHwZsnQ0q/GsHtWl4tDGgS/lTMA== )
        $userInfoJSON = json_encode($userInfo);
        $_COOKIE[VARYN_SESSION_COOKIE] = $userInfoJSON;
        setcookie(VARYN_SESSION_COOKIE, $userInfoJSON, time() + (SESSION_DAYSTAMP_HOURS * 60 * 60), '/', $domain);
        debugLog('setVarynUserCookie ' . $userInfoJSON);
    }

    function getVarynUserCookie () {
        return isset($_COOKIE[VARYN_SESSION_COOKIE]) ? $_COOKIE[VARYN_SESSION_COOKIE] : null;
    }

    function getVarynUserCookieObject () {
        $userInfo = null;
        $userInfoJSON = getVarynUserCookie();
        if ($userInfoJSON != null) {
            $userInfo = json_decode($userInfoJSON);
        }
        return $userInfo;
    }

    function clearVarynUserCookie ($domain) {
        $_COOKIE[VARYN_SESSION_COOKIE] = null;
        setcookie(VARYN_SESSION_COOKIE, null, time() - 86400, '/', $domain);
    }

    /**
     * Helper function to determine if the current session is valid. What we are looking for:
     *   1. User id and token exist
     *   2. user id matches token
     *   3. not expired
     * @param $userId
     * @param $token
     * @return bool
     */
    function verifySessionIsValid($userId, $token) {
        // TODO: We need to write the code for this!
        return true;
    }
    /**
     * Search $text for tokens in the form %token% and replace them with their respective parameter value.
     * Example:
     *    $updatedText = ReplaceTokenArgs ( "This %food% is a %meat% %food%.", array("food" => "sandwich", "meat" => "turkey" )
     * will return "This sandwich is a turkey sandwich."
     * @param $text
     * @param $paramsArray
     * @return string replaced text
     */
    function tokenReplace ($text, $paramsArray) {
        foreach ($paramsArray as $token => $value) {
            $token = "%$token%";
            if (stripos($text, $token) !== false ) {
                $text = str_replace($token, $value, $text);
            }
        }
        return $text;
    }

    // "Global" PHP variables available to all scripts. Use ROOTPATH when you need the full file path to the root of the website (not web path).
    if ( ! defined('ROOTPATH') ) {
        define('ROOTPATH', $_SERVER['DOCUMENT_ROOT']);
    }
    $page = '';
    $siteId = 106;
    $developerKey = '34A9EBE91B578504';
    $languageCode = 'en';
    if ( ! isset($userId)) {
        $userId = 0;
    }
    $webServer = '';
    $enginesis = new Enginesis($siteId, null, $developerKey);
    $stage = $enginesis->getServerStage();
    setErrorReporting($stage != ''); // turn on errors for all stages except LIVE TODO: Remove from above when we are going live.
    $isLoggedIn = $enginesis->isLoggedInUser();
    $sqlDatabaseConnectionInfo = getDatabaseConnectionInfo($stage);
    setMailHostsTable($stage);
    processTrackBack();
