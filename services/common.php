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
    date_default_timezone_set('America/New_York');
    ini_set('error_reporting', E_ALL);
    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    function getServiceProtocol () {
        // return http or https. you should use the result of this and never hard-code http:// into any URLs.
        if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
            $protocol = 'https';
        } else {
            $protocol = 'http';
        }
        return $protocol;
    }

    function encodeURLParams ($data) {
        $encodedURLParams = '';
        foreach ($data as $key => $value) {
            if ($encodedURLParams != '') {
                $encodedURLParams .= '&';
            }
            $encodedURLParams .= urlencode($key) . '=' . urlencode($value);
        }
        return $encodedURLParams;
    }

    function decodeURLParams ($encodedURLParams) {
        $data = array();
        $arrayOfParameters = explode('&', $encodedURLParams);
        $i = 0;
        while ($i < count($arrayOfParameters))  {
            $parameter = explode('=', $arrayOfParameters[$i]);
            if (count($parameter) > 0) {
                $data[urldecode($parameter[0])] = urldecode($parameter[1]);
            }
            $i ++;
        }
        return $data;
    }

    function getPostOrRequestVar ($varName, $defaultValue = NULL) {
        if (isset($_POST[$varName])) {
            return($_POST[$varName]);
        } elseif (isset($_REQUEST[$varName])) {
            return($_REQUEST[$varName]);
        } else {
            return $defaultValue;
        }
    }

    function serverName () {
        if ( strpos( $_SERVER['HTTP_HOST'], ':' ) !== false ) {
            $host_name = isset($_SERVER['HTTP_X_FORWARDED_HOST'] ) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : $_SERVER['HTTP_HOST'];
            $server = substr($host_name, 0, strpos( $host_name, ':' ) );
        } else {
            $server = isset($_SERVER['HTTP_X_FORWARDED_HOST'] ) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : $_SERVER['HTTP_HOST'];
        }
        return $server;
    }

    function serverStage ($hostName = null) {
        // return just the -l, -d, -q, -x part, or '' for live.
        $targetPlatform = ''; // assume live until we prove otherwise
        if (strlen($hostName) == 0) {
            $hostName = serverName();
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

    function setDatabaseConnectionInfo () {
        global $sqlDatabaseConnectionInfo;
        $serverStage = serverStage(null);
        switch($serverStage) {
            case '-d':	// dev
                $sqlDatabaseConnectionInfo = array(
                        'host' => 'localhost',
                        'port' => '3306',
                        'user' => 'varynwp',
                        'password' => 'm3@tEr45',
                        'db' => 'wordpressvaryn');
                break;
            case '-q':	// qa
                $sqlDatabaseConnectionInfo = array(
                        'host' => 'localhost',
                        'port' => '3306',
                        'user' => 'varynwp',
                        'password' => 'm3@tEr45',
                        'db' => 'wordpressvaryn');
                break;
            case '-l':	// localhost
                $sqlDatabaseConnectionInfo = array(
                        'host' => '127.0.0.1',
                        'port' => '3306',
                        'user' => 'varynwp',
                        'password' => 'm3@tEr45',
                        'db' => 'wordpressvaryn');
                break;
            case '-x':	// external dev
                $sqlDatabaseConnectionInfo = array(
                        'host' => 'localhost',
                        'port' => '3306',
                        'user' => 'varynwp',
                        'password' => 'm3@tEr45',
                        'db' => 'wordpressvaryn');
                break;
            default:	// live
                $sqlDatabaseConnectionInfo = array(
                        'host' => 'localhost',
                        'port' => '3306',
                        'user' => 'varynwp',
                        'password' => 'm3@tEr45',
                        'db' => 'wordpressvaryn');
                break;
        }
    }

    function setMailHostsTable ($serverStage) {
        global $_MAIL_HOSTS;
        // Mail/sendmail/Postfix/Mailgun config
        $_MAIL_HOSTS = array(
            '-l' => array('host' => 'smtp.verizon.net', 'port' => 465, 'ssl' => true, 'tls' => false, 'user' => 'jlf990@verizon.net', 'password' => 'proPhet5++'),
            '-d' => array('host' => 'smtp.mailgun.org', 'port' => 587, 'ssl' => false, 'tls' => true, 'user' => 'postmaster@mailer.enginesis-q.com', 'password' => '1h4disai51w5'),
            '-q' => array('host' => 'smtp.mailgun.org', 'port' => 587, 'ssl' => false, 'tls' => true, 'user' => 'postmaster@mailer.enginesis-q.com', 'password' => '1h4disai51w5'),
            '-x' => array('host' => 'smtpout.secureserver.net', 'port' => 25, 'ssl' => false, 'tls' => false, 'user' => '', 'password' => ''),
            ''   => array('host' => 'smtp.mailgun.org', 'port' => 587, 'ssl' => false, 'tls' => true, 'user' => 'postmaster@mailer.enginesis.com', 'password' => '6w88jmvawr63')
        );
        ini_set('SMTP', $_MAIL_HOSTS[$serverStage]['host']);
    }

    function hashPassword ($password) {
        // Call this function to generate a password hash to save in the database instead of the password.
        // Generate random salt, can only be used with the exact password match.
        // This calls PHP's crypt function with the specific setup for blowfish.

        $chars = './ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $salt = '$2a$10$';
        for ($i = 0; $i < 22; $i ++) {
            $salt .= $chars[mt_rand(0, 63)];
        }
        return crypt($password, $salt);
    }

    function verifyHashPassword ($pass, $hashStoredInDatabase) {
        // Test a password and the user's stored hash of that password
        return $hashStoredInDatabase == crypt($pass, $hashStoredInDatabase);
    }

    function isLoggedInUser () {
        // check we have the Enginesis authtoken in engsession cookie
        if (isset($_COOKIE['engsession'])) {
            $authtoken = $_COOKIE['engsession'];
            $isLoggedIn = ($authtoken != NULL && strlen($authtoken) > 0);
        } else {
            $isLoggedIn = false;
        }
        return $isLoggedIn;
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


    function callEnginesisAPI ($fn, $serverURL, $paramArray) {
        /**
         * callEnginesisAPI: Make an Enginesis API request over the WWW
         * @param $fn is the API to call
         * @param $serverURL is the URL to contact without any query string (use $paramArray)
         * @param $paramArray key => value array of parameters e.g. array('site_id' => 100);
         */
        if ( ! isset($paramArray['response'])) {
            $paramArray['response'] = 'json';
        }
        $response = $paramArray['response'];
        if ( ! isset($paramArray['state_seq'])) {
            $paramArray['state_seq'] = 1;
        }
        if ( ! isset($paramArray['fn'])) {
            $paramArray['fn'] = $fn;
        }
        $ch = curl_init();
        if ($ch) {
            curl_setopt($ch, CURLOPT_URL, $serverURL);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
            curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie.txt');
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, encodeURLParams($paramArray));
            $contents = curl_exec($ch);
            curl_close($ch);
            $succeeded = strlen($contents) > 0;
            // TODO: We should verify the response is a valid EnginesisReponse object
            if ( ! $succeeded) {
                $contents = '{"results":{"status":{"success":"0","message":"SYSTEM_ERR","extended_info":"System error: ' . $serverURL . ' replied with no data."},"passthru":{"fn":"' . $fn . '","state_seq":0}}}';
            }
        } else {
            $contents = '{"results":{"status":{"success":"0","message":"SYSTEM_ERR","extended_info":"System error: unable to contact ' . $serverURL . ' or the server did not respond."},"passthru":{"fn":"' . $fn . '","state_seq":0}}}';
        }
        return $contents;
    }

    /**
     * @function: checkEmailAddress: process a possible track back request when a page loads.
     * @param {string} email address to validate
     * @returns bool true if possibly valid
     */
    function checkEmailAddress ($email) {
        //
        // Email address validator. Given a single email address returns true if format acceptable or false.
        //
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * processTrackBack: process a possible track back request when a page loads.
     * @param e: the event we are tracking, such as "Clicked Logo". While these are arbitrary, we should try to use the same value for the same event across all pages.
     * @param u: the anonymous userId who generated the event.
     * @param: i: which newsletter this event came from.
     *
     * This data gets recorded in the database to be processed later.
     *
     */
    function processTrackBack () {
        global $enginesisServer;
        $event = getPostOrRequestVar('e', '');
        $userId = getPostOrRequestVar('u', '');
        $newsletterId = getPostOrRequestVar('i', '');
        if ($event != '' && $userId != '' && $newsletterId != '') {
            if (isset($_SERVER['HTTP_REFERER'])) {
                $url = parse_url($_SERVER['HTTP_REFERER']);
                $referrer = $url['host'];
            } else {
                $referrer = 'varyn.com';
            }
            $params = array('u_id' => $userId, 'newsletter_id' => $newsletterId, 'event_id' => $event, 'event_details' => '', 'referrer' => $referrer);
            callEnginesisAPI('NewsletterTrackingRecord', $enginesisServer, $params);
        }
    }

    // "Global" PHP variables available to all scripts
    if ( ! defined('ROOTPATH') ) {
        define('ROOTPATH', $_SERVER['DOCUMENT_ROOT']);
    }

    $page = '';
    $siteId = 106;
    $isLoggedIn = isLoggedInUser();
    $server = '';
    $stage = '';
    $webServer = '';
    $sqlDatabaseConnectionInfo = null;
    $_MAIL_HOSTS = null;
    $server = serverName();
    $stage = serverStage($server);
    $serviceProtocol = getServiceProtocol();
    $enginesisServer = $serviceProtocol . '://enginesis.varyn' . $stage . '.com';
    $webServer = $serviceProtocol . '://www.varyn' . $stage . '.com';
    setDatabaseConnectionInfo();
    setMailHostsTable($stage);
