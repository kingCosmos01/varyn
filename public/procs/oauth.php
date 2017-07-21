<?php
/**
 * OAuth services come here for the redirect URI when an oauth is requested from any of our supported SSO networks.
 * Author: jf
 * Date: 5/22/2017
 *
 */
    require_once('../../services/common.php');
    require_once('../../services/TwitterOAuth.php');
    setErrorReporting(true);
    define('TOKEN_STORE_KEY', 'varyn-sso-token-store');

    /**
     * Simple debug function that places debug output in the HTML stream.
     * @param $message
     */
    function debugX($message) {
        global $debug;

        if ( ! isset($debug)) {
            $debug = false;
        }
        if ($debug) {
            echo("<h3>Debug: $message</h3>\n");
        } else {
            debugLog('oauth.php: ' . $message);
        }
    }

    /**
     * An attempt to determine if the referrer is valid. I'm not sure this is going to work as referrer can be easily
     * spoofed.
     * @param $referrer
     * @return bool
     */
    function isValidReferrer($referrer) {
        $isValid = false;
        if (strlen($referrer) > 0) {
            $isValid = true;
        }
        return $isValid;
    }

    /**
     * Allow the user to save the refresh token with this session. Then we can use it if we detect an
     * expired authentication token. The token is saved in a browser cookie and is read back when the user
     * or the SSO authentication service returns on behalf of that user.
     * @param $tokens {Array} array of things to save.
     */
    function saveTokens($tokens) {
        $tokenString = json_encode($tokens);
        setcookie(TOKEN_STORE_KEY, $tokenString, time() + (48 * 60 * 60), '/');
        $_COOKIE[TOKEN_STORE_KEY] = $tokenString;
    }

    /**
     * Restore any saved tokens from a prior session.
     * @return array|mixed|null|object
     */
    function readTokens() {
        $tokens = null;
        if (isset($_COOKIE[TOKEN_STORE_KEY])) {
            $tokens = json_decode($_COOKIE[TOKEN_STORE_KEY]);
        }
        return $tokens;
    }

    // TODO: If user is already logged in? Logout? Or Invalid call to this page?
    // TODO: What if refreshing tokens?

    if ($enginesis->isLoggedInUser()) {
        header('Location: /profile.php');
        exit(0);
    }
    $debug = (int) getPostOrRequestVar('debug', 0);
    $errorMessage = '';
    $errorCode = null;
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

        $oauthToken = getPostOrRequestVar('oauth_token', '');
        $oauthVerifier = getPostOrRequestVar('oauth_verifier', '');
        if (strlen($oauthToken) > 0 && strlen($oauthVerifier) > 0 && isValidReferrer($referrer)) {
            // TODO: match token with the outstanding token we saved in the cookie
            debugX("Accepting connection from $referrer");
            $provider = 'twitter';
            $action = 'login';
            $oauthState = 'callback';
        }
    }
    debugX('action ' . $action);
    switch ($action) {
        case 'login':
            debugX('provider ' . $provider);
            switch ($provider) {
                case 'twitter':
                    $network_id = 11;
                    $twitterConsumerKey = $socialServiceKeys[$network_id]['app_id'];
                    $twitterConsumerSecret = $socialServiceKeys[$network_id]['app_secret'];
                    $oauthCallback = 'http://www.varyn-l.com/procs/oauth.php';
                    $twitterOAuth = new TwitterOAuth($twitterConsumerKey, $twitterConsumerSecret);
                    if ($twitterOAuth != null) {
                        if ($oauthState == 'init') {
                            try {
                                if ($debug) {
                                    $oauthToken = 'Bj-zEQAAAAAA1YsSAAABXSls3NQ';
                                    $oauthTokenSecret = 'sUIJloJq6ePJrF3MKeJ0rmq87vSHWsuH';
                                    $requestToken = ['oauth_token' => $oauthToken, 'oauth_token_secret' => 'sUIJloJq6ePJrF3MKeJ0rmq87vSHWsuH', 'oauth_callback_confirmed' => true];
                                    saveTokens(['oauth_token' => $oauthToken, 'oauth_token_secret' => $oauthTokenSecret]);
                                    $url = $oauthCallback . '?oauth_token=' . $oauthToken . '&oauth_verifier=' . $oauthTokenSecret . '&debug=1';
                                    header('Location: ' . $url);
                                    exit(0);
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
                                            header('Location: ' . $url);
                                            exit(0);
                                        } else {
                                            debugX("Invalid token received from $provider : $oauthToken / $oauthTokenSecret");
                                        }
                                    } else {
                                        debugX('oauth_callback_confirmed is ' . $requestToken['oauth_callback_confirmed'] . ', thats an error!');
                                    }
                                }
                            } catch (Exception $exception) {
                                debugX('init Caught exception ' . $exception->getmessage());
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
                                    var_dump(tokens);
                                    exit(0);
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
                                            debugX("User properly logged in with $provider : $oauthToken / $oauthTokenSecret");
                                            // let's get the user's info
                                            $twitterUserInfo = $twitterOAuth->getUser();
                                            $rememberMe = true;
                                            $userInfoSSO = array(
                                                'user_name' => $twitterUserInfo->screen_name,
                                                'real_name' => $twitterUserInfo->name,
                                                'user_id' => $twitterUserInfo->id,
                                                'email_address' => $twitterUserInfo->email,
                                                'network_id' => EnginesisNetworks::Twitter,
                                                'site_user_id' => $twitterUserInfo->id_str,
                                                'dob' => dateToMysqlDate(date('Y-m-d H:i:s', strtotime('-13 year'))),
                                                'gender' => 'U',
                                                'avatarURL' => $twitterUserInfo->profile_image_url_https
                                            );
                                            $userInfo = $enginesis->userLoginCoreg($userInfoSSO, $rememberMe);
                                            if ($userInfo == null) {
                                                $error = $enginesis->getLastError();
                                                if ($error != null) {
                                                    $errorMessage = '<p class="error-text">Your account could not be logged in at this time. ' . errorToLocalString($error['message']) . '</p>';
                                                }
                                            } else {
                                                $isLoggedIn = true;
                                                $authToken = $userInfo->authtok;
                                                $refreshToken = $userInfo->refreshToken;
                                                $userId = $userInfo->user_id;
                                                setVarynUserCookie($userInfo, $enginesis->getServerName());
                                                if ( ! $debug) {
                                                    header('Location: /profile.php');
                                                }
                                                exit(0);
                                            }
                                        } else {
                                            debugX("Invalid token received from $provider : $oauthToken / $oauthTokenSecret");
                                        }
                                    } else {
                                        debugX("Received HTTP error " . $twitterOAuth->getLastHttpCode() . " from Twitter!");
                                    }
                                } else {
                                    debugX("Invalid request prior session was not properly stored. Start over.");
                                }
                            } catch (Exception $exception) {
                                debugX('callback Caught exception ' . $exception->getmessage());
                            }
                        }
                    }
                    break;
                default:
                    break;
            }
            break;
        default:
            debugX("Invalid, unmatched, or unexpected connection from $referrer");
            break;
    }
    if ($errorCode != null) {

    }