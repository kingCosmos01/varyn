/** @file: enginesis.js - JavaScript interface for Enginesis SDK
 * @author: jf
 * @date: 7/25/13
 * @summary: A JavaScript interface to the Enginesis API. This is designed to be a singleton
 *  object, only one should ever exist. It represents the data model and service/event model
 *  to converse with the server, and provides an overridable callback function to get the server response.
 *
 * git $Header$
 *
 **/

"use strict";

/**
 * Construct the singleton Enginesis object with initial parameters.
 * @param parameters object {
 *      siteId: number, required,
 *      developerKey: string, required,
 *      authToken: string, optional,
 *      gameId: number | 0, optional,
 *      gameGroupId: number | 0, optional,
 *      languageCode: string, optional,
 *      serverStage: string, optional, default to live server,
 *      callBackFunction: function, optional but highly recommended.
 *      }
 * @returns {object}
 */
(function enginesis (global) {
    'use strict';

    var enginesis = {
        VERSION: '2.3.39',
        debugging: true,
        disabled: false, // use this flag to turn off communicating with the server
        errorLevel: 15,  // bitmask: 1=info, 2=warning, 4=error, 8=severe
        useHTTPS: false,
        serverStage: null,
        serverHost: null,
        submitToURL: null,
        avatarImageURL: null,
        siteId: 0,
        gameId: 0,
        gameWidth: 0,
        gameHeight: 0,
        gamePluginId: 0,
        gameGroupId: 0,
        languageCode: 'en',
        syncId: 0,
        lastCommand: null,
        lastError: '',
        lastErrorMessage: '',
        callBackFunction: null,
        authToken: null,
        authTokenWasValidated: false,
        developerKey: null,
        loggedInUserId: 0,
        loggedInUserName: '',
        loggedInUserFullName: '',
        loggedInUserGender: 'U',
        loggedInUserDOB: null,
        userAccessLevel: 0,
        siteUserId: '',
        networkId: 1,
        platform: '',
        locale: 'US-en',
        isNativeBuild: false,
        isTouchDeviceFlag: false,
        SESSION_COOKIE: 'engsession',
        SESSION_USERINFO: 'engsession_user',
        refreshTokenStorageKey: 'engrefreshtoken',
        captchaId: '99999',
        captchaResponse: 'DEADMAN',
        anonymousUserKey: 'enginesisAnonymousUser',
        anonymousUser: null,

        supportedNetworks: {
            Enginesis: 1,
            Facebook:  2,
            Google:    7,
            Twitter:  11
        }
    };

    enginesis.init = function(parameters) {
        if (parameters) {
            enginesis.siteId = parameters.siteId != undefined ? parameters.siteId : 0;
            enginesis.gameId = parameters.gameId != undefined ? parameters.gameId : 0;
            enginesis.gameGroupId = parameters.gameGroupId != undefined ? parameters.gameGroupId : 0;
            enginesis.languageCode = parameters.languageCode != undefined ? parameters.languageCode : 'en';
            enginesis.serverStage = parameters.serverStage != undefined ? parameters.serverStage : '';
            enginesis.developerKey = parameters.developerKey != undefined ? parameters.developerKey : '';
            enginesis.authToken = parameters.authToken != undefined ? parameters.authToken : null;
            enginesis.callBackFunction = parameters.callBackFunction != undefined ? parameters.callBackFunction : null;
        }
        setPlatform();
        setProtocolFromCurrentLocation();
        qualifyAndSetServerStage(enginesis.serverStage);
        restoreUserFromAuthToken();
        if ( ! enginesis.isUserLoggedIn()) {
            anonymousUserLoad();
        }
    };

    /**
     * Determine if a given variable is considered an empty value.
     * @param field
     * @returns {boolean}
     */
    function isEmpty (field) {
        return (typeof field === 'undefined') || field === null || (typeof field === 'string' && field === "") || (field instanceof Array && field.length == 0) || field === false || (typeof field === 'number' && (isNaN(field) || field === 0));
    }

    /**
     * Verify we only deal with valid genders. Valie genders are M, F, and U.
     * @param gender {string} any string.
     * @returns {string|*} a single character, one of [M|F|U]
     * TODO: Consider language code.
     */
    function validGender(gender) {
        gender = gender.toUpperCase();
        if (gender[0] == 'M') {
            gender = 'M';
        } else if (gender[0] == 'F') {
            gender = 'F';
        } else {
            gender = 'U';
        }
        return gender;
    }

    /**
     * Internal function to handle completed service request and convert the JSON response to
     * an object and then invoke the call back function.
     * @param enginesisResponseData
     * @param overRideCallBackFunction
     */
    function requestComplete (enginesisResponseData, overRideCallBackFunction) {
        var enginesisResponseObject;

        debugLog("Enginesis CORS request complete " + enginesisResponseData);
        try {
            enginesisResponseObject = JSON.parse(enginesisResponseData);
        } catch (exception) {
            enginesisResponseObject = {results:{status:{success:0,message:"Error: " + exception.message,extended_info:enginesisResponseData.toString()},passthru:{fn:"unknown",state_seq:"0"}}};
        }
        enginesisResponseObject.fn = enginesisResponseObject.results.passthru.fn;
        if (overRideCallBackFunction != null) {
            overRideCallBackFunction(enginesisResponseObject);
        } else if (enginesis.callBackFunction != null) {
            enginesis.callBackFunction(enginesisResponseObject);
        }
    }

    /**
     * Internal function to send a service request to the server.
     * @param fn
     * @param parameters
     * @param overRideCallBackFunction
     * @returns {boolean}
     */
    function sendRequest (fn, parameters, overRideCallBackFunction) {
        var enginesisParameters = serverParamObjectMake(fn, parameters),
            crossOriginRequest = new XMLHttpRequest(),
            requestSent = false;

        if ( ! enginesis.disabled) {
            crossOriginRequest.onload = function(e) {
                requestComplete(this.responseText, overRideCallBackFunction);
            };
            crossOriginRequest.onerror = function(e) {
                debugLog("CORS request error " + crossOriginRequest.status + " " + e.toString());
                // TODO: Enginesis.requestError(errorMessage); generate a canned error response (see PHP code)
            };

            // TODO: Need "GET", "PUT", and "DELETE" methods
            crossOriginRequest.open("POST", enginesis.submitToURL, true);
            crossOriginRequest.overrideMimeType("application/json");
            crossOriginRequest.send(convertParamsToFormData(enginesisParameters));
            enginesis.lastCommand = fn;
            requestSent = true;
        }
        return requestSent;
    }

    /**
     * Internal function to make a parameter object complementing a service request. Depending on the
     * current state of the system specific internal variables are appended to the service request.
     * @param whichCommand
     * @param additionalParameters
     * @returns {{fn: *, language_code: *, site_id: *, user_id: *, game_id: *, state_seq: number, response: string}}
     */
    function serverParamObjectMake (whichCommand, additionalParameters) {
        var serverParams = { // these are defaults that could be overridden with additionalParameters
            fn: whichCommand,
            language_code: enginesis.languageCode,
            site_id: enginesis.siteId,
            user_id: enginesis.loggedInUserId,
            game_id: enginesis.gameId,
            state_seq: ++ enginesis.syncId,
            response: "json"
        };
        if (enginesis.loggedInUserId != 0) {
            serverParams.logged_in_user_id = enginesis.loggedInUserId;
            serverParams.authtok = enginesis.authToken;
        }
        if (additionalParameters != null) {
            for (var key in additionalParameters) {
                if (additionalParameters.hasOwnProperty(key)) {
                    serverParams[key] = additionalParameters[key];
                }
            }
        }
        return serverParams;
    }

    /**
     * Generate an internal error that looks the same as an error response from the server.
     * @param fn
     * @param stateSeq
     * @param errorCode
     * @param ErrorMessage
     * @return {string} a JSON string representing a standard Enginesis error.
     */
    function forceErrorResponse (fn, stateSeq, errorCode, ErrorMessage) {
        return '{"results":{"status":{"success":"0","message":"' + errorCode + '","extended_info":"' + ErrorMessage + '"},"passthru":{"fn":"' + fn + '","state_seq":"' + stateSeq + '"}}}';
    }

    /**
     * Convert a parameter object to a proper HTTP Form request.
     * @param parameterObject
     * @returns {*}
     */
    function convertParamsToFormData (parameterObject) {
        var key,
            formDataObject = new FormData();

        for (key in parameterObject) {
            if (parameterObject.hasOwnProperty(key)) {
                formDataObject.append(key, parameterObject[key]);
            }
        }
        return formDataObject;
    }

    /**
     * Set the internal https protocol flag based on the current page we are loaded on.
     */
    function setProtocolFromCurrentLocation () {
        enginesis.useHTTPS = window.location.protocol == 'https:';
    }

    /**
     * Return the proper protocol based on our interal HTTPS setting.
     * @returns {string}
     */
    function getProtocol() {
        return enginesis.useHTTPS ? 'https://' : 'http://';
    }

    /**
     * Set the server stage we will converse with using some simple heuristics.
     * @param newServerStage
     * @returns {*}
     */
    function qualifyAndSetServerStage (newServerStage) {
        var regMatch;

        if (newServerStage === undefined || newServerStage == null) {
            newServerStage = window.location.host;
        }
        switch (newServerStage) {
            case '':
            case '-l':
            case '-d':
            case '-q':
            case '-x':
                enginesis.serverStage = newServerStage;
                enginesis.serverHost = 'www.enginesis' + enginesis.serverStage + '.com';
                break;
            default:
                // if it was not a stage match assume it is a full host name, find the stage in it if it exists
                regMatch = /\-[ldqx]\./.exec(newServerStage);
                if (regMatch != null && regMatch.index > 0) {
                    enginesis.serverStage = newServerStage.substr(regMatch.index, 2);
                } else {
                    enginesis.serverStage = ''; // anything we do not expect goes to the live instance
                }
                enginesis.serverHost = newServerStage;
                break;
        }
        enginesis.submitToURL = getProtocol() + enginesis.serverHost + '/index.php';
        enginesis.avatarImageURL = getProtocol() + enginesis.serverHost + '/avatar.php';
        return enginesis.serverStage;
    }

    function touchDevice () {
        var isTouch = false;
        if ('ontouchstart' in window) {
            isTouch = true;
        } else if (window.DocumentTouch && document instanceof DocumentTouch) {
            isTouch = true;
        }
        return isTouch;
    }

    /**
     * Cache settings regarding the current platform we are running on.
     */
    function setPlatform () {
        enginesis.platform = navigator.platform;
        enginesis.locale = navigator.language;
        enginesis.isNativeBuild = window.location.protocol == 'file:';
        enginesis.isTouchDeviceFlag = touchDevice();
    }

    /**
     * Return the current document query string as an object with
     * key/value pairs converted to properties.
     *
     * @method queryStringToObject
     * @param {string} urlParamterString An optional query string to parse as the query string. If not
     *   provided then use window.location.search.
     * @return {object} result The query string converted to an object of key/value pairs.
     */
    function queryStringToObject (urlParameterString) {
        var match,
            search = /([^&=]+)=?([^&]*)/g,
            decode = function (s) {
                return decodeURIComponent(s.replace(/\+/g, " "));
            },
            result = {};
        if ( ! urlParameterString) {
            urlParameterString = window.location.search.substring(1);
        }
        while (match = search.exec(urlParameterString)) {
            result[decode(match[1])] = decode(match[2]);
        }
        return result;
    }

    /**
     * Return the contents of the cookie indexed by the specified key.
     *
     * @method cookieGet
     * @param {string} key Indicate which cookie to get.
     * @returns {string} value Contents of cookie stored with key.
     */
    function cookieGet (key) {
        if (key) {
            return decodeURIComponent(document.cookie.replace(new RegExp("(?:(?:^|.*;)\\s*" + encodeURIComponent(key).replace(/[\-\.\+\*]/g, "\\$&") + "\\s*\\=\\s*([^;]*).*$)|^.*$"), "$1")) || null;
        } else {
            return '';
        }
    }

    /**
     * Get info about the current logged in user, if there is one, from authtok parameter or cookie
     */
    function restoreUserFromAuthToken () {
        var queryParameters,
            authToken = enginesis.authToken,
            userInfo;

        if (authToken == null || authToken == '') {
            queryParameters = queryStringToObject();
            if (queryParameters.authtok !== undefined) {
                authToken = queryParameters.authtok;
            }
        }
        if (authToken == null || authToken == '') {
            authToken = cookieGet(enginesis.SESSION_COOKIE);
        }
        if (authToken != null && authToken != '') {
            // TODO: Validate the token (for now we are accepting that it is valid but we should check!) If the authToken is valid then we can trust the userInfo
            // TODO: we can use cr to validate the token was not changed
            enginesis.authToken = authToken;
            enginesis.authTokenWasValidated = true;
            userInfo = cookieGet(enginesis.SESSION_USERINFO);
            if (userInfo != null && userInfo != '') {
                userInfo = JSON.parse(userInfo);
                if (userInfo != null) {
                    enginesis.loggedInUserId = Math.floor(userInfo.user_id);
                    enginesis.loggedInUserName = userInfo.user_name;
                    enginesis.userAccessLevel = Math.floor(userInfo.access_level);
                    enginesis.siteUserId = userInfo.site_user_id;
                    enginesis.networkId = Math.floor(userInfo.network_id);
                }
            }
        }
    }

    /**
     * Save a refresh token in local storage.
     * @param refreshToken
     */
    function _saveRefreshToken(refreshToken) {
        if ( ! isEmpty(refreshToken)) {
            var refreshTokenData = {
                    refreshToken: refreshToken,
                    timestamp: new Date().getTime()
                };
            saveObjectWithKey(enginesis.refreshTokenStorageKey, refreshTokenData);
        }
    }

    /**
     * Recall a refresh token in local storage.
     * @returns {string} either the token that was saved or an empty string.
     */
    function _getRefreshToken() {
        var refreshToken,
            refreshTokenData = loadObjectWithKey(enginesis.refreshTokenStorageKey);

        if (refreshTokenData != null && refreshTokenData.refreshToken !== undefined) {
            refreshToken = refreshTokenData.refreshToken;
        }
        return refreshToken;
    }

    /**
     * Remove a refresh token in local storage.
     */
    function _clearRefreshToken() {
        removeObjectWithKey(enginesis.refreshTokenStorageKey);
    }

    /**
     * Internal logging function. All logging should call this function to abstract and control the interface.
     * @param message
     * @param level
     */
    function debugLog(message, level) {
        if (enginesis.debugging) {
            if (level == null) {
                level = 15;
            }
            if ((enginesis.errorLevel & level) > 0) { // only show this message if the error level is on for the level we are watching
                console.log(message);
            }
            if (level == 9) {
                alert(message);
            }
        }
    }

    /**
     * Save an object in HTML5 local storage given a key.
     * @param key
     * @param object
     */
    function saveObjectWithKey(key, object) {
        if (key != null && object != null) {
            window.localStorage[key] = JSON.stringify(object);
        }
    }

    /**
     * Delete a local storage key.
     * @param key
     */
    function removeObjectWithKey(key) {
        if (key != null) {
            window.localStorage.removeItem(key);
        }
    }

    /**
     * Restore an object previously saved in HTML5 local storage
     * @param key
     * @returns {object}
     */
    function loadObjectWithKey(key) {
        var jsonData,
            object = null;

        if (key != null) {
            jsonData = window.localStorage[key];
            if (jsonData != null) {
                object = JSON.parse(jsonData);
            }
        }
        return object;
    }

    /**
     * Load the anonymous user data from HTML5 local storage. If we do not have a prior save then initialize
     * a first time user.
     * @return object
     */
    function anonymousUserLoad() {
        if (enginesis.anonymousUser == null) {
            enginesis.anonymousUser = loadObjectWithKey(enginesis.anonymousUserKey);
            if (enginesis.anonymousUser == null) {
                enginesis.anonymousUser = {
                    dateCreated: new Date(),
                    dateLastVisit: new Date(),
                    subscriberEmail: '',
                    userName: '',
                    favoriteGames: [],
                    gamesPlayed: []
                };
            }
        }
        return enginesis.anonymousUser;
    }

    /**
     * Save the anonymous user to HTML5 local storage.
     */
    function anonymousUserSave() {
        if (enginesis.anonymousUser != null) {
            saveObjectWithKey(enginesis.anonymousUserKey, enginesis.anonymousUser);
        }
    }

    /**
     * Return the Enginesis version.
     * @returns {string}
     */
    enginesis.versionGet = function () {
        return enginesis.VERSION;
    };

    /**
     * Determine if we have a logged in user.
     * @returns {boolean}
     */
    enginesis.isUserLoggedIn = function () {
        return enginesis.loggedInUserId != 0 && enginesis.authToken != '';
    };

    /**
     * Return the error of the most recent service call.
     * @returns {{isError: boolean, error: string, description: string}}
     */
    enginesis.getLastError = function () {
        return {isError: enginesis.lastError != '', error: enginesis.lastError, description: enginesis.lastErrorMessage};
    };

    /**
     * Return an object of user information. If no user is logged in a valid object is still returned but with invalid user info.
     * @returns {{isLoggedIn: boolean, userId: number, userName: string, fullName: string, siteUserId: string, networkId: number, accessLevel: number, gender: string, DOB: date, accessToken: string, tokenExpiration: date}}
     */
    enginesis.getLoggedInUserInfo = function () {
        return {
            isLoggedIn: enginesis.loggedInUserId != 0,
            userId: enginesis.loggedInUserId,
            userName: enginesis.loggedInUserName,
            fullName: enginesis.loggedInUserFullName,
            siteUserId: enginesis.siteUserId,
            networkId: enginesis.networkId,
            accessLevel: enginesis.userAccessLevel,
            gender: enginesis.loggedInUserGender,
            DOB: enginesis.loggedInUserDOB,
            accessToken: enginesis.authToken,
            tokenExpiration: enginesis.tokenExpirationDate
        };
    };

    /**
     * Return true if the current device is a touch device.
     * @returns {boolean}
     */
    enginesis.isTouchDevice = function () {
        return enginesis.isTouchDeviceFlag;
    };

    /**
     * Determine if the user name is a valid format that would be accepted by the server.
     * @param userName
     * @returns {boolean}
     */
    enginesis.isValidUserName = function (userName) {
        // TODO: reuse the regex we used on enginesis or varyn
        return userName.length > 2;
    };

    /**
     * Determine if the password is a valid password that will be accepted by the server.
     * @param password
     * @returns {boolean}
     */
    enginesis.isValidPassword = function (password) {
        // TODO: reuse the regex we use on enginesis or varyn
        // TODO: Passwords should be no fewer than 8 chars.
        return password.length > 4;
    };

    /**
     * Return the Enginesis refresh token if one has been previously saved.
     * @returns {string}
     */
    enginesis.getRefreshToken = function () {
        return _getRefreshToken();
    };

    /**
     * Save the Enginesis refresh token for later recall.
     * @returns {string}
     */
    enginesis.saveRefreshToken = function (refreshToken) {
        return _saveRefreshToken(refreshToken);
    };

    /**
     * Remove the Enginesis refresh token.
     */
    enginesis.clearRefreshToken = function () {
        _clearRefreshToken();
    };

    /**
     * Determine and set the server stage from the specified string. It can be a stage request or a domain.
     * @param newServerStage
     * @returns {string}
     */
    enginesis.serverStageSet = function (newServerStage) {
        return qualifyAndSetServerStage(newServerStage);
    };

    /**
     * Return the current server stage we are set to converse with.
     * @returns {string}
     */
    enginesis.serverStageGet = function () {
        return enginesis.serverStage;
    };

    /**
     * @method: useHTTPS
     * @purpose: get and/or set the use HTTPS flag, allowing the caller to force the protocol. By default we set
     *           useHTTPS from the current document location. This allows the caller to query it and override its value.
     * @param: {boolean} useHTTPSFlag should be either true to force https or false to force http, or undefined to leave it as is
     * @returns: {boolean} the current state of the useHTTPS flag.
     */
    enginesis.useHTTPS = function (useHTTPSFlag) {
        if (useHTTPSFlag !== undefined) {
            enginesis.useHTTPS = useHTTPSFlag ? true : false; // force implicit boolean conversion of flag in case we get some value other than true/false
        }
        return enginesis.useHTTPS;
    };

    /**
     * Return the base URL we are using to converse with the server.  We can use this base URL to construct a path to
     * sub-services.
     * @returns {string}
     */
    enginesis.serverBaseUrlGet = function () {
        return enginesis.serverHost;
    };

    /**
     * Return the current game-id.
     * @returns {number}
     */
    enginesis.gameIdGet = function () {
        return enginesis.gameId;
    };

    /**
     * Set or override the current game-id.
     * @param newGameId
     * @returns {*}
     */
    enginesis.gameIdSet = function (newGameId) {
        return enginesis.gameId = newGameId;
    };

    /**
     * Return the current game-group-id.
     * @returns {number}
     */
    enginesis.gameGroupIdGet = function () {
        return enginesis.gameGroupId;
    };

    /**
     * Set or override the current game-group-id.
     * @param newGameGroupId
     * @returns {number}
     */
    enginesis.gameGroupIdSet = function (newGameGroupId) {
        return enginesis.gameGroupId = newGameGroupId;
    };

    /**
     * Return the current site-id.
     * @returns {number}
     */
    enginesis.siteIdGet = function () {
        return enginesis.siteId;
    };

    /**
     * Set or override the current site-id.
     * @param newSiteId
     * @returns {number}
     */
    enginesis.siteIdSet = function (newSiteId) {
        return enginesis.siteId = newSiteId;
    };

    /**
     * Return the list of supported networks capable of SSO.
     * @returns {enginesis.supportedNetworks|{Enginesis, Facebook, Google, Twitter}}
     */
    enginesis.supportedSSONetworks = function() {
        return enginesis.supportedNetworks;
    };

    /**
     * Return the URL of the request game image.
     * @param parameters {object} Parameters object as we want to be flexible about what we will accept.
     *    Parameters are:
     *    gameName {string} game folder on server where the game assets are stored. Most of the game queries
     *    (GameGet, GameList, etc) return game_name and this is used as the game folder.
     *    width {int} optional width, use null to ignore. Server will choose common width.
     *    height {int} optional height, use null to ignore. Server will choose common height.
     *    format {string} optional image format, use null and server will choose. Otherwise {jpg|png|svg}
     * @returns {string} a URL you can use to load the image.
     * TODO: this really needs to call a server-side service to perform this resolution as we need to use PHP to determine which files are available and the closest match.
     */
    enginesis.getGameImageURL = function (parameters) {
        var gameName = null,
            width = 0,
            height = 0,
            format = null,
            defaultImageFormat = '.jpg';

        if (parameters !== undefined && parameters != null) {
            if ( ! isEmpty(parameters.gameName)) {
                gameName = parameters.gameName;
            }
            if ( ! isEmpty(parameters.format)) {
                format = parameters.format;
            }
            if (parameters.width !== undefined) {
                width = parameters.width;
            }
            if (parameters.height !== undefined) {
                width = parameters.height;
            }
        }
        if (isEmpty(format)) {
            format = defaultImageFormat;
        } else {
            if (format[0] != '.') {
                format = '.' + format;
            }
            var regexPattern = /\.(jpg|png|svg)/i;
            if ( ! regexPattern.match(format)) {
                format = defaultImageFormat;
            }
        }
        if (isEmpty(width) || width == '*') {
            width = 600;
        }
        if (isEmpty(height) || height == '*') {
            height = 450;
        }
        return getProtocol() + enginesis.serverHost + '/games/' + gameName + '/images/' + width + 'x' + height + format;
    };

    /**
     * Return the current date in a standard format such as "2017-01-15 23:11:52".
     * @returns {string}
     */
    enginesis.getDateNow = function () {
        return new Date().toISOString().slice(0, 19).replace('T', ' ');
    };

    enginesis.validGender = function(gender) {
        return validGender(gender);
    };

    /**
     * Call Enginesis SessionBegin which is used to start any conversation with the server. Must call before beginning a game.
     * @param gameKey
     * @param overRideCallBackFunction
     * @returns {boolean}
     */
    enginesis.sessionBegin = function (gameId, gameKey, overRideCallBackFunction) {
        if (gameId === undefined || gameId == 0) {
            gameId = enginesis.gameIdGet();
        }
        return sendRequest("SessionBegin", {game_id: gameId, gamekey: gameKey}, overRideCallBackFunction);
    };

    /**
     * Call Enginesis SessionRefresh to exchange the long-lived refresh token for a new authentication token. Usually you
     * call this when you attempt to call a service and it replied with TOKEN_EXPIRED.
     * @param refreshToken {string} optional, if not provided (empty/null) then we try to pull the one we have in the local store.
     * @param overRideCallBackFunction
     * @returns {boolean} true if successful but if false call getLastError to get an error code as to what went wrong.
     */
    enginesis.sessionRefresh = function (refreshToken, overRideCallBackFunction) {
        if (isEmpty(refreshToken)) {
            refreshToken = _getRefreshToken();
            if (isEmpty(refreshToken)) {
                enginesis.lastError = 'INVALID_TOKEN';
                enginesis.lastErrorMessage = 'Refresh token not provided or is invalid.';
                return false;
            }
        }
        return sendRequest("SessionRefresh", {token: refreshToken}, overRideCallBackFunction);
    };

    /**
     * Submit a vote for a URI key.
     * @param voteURI {string} the URI key of the item we are voting on.
     * @param voteGroupURI {string} the URI group used to sub-group keys, for example you are voting on the best of 5 images.
     * @param voteValue {int} the value of the vote. This depends on the voting system set by the URI key/group (for example a rating vote may range from 1 to 5.)
     * @param overRideCallBackFunction
     * @returns {boolean}
     */
    enginesis.voteForURIUnauth = function (voteURI, voteGroupURI, voteValue, securityKey, overRideCallBackFunction) {
        return sendRequest("VoteForURIUnauth", {uri: voteURI, vote_group_uri: voteGroupURI, vote_value: voteValue, security_key: securityKey}, overRideCallBackFunction);
    };

    /**
     * Return voting results by voting group key.
     * @param voteGroupURI {string} voting group that collects all the items to be voted on
     * @param overRideCallBackFunction
     * @returns {boolean}
     * @seealso: addOrUpdateVoteByURI
     */
    enginesis.voteCountPerURIGroup = function (voteGroupURI, overRideCallBackFunction) {
        return sendRequest("VoteCountPerURIGroup", {vote_group_uri: voteGroupURI}, overRideCallBackFunction);
    };

    /**
     * Return information about a specific Enginesis Developer.
     * @param developerId {int} developer id.
     * @param overRideCallBackFunction
     * @returns {boolean}
     */
    enginesis.developerGet = function (developerId, overRideCallBackFunction) {
        return sendRequest("DeveloperGet", {developer_id: developerId}, overRideCallBackFunction);
    };

    /**
     * Return the current developer key. This can only be set when the Enginesis object is constructed.
     * @returns {string}
     */
    enginesis.developerKeyGet = function () {
        return enginesis.developerKey;
    };

    /**
     * @method: gameDataGet
     * @purpose: Get user generated game data. Not to be confused with gameConfigGet (which is system generated.)
     * @param: {int} gameDataId The specific id assigned to the game data to get. Was generated by gameDataCreate.
     * @returns: {boolean} status of send to server.
     */
    enginesis.gameDataGet = function (gameDataId, overRideCallBackFunction) {
        return sendRequest("GameDataGet", {game_data_id: gameDataId}, overRideCallBackFunction);
    };

    /**
     * Create a user generated content object on the server and send it to the requested individual.
     * @param referrer
     * @param fromAddress
     * @param fromName
     * @param toAddress
     * @param toName
     * @param userMessage
     * @param userFiles
     * @param gameData
     * @param nameTag
     * @param addToGallery
     * @param lastScore
     * @param overRideCallBackFunction
     * @returns {boolean}
     */
    enginesis.gameDataCreate = function (referrer, fromAddress, fromName, toAddress, toName, userMessage, userFiles, gameData, nameTag, addToGallery, lastScore, overRideCallBackFunction) {
        return sendRequest("GameDataCreate", {
            referrer: referrer,
            from_address: fromAddress,
            from_name: fromName,
            to_address: toAddress,
            to_name: toName,
            user_msg: userMessage,
            user_files: userFiles,
            game_data: gameData,
            name_tag: nameTag,
            add_to_gallery: addToGallery ? 1 : 0,
            last_score: lastScore
        }, overRideCallBackFunction);
    };

    /**
     * @method: gameConfigGet
     * @purpose: Get game data configuration. Not to be confused with GameData (which is user generated.)
     * @param: {int} gameConfigId A specific game data configuration to get. If provided the other parameters are ignored.
     * @param: {int} gameId The gameId, if 0 then the gameId set previously will be assumed. gameId is mandatory.
     * @param: {int} categoryId A category id if the game organizes its data configurations by categories. Otherwise use 0.
     * @param: {date} airDate A specific date to return game configuration data. Use "" to let the server decide (usually means "today" or most recent.)
     * @returns: {boolean} status of send to server.
     */
    enginesis.gameConfigGet = function (gameConfigId, gameId, categoryId, airDate, overRideCallBackFunction) {
        if (gameConfigId === undefined) {
            gameConfigId = 0;
        }
        if (gameId === undefined || gameId == 0) {
            gameId = enginesis.gameIdGet();
        }
        if (airDate === undefined) {
            airDate = "";
        }
        if (categoryId === undefined) {
            categoryId = 0;
        }
        return sendRequest("GameConfigGet", {game_config_id: gameConfigId, game_id: gameId, category_id: categoryId, air_date: airDate}, overRideCallBackFunction);
    };

    /**
     * Track a game event for game-play metrics.
     * @param category {string} what generated the event
     * @param action {string} what happened (LOAD, PLAY, GAMEOVER, EVENT, ZONECHG)
     * @param label {string} path in game where event occurred
     * @param hitData {string} a value related to the action, quantifying the action, if any
     * @param overRideCallBackFunction
     * @returns {boolean}
     */
    enginesis.gameTrackingRecord = function (category, action, label, hitData, overRideCallBackFunction) {
        if (window.ga != null) {
            // use Google Analytics if it is there (send, event, category, action, label, value)
            ga('send', 'event', category, action, label, hitData);
        }
        return sendRequest("GameTrackingRecord", {hit_type: 'REQUEST', hit_category: category, hit_action: action, hit_label: label, hit_data: hitData}, overRideCallBackFunction);
    };

    /**
     * Search for games given a keyword search.
     * @param game_name_part
     * @param overRideCallBackFunction
     * @returns {boolean}
     */
    enginesis.gameFind = function(game_name_part, overRideCallBackFunction) {
        return sendRequest("GameFind", {game_name_part: game_name_part}, overRideCallBackFunction);
    };

    /**
     * Search for games by only search game names.
     * @param gameName
     * @param overRideCallBackFunction
     * @returns {boolean}
     */
    enginesis.gameFindByName = function (gameName, overRideCallBackFunction) {
        return sendRequest("GameFindByName", {game_name: gameName}, overRideCallBackFunction);
    };

    /**
     * Return game info given a specific game-id.
     * @param gameId
     * @param overRideCallBackFunction
     * @returns {boolean}
     */
    enginesis.gameGet = function (gameId, overRideCallBackFunction) {
        return sendRequest("GameGet", {game_id: gameId}, overRideCallBackFunction);
    };

    /**
     * Return game info given the game name.
     * @param gameName
     * @param overRideCallBackFunction
     * @returns {boolean}
     */
    enginesis.gameGetByName = function (gameName, overRideCallBackFunction) {
        return sendRequest("GameGetByName", {game_name: gameName}, overRideCallBackFunction);
    };

    /**
     * Return a list of games for each game category.
     * @param numItemsPerCategory
     * @param gameStatusId
     * @param overRideCallBackFunction
     * @returns {boolean}
     */
    enginesis.gameListByCategory = function (numItemsPerCategory, gameStatusId, overRideCallBackFunction) {
        return sendRequest("GameListByCategory", {num_items_per_category: numItemsPerCategory, game_status_id: gameStatusId}, overRideCallBackFunction);
    };

    /**
     * Return a list of available game lists for the current site-id.
     * @param overRideCallBackFunction
     * @returns {boolean}
     */
    enginesis.gameListList = function (overRideCallBackFunction) {
        return sendRequest("GameListList", {}, overRideCallBackFunction);
    };

    /**
     * Return the list of games belonging to the requested game list id.
     * @param gameListId
     * @param overRideCallBackFunction
     * @returns {boolean}
     */
    enginesis.gameListListGames = function (gameListId, overRideCallBackFunction) {
        return sendRequest("GameListListGames", {game_list_id: gameListId}, overRideCallBackFunction);
    };

    /**
     * Return the list of games belonging to the requested game list given its name.
     * @param gameListName
     * @param overRideCallBackFunction
     * @returns {boolean}
     */
    enginesis.gameListListGamesByName = function (gameListName, overRideCallBackFunction) {
        return sendRequest("GameListListGamesByName", {game_list_name: gameListName}, overRideCallBackFunction);
    };

    enginesis.gameListByMostPopular = function (startDate, endDate, firstItem, numItems, overRideCallBackFunction) {
        return sendRequest("GameListByMostPopular", {start_date: startDate, end_date: endDate, first_item: firstItem, num_items: numItems}, overRideCallBackFunction);
    };

    /**
     * Return a list of games when given a list of individual game ids. Specify the list delimiter, default is ','.
     * @param gameIdList
     * @param delimiter
     * @param overRideCallBackFunction
     * @returns {boolean}
     */
    enginesis.gameListByIdList = function (gameIdList, delimiter, overRideCallBackFunction) {
        return sendRequest("GameListByIdList", {game_id_list: gameIdList, delimiter: delimiter}, overRideCallBackFunction);
    };

    enginesis.gameListCategoryList = function (overRideCallBackFunction) {
        return sendRequest("GameListCategoryList", {}, overRideCallBackFunction);
    };

    enginesis.gameListListRecommendedGames = function (gameListId, overRideCallBackFunction) {
        return sendRequest("GameListListRecommendedGames", {game_list_id: gameListId}, overRideCallBackFunction);
    };

    enginesis.gamePlayEventListByMostPlayed = function (startDate, endDate, numItems, overRideCallBackFunction) {
        return sendRequest("GamePlayEventListByMostPlayed", {start_date: startDate, end_date: endDate, num_items: numItems}, overRideCallBackFunction);
    };

    enginesis.gameRatingGet = function (gameId, overRideCallBackFunction) {
        return sendRequest("GameRatingGet", {game_id: gameId}, overRideCallBackFunction);
    };

    enginesis.gameRatingList = function (gameId, numberOfGames, overRideCallBackFunction) {
        return sendRequest("GameRatingList", {game_id: gameId, num_items: numberOfGames}, overRideCallBackFunction);
    };

    enginesis.gameRatingUpdate = function (gameId, rating, overRideCallBackFunction) {
        return sendRequest("GameRatingUpdate", {game_id: gameId, rating: rating}, overRideCallBackFunction);
    };

    enginesis.newsletterCategoryList = function (overRideCallBackFunction) {
        return sendRequest("NewsletterCategoryList", {}, overRideCallBackFunction);
    };

    enginesis.newsletterAddressAssign = function (emailAddress, userName, companyName, categories, overRideCallBackFunction) {
        return sendRequest("NewsletterAddressAssign", {email_address: emailAddress, user_name: userName, company_name: companyName, categories: categories, delimiter: ","}, overRideCallBackFunction);
    };

    enginesis.newsletterAddressUpdate = function (newsletterAddressId, emailAddress, userName, companyName, active, overRideCallBackFunction) {
        return sendRequest("NewsletterAddressUpdate", {newsletter_address_id: newsletterAddressId, email_address: emailAddress, user_name: userName, company_name: companyName, active: active}, overRideCallBackFunction);
    };

    enginesis.newsletterAddressDelete = function (emailAddress, overRideCallBackFunction) {
        return sendRequest("NewsletterAddressDelete", {email_address: emailAddress, newsletter_address_id: "NULL"}, overRideCallBackFunction);
    };

    enginesis.newsletterAddressGet = function (emailAddress, overRideCallBackFunction) {
        return sendRequest("NewsletterAddressGet", {email_address: emailAddress}, overRideCallBackFunction);
    };

    enginesis.promotionItemList = function (promotionId, queryDate, overRideCallBackFunction) {
        return sendRequest("PromotionItemList", {promotion_id: promotionId, query_date: queryDate}, overRideCallBackFunction);
    };

    enginesis.promotionList = function (promotionId, queryDate, showItems, overRideCallBackFunction) {
        return sendRequest("PromotionItemList", {promotion_id: promotionId, query_date: queryDate, show_items: showItems}, overRideCallBackFunction);
    };

    enginesis.recommendedGameList = function (gameId, overRideCallBackFunction) {
        return sendRequest("RecommendedGameList", {game_id: gameId}, overRideCallBackFunction);
    };

    enginesis.registeredUserCreate = function (userName, password, email, realName, dateOfBirth, gender, city, state, zipcode, countryCode, mobileNumber, imId, tagline, siteUserId, networkId, agreement, securityQuestionId, securityAnswer, imgUrl, aboutMe, additionalInfo, sourceSiteId, captchaId, captchaResponse, overRideCallBackFunction) {
        return sendRequest("RegisteredUserCreate", {
            site_id: siteId,
            captcha_id: isEmpty(captchaId) ? enginesis.captchaId : captchaId,
            captcha_response: isEmpty(captchaResponse) ? enginesis.captchaResponse : captchaResponse,
            user_name: userName,
            site_user_id: siteUserId,
            network_id: networkId,
            real_name: realName,
            password: password,
            dob: dateOfBirth,
            gender: gender,
            city: city,
            state: state,
            zipcode: zipcode,
            email_address: email,
            country_code: countryCode,
            mobile_number: mobileNumber,
            im_id: imId,
            agreement: agreement,
            security_question_id: 1,
            security_answer: '',
            img_url: '',
            about_me: aboutMe,
            tagline: tagline,
            additional_info: additionalInfo,
            source_site_id: sourceSiteId
        }, overRideCallBackFunction);
    };

    enginesis.registeredUserUpdate = function (userName, password, email, realName, dateOfBirth, gender, city, state, zipcode, countryCode, mobileNumber, imId, tagline, siteUserId, networkId, agreement, securityQuestionId, securityAnswer, imgUrl, aboutMe, additionalInfo, sourceSiteId, captchaId, captchaResponse, overRideCallBackFunction) {
        return sendRequest("RegisteredUserUpdate", {
            site_id: siteId,
            captcha_id: isEmpty(captchaId) ? enginesis.captchaId : captchaId,
            captcha_response: isEmpty(captchaResponse) ? enginesis.captchaResponse : captchaResponse,
            user_name: userName,
            real_name: realName,
            dob: dateOfBirth,
            gender: gender,
            city: city,
            state: state,
            zipcode: zipcode,
            email_address: email,
            country_code: countryCode,
            mobile_number: mobileNumber,
            im_id: imId,
            img_url: '',
            about_me: aboutMe,
            tagline: tagline,
            additional_info: additionalInfo
        }, overRideCallBackFunction);
    };

    enginesis.registeredUserSecurityUpdate = function (captcha_id, captcha_response, security_question_id, security_question, security_answer, overRideCallBackFunction) {
        return sendRequest("RegisteredUserSecurityUpdate", {
            site_id: siteId,
            captcha_id: isEmpty(captchaId) ? enginesis.captchaId : captchaId,
            captcha_response: isEmpty(captchaResponse) ? enginesis.captchaResponse : captchaResponse,
            security_question_id: security_question_id,
            security_question: security_question,
            security_answer: security_answer
        }, overRideCallBackFunction);
    };

    /**
     * Confirm a new user registration given the user-id and the token. These are supplied in the email sent when
     * a new registration is created with RegisteredUserCreate. If successful the user is logged in and a login
     * token (authtok) is sent back from the server.
     * @param user_id
     * @param secondary_password
     * @param overRideCallBackFunction
     */
    enginesis.registeredUserConfirm = function (user_id, secondary_password, overRideCallBackFunction) {
        return sendRequest("RegisteredUserConfirm", {user_id: user_id, secondary_password: secondary_password}, overRideCallBackFunction);
    };

    /**
     * this function generates the email that is sent to the email address matching username or email address.
     * that email leads to the change password web page. Currently only user name or email address is required to invoke
     * the flow, but we should consider more matching info before we start it in case accounts are being hacked.
     * @param userName
     * @param email
     * @param overRideCallBackFunction
     * @returns {boolean}
     */
    enginesis.registeredUserForgotPassword = function (userName, email, overRideCallBackFunction) {
        return sendRequest("RegisteredUserForgotPassword", {user_name: userName, email: email}, overRideCallBackFunction);
    };

    /**
     * this function generates the email that is sent to the email address matching user_id if the secondary password matches.
     * This is used when the secondary password is attempted but expired (such as user lost the reset email).
     * @param user_id - the user in question.
     * @param secondary_password - the original secondary password generated in forgot password flow.
     * @param overRideCallBackFunction
     * @returns {boolean}
     */
    enginesis.registeredUserResetSecondaryPassword = function (user_id, secondary_password, overRideCallBackFunction) {
        return sendRequest("RegisteredUserResetSecondaryPassword", {user_id: userid, secondary_password: secondary_password}, overRideCallBackFunction);
    };

    enginesis.registeredUserRequestPasswordChange = function (overRideCallBackFunction) {
        return sendRequest("RegisteredUserRequestPasswordChange", {
            site_id: enginesis.siteId
        }, overRideCallBackFunction);
    };

        // TODO: SHould include the user-id?
    enginesis.registeredUserPasswordChange = function (captcha_id, captcha_response, password, secondary_password, overRideCallBackFunction) {
        return sendRequest("RegisteredUserPasswordChange", {
            site_id: siteId,
            captcha_id: isEmpty(captchaId) ? enginesis.captchaId : captchaId,
            captcha_response: isEmpty(captchaResponse) ? enginesis.captchaResponse : captchaResponse,
            password: password,
            secondary_password: secondary_password
        }, overRideCallBackFunction);
    };

    enginesis.registeredUserSecurityGet = function (overRideCallBackFunction) {
        return sendRequest("RegisteredUserSecurityGet", {
            site_id: enginesis.siteId,
            site_user_id: ''
        }, overRideCallBackFunction);
    };

    enginesis.registeredUserGet = function (userId, siteUserId, networkId, overRideCallBackFunction) {
        // Return public information about user given id
        return sendRequest("RegisteredUserGet", {get_user_id: userId, site_user_id: siteUserId, network_id: networkId}, overRideCallBackFunction);
    };

    enginesis.siteListGames = function(firstItem, numItems, gameStatusId, overRideCallBackFunction) {
        // return a list of all assets assigned to the site in title order
        if (firstItem == null || firstItem < 0) {
            firstItem = 1;
        }
        if (numItems == null || numItems > 500) {
            numItems = 500;
        }
        if (gameStatusId == null || gameStatusId > 3) {
            gameStatusId = 2;
        }
        return sendRequest("SiteListGames", {first_item: firstItem, num_items: numItems, game_status_id: gameStatusId}, overRideCallBackFunction);
    };

    enginesis.siteListGamesRandom = function(numItems, overRideCallBackFunction) {
        if (numItems == null || numItems > 500) {
            numItems = 500;
        }
        return sendRequest("SiteListGamesRandom", {num_items: numItems}, overRideCallBackFunction);
    };

    enginesis.userGetByName = function (userName, overRideCallBackFunction) {
        // Return public information about user give name
        return sendRequest("UserGetByName", {user_name: userName}, overRideCallBackFunction);
    };

    enginesis.userLogin = function(userName, password, overRideCallBackFunction) {
        return sendRequest("UserLogin", {user_name: userName, password: password}, overRideCallBackFunction);
    };

    /**
     * Enginesis co-registration accepts validated login from another network and creates a new user or logs in
     * a matching user. site-user-id, user-name, and network-id are mandatory. Everything else is optional.
     * @param registrationParameters {object} registration data values
     * @param networkId {int} we must know which network this registration comes from.
     * @param overRideCallBackFunction {function} called when server replies.
     */
    enginesis.userLoginCoreg = function (registrationParameters, networkId, overRideCallBackFunction) {
        if (registrationParameters.siteUserId === undefined || registrationParameters.siteUserId.length == 0) {
            return false;
        }
        if ((registrationParameters.userName === undefined || registrationParameters.userName.length == 0) && (registrationParameters.realName === undefined || registrationParameters.realName.length == 0)) {
            return false; // Must provide either userName, realName, or both
        }
        if (registrationParameters.userName === undefined) {
            registrationParameters.userName = '';
        }
        if (registrationParameters.realName === undefined) {
            registrationParameters.realName = '';
        }
        if (registrationParameters.gender === undefined || registrationParameters.gender.length == 0) {
            registrationParameters.gender = 'F';
        } else if (registrationParameters.gender != 'M' && registrationParameters.gender != 'F' && registrationParameters.gender != 'U') {
            registrationParameters.gender = 'U';
        }
        if (registrationParameters.emailAddress === undefined) {
            registrationParameters.emailAddress = '';
        }
        if (registrationParameters.scope === undefined) {
            registrationParameters.scope = '';
        }
        if (registrationParameters.dob === undefined || registrationParameters.dob.length == 0) {
            registrationParameters.dob = new Date();
            registrationParameters.dob = registrationParameters.dob.toISOString().slice(0, 9);
        } else if (registrationParameters.dob instanceof Date) {
            // if is date() then convert to string
            registrationParameters.dob = registrationParameters.dob.toISOString().slice(0, 9);
        }

        return sendRequest("UserLoginCoreg", {
            site_user_id: registrationParameters.siteUserId,
            user_name: registrationParameters.userName,
            real_name: registrationParameters.realName,
            email_address: registrationParameters.emailAddress,
            gender: registrationParameters.gender,
            dob: registrationParameters.dob,
            network_id: networkId,
            scope: registrationParameters.scope
        },
        overRideCallBackFunction);
    };

    /**
     * Return the proper URL to use to show an avatar for a given user. The default is the default size and the current user.
     * @param size {int} 0 small, 1 medium, 2 large
     * @param userId {int}
     * @return string
     */
    enginesis.avatarURL = function (size, userId) {
        if (userId == 0) {
            userId = loggedInUserId;
        }
        size = 0;
        return avatarImageURL + '?site_id=' + siteId + '&user_id=' + userId + '&size=' + size;
    };

    /**
     * Get information about a specific quiz.
     * @param quiz_id
     * @param overRideCallBackFunction
     */
    enginesis.quizGet = function (quiz_id, overRideCallBackFunction) {
        return sendRequest("QuizGet", {game_id: quiz_id}, overRideCallBackFunction);
    };

    /**
     * Ask quiz service to begin playing a specific quiz given the quiz id. If the quiz-id does not exist
     * then an error is returned.
     * @param quiz_id
     * @param game_group_id
     * @param overRideCallBackFunction
     */
    enginesis.quizPlay = function (quiz_id, game_group_id, overRideCallBackFunction) {
        return sendRequest("QuizPlay", {game_id: quiz_id, game_group_id: game_group_id}, overRideCallBackFunction);
    };

    /**
     * Ask quiz service to begin playing the next quiz in a scheduled quiz series. This should always return at least
     * one quiz.
     * @param quiz_id {int} if a specific quiz id is requested we try to return this one. If for some reason we cannot, the next quiz in the scheduled series is returned.
     * @param game_group_id {int} quiz group id.
     * @param overRideCallBackFunction
     */
    enginesis.quizPlayScheduled = function (quiz_id, game_group_id, overRideCallBackFunction) {
        return sendRequest("QuizPlayScheduled", {game_id: quiz_id, game_group_id: game_group_id}, overRideCallBackFunction);
    };

    /**
     * Return a summary of quiz outcomes for the given quiz id.
     * @param quiz_id
     * @param game_group_id
     * @param overRideCallBackFunction
     */
    enginesis.quizOutcomesCountList = function(quiz_id, game_group_id, overRideCallBackFunction) {
        return sendRequest("QuizOutcomesCountList", {game_id: quiz_id, game_group_id: game_group_id}, overRideCallBackFunction);
    };

    /**
     * Submit the results of a completed quiz. Results is a JSON object we need to document.
     * @param quiz_id
     * @param results
     * @param overRideCallBackFunction
     */
    enginesis.quizSubmit = function(quiz_id, results, overRideCallBackFunction) {
        return sendRequest("QuizSubmit", {game_id: quiz_id, results: results}, overRideCallBackFunction);
    };

    /**
     * When the user plays a question we record the event and the choice the user made. This helps us with question
     * usage statistics and allows us to track question consumption so the return visits to this quiz can provide
     * fresh questions for this user.
     * @param quiz_id
     * @param question_id
     * @param choice_id
     * @param overRideCallBackFunction
     * @returns {boolean}
     */
    enginesis.quizQuestionPlayed = function(quiz_id, question_id, choice_id, overRideCallBackFunction) {
        return sendRequest("QuizQuestionPlayed", {game_id: quiz_id, question_id: question_id, choice_id: choice_id}, overRideCallBackFunction);
    };

    /**
     * Get list of users favorite games. User must be logged in.
     * @param overRideCallBackFunction
     * @returns {boolean}
     */
    enginesis.userFavoriteGamesList = function (overRideCallBackFunction) {
        return sendRequest("UserFavoriteGamesList", {}, overRideCallBackFunction);
    };

    /**
     * Assign a game-id to the list of user favorite games. User must be logged in.
     * @param game_id
     * @param overRideCallBackFunction
     * @returns {boolean}
     */
    enginesis.userFavoriteGamesAssign = function(game_id, overRideCallBackFunction) {
        return sendRequest("UserFavoriteGamesAssign", {game_id: game_id}, overRideCallBackFunction);
    };

    /**
     * Assign a list of game-ids to the list of user favorite games. User must be logged in. List is separated by commas.
     * @param game_id_list
     * @param overRideCallBackFunction
     * @returns {boolean}
     */
    enginesis.userFavoriteGamesAssignList = function(game_id_list, overRideCallBackFunction) {
        return sendRequest("UserFavoriteGamesAssignList", {game_id_list: game_id_list, delimiter: ','}, overRideCallBackFunction);
    };

    /**
     * Remove a game-id from the list of user favorite games. User must be logged in.
     * @param game_id
     * @param overRideCallBackFunction
     * @returns {boolean}
     */
    enginesis.userFavoriteGamesDelete = function(game_id, overRideCallBackFunction) {
        return sendRequest("UserFavoriteGamesDelete", {game_id: game_id}, overRideCallBackFunction);
    };

    /**
     * Remove a list of game-ids from the list of user favorite games. User must be logged in. List is separated by commas.
     * @param game_id_list
     * @param overRideCallBackFunction
     * @returns {boolean}
     */
    enginesis.userFavoriteGamesDeleteList = function(game_id_list, overRideCallBackFunction) {
        return sendRequest("UserFavoriteGamesDeleteList", {game_id_list: game_id_list, delimiter: ','}, overRideCallBackFunction);
    };

    /**
     * Change the order of a game in the list of user favorites.
     * @param game_id
     * @param sort_order
     * @param overRideCallBackFunction
     * @returns {boolean}
     */
    enginesis.userFavoriteGamesMove = function(game_id, sort_order, overRideCallBackFunction) {
        return sendRequest("UserFavoriteGamesMove", {game_id: game_id, sort_order: sort_order}, overRideCallBackFunction);
    };

    enginesis.anonymousUserSetDateLastVisit = function() {
        if (enginesis.anonymousUser == null) {
            anonymousUserLoad();
        }
        enginesis.anonymousUser.dateLastVisit = new Date();
    };

    /**
     * Set the user email address and save the user data.
     * @param emailAddress
     */
    enginesis.anonymousUserSetSubscriberEmail = function(emailAddress) {
        if (enginesis.anonymousUser == null) {
            anonymousUserLoad();
        }
        enginesis.anonymousUser.subscriberEmail = emailAddress;
        anonymousUserSave();
    };

    /**
     * Return the anonymous user email.
     * @returns {string}
     */
    enginesis.anonymousUserGetSubscriberEmail = function() {
        if (enginesis.anonymousUser == null) {
            anonymousUserLoad();
        }
        return enginesis.anonymousUser.subscriberEmail;
    };

    /**
     * Set the user name and save the user data.
     * @param userName
     */
    enginesis.anonymousUserSetUserName = function(userName) {
        if (enginesis.anonymousUser == null) {
            anonymousUserLoad();
        }
        enginesis.anonymousUser.userName = userName;
        anonymousUserSave();
    };

    /**
     * Get the anonymous user name.
     * @returns {string}
     */
    enginesis.anonymousUserGetUserName = function() {
        if (enginesis.anonymousUser == null) {
            anonymousUserLoad();
        }
        return enginesis.anonymousUser.userName;
    };

    /**
     * Add a favorite game_id to the user favorite games list only if it does not already exist in the list.
     * @param gameId
     */
    enginesis.anonymousUserAddFavoriteGame = function(gameId) {
        var gameIdList,
            existingPos;

        if (enginesis.anonymousUser == null) {
            anonymousUserLoad();
        }
        gameIdList = enginesis.anonymousUser.favoriteGames;
        if (gameIdList != null && gameIdList.length > 0) {
            existingPos = gameIdList.indexOf(gameId);
            if (existingPos < 0) {
                gameIdList.unshift(gameId);
            }
        } else if (gameIdList == null) {
            gameIdList = [gameId];
        } else {
            gameIdList.push(gameId);
        }
        enginesis.anonymousUser.favoriteGames = gameIdList;
        anonymousUserSave();
    };

    /**
     * Add a gameId to the list of game_ids played by this user. If the game_id already exists it moves to
     * the top of the list.
     * @param gameId
     */
    enginesis.anonymousUserGamePlayed = function(gameId) {
        var gameIdList,
            existingPos;

        if (enginesis.anonymousUser == null) {
            anonymousUserLoad();
        }
        gameIdList = enginesis.anonymousUser.gamesPlayed;
        if (gameIdList != null && gameIdList.length > 0) {
            existingPos = gameIdList.indexOf(gameId);
            if (existingPos > 0) {
                gameIdList.splice(0, 0, gameIdList.splice(existingPos, 1)[0]);
            } else if (existingPos < 0) {
                gameIdList.unshift(gameId);
            }
        } else if (gameIdList == null) {
            gameIdList = [gameId];
        } else {
            gameIdList.push(gameId);
        }
        enginesis.anonymousUser.gamesPlayed = gameIdList;
        anonymousUserSave();
    };


    /* ----------------------------------------------------------------------------------
     * Setup for AMD, node, or standalone reference the commonUtilities object.
     * ----------------------------------------------------------------------------------*/
    if (typeof define === 'function' && define.amd) {
        define(function () { return enginesis; });
    } else if (typeof exports === 'object') {
        module.exports = enginesis;
    } else {
        var existingEnginesis = global.enginesis;
        enginesis.existingEnginesis = function () {
            global.enginesis = existingEnginesis;
            return this;
        };
        global.enginesis = enginesis;
    }
})(window);
