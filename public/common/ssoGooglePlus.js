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
        _scope = 'email',
        _initialized = false,
        _loading = false,
        _loaded = false,
        _tokenExpiration = null,
        _token = null,
        _callbackWhenLoaded = null,
        _gplusAuth = {},
        _userInfo = {
            networkId: 0,
            userName: '',
            realName: '',
            userId: '',
            siteUserId: '',
            gender: 'U',
            dob: null,
            avatarURL: '',
            scope: _scope
        };

    ssoGooglePlus.debugLog = function (message) {
        if (_debug) {
            console.log('ssoGooglePlus: ' + message);
        }
    };

    /**
     * Initialize the library and prepare it for use.
     * @param parameters
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
        }
        return errors;
    };

    /**
     * Initialize the library and prepare it for use. This is called from the GooglePlus SDK load callback.
     * @returns {boolean}
     */
    ssoGooglePlus.init = function () {
        var googleApi = window.gapi,
            googlePlusInstance = this;

        if (googleApi) {
            googlePlusInstance.debugLog('init');
            _loading = false;
            _loaded = true;
            googleApi.load('auth2', function () {
                googleApi.client.load('plus', 'v1').then(function () {
                    _initialized = true;
                    googlePlusInstance.debugLog('init complete');
                    _gplusAuth = googleApi.auth2.init({
                            client_id: _applicationId,
                            cookiepolicy: 'single_host_origin',
                            scope: 'profile email'
                        });
                    // setup Google API listeners for state change events
                    _gplusAuth.isSignedIn.listen(googlePlusInstance.updateSignInState.bind(googlePlusInstance));
                    _gplusAuth.currentUser.listen(googlePlusInstance.userChanged.bind(googlePlusInstance));
                    if (_gplusAuth.isSignedIn.get()) {
                        _gplusAuth.signIn();
                    } else {
                        googlePlusInstance.attachGoogleLoginButton();
                        if (_callbackWhenLoaded != null) {
                            var callback = _callbackWhenLoaded;
                            _callbackWhenLoaded = null;
                            googlePlusInstance.debugLog('calling callback for no logged in user');
                            callback(Error('User is not logged in with Google.'));
                        }
                    }
                });
            });
        }
        return _initialized;
    };

    /**
     * If we don't have a logged in user and the current page is showing a Sign in with Google button then
     * attach Google's click handler.
     */
    ssoGooglePlus.attachGoogleLoginButton = function () {
        var buttonId = 'gapi-signin-button',
            googlePlusInstance = this;

        if (document.getElementById(buttonId)) {
            // No logged in user: attach the login click handler only if the current page offers the button.
            _gplusAuth.attachClickHandler(buttonId, {},
                function (googleUser) {
                    var gplusProfile = googleUser.getBasicProfile();
                    googlePlusInstance.debugLog('Signed in: ' + gplusProfile.getName());
                    var registrationParameters = {
                        networkId: 7,
                        userName: gplusProfile.getName(),
                        realName: gplusProfile.getGivenName(),
                        emails: gplusProfile.getEmail(),
                        siteUserId: gplusProfile.getId(),
                        gender: 'U',
                        dob: commonUtilities.MySQLDate(commonUtilities.subtractYearsFromNow(13)),
                        avatarURL: '',
                        scope: ''
                    };
                    varynApp.registerSSO(registrationParameters, registrationParameters.networkId);
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
                resolve(userInfo);
            } else {
                reject(Error('User is not logged in with Google.'));
            }
        });
    };

    /**
     * Event triggered when a user changes.
     */
    ssoGooglePlus.userChanged = function (user) {
        this.debugLog('GPlus user change event');
    };

    /**
     * Event listener when the user's sign in state changes.
     */
    ssoGooglePlus.updateSignInState = function () {
        this.debugLog('GPlus update sign in state change');
        var googleAuthInstance;
        if (_gplusAuth.isSignedIn.get()) {
            googleAuthInstance = gapi.auth2.getAuthInstance();
            this.debugLog('user is signed in as ' + JSON.stringify(googleAuthInstance));
        } else {
            googleAuthInstance = gapi.auth2.getAuthInstance();
            this.debugLog('user is signed out: ' + JSON.stringify(googleAuthInstance));
        }
    };

    ssoGooglePlus.onGapiSuccess = function (googleUser) {
        this.debugLog('success');

    };

    ssoGooglePlus.onGapiFailure = function (error) {
        this.debugLog('failed');

    };

    ssoGooglePlus.login = function (callBackWhenComplete) {
        // start the user login process.
        var response = {
            name: 'Google user',
            email: 'user@gmail.com',
            id: '12312312312312',
            gender: 'U'
        };
        var registrationParameters = {
            networkId: 7,
            userName: 'Fake Google User',
            realName: response.name,
            email: response.email,
            siteUserId: response.id,
            gender: enginesisSession.validGender(response.gender),
            dob: commonUtilities.MySQLDate(commonUtilities.subtractYearsFromNow(13)),
            scope: _scope
        };
        this.debugLog('login ? ' + JSON.stringify(registrationParameters));
        callBackWhenComplete(registrationParameters);
    };

    /**
     * Cause the user to fully logout from Google such that no cookies or local data persist.
     * @param callBackWhenComplete
     */
    ssoGooglePlus.logout = function (callBackWhenComplete) {
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
    console.log('ssoGooglePlus loaded, we are in ssoGooglePlusInit, calling init');
    ssoGooglePlus.init();
}