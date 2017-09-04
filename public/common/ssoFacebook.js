/**
 * Single Sign On for Facebook.
 * When this module loads, we immediately check if we have a logged in user.
 * If yes,
 */

(function ssoFacebook (global) {
    'use strict';
    var ssoFacebook = {},
        _debug = true,
        _networkId = 2,
        _siteUserId = '',
        _applicationId = '489296364486097',
        _facebookSDKVersion = 'v2.8',
        _scope = 'email',
        _initialized = false,
        _loaded = false,
        _loading = false,
        _facebookTokenExpiration = null,
        _facebookToken = null,
        _callbackWhenLoaded = null,
        _userInfo = {
            userName: '',
            realName: '',
            userId: '',      // Enginesis user id
            networkId: 0,
            siteUserId: '',  // Facebook user id
            siteUserToken: '',
            dob: null,
            gender: 'U',
            avatarURL: '',
            email: ''
        };

    ssoFacebook.debugLog = function (message) {
        if (_debug) {
            console.log('ssoFacebook: ' + message);
        }
    };

    /**
     * Initialize the library and prepare it for use.
     * @param parameters
     * @returns {boolean}
     */
    ssoFacebook.setParameters = function (parameters) {
        var errors = null;
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
     * Initialize the library and prepare it for use. This is called from the Facebook load callback. When loaded
     * we immediately check to see if we already have a logged in user.
     * @returns {boolean}
     */
    ssoFacebook.init = function () {
        _loading = false;
        _loaded = true;
        if (window.FB) {
            this.debugLog('Facebook SDK is loaded');
            var FB = window.FB;
            FB.init({
                appId: _applicationId,
                cookie: true,
                xfbml: true,
                version: _facebookSDKVersion
            });
            _initialized = true;
            FB.AppEvents.logPageView();
            if (_callbackWhenLoaded != null) {
                this.getLoginStatus().then(_callbackWhenLoaded, _callbackWhenLoaded);
                _callbackWhenLoaded = null;
            }
        }
        return _initialized;
    };

    /**
     * Load the Facebook library. This function must be called on any page that requires knowing if a user
     * is currently logged in with Facebook or any other Facebook services. Once loaded the Facebook SDK
     * calls
     * Example:
     *   ssoFacebook.load(parameters).then(function(result) { console.log('Facebook loaded'); }, function(error) { console.log('Facebook load failed ' + error.message); });
     * @param parameters {object} parameters to configure our Facebook application.
     * @returns {Promise}
     */
    ssoFacebook.load = function (parameters) {
        if ( ! _loaded) {
            this.debugLog('loading Facebook SDK');
            _loaded = false;
            _loading = true;
            window.fbAsyncInit = this.init.bind(this);
            this.setParameters(parameters);
            (function (d, s, id) {
                var js, fjs = d.getElementsByTagName(s)[0];
                if (d.getElementById(id)) {
                    return;
                }
                js = d.createElement(s);
                js.id = id;
                js.src = "https://connect.facebook.net/en_US/sdk.js";
                fjs.parentNode.insertBefore(js, fjs);
                // once loaded Facebook SDK automatically calls window.fbAsyncInit()
            }(document, 'script', 'facebook-jssdk'));
        } else if ( ! _initialized) {
            this.init();
        }
    };

    /**
     * This is a "shortcut" to loading the Facebook SDK and getting the users current status. Because all these things
     * take time, we tried to pull everything together in a single function that could take quite a long time to
     * resolve.
     *
     * This function returns a promise that will resolve to a function that is called with the user's Facebook info
     * in the standard Enginesis object format. If the promise fails the function is called with an Error object.
     *
     * @param parameters {object} same parameters you pass to load().
     * @returns {Promise}
     */
    ssoFacebook.loadThenLogin = function (parameters) {
        var ssoFacebookInstance = this;
        return new Promise(function(resolve) {
            if (ssoFacebookInstance.isReady()) {
                ssoFacebookInstance.debugLog('Facebook SDK is ready');
                ssoFacebookInstance.getLoginStatus().then(resolve, resolve);
            } else {
                ssoFacebookInstance.debugLog('Facebook SDK is not loaded');
                _callbackWhenLoaded = resolve;
                ssoFacebookInstance.load(parameters);
            }
        });
    };

    /**
     * Determine if the Facebook API is ready for action.
     * @returns {boolean}
     */
    ssoFacebook.isReady = function () {
        return _loaded && _initialized;
    };

    /**
     * Return the Enginesis network id for Facebook.
     * @returns {number}
     */
    ssoFacebook.networkId = function () {
        return _networkId;
    };

    /**
     * Return the Enginesis site-user-id which is the unique user id for this network.
     * @returns {string}
     */
    ssoFacebook.siteUserId = function () {
        return _siteUserId;
    };

    /**
     * Return the complete user info object, of null if no user is logged in.
     * @returns {{userName: string, realName: string, userId: string, networkId: number, siteUserId: string, dob: null, gender: string, avatarURL: string}}
     */
    ssoFacebook.userInfo = function () {
        return _userInfo;
    };

    /**
     * Return the networks user token.
     * @returns {*}
     */
    ssoFacebook.token = function () {
        return _facebookToken;
    };

    /**
     * Return the networks user token expiration date as a JavaScript date object. This could be null if the token
     * is invaid or if no user is logged in.
     * @returns {*}
     */
    ssoFacebook.tokenExpirationDate = function () {
        return _facebookTokenExpiration;
    };

    /**
     * Return true if the network user token has expired or is invalid. If the token is valid and not expired this function returns false.
     * @returns {boolean}
     */
    ssoFacebook.isTokenExpired = function () {
        return _facebookTokenExpiration == null;
    };

    /**
     * Determine if we have a logged in user according to Facebook's SDK. This function returns a Promise, that
     * will resolve once the status can be determined, since usually this requires a network call and some delay
     * to figure it out.
     * @returns {Promise} Resolve function is called with the _userInfo object. Reject is called if no user is logged in.
     */
    ssoFacebook.getLoginStatus = function () {
        var ssoFacebookInstance = this;
        return new Promise(function(resolve, reject) {
            if (FB !== undefined && _loaded) {
                FB.getLoginStatus(function(facebookResponse) {
                    if (facebookResponse.status === 'connected') {
                        // Logged in to Facebook and authorized Varyn.
                        _facebookToken = facebookResponse.authResponse.accessToken;
                        _facebookTokenExpiration = facebookResponse.authResponse.expiredIn;
                        _siteUserId = facebookResponse.authResponse.userID;
                        FB.api('/me', 'get', {fields: 'id,name,email,gender'}, function (response) {
                            _userInfo = {
                                networkId: _networkId,
                                userName: response.name,
                                realName: response.name,
                                email: response.email || '',
                                siteUserId: response.id,
                                siteUserToken: response.id,
                                gender: enginesis.validGender(response.gender),
                                dob: commonUtilities.MySQLDate(commonUtilities.subtractYearsFromNow(14)),
                                avatarURL: 'https://graph.facebook.com/' + response.id + '/picture?type=square',
                                scope: _scope
                            };
                            // if we get here, the user has approved our app AND they are logged in.
                            // We need to check this state IF a user is not currently logged in, this would indicate they should be logged in
                            // automatically with Facebook
                            ssoFacebookInstance.debugLog('Successful Facebook login for: ' + response.name + ' (' + response.id + ')');
                            resolve(_userInfo);
                        });
                    } else {
                        ssoFacebookInstance.debugLog('no one logged in with Facebook status: ' + facebookResponse.status);
                        reject(Error('User is not logged in with Facebook.'));
                    }
                });
            } else {
                ssoFacebookInstance.debugLog('Facebook SDK does not appear to be loaded');
                reject(Error('Facebook SDK does not appear to be loaded.'));
            }
        });
    };

    /**
     * This callback handles Facebook's reply from a status request to see if a user is properly logged in.
     * @param facebookResponse {object} defined over at Facebook SDK
     */
    ssoFacebook.statusChangeCallback = function (facebookResponse) {
        var sso = this;
        if (facebookResponse.status === 'connected') {
            // Logged in to Facebook and authorized Varyn.
            _facebookToken = facebookResponse.authResponse.accessToken;
            _facebookTokenExpiration = facebookResponse.authResponse.expiredIn;
            _siteUserId = facebookResponse.authResponse.userID;
            FB.api('/me', 'get', {fields: 'id,name,email,gender'}, function (response) {
                // if we get here, the user has approved our app AND they are logged in.
                // We need to check this state IF a user is not currently logged in, this would indicate they should be logged in
                // automatically with Facebook
                sso.debugLog('Successful Facebook login for: ' + response.name + ' (' + response.id + ')');
                _userInfo = {
                    networkId: _networkId,
                    userName: response.name,
                    realName: response.name,
                    email: response.email || '',
                    siteUserId: response.id,
                    siteUserToken: response.id,
                    gender: enginesis.validGender(response.gender),
                    dob: commonUtilities.MySQLDate(commonUtilities.subtractYearsFromNow(14)),
                    avatarURL: 'https://graph.facebook.com/' + response.id + '/picture?type=square',
                    scope: _scope
                };
                // this.loginSSO(); ???
            });
        }
    };

    ssoFacebook.login = function (callBackWhenComplete) {
        // start the user login process.
        var sso = this;
        FB.login(function(response) {
            var status = response.status; // TODO: if we get "unknown" then redirect to Facebook login ?
            if (response.authResponse) {
                FB.api('/me', 'get', {fields: 'id,name,email,gender'}, function(response) {
                    var registrationParameters = {
                        networkId: _networkId,
                        userName: response.name,
                        realName: response.name,
                        email: response.email,
                        siteUserId: response.id,
                        siteUserToken: response.id,
                        gender: enginesis.validGender(response.gender),
                        dob: commonUtilities.MySQLDate(commonUtilities.subtractYearsFromNow(14)),
                        avatarURL: 'https://graph.facebook.com/' + response.id + '/picture?type=square',
                        scope: _scope
                    };
                    sso.debugLog('User login complete for ' + response.name);
                    callBackWhenComplete(registrationParameters);
                });
            } else {
                // TODO: I'm not sure what we do here, should we message the UI? "Login was not successful, do you want to try again?"
                sso.debugLog('User cancelled login or did not fully authorize.');
                callBackWhenComplete(null);
            }
        }, {scope: _scope, return_scopes: true});
    };

    /**
     * Cause the user to fully logout from Facebook such that no cookies or local data persist.
     * @param callBackWhenComplete
     */
    ssoFacebook.logout = function (callBackWhenComplete) {
    };

    /**
     * Disconnect the user from Facebook which should invoke a full user delete.
     * @param callBackWhenComplete
     */
    ssoFacebook.disconnect = function (callBackWhenComplete) {
    };

        /* ----------------------------------------------------------------------------------
         * Setup for AMD, node, or standalone reference the commonUtilities object.
         * ----------------------------------------------------------------------------------*/

    if (typeof define === 'function' && define.amd) {
        define(function () { return ssoFacebook; });
    } else if (typeof exports === 'object') {
        module.exports = ssoFacebook;
    } else {
        var existingFunctions = global.ssoFacebook;
        ssoFacebook.existingFunctions = function () {
            global.ssoFacebook = existingFunctions;
            return this;
        };
        global.ssoFacebook = ssoFacebook;
    }
})(this);
