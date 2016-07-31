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
    define('SERVER_DATA_PATH', '..' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR);
    define('VARYN_SESSION_COOKIE', 'varynuser');

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
     * Return a variable that was posted from a form, or in the REQUEST object (GET or COOKIES), or a default if not found.
     * This way POST is the primary concern but if not found will fallback to the other methods.
     * @param $varName
     * @param null $defaultValue
     * @return null
     */
    function getPostOrRequestVar ($varName, $defaultValue = NULL) {
        if (isset($_POST[$varName])) {
            return($_POST[$varName]);
        } elseif (isset($_REQUEST[$varName])) {
            return($_REQUEST[$varName]);
        } else {
            return $defaultValue;
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

    function setDatabaseConnectionInfo ($serverStage) {
        global $_DB_CONNECTIONS;
        global $sqlDatabaseConnectionInfo;

        $dbConnectInfo = $_DB_CONNECTIONS[$serverStage];
        if ($dbConnectInfo != null) {
            $sqlDatabaseConnectionInfo = array(
                'host' => $dbConnectInfo['host'],
                'port' => $dbConnectInfo['port'],
                'user' => $dbConnectInfo['user'],
                'password' => $dbConnectInfo['password'],
                'db' => $dbConnectInfo['db']);
        }
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
        // $userInfo Object ( [user_id] => 10239 [site_id] => 106 [user_name] => Varyn [real_name] => Varyn [site_user_id] => [dob] => 2004-02-16 [gender] => F [city] => [state] => [zipcode] => [country_code] => [email_address] => john@varyn.com [mobile_number] => [im_id] => [agreement] => 1 [img_url] => [about_me] => [date_created] => 2016-02-16 20:47:45 [date_updated] => [source_site_id] => 106 [last_login] => 2016-02-20 22:27:38 [login_count] => 34 [tagline] => [additional_info] => [reg_confirmed] => 1 [user_status_id] => 1 [site_currency_value] => 0 [site_experience_points] => 0 [view_count] => 0 [access_level] => 10 [role_name] => [user_rank] => 10001 [session_id] => cecfe3b4b5dac00d464eff98ba5c75c3 [cr] => d2a1bae6ef968501b648ccf253451a1a [authtok] => Dk39dEasNBgO79Mp0gjXnvGYBEPP06d5Pd KmpdvCnVEehliQpl5eezAdVfc9t9xsE7RDp5i9rPDjj73TXxaW1XOrVjWHwZsnQ0q/GsHtWl4tDGgS/lTMA== )
        $userInfoJSON = json_encode($userInfo);
        $_COOKIE[VARYN_SESSION_COOKIE] = $userInfoJSON;
        setcookie(VARYN_SESSION_COOKIE, $userInfoJSON, time() + (SESSION_DAYSTAMP_HOURS * 60 * 60), '/', $domain);
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

    // "Global" PHP variables available to all scripts
    if ( ! defined('ROOTPATH') ) {
        define('ROOTPATH', $_SERVER['DOCUMENT_ROOT']);
    }

    $page = '';
    $siteId = 106;
    $userId = 0;
    $webServer = '';
    $enginesis = new Enginesis($siteId, null, $developerKey);
    $stage = $enginesis->getServerStage();
    setErrorReporting($stage != ''); // turn on errors for all stages except LIVE TODO: Remove from above when we are going live.
    $isLoggedIn = $enginesis->isLoggedInUser();
    $sqlDatabaseConnectionInfo = null;
    setDatabaseConnectionInfo($stage);
    setMailHostsTable($stage);
    processTrackBack();
