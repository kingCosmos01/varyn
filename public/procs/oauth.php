<?php
/**
 * OAuth services come here for the redirect URI when an oauth is requested
 * from any of our supported SSO networks. This page always redirects to
 * a page that expects an Enginesis authentication cookie to be set. If
 * there is an error it appends ?network=X&error=E to the URL.
 * 
 * Note at this writing this only handles Twitter oauth 2 authentication.
 * Facebook and Google both use entirely client-side JavaScript. Apple redirects
 * to appleauth.php.
 * 
 * To test this page:
 *   - https://varyn-l.com/procs/oauth.php?debug=1&action=login&provider=twitter&redirect=1105
 *   - https://varyn-l.com/procs/oauth.php?debug=1&action=test&provider=twitter&redirect=1105
 * 
 * Author: jf
 * Date: 5/22/2017
 */
require_once('../../services/common.php');
require_once('../../services/TwitterOAuth.php');
setErrorReporting(true);
define('TOKEN_STORE_KEY', 'varyn-sso-token-store');
$redirectPage = '';
$debug = true;
$errorCode = EnginesisErrors::NO_ERROR;

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
            echo("<h3>oauth.php Debug: $message</h3>\n");
        }
        debugLog('oauth.php: ' . $message);
    }

    /**
     * An attempt to determine if the referrer is valid. I'm not sure this is going to work as referrer can be easily
     * spoofed. We expect referrer to be a valid Varyn domain.
     * 
     * @param string $referrer String to check.
     * @return boolean true if referrer is considered valid.
     */
    function isValidReferrer($referrer) {
        $isValid = false;
        debugLog('oauth.php: referrer from ' . $referrer);
        if (strlen($referrer) > 0) {
            // localhost || varyn[-[l|d|q|x]].com
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
     * Set the page to redirect to if the request included information
     * about which next page should load.
     * 
     * @return string A URL or local path to redirect to.
     */
    function setRedirectPage() {
        $redirectRequest = strtolower(getPostOrRequestVar('redirect', ''));
        if ($redirectRequest != '') {
            // if a game, get the game page
            if (isValidId($redirectRequest)) {
                $redirectPage = "/play/?id=$redirectRequest";
            }
        } else {
            $redirectPage = '/profile/';
        }
        return $redirectPage;
    }

    /**
     * When sign in authentication completes always redirect to the next page, but if
     * we are testing then just echo that out and do not redirect.
     *
     * @param string $redirectPage The page to redirect to, including any query string parameters.
     * @param string $errorCode An error condition if the log in was not processed to a valid user.
     */
    function redirectToPage($redirectPage) {
        global $debug;

        if ($debug) {
            debugX("Would redirect to $redirectPage");
        } else {
            header('Location: ' . $redirectPage);
        }
        exit(0);
    }

    // TODO: If user is already logged in? Logout? Or Invalid call to this page?
    // TODO: What if refreshing tokens?

    $debug = (int) getPostOrRequestVar('debug', 0);
    $redirectPage = setRedirectPage();

    if ($enginesis->isLoggedInUser()) {
        debugX("called but a user is already logged in?");
        redirectToPage($redirectPage);
    }
    if (isset($_SERVER['HTTP_REFERER'])) {
        $referrer = $_SERVER['HTTP_REFERER'];
    } else {
        $referrer = 'unknown';
    }
    $action = strtolower(getPostOrRequestVar('action', ''));
    $provider = strtolower(getPostOrRequestVar('provider', ''));
    $oauthState = 'init';
    if ($action == '') {
        // we didn't call this page, so try to determine who did and if it is a valid callback
        // Is it a Twitter oauth callback?
        $network_id = EnginesisNetworks::Twitter;
        $isDenied = getPostOrRequestVar('denied', '');
        $oauthToken = getPostOrRequestVar('oauth_token', '');
        $oauthVerifier = getPostOrRequestVar('oauth_verifier', '');
        if (strlen($oauthToken) > 0 && strlen($oauthVerifier) > 0 && isValidReferrer($referrer)) {
            // TODO: match token with the outstanding token we saved in the cookie
            debugX("Accepting connection from $referrer and assuming it's twitter.");
            $provider = 'twitter';
            $action = 'login';
            $oauthState = 'callback';
        } elseif ($isDenied != '') {
            debugX('Twitter denied login with ' . $isDenied);
            // Append error information to the redirect URL to help the page display an error.
            $errorCode = EnginesisErrors::REGISTRATION_NOT_ACCEPTED;
            $hasQuery = strpos($redirectPage, '?');
            $redirectPage .= ($hasQuery === false ? '?' : '&') . 'code=' . $errorCode . '&network=' . $network_id . '&reason=' . $isDenied;
            redirectToPage($redirectPage);
        }
    }
    debugX('action ' . $action . ', provider ' . $provider);
    switch ($action) {
        case 'login':
            debugX('provider ' . $provider);
            switch ($provider) {
                case 'twitter':
                    $network_id = EnginesisNetworks::Twitter;
                    $twitterConsumerKey = $socialServiceKeys[$network_id]['app_id'];
                    $twitterConsumerSecret = $socialServiceKeys[$network_id]['app_secret'];
                    $stage = serverStage();
                    if ($stage == '') {
                        $protocol = 'https';
                    } else {
                        $protocol = 'http';
                    }
                    $oauthCallback = $protocol . '://varyn' . $stage . '.com/procs/oauth.php';
                    $twitterOAuth = new TwitterOAuth($twitterConsumerKey, $twitterConsumerSecret);
                    if ($twitterOAuth != null) {
                        if ($oauthState == 'init') {
                            try {
                                if ($debug) {
                                    // This is a debug mock and is not a valid token.
                                    $oauthToken = 'Bj-zEQAAAAAA1YsSAAABXSls3NQ';
                                    $oauthTokenSecret = 'sUIJloJq6ePJrF3MKeJ0rmq87vSHWsuH';
                                    $requestToken = ['oauth_token' => $oauthToken, 'oauth_token_secret' => $oauthTokenSecret, 'oauth_callback_confirmed' => true];
                                    saveTokens(['oauth_token' => $oauthToken, 'oauth_token_secret' => $oauthTokenSecret]);
                                    $url = $oauthCallback . '?oauth_token=' . $oauthToken . '&oauth_verifier=' . $oauthTokenSecret . '&debug=' . ($debug ? '1' : '0');
                                    redirectToPage($url);
                                } else {
                                    $requestToken = $twitterOAuth->requestToken(array('oauth_callback' => $oauthCallback));
                                    // if all goes according to plan, we get array(oauth_token, oauth_token_secret, oauth_callback_confirmed)
                                    // if status == 200
                                    if ($requestToken['oauth_callback_confirmed']) {
                                        $oauthToken = $requestToken['oauth_token'];
                                        $oauthTokenSecret = $requestToken['oauth_token_secret'];
                                        if (strlen($oauthToken) > 0 && strlen($oauthTokenSecret) > 0) {
                                            $twitterOAuth->setOauthToken($oauthToken, $oauthTokenSecret);
                                            $url = $twitterOAuth->url(TwitterOAuth::API_AUTHORIZE, ['oauth_token' => $oauthToken]);
                                            // TODO: save in cookie so we can match it when the user returns from login
                                            saveTokens(['oauth_token' => $oauthToken, 'oauth_token_secret' => $oauthTokenSecret]);
                                            redirectToPage($url);
                                        } else {
                                            debugX("Invalid token received from $provider : $oauthToken / $oauthTokenSecret");
                                            $errorCode = EnginesisErrors::INVALID_TOKEN;
                                        }
                                    } else {
                                        debugX('oauth_callback_confirmed is ' . $requestToken['oauth_callback_confirmed'] . ', thats an error!');
                                        $errorCode = EnginesisErrors::INVALID_SIGN_IN_STATE;
                                    }
                                }
                            } catch (Exception $exception) {
                                debugX('init Caught exception ' . $exception->getmessage());
                                $errorCode = EnginesisErrors::INVALID_SIGN_IN_STATE;
                                // init Caught exception
                                //<?xml version="1.0" encoding="UTF-8">
                                //<hash>
                                //   <error>This client application's callback url has been locked</error>
                                //   <request>/oauth/request_token</request>
                                //</hash>
                            }
                        } elseif ($oauthState == 'callback') {
                            try {
                                // TODO: Verify $oauthToken we just received matches the one we stored in the cookie
                                $tokens = readTokens();
                                if (is_array($tokens)) {
                                    $priorOauthToken = $tokens['oauth_token'];
                                    $priorOauthSecret = $tokens['oauth_token_secret'];
                                } elseif (is_object($tokens)) {
                                    $priorOauthToken = $tokens->oauth_token;
                                    $priorOauthSecret = $tokens->oauth_token_secret;
                                } else {
                                    debugX("Cannot restore prior tokens so this is an invalid request.");
                                    $errorCode = EnginesisErrors::INVALID_SIGN_IN_STATE;
                                    debugX('tokens: ' . var_export($tokens, true));
                                }
                                if ($oauthToken == $priorOauthToken && isset($priorOauthSecret)) {
                                    $twitterOAuth->setOauthToken($oauthToken, $priorOauthSecret);
                                    $accessToken = $twitterOAuth->accessToken(array('oauth_verifier' => $oauthVerifier));
                                    // if all goes according to plan, we get array(oauth_token, oauth_token_secret)
                                    // if status == 200
                                    if ($twitterOAuth->getLastHttpCode() == 200) {
                                        $oauthToken = $accessToken['oauth_token'];
                                        $oauthTokenSecret = $accessToken['oauth_token_secret'];
                                        if (strlen($oauthToken) > 0 && strlen($oauthTokenSecret) > 0) {
                                            $twitterOAuth->setOauthToken($oauthToken, $oauthTokenSecret);
                                            $twitterUserInfo = $twitterOAuth->getUser();
                                            $rememberMe = true;
                                            debugX("User " . $twitterUserInfo->screen_name . " properly logged in with $provider : $oauthToken / $oauthTokenSecret");
                                            $userInfoSSO = [
                                                'network_id' => EnginesisNetworks::Twitter,
                                                'site_user_id' => $twitterUserInfo->id_str,
                                                'user_name' => $twitterUserInfo->screen_name,
                                                'real_name' => $twitterUserInfo->name,
                                                'email_address' => $twitterUserInfo->email,
                                                'dob' => dateToMysqlDate(date('Y-m-d H:i:s', strtotime('-14 year'))),
                                                'gender' => 'U',
                                                'scope' => '',
                                                'agreement' => '1',
                                                'avatar_url' => $twitterUserInfo->profile_image_url_https,
                                                'id_token' => ''
                                            ];
                                            $userInfo = $enginesis->userLoginCoreg($userInfoSSO, $rememberMe);
                                            if ($userInfo == null) {
                                                $error = $enginesis->getLastError();
                                                if ($error != null) {
                                                    $errorCode = $error['message'];
                                                }
                                            } else {
                                                $isLoggedIn = true;
                                                $authToken = $userInfo->authtok;
                                                $refreshToken = $userInfo->refreshToken;
                                                $userId = $userInfo->user_id;
                                                if ( ! $debug) {
                                                    redirectToPage($redirectPage);
                                                }
                                                exit(0);
                                            }
                                        } else {
                                            debugX("Invalid token received from $provider : $oauthToken / $oauthTokenSecret");
                                            $errorCode = EnginesisErrors::NETWORK_SIGN_IN_ERROR;
                                        }
                                    } else {
                                        debugX("Received HTTP error " . $twitterOAuth->getLastHttpCode() . " from Twitter!");
                                        $errorCode = EnginesisErrors::NETWORK_SIGN_IN_ERROR;
                                    }
                                } else {
                                    debugX("Invalid request prior session was not properly stored. Start over.");
                                    $errorCode = EnginesisErrors::NETWORK_SIGN_IN_ERROR;
                                }
                            } catch (Exception $exception) {
                                debugX('callback Caught exception ' . $exception->getmessage());
                                $errorCode = EnginesisErrors::NETWORK_SIGN_IN_ERROR;
                            }
                        }
                    }
                    break;

                case 'gapi':
                    $id_token = strtolower(getPostOrRequestVar('idtoken', ''));
                    if ($id_token != '') {
                        // use SocialServicesGoogle to register this user's token
                    }
                    break;

                default:
                    break;
            }
            break;

        case 'test':
            $network_id = EnginesisNetworks::Twitter;
            $provider = 'twitter';
            $oauthState = 'callback';
            $isValid = isValidReferrer($referrer);
            $redirect = setRedirectPage();
            $errorCode = EnginesisErrors::NETWORK_SIGN_IN_ERROR;
            debugX("oauth test referrer $referrer redirect to $redirectPage");
            break;

        default:
            debugX("Invalid, unmatched, or unexpected connection from $referrer with $action");
            $errorCode = EnginesisErrors::NETWORK_SIGN_IN_ERROR;
            break;
    }
    if ($errorCode != null) {
        // Append error information to the redirect URL to help the page to display an error.
        $hasQuery = strpos($redirectPage, '?');
        $redirectPage .= ($hasQuery === false ? '?' : '&') . 'code=' . $errorCode . '&network=' . $network_id;
    }
    redirectToPage($redirectPage);
