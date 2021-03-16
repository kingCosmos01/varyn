<?php
/**
 * Support for Sign in with Apple authentication flow. Apple sign in comes here for the redirect URI 
 * when an authentication is requested. This page always redirects to /profile. If there is an error
 * it sends &network=X&error=E
 *
 * Author: jf
 * Date: 11/16/2019
 *
 * Test: https://www.varyn-l.com/procs/appleauth.php?debug=1&state=signin&code=1237198237&id_token=12367186238&user={"name":{"firstName":"Steve","lastName":"Crappleseed"},"email":"varyn.dev@gmail.com"}
 * Test: https://www.varyn-l.com/procs/appleauth.php?debug=1&state=signin&error=invalid_user
 */
require_once('../../services/common.php');
require_once('../../services/strings.php');
setErrorReporting(true);
$debug = true;
$errorCode = EnginesisErrors::NO_ERROR;
$network_id = EnginesisNetworks::Apple;
$provider = 'Apple';

    /**
     * Simple debug function that writes a debug message to the log and places debug
     * output in the HTML stream if debugging mode is enabled.
     *
     * @param string $message Message to display.
     */
    function debugX($message) {
        global $debug;

        if ( ! isset($debug)) {
            $debug = false;
        }
        if ($debug) {
            echo("<p>appleauth.php Debug: $message</p>\n");
        }
        debugLog('appleauth.php: ' . $message);
    }

    /**
     * An attempt to determine if the referrer is valid. I'm not sure this is going to work as referrer can be easily
     * spoofed. In debug mode the referrer is always accepted.
     *
     * @param string $referrer Referrer to check.
     * @return boolean true if acceptable, false if not.
     */
    function isValidReferrer($referrer) {
        global $debug;
        // TODO: Should be https://appleid.apple.com
        $isValid = false;
        $appleReferrer = 'https://appleid.apple.com';
        debugLog('appleauth.php: referrer from ' . $referrer);
        $referrer = strtolower($referrer);
        if ($debug || $referrer == $appleReferrer) {
            $isValid = true;
        }
        return $isValid;
    }

    /**
     * Allow the user to save the refresh token with this session. Then we can use it if we detect an
     * expired authentication token. The token is saved in a browser cookie and is read back when the user
     * or the SSO authentication service returns on behalf of that user.
     *
     * @param Array $tokens Array of things to save.
     */
    function saveTokens($tokens) {
        $tokenString = json_encode($tokens);
        if (strlen($tokenString) > 4095) {
            debugX("Trying to save a cookie > 4095: " . $tokenString);
        }
        setcookie(TOKEN_STORE_KEY, $tokenString, time() + (48 * 60 * 60), '/');
        $_COOKIE[TOKEN_STORE_KEY] = $tokenString;
    }

    /**
     * Restore any saved tokens previously saved with `saveTokens()` from a prior session.
     *
     * @return Array|null Null is returned if no prior token is found.
     */
    function readTokens() {
        $tokens = null;
        if (isset($_COOKIE[TOKEN_STORE_KEY])) {
            $tokens = json_decode($_COOKIE[TOKEN_STORE_KEY]);
        }
        return $tokens;
    }

    /**
     * When sign in authentication completes always redirect to /profile/. The profile page is then
     * expected to detect the Enginesis session cookie and complete the session set up.
     *
     * @param string $errorCode An error condition if the log in was not processed to a valid user.
     */
    function redirectToProfile($errorCode) {
        global $network_id;
        global $debug;

        if ( ! empty($errorCode)) {
            $query = '?code=' . urlencode($errorCode) . '&network=' . $network_id;
        } else {
            $query = '?network=' . $network_id;
        }
        if ($debug) {
            debugX("Would redirect to /profile/$query");
        } else {
            header('Location: /profile/' . $query);
        }
        exit(0);
    }

    $debug = getPostOrRequestVar('debug', $debug);
    if ($enginesis->isLoggedInUser()) {
        debugX("called but a user is already logged in?");
        $errorCode = EnginesisErrors::ALREADY_SIGNED_IN;
        redirectToProfile($errorCode);
    }
    if (isset($_SERVER['HTTP_REFERER'])) {
        $referrer = $_SERVER['HTTP_REFERER'];
    } else {
        $referrer = 'unknown';
    }
    $authState = getPostOrRequestVar('state', '');
    $error = getPostOrRequestVar('error', '');
    $authenticationCode = getPostOrRequestVar('code', '');
    $jwt = getPostOrRequestVar('id_token', '');
    $userInfoJSON = getPostOrRequestVar('user', '');
    if ($authState != 'signin') {
        // Our JS code set the state and we are supposed the get only that value back, if it is anything else then it wasn't called by us.
        debugX('Unknown authentication state ' . $authState);
        $errorCode = EnginesisErrors::INVALID_SIGN_IN_STATE;
        redirectToProfile($errorCode);
    }
    if ($error != '') {
        // If Apple gives us an error we should not continue.
        debugX('Apple sign in reports error ' . $error);
        $errorCode = EnginesisErrors::NETWORK_SIGN_IN_ERROR;
        redirectToProfile($errorCode);
    }
    if ( ! isValidReferrer($referrer)) {
        // If Apple gives us an error we should not continue.
        debugX('Apple sign in invalid referring server ' . $referrer);
        $errorCode = EnginesisErrors::NETWORK_SIGN_IN_ERROR;
        redirectToProfile($errorCode);
    }
    if ($authenticationCode == '' || $jwt == '' || $userInfoJSON == '') {
        // All this is mandatory. If anything is missing we should not process the log in request.
        debugX('Apple sign in missing required data authenticationCode: "' . $authenticationCode . '" JWT: "' . $jwt . '" userInfo: "' . $userInfoJSON .'"');
        $errorCode = EnginesisErrors::NETWORK_SIGN_IN_ERROR;
        redirectToProfile($errorCode);
    }

    $userInfo = json_decode($userInfoJSON);
    if ($userInfo != null) {
        $email = $userInfo->email;
        $firstName = $userInfo->name->firstName;
        $lastName = $userInfo->name->lastName;
        $realName = mb_strimwidth($firstName . ' ' . $lastName, 0, 50);
        $rememberMe = true;
        $siteUserId = mb_strimwidth($jwt, 0, 50); // TODO: How do we get user-id from the JWT?

        debugX("User $realName properly logged in as $jwt");
        $userInfoSSO = [
            'network_id' => $network_id,
            'site_user_id' => $siteUserId,
            'user_name' => $realName,
            'real_name' => $realName,
            'email_address' => $email,
            'dob' => dateToMysqlDate(date('Y-m-d H:i:s', strtotime('-14 year'))),
            'gender' => 'U',
            'scope' => '',
            'agreement' => '1',
            'avatar_url' => '', // TODO: Use the Gravitar URL from the email address
            'id_token' => ''
        ];
        $userInfo = $enginesis->userLoginCoreg($userInfoSSO, $rememberMe);
        if ($userInfo == null) {
            $error = $enginesis->getLastError();
            if ($error != null) {
                $errorCode = $error['message'];
            } else {
                $errorCode = EnginesisErrors::INVALID_NETWORK_SIGN_IN;
            }
            debugX("User $realName failed co-reg with $errorCode");
            var_dump($error);
        } else {
            $isLoggedIn = true;
            $authToken = $userInfo->authtok;
            $refreshToken = $userInfo->refresh_token;
            $userId = $userInfo->user_id;
            debugX("User $userId properly registered as $realName");
            var_dump($userInfo);
        }
    } else {
        $errorCode = EnginesisErrors::INVALID_NETWORK_SIGN_IN; // 'Invalid user data';
        debugX("Unable to decode JSON $userInfoJSON");
    }
    redirectToProfile($errorCode);
