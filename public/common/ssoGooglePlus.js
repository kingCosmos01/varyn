/**
 * Single Sign On for Google +
 */

(function ssoGooglePlus (global) {
    'use strict';
    var ssoGooglePlus = {},
        _debug = true,
        _networkId = 7,
        _siteUserId = '',
        _applicationId = '1065156255426-al1fbn6kk4enqfq1f9drn8q1111optvt.apps.googleusercontent.com',
        _scope = 'email profile',
        _initialized = false,
        _loading = false,
        _loaded = false,
        _loginPending = false,
        _tokenExpiration = null,
        _token = null,
        _callbackWhenLoaded = null,
        _callbackWhenLoggedIn = null,
        _callbackWhenLoggedOut = null,
        _gplusAuth = {},
        _userInfo = null,
        _authCookieToken = 'enggapisession',
        _authCookieCode = 'enggapicode',
        _loginButtonId = 'gapi-signin-button';

    ssoGooglePlus.debugLog = function (message) {
        if (_debug) {
            console.log('ssoGooglePlus: ' + message);
        }
    };

    ssoGooglePlus.clearUserInfo = function () {
        _userInfo = {
            networkId: _networkId,
            userName: '',
            realName: '',
            email: '',
            userId: '',
            siteUserId: '',
            siteUserToken: '',
            gender: 'U',
            dob: null,
            avatarURL: '',
            scope: _scope
        };
    };

    /**
     * Initialize the library and prepare it for use.
     * @param parameters {object} of
     *    networkId: {integer} Enginesis network id (should be 7!)
     *    applicationId: {string} gapi client Id for API calls
     *    loginCallback: {function} who to call when login completes
     *    logoutCallback: {function} who to call when logout completes
     * @returns {boolean}
     */
    ssoGooglePlus.setParameters = function (parameters) {
        var errors = null;
        this.debugLog('setParameters ' + JSON.stringify(parameters));
        if (parameters) {
            if (parameters.networkId) {
                _networkId = parameters.networkId;
            }
            if (parameters.applicationId) {
                _applicationId = parameters.applicationId;
            }
            if (parameters.loginCallback) {
                _callbackWhenLoggedIn = parameters.loginCallback;
            }
            if (parameters.logoutCallback) {
                _callbackWhenLoggedOut = parameters.logoutCallback;
            }
        }
        return errors;
    };

    /**
     * We need a set a callback to fire when a login completes.
     * @param loginCallback
     */
    ssoGooglePlus.setLoginCallback = function (loginCallback) {
        _callbackWhenLoggedIn = loginCallback;
    };

    /**
     * Initialize the library and prepare it for use. This is called from the GooglePlus SDK load callback.
     * @returns {boolean}
     */
    ssoGooglePlus.init = function () {
        var googleApi = window.gapi,
            googlePlusInstance = this;

        googlePlusInstance.clearUserInfo();
        commonUtilities.cookieRemove(_authCookieCode, '/', '');
        if (googleApi) {
            googlePlusInstance.debugLog('init');
            _loading = false;
            _loaded = true;
            googleApi.load('auth2', function () {
                googleApi.client.load('plus', 'v1').then(function () {
                    _initialized = true;
                    _gplusAuth = googleApi.auth2.init({
                            client_id: _applicationId,
                            cookiepolicy: 'single_host_origin',
                            scope: _scope
                        });
                    if (_callbackWhenLoggedOut != null) {
                        // a deferred logout was pending handle it now
                        var callback = _callbackWhenLoggedOut;
                        _callbackWhenLoggedOut = null;
                        googlePlusInstance.logout(callback);
                    } else {
                        // setup Google API listeners for state change events
                        _gplusAuth.isSignedIn.listen(googlePlusInstance.updateSignInState.bind(googlePlusInstance));
                        _gplusAuth.currentUser.listen(googlePlusInstance.userChanged.bind(googlePlusInstance));
                        if (_gplusAuth.isSignedIn.get()) {
                            googlePlusInstance.debugLog('init complete calling sign in');
                            _gplusAuth.signIn();
                        } else {
                            googlePlusInstance.attachGoogleLoginButton();
                            googlePlusInstance.debugLog('init complete not signed in');
                            if (_callbackWhenLoaded != null) {
                                var callback = _callbackWhenLoaded;
                                _callbackWhenLoaded = null;
                                callback(Error('User is not logged in with Google.'));
                            }
                        }
                    }
                });
            });
        }
        return _initialized;
    };

    /**
     * To complete a Google Plus login we need to set a cookie with some information then refresh the profile page
     * so the backend server code can pick up the cookie and complete the login.
     * TODO: Maybe better to call /procs/oauth.php with google specific parameters so we can do this without a cookie and page refresh
     * @param authCode - Google's id-token
     */
    ssoGooglePlus.setLoginCookie = function (googleUser, authCode) {
        var timeNow = new Date();
        var cookieExpireMinutes = 30;
        timeNow.setTime(timeNow.getTime() + (cookieExpireMinutes * 60 * 1000));
        commonUtilities.cookieSet(_authCookieCode, authCode, timeNow.toUTCString(), '/', '', false);
        //var url = '/procs/oauth.php';
        //var parameters = {
        //    provider: 'gapi',
        //    action: 'login',
        //    idtoken: authCode
        //};
        //post(url, parameters).then(function(response) {
        //    if (response.success) {
        //        login_complete();
        //    } else {
        //        login_failed();
        //    }
        //});
    };

    /**
     * If we don't have a logged in user and the current page is showing a Sign in with Google button then
     * attach Google's click handler.
     */
    ssoGooglePlus.attachGoogleLoginButton = function () {
        var googlePlusInstance = this;
        var buttonElement = document.getElementById(_loginButtonId);

        if (buttonElement != null) {
            _gplusAuth.attachClickHandler(buttonElement, {},
                function (currentGoogleUser) {
                    var basicProfile = currentGoogleUser.getBasicProfile(),
                        authResponse = currentGoogleUser.getAuthResponse();
                    _loginPending = true;
                    googlePlusInstance.debugLog('Signed in: ' + basicProfile.getName());
                    _userInfo = {
                        networkId: _networkId,
                        userName: basicProfile.getName(),
                        realName: basicProfile.getGivenName() + ' ' + basicProfile.getFamilyName(),
                        email: basicProfile.getEmail(),
                        siteUserId: basicProfile.getId(),
                        siteUserIdToken: authResponse.id_token,
                        gender: 'U',
                        dob: commonUtilities.MySQLDate(commonUtilities.subtractYearsFromNow(13)),
                        avatarURL: basicProfile.getImageUrl(),
                        scope: _scope
                    };
                    googlePlusInstance.setLoginCookie(currentGoogleUser, authResponse.id_token);
                    if (_callbackWhenLoggedIn != null) {
                        ssoGooglePlus.debugLog('calling callback for logged in user ' + _userInfo.userName);
                        _callbackWhenLoggedIn(_userInfo);
                    } else {
                        ssoGooglePlus.debugLog('no callback for logged in user ' + _userInfo.userName);
                    }
                    // I cant get this code to work, Google crashes if we try to get the grantOfflineAccess so that never works.
                    //authResponse.grantOfflineAccess({
                    //    scope: _scope
                    //}).then(function(response) {
                    //    googlePlusInstance.setLoginCookie(currentGoogleUser, response.code);
                    //    _loginPending = false;
                    //    if (_callbackWhenLoggedIn != null) {
                    //        googlePlusInstance.debugLog('calling callback for logged in user ' + _userInfo.userName);
                    //        _callbackWhenLoggedIn(_userInfo, _networkId);
                    //    }
                    //});
                }, function (error) {
                    googlePlusInstance.debugLog('error: ' + (JSON.stringify(error, undefined, 2)));
                });
        }
    };

    /**
     * Load the Google SDK. This function must be called on any page that requires knowing if a user
     * is currently logged in with Google Plus or any other Google services. Once loaded the Google SDK
     * calls its init() function.
     * Replaces <script src="https://apis.google.com/js/client:platform.js?onload=initGplus"></script>
     * Example:
     *   ssoFacebook.load(parameters).then(function(result) { console.log('Facebook loaded'); }, function(error) { console.log('Facebook load failed ' + error.message); });
     * @param parameters {object} parameters to configure our Google application.
     */
    ssoGooglePlus.load = function (parameters) {
        if ( ! _loaded) {
            this.debugLog('loading');
            _loaded = false;
            _loading = true;
            this.setParameters(parameters);
            (function (d, s, id, callback) {
                var js, gjs = d.getElementsByTagName(s)[0];
                if (d.getElementById(id)) {
                    return;
                }
                js = d.createElement(s);
                js.id = id;
                js.src = "https://apis.google.com/js/client:platform.js?onload=" + callback;
                gjs.parentNode.insertBefore(js, gjs);
                // once loaded Google should call our callback function
            }(document, 'script', 'gplus-sdk', 'ssoGooglePlusInit'));
        } else if ( ! _initialized) {
            this.init();
        }
    };

    /**
     * This is a "shortcut" to loading the Google SDK and getting the users current status. Because all these things
     * take time, we tried to pull everything together in a single function that could take quite a long time to
     * resolve.
     *
     * This function returns a promise that will resolve to a function that is called with the user's Google info
     * in the standard Enginesis object format. If the promise fails the function is called with an Error object.
     *
     * @param parameters {object} same parameters you pass to load().
     * @returns {Promise}
     */
    ssoGooglePlus.loadThenLogin = function (parameters) {
        var googlePlusInstance = this;
        return new Promise(function(resolve) {
            if (googlePlusInstance.isReady()) {
                googlePlusInstance.debugLog('loaded and ready');
                googlePlusInstance.getLoginStatus().then(resolve, resolve);
            } else {
                _callbackWhenLoaded = resolve;
                googlePlusInstance.debugLog('not loaded, loading first then logging in');
                googlePlusInstance.load(parameters);
            }
        });
    };

    /**
     * Determine if the Google API is ready for action.
     * @returns {boolean}
     */
    ssoGooglePlus.isReady = function () {
        return _loaded && _initialized;
    };

    /**
     * Return the Enginesis network id for Google Plus.
     * @returns {number}
     */
    ssoGooglePlus.networkId = function () {
        return _networkId;
    };

    /**
     * Return the Enginesis site-user-id which is the unique user id for this network.
     * @returns {string}
     */
    ssoGooglePlus.siteUserId = function () {
        return _siteUserId;
    };

    /**
     * Return the complete user info object, of null if no user is logged in.
     * @returns {{userName: string, realName: string, userId: string, networkId: number, siteUserId: string, dob: null, gender: string, avatarURL: string}}
     */
    ssoGooglePlus.userInfo = function () {
        return _userInfo;
    };

    /**
     * Return the networks user token.
     * @returns {*}
     */
    ssoGooglePlus.token = function () {
        return _token;
    };

    /**
     * Return the networks user token expiration date as a JavaScript date object. This could be null if the token
     * is invaid or if no user is logged in.
     * @returns {*}
     */
    ssoGooglePlus.tokenExpirationDate = function () {
        return _tokenExpiration;
    };

    /**
     * Return true if the network user token has expired or is invalid. If the token is valid and not expired this function returns false.
     * @returns {boolean}
     */
    ssoGooglePlus.isTokenExpired = function () {
        return _tokenExpiration == null;
    };

    /**
     * Determine if we have a logged in user according to Google's rules. This function returns a Promise, that
     * will resolve once the status can be determined, since usually this requires a network call and some delay to figure it out.
     */
    ssoGooglePlus.getLoginStatus = function (loginStatus) {
        return new Promise(function(resolve, reject) {
            if (_gplusAuth.isSignedIn.get()) {
                ssoGooglePlus.debugLog('user is signed in resolving getLoginStatus');
                resolve(_userInfo);
            } else {
                reject(Error('User is not logged in with Google.'));
            }
        });
    };

    /**
     * Event triggered when a user changes. Not sure yet what usefulness this has as we have no idea what
     * changed, and whether that change is something we should be concerned with.
     */
    ssoGooglePlus.userChanged = function (currentGoogleUser) {
        if (currentGoogleUser != null) {
            var gplusProfile = currentGoogleUser.getBasicProfile();
            if (gplusProfile != null) {
                this.debugLog('user change event for ' + gplusProfile.getName());
            }
        }
    };

    /**
     * Event listener when the user's sign in state changes.
     */
    ssoGooglePlus.updateSignInState = function () {
        var googleAuthInstance = gapi.auth2.getAuthInstance();
        if (googleAuthInstance != null) {
            if (_gplusAuth.isSignedIn.get()) {
                this.debugLog('update sign in state change for a logged in user');
                // TODO: Not sure yet what to do here. We could check to see if any user info has changed since last time we saw this user.
                // if ( ! _loginPending && _callbackWhenLoggedOut == null) {
                    //var currentGoogleUser = googleAuthInstance.currentUser.get(),
                    //    basicProfile = currentGoogleUser.getBasicProfile(),
                    //    authResponse = currentGoogleUser.getAuthResponse();
                    //_userInfo = {
                    //    networkId: _networkId,
                    //    userName: basicProfile.getName(),
                    //    realName: basicProfile.getGivenName() + ' ' + basicProfile.getFamilyName(),
                    //    email: basicProfile.getEmail(),
                    //    siteUserId: basicProfile.getId(),
                    //    siteUserIdToken: authResponse.id_token,
                    //    gender: 'U',
                    //    dob: commonUtilities.MySQLDate(commonUtilities.subtractYearsFromNow(13)),
                    //    avatarURL: basicProfile.getImageUrl(),
                    //    scope: _scope
                    //};
                    //ssoGooglePlus.setLoginCookie(currentGoogleUser, authResponse.id_token);
                    //if (_callbackWhenLoggedIn != null) {
                    //    ssoGooglePlus.debugLog('calling callback for logged in user ' + _userInfo.userName);
                    //    _callbackWhenLoggedIn(_userInfo);
                    //} else {
                    //    ssoGooglePlus.debugLog('no callback for logged in user ' + _userInfo.userName);
                    //}
                //} else {
                //    if (_callbackWhenLoggedOut != null) {
                //        ssoGooglePlus.debugLog('A logout is currently pending so ignoring login state change');
                //    } else {
                //        ssoGooglePlus.debugLog('A login is currently pending so ignoring login state change until offline access is granted');
                //    }
                //}
            } else {
                this.debugLog('update sign in state change for a signout');
                // TODO: perform signout, this was in response to a logout() call and the server replied.
            }
        } else {
            this.debugLog('error cannot determine current user auth instance');
        }
    };

    ssoGooglePlus.onGapiSuccess = function (googleUser) {
        this.debugLog('onGapiSuccess');
    };

    ssoGooglePlus.onGapiFailure = function (error) {
        this.debugLog('onGapiFailure');
    };

    /**
     * Log the user in with Google + API. This currently doesn't do anything
     * because with Google we are using the attachGoogleLoginButton method.
     * @param callBackWhenComplete
     */
    ssoGooglePlus.login = function (callBackWhenComplete) {
    };

    /**
     * Cause the user to fully logout from Google such that no cookies or local data persist.
     * @param callBackWhenComplete
     */
    ssoGooglePlus.logout = function (callBackWhenComplete) {
        if (typeof gapi !== 'undefined' && typeof gapi.auth2 !== 'undefined') {
            var googleAuthInstance = gapi.auth2.getAuthInstance();
            if (googleAuthInstance != null) {
                googleAuthInstance.signOut().then(function () {
                    ssoGooglePlus.debugLog(' user logout complete');
                    ssoGooglePlus.clearUserInfo();
                    if (typeof callBackWhenComplete !== 'undefined' && callBackWhenComplete != null) {
                        callBackWhenComplete();
                    }
                });
            } else {
                this.debugLog('logout failed because auth2 module not initialized');
                if (typeof callBackWhenComplete !== 'undefined' && callBackWhenComplete != null) {
                    _callbackWhenLoggedOut = callBackWhenComplete;
                }
            }
        } else {
            this.debugLog('logout failed because auth2 module not loaded');
            if (typeof callBackWhenComplete !== 'undefined' && callBackWhenComplete != null) {
                _callbackWhenLoggedOut = callBackWhenComplete;
            }
        }
    };

    /**
     * Disconnect the user from Google which should invoke a full user delete.
     * @param callBackWhenComplete
     */
    ssoGooglePlus.disconnect = function (callBackWhenComplete) {
    };


    /* ----------------------------------------------------------------------------------
     * Setup for AMD, node, or standalone reference the commonUtilities object.
     * ----------------------------------------------------------------------------------*/

    if (typeof define === 'function' && define.amd) {
        define(function () { return ssoGooglePlus; });
    } else if (typeof exports === 'object') {
        module.exports = ssoGooglePlus;
    } else {
        var existingFunctions = global.ssoGooglePlus;
        ssoGooglePlus.existingFunctions = function () {
            global.ssoGooglePlus = existingFunctions;
            return this;
        };
        global.ssoGooglePlus = ssoGooglePlus;
    }
})(this);

/**
 * Google forces this as a global function to callback after the SDK loads.
 */
function ssoGooglePlusInit () {
    ssoGooglePlus.init();
}