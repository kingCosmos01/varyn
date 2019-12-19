/**
 * Single Sign On for Google
 */

(function ssoGoogle (global) {
    "use strict";
    var ssoGoogle = {},
        _debug = true,
        _networkId = 7,
        _siteUserId = "",
        _applicationId = "",
        _SDKVersion = "v1",
        _scope = "email profile",
        _initialized = false,
        _loading = false,
        _loaded = false,
        _loginPending = false,
        _tokenExpiration = null,
        _token = null,
        _callbackWhenLoaded = null,
        _callbackWhenLoggedIn = null,
        _callbackWhenLoggedOut = null,
        _googleAuth = {},
        _userInfo = null,
        _authCookieToken = "enggapisession",
        _authCookieCode = "enggapicode",
        _loginButtonId = "gapi-signin-button";

    ssoGoogle.debugLog = function (message) {
        if (_debug) {
            console.log("ssoGoogle: " + message);
        }
    };

    /**
     * Define the data structure for what a logged in user shoud look like. This
     * is common to all SSO modules.
     */
    ssoGoogle.clearUserInfo = function () {
        _userInfo = {
            networkId: _networkId,
            userName: "",
            realName: "",
            email: "",
            userId: "",
            siteUserId: "",
            siteUserToken: "",
            gender: "U",
            dob: null,
            avatarURL: "",
            scope: _scope
        };
    };

    /**
     * Initialize the library and prepare it for use.
     * 
     * @param parameters {object} of
     *    networkId: {integer} Enginesis network id (should be 7!)
     *    applicationId: {string} gapi client Id for API calls
     *    loginCallback: {function} who to call when login completes
     *    logoutCallback: {function} who to call when logout completes
     * @returns {boolean}
     */
    ssoGoogle.setParameters = function (parameters) {
        var errors = null;
        if (parameters) {
            if (parameters.networkId) {
                _networkId = parameters.networkId;
            }
            if (parameters.applicationId) {
                _applicationId = parameters.applicationId;
            }
            if (parameters.SDKVersion) {
                _SDKVersion = parameters.SDKVersion;
            }
            if (parameters.scope) {
                _scope = parameters.scope;
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
    ssoGoogle.setLoginCallback = function (loginCallback) {
        _callbackWhenLoggedIn = loginCallback;
    };

    /**
     * Initialize the library and prepare it for use. This is called from the Google SDK load callback.
     * @returns {boolean}
     */
    ssoGoogle.init = function () {
        var googleApi = window.gapi,
            googleInstance = this;

        googleInstance.clearUserInfo();
        commonUtilities.cookieRemove(_authCookieCode, "/", "");
        if (googleApi) {
            _loading = false;
            _loaded = true;
            googleApi.load("auth2", function () {
                googleApi.client.load("google", _SDKVersion).then(function () {
                    _initialized = true;
                    _googleAuth = googleApi.auth2.init({
                            client_id: _applicationId,
                            cookiepolicy:"single_host_origin",
                            scope: _scope
                        });
                    if (_callbackWhenLoggedOut != null) {
                        // a deferred logout was pending handle it now
                        var callback = _callbackWhenLoggedOut;
                        _callbackWhenLoggedOut = null;
                        googleInstance.logout(callback);
                    } else {
                        // setup Google API listeners for state change events
                        _googleAuth.isSignedIn.listen(googleInstance.updateSignInState.bind(googleInstance));
                        _googleAuth.currentUser.listen(googleInstance.userChanged.bind(googleInstance));
                        if (_googleAuth.isSignedIn.get()) {
                            _googleAuth.signIn();
                        } else {
                            googleInstance.attachGoogleLoginButton();
                            if (_callbackWhenLoaded != null) {
                                var callback = _callbackWhenLoaded;
                                _callbackWhenLoaded = null;
                                callback(Error("User is not logged in with Google."));
                            }
                        }
                    }
                });
            });
        }
        return _initialized;
    };

    /**
     * To complete a Google login we need to set a cookie with some information then refresh the profile page
     * so the backend server code can pick up the cookie and complete the login.
     * TODO: Maybe better to call /procs/oauth.php with google specific parameters so we can do this without a cookie and page refresh
     * @param authCode - Google's id-token
     */
    ssoGoogle.setLoginCookie = function (googleUser, authCode) {
        var timeNow = new Date();
        var cookieExpireMinutes = 30;
        timeNow.setTime(timeNow.getTime() + (cookieExpireMinutes * 60 * 1000));
        commonUtilities.cookieSet(_authCookieCode, authCode, timeNow.toUTCString(), "/", "", false);
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
    ssoGoogle.attachGoogleLoginButton = function () {
        var googleInstance = this;
        var buttonElement = document.getElementById(_loginButtonId);

        if (buttonElement != null) {
            _googleAuth.attachClickHandler(buttonElement, {},
                function (currentGoogleUser) {
                    var basicProfile = currentGoogleUser.getBasicProfile(),
                        authResponse = currentGoogleUser.getAuthResponse();
                    _loginPending = true;
                    googleInstance.debugLog('Signed in: ' + basicProfile.getName());
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
                    googleInstance.setLoginCookie(currentGoogleUser, authResponse.id_token);
                    if (_callbackWhenLoggedIn != null) {
                        _callbackWhenLoggedIn(_userInfo);
                    }
                    // I cant get this code to work, Google crashes if we try to get the grantOfflineAccess so that never works.
                    //authResponse.grantOfflineAccess({
                    //    scope: _scope
                    //}).then(function(response) {
                    //    googleInstance.setLoginCookie(currentGoogleUser, response.code);
                    //    _loginPending = false;
                    //    if (_callbackWhenLoggedIn != null) {
                    //        googleInstance.debugLog('calling callback for logged in user ' + _userInfo.userName);
                    //        _callbackWhenLoggedIn(_userInfo, _networkId);
                    //    }
                    //});
                }, function (error) {
                    googleInstance.debugLog("error: " + (JSON.stringify(error, undefined, 2)));
                });
        }
    };

    /**
     * Load the Google SDK. This function must be called on any page that requires knowing if a user
     * is currently logged in with Google or any other Google services. Once loaded the Google SDK
     * calls its init() function.
     * Replaces <script src="https://apis.google.com/js/client:platform.js?onload=initGoogle"></script>
     * Example:
     *   ssoGoogle.load(parameters).then(function(result) { console.log('Facebook loaded'); }, function(error) { console.log('Facebook load failed ' + error.message); });
     * 
     * @param {object} parameters to configure our Google application.
     */
    ssoGoogle.load = function (parameters) {
        if ( ! _loaded) {
            _loaded = false;
            _loading = true;
            this.setParameters(parameters);
            (function (d, s, id, scriptSource, callback) {
                var js, gjs = d.getElementsByTagName(s)[0];
                if (d.getElementById(id)) {
                    return;
                }
                js = d.createElement(s);
                js.id = id;
                js.src = scriptSource + "?onload=" + callback;
                // https://apis.google.com/js/platform.js
                gjs.parentNode.insertBefore(js, gjs);
                // once loaded Google should call our callback function
            }(document, "script", "google-sdk", "https://apis.google.com/js/client:platform.js", "ssoGoogleInit"));
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
     * @param {object} parameters to configure our Google application.
     * @returns {Promise}
     */
    ssoGoogle.loadThenLogin = function (parameters) {
        var googleInstance = this;
        return new Promise(function(resolve) {
            if (googleInstance.isReady()) {
                googleInstance.getLoginStatus().then(resolve, resolve);
            } else {
                _callbackWhenLoaded = resolve;
                googleInstance.debugLog("not loaded, loading first then logging in");
                googleInstance.load(parameters);
            }
        });
    };

    /**
     * Determine if the Google API is ready for action.
     * @returns {boolean}
     */
    ssoGoogle.isReady = function () {
        return _loaded && _initialized;
    };

    /**
     * Return the Enginesis network id for Google.
     * @returns {number}
     */
    ssoGoogle.networkId = function () {
        return _networkId;
    };

    /**
     * Return the Enginesis site-user-id which is the unique user id for this network.
     * @returns {string}
     */
    ssoGoogle.siteUserId = function () {
        return _siteUserId;
    };

    /**
     * Return the complete user info object, of null if no user is logged in.
     * @returns {{userName: string, realName: string, userId: string, networkId: number, siteUserId: string, dob: null, gender: string, avatarURL: string}}
     */
    ssoGoogle.userInfo = function () {
        return _userInfo;
    };

    /**
     * Return the networks user token.
     * @returns {*}
     */
    ssoGoogle.token = function () {
        return _token;
    };

    /**
     * Return the networks user token expiration date as a JavaScript date object. This could be null if the token
     * is invaid or if no user is logged in.
     * @returns {*}
     */
    ssoGoogle.tokenExpirationDate = function () {
        return _tokenExpiration;
    };

    /**
     * Return true if the network user token has expired or is invalid. If the token is valid and not expired this function returns false.
     * @returns {boolean}
     */
    ssoGoogle.isTokenExpired = function () {
        return _tokenExpiration == null;
    };

    /**
     * Determine if we have a logged in user according to Google's rules. This function returns a Promise, that
     * will resolve once the status can be determined, since usually this requires a network call and some delay to figure it out.
     */
    ssoGoogle.getLoginStatus = function (loginStatus) {
        return new Promise(function(resolve, reject) {
            if (_googleAuth.isSignedIn.get()) {
                resolve(_userInfo);
            } else {
                reject(Error("User is not logged in with Google."));
            }
        });
    };

    /**
     * Event triggered when a user changes. Not sure yet what usefulness this has as we have no idea what
     * changed, and whether that change is something we should be concerned with.
     */
    ssoGoogle.userChanged = function (currentGoogleUser) {
        if (currentGoogleUser != null) {
            var googleProfile = currentGoogleUser.getBasicProfile();
            if (googleProfile != null) {
                this.debugLog("user change event for " + googleProfile.getName());
            }
        }
    };

    /**
     * Event listener when the user's sign in state changes.
     */
    ssoGoogle.updateSignInState = function () {
        var googleAuthInstance = gapi.auth2.getAuthInstance();
        if (googleAuthInstance != null) {
            if (_googleAuth.isSignedIn.get()) {
                this.debugLog("update sign in state change for a logged in user");
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
                    //ssoGoogle.setLoginCookie(currentGoogleUser, authResponse.id_token);
                    //if (_callbackWhenLoggedIn != null) {
                    //    ssoGoogle.debugLog('calling callback for logged in user ' + _userInfo.userName);
                    //    _callbackWhenLoggedIn(_userInfo);
                    //} else {
                    //    ssoGoogle.debugLog('no callback for logged in user ' + _userInfo.userName);
                    //}
                //} else {
                //    if (_callbackWhenLoggedOut != null) {
                //        ssoGoogle.debugLog('A logout is currently pending so ignoring login state change');
                //    } else {
                //        ssoGoogle.debugLog('A login is currently pending so ignoring login state change until offline access is granted');
                //    }
                //}
            } else {
                this.debugLog("update sign in state change for a signout");
                // TODO: perform signout, this was in response to a logout() call and the server replied.
            }
        } else {
            this.debugLog("error cannot determine current user auth instance");
        }
    };

    ssoGoogle.onGapiSuccess = function (googleUser) {
        this.debugLog("onGapiSuccess");
    };

    ssoGoogle.onGapiFailure = function (error) {
        this.debugLog("onGapiFailure");
    };

    /**
     * Log the user in with Google + API. This currently doesn't do anything
     * because with Google we are using the attachGoogleLoginButton method.
     * @param callBackWhenComplete
     */
    ssoGoogle.login = function (callBackWhenComplete) {
    };

    /**
     * Cause the user to fully logout from Google such that no cookies or local data persist.
     * @param callBackWhenComplete
     */
    ssoGoogle.logout = function (callBackWhenComplete) {
        if (gapi !== undefined && gapi.auth2 !== undefined) {
            var googleAuthInstance = gapi.auth2.getAuthInstance();
            if (googleAuthInstance != null) {
                googleAuthInstance.signOut().then(function () {
                    ssoGoogle.clearUserInfo();
                    if (callBackWhenComplete !== undefined && callBackWhenComplete != null) {
                        callBackWhenComplete();
                    }
                });
            } else {
                this.debugLog("logout failed because auth2 module not initialized");
                if (typeof callBackWhenComplete !== 'undefined' && callBackWhenComplete != null) {
                    _callbackWhenLoggedOut = callBackWhenComplete;
                }
            }
        } else {
            this.debugLog("logout failed because auth2 module not loaded");
            if (callBackWhenComplete !== undefined && callBackWhenComplete != null) {
                _callbackWhenLoggedOut = callBackWhenComplete;
            }
        }
    };

    /**
     * Disconnect the user from Google which should invoke a full user delete.
     * @param callBackWhenComplete
     */
    ssoGoogle.disconnect = function (callBackWhenComplete) {
    };


    /* ----------------------------------------------------------------------------------
     * Setup for AMD, node, or standalone reference the commonUtilities object.
     * ----------------------------------------------------------------------------------*/

    if (typeof define === "function" && define.amd) {
        define(function () { return ssoGoogle; });
    } else if (typeof exports === "object") {
        module.exports = ssoGoogle;
    } else {
        var existingFunctions = global.ssoGoogle;
        ssoGoogle.existingFunctions = function () {
            global.ssoGoogle = existingFunctions;
            return this;
        };
        global.ssoGoogle = ssoGoogle;
    }
})(this);

/**
 * Google forces this as a global function to callback after the SDK loads.
 */
function ssoGoogleInit () {
    ssoGoogle.init();
}