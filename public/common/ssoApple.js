/**
 * Single Sign On for Apple.
 * This module handles the logic for SSO with Apple's sign in.
 */

(function ssoApple(global) {
    "use strict";
    var ssoApple = {},
        _debug = true,
        _networkId = 14,
        _siteUserId = "",
        _applicationId = "",
        _redirectURI = "/procs/appleauth.php",
        _SDKVersion = "v1",
        _scope = "name email",
        _initialized = false,
        _loaded = false,
        _loading = false,
        _appleTokenExpiration = null,
        _appleToken = null,
        _callbackWhenLoaded = null,
        _callbackWhenLoggedIn = null,
        _callbackWhenLoggedOut = null,
        _userInfo = null,
        _appleAuth = {
            clientId: "",
            scope: "",
            redirectURI: "",
            state: "signin"
        };

    ssoApple.debugLog = function (message) {
        if (_debug) {
            console.log("ssoApple: " + message);
        }
    };

    /**
     * Define the data structure for what a logged in user shoud look like. This
     * is common to all SSO modules.
     */
    ssoApple.clearUserInfo = function () {
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
     * @param {object} parameters required to configure the service on behalf of the app.
     * @returns {boolean}
     */
    ssoApple.setParameters = function (parameters) {
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
        _appleAuth.clientId = _applicationId;
        _appleAuth.scope = _scope;
        _appleAuth.redirectURI = _redirectURI;
        return errors;
    };

    /**
     * Initialize the library and prepare it for use. This is called from the Apple load callback. When loaded
     * we immediately check to see if we already have a logged in user.
     * @returns {boolean}
     */
    ssoApple.init = function () {
        ssoApple.clearUserInfo();
        if (ssoApple._loaded && window.AppleID) {
            this.debugLog("Apple SDK is loaded");
            try {
                var AppleID = window.AppleID;
                AppleID.auth.init(_appleAuth);
                _initialized = true;
                if (_callbackWhenLoaded != null) {
                    // this.getLoginStatus().then(_callbackWhenLoaded, _callbackWhenLoaded);
                    _callbackWhenLoaded = null;
                }
            } catch(appleError) {
                this.debugLog("Apple SDK load error " + appleError.toString());
            }
        } else {
            this.debugLog("Cannot init Apple SDK because it is not loaded.");
        }
        return _initialized;
    };

    /**
     * Load the Apple library. This is accomplished by dynamically creating a new
     * script tag on the page and setting it to the Apple sign in SDK script. This function
     * must be called on any page that requires knowing if a user is currently logged in
     * with Apple or any other Apple services. Once loaded the Apple SDK calls `window.appleInit`
     * to continue the sign in process.
     * Example:
     *   ssoApple.load(parameters).then(function(result) { console.log('Apple loaded'); }, function(error) { console.log('Apple load failed ' + error.message); });
     * 
     * @param {object} parameters to configure our Apple application (see `setParameters()`).
     */
    ssoApple.load = function (parameters) {
        if (!_loaded) {
            this.debugLog("loading Apple SDK");
            _loaded = false;
            _loading = true;
            this.setParameters(parameters);
            (function (d, s, id, scriptSource) {
                var js, fjs;
                if (d.getElementById(id)) {
                    return;
                }
                fjs = d.getElementsByTagName(s)[0];
                if (fjs == null) {
                    fjs = d.getElementsByTagName("div")[0];
                }
                js = d.createElement(s);
                js.onload = function() {
                    ssoApple._loaded = true;
                    ssoApple.init();
                };
                js.id = id;
                js.src = scriptSource;
                fjs.parentNode.insertBefore(js, fjs);
            }(document, "script", "apple-jssdk", "https://appleid.cdn-apple.com/appleauth/static/jsapi/appleid/1/en_US/appleid.auth.js"));
        } else if (!_initialized) {
            this.init();
        }
    };

    /**
     * This is a "shortcut" to loading the Apple SDK and getting the users current status. Because all these things
     * take time, we tried to pull everything together in a single function that could take quite a long time to
     * resolve.
     *
     * This function returns a promise that will resolve to a function that is called with the user's Apple info
     * in the standard Enginesis object format. If the promise fails the function is called with an Error object.
     *
     * @param {object} parameters to configure our Apple application (see `setParameters()`).
     * @returns {Promise}
     */
    ssoApple.loadThenLogin = function (parameters) {
        var ssoAppleInstance = this;
        return new Promise(function (resolve) {
            if (ssoAppleInstance.isReady()) {
                ssoAppleInstance.debugLog("Apple SDK is ready");
                ssoAppleInstance.getLoginStatus().then(resolve, resolve);
            } else {
                ssoAppleInstance.debugLog("Apple SDK is not loaded");
                _callbackWhenLoaded = resolve;
                ssoAppleInstance.load(parameters);
            }
        });
    };

    /**
     * Determine if the Apple API is ready for action.
     * @returns {boolean}
     */
    ssoApple.isReady = function () {
        return _loaded && _initialized;
    };

    /**
     * Return the Enginesis network id for Apple.
     * @returns {number}
     */
    ssoApple.networkId = function () {
        return _networkId;
    };

    /**
     * Return the Enginesis site-user-id which is the unique user id for this network.
     * @returns {string}
     */
    ssoApple.siteUserId = function () {
        return _siteUserId;
    };

    /**
     * Return the complete user info object, of null if no user is logged in.
     * @returns {{userName: string, realName: string, userId: string, networkId: number, siteUserId: string, dob: null, gender: string, avatarURL: string}}
     */
    ssoApple.userInfo = function () {
        return _userInfo;
    };

    /**
     * Return the networks user token.
     * @returns {*}
     */
    ssoApple.token = function () {
        return _appleToken;
    };

    /**
     * Return the networks' user token expiration date as a JavaScript date object. This could be null if the token
     * is invaid or if no user is logged in.
     * @returns {*}
     */
    ssoApple.tokenExpirationDate = function () {
        return _appleTokenExpiration;
    };

    /**
     * Return true if the network user token has expired or is invalid. If the token is valid and not expired this function returns false.
     * @returns {boolean}
     */
    ssoApple.isTokenExpired = function () {
        return _appleTokenExpiration == null;
    };

    /**
     * Cause the user to fully logout from Apple such that no cookies or local data persist.
     * @param callBackWhenComplete
     */
    ssoApple.logout = function (callBackWhenComplete) {
    };

    /**
     * Disconnect the user from Apple which should invoke a full user delete.
     * @param callBackWhenComplete
     */
    ssoApple.disconnect = function (callBackWhenComplete) {
    };

    /* ----------------------------------------------------------------------------------
     * Setup for AMD, node, or standalone reference the commonUtilities object.
     * ----------------------------------------------------------------------------------*/

    if (typeof define === "function" && define.amd) {
        define(function () { return ssoApple; });
    } else if (typeof exports === "object") {
        module.exports = ssoApple;
    } else {
        var existingFunctions = global.ssoApple;
        ssoApple.existingFunctions = function () {
            global.ssoApple = existingFunctions;
            return this;
        };
        global.ssoApple = ssoApple;
    }
})(this);
