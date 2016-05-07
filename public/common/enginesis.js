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
 * Construct the singleton Enginesis object with initial parmeters.
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
 * @returns {{ShareHelper, gameId: (*|number), gameWidth: number, gameHeight: number, gamePluginId: number, version: string, versionGet: versionGet, isTouchDevice: isTouchDevice, serverStageSet: serverStageSet, serverStageGet: serverStageGet, useHTTPS: useHTTPS, serverBaseUrlGet: serverBaseUrlGet, gameIdGet: gameIdGet, gameIdSet: gameIdSet, gameGroupIdGet: gameGroupIdGet, gameGroupIdSet: gameGroupIdSet, siteIdGet: siteIdGet, siteIdSet: siteIdSet, getGameImageURL: getGameImageURL, getDateNow: getDateNow, sessionBegin: sessionBegin, addOrUpdateVoteByURI: addOrUpdateVoteByURI, developerGet: developerGet, gameDataGet: gameDataGet, gameDataCreate: gameDataCreate, gameTrackingRecord: gameTrackingRecord, getNumberOfVotesPerURIGroup: getNumberOfVotesPerURIGroup, gameFind: gameFind, gameFindByName: gameFindByName, gameGet: gameGet, gameGetByName: gameGetByName, gameListByCategory: gameListByCategory, gameListList: gameListList, gameListListGames: gameListListGames, gameListListGamesByName: gameListListGamesByName, gameListByMostPopular: gameListByMostPopular, gameListCategoryList: gameListCategoryList, gameListListRecommendedGames: gameListListRecommendedGames, gamePlayEventListByMostPlayed: gamePlayEventListByMostPlayed, newsletterCategoryList: newsletterCategoryList, newsletterAddressAssign: newsletterAddressAssign, newsletterAddressUpdate: newsletterAddressUpdate, newsletterAddressDelete: newsletterAddressDelete, newsletterAddressGet: newsletterAddressGet, promotionItemList: promotionItemList, promotionList: promotionList, recommendedGameList: recommendedGameList, registeredUserCreate: registeredUserCreate, registeredUserUpdate: registeredUserUpdate, registeredUserSecurityUpdate: registeredUserSecurityUpdate, registeredUserForgotPassword: registeredUserForgotPassword, registeredUserGet: registeredUserGet, siteListGames: siteListGames, siteListGamesRandom: siteListGamesRandom, userGetByName: userGetByName, userLogin: userLogin, userLoginCoreg: userLoginCoreg}}
 */
var enginesis = function (parameters) {

    var VERSION = '2.3.25',
        debugging = true,
        disabled = false, // use this flag to turn off communicating with the server
        errorLevel = 15, // bitmask: 1=info, 2=warning, 4=error, 8=severe
        useHTTPS = false,
        serverStage = null,
        serverHost = null,
        submitToURL = null,
        avatarImageURL = null,
        siteId = 0,
        gameId = 0,
        gameWidth = 0,
        gameHeight = 0,
        gamePluginId = 0,
        gameGroupId = 0,
        languageCode = 'en',
        syncId = 0,
        lastCommand = null,
        lastError = '',
        lastErrorMessage = '',
        callBackFunction = null,
        authToken = null,
        developerKey = null,
        loggedInUserId = 0,
        loggedInUserName = '',
        userAccessLevel = 0,
        siteUserId = '',
        networkId = 1,
        platform = '',
        locale = 'US-en',
        isNativeBuild = false,
        isTouchDevice = false;

    if (parameters) {
        siteId = parameters.siteId != undefined ? parameters.siteId : 0;
        gameId = parameters.gameId != undefined ? parameters.gameId : 0;
        gameGroupId = parameters.gameGroupId != undefined ? parameters.gameGroupId : 0;
        languageCode = parameters.languageCode != undefined ? parameters.languageCode : 'en';
        serverStage = parameters.serverStage != undefined ? parameters.serverStage : '';
        developerKey = parameters.developerKey != undefined ? parameters.developerKey : '';
        authToken = parameters.authToken != undefined ? parameters.authToken : null;
        callBackFunction = parameters.callBackFunction != undefined ? parameters.callBackFunction : null;
    }

    var requestComplete = function (enginesisResponseData, overRideCallBackFunction) {
        var enginesisResponseObject;

        debugLog("CORS request complete " + enginesisResponseData);
        try {
            enginesisResponseObject = JSON.parse(enginesisResponseData);
        } catch (exception) {
            enginesisResponseObject = {results:{status:{success:0,message:"Error: " + exception.message,extended_info:enginesisResponseData.toString()},passthru:{fn:"unknown",state_seq:"0"}}};
        }
        enginesisResponseObject.fn = enginesisResponseObject.results.passthru.fn;
        if (overRideCallBackFunction != null) {
            overRideCallBackFunction(enginesisResponseObject);
        } else if (callBackFunction != null) {
            callBackFunction(enginesisResponseObject);
        }
    };

    var sendRequest = function (fn, parameters, overRideCallBackFunction) {
        var enginesisParameters = serverParamObjectMake(fn, parameters),
            crossOriginRequest = new XMLHttpRequest();

        if (typeof crossOriginRequest.withCredentials === undefined) {
            debugLog("CORS is not supported");
        } else if ( ! disabled) {
            crossOriginRequest.onload = function(e) {
                requestComplete(this.responseText, overRideCallBackFunction);
            };

            crossOriginRequest.onerror = function(e) {
                debugLog("CORS request error " + crossOriginRequest.status + " " + e.toString());
                // TODO: Enginesis.requestError(errorMessage); generate a canned error response (see PHP code)
            };

            // TODO: Need "GET", "PUT", and "DELETE" methods
            crossOriginRequest.open("POST", submitToURL, true);
            crossOriginRequest.overrideMimeType("application/json");
            crossOriginRequest.send(convertParamsToFormData(enginesisParameters));
            lastCommand = fn;
        }
    };

    var serverParamObjectMake = function (whichCommand, additionalParameters) {
        var serverParams = {
            fn: whichCommand,
            language_code: languageCode,
            site_id: siteId,
            user_id: loggedInUserId,
            game_id: gameId,
            state_seq: ++ syncId,
            response: "json"
        };
        if (loggedInUserId != 0) {
            serverParams.logged_in_user_id = loggedInUserId;
        }
        if (additionalParameters != null) {
            for (var key in additionalParameters) {
                if (additionalParameters.hasOwnProperty(key)) {
                    serverParams[key] = additionalParameters[key];
                }
            }
        }
        return serverParams;
    };

    var forceErrorResponse = function (fn, stateSeq, errorCode, ErrorMessage) {
        // generate an internal error that looks the same as an error response from the server.
        var errorJSONString = '{"results":{"status":{"success":"0","message":"' + errorCode + '","extended_info":"' + ErrorMessage + '"},"passthru":{"fn":"' + fn + '","state_seq":"' + stateSeq + '"}}}';
    };

    var convertParamsToFormData = function (parameterObject)
    {
        var key,
            formDataObject = new FormData();

        for (key in parameterObject) {
            if (parameterObject.hasOwnProperty(key)) {
                formDataObject.append(key, parameterObject[key]);
            }
        }
        return formDataObject;
    };

    var setProtocolFromCurrentLocation = function () {
        useHTTPS = document.location.protocol == 'https:';
    };

    var qualifyAndSetServerStage = function (newServerStage) {
        var regMatch;

        switch (newServerStage) {
            case '':
            case '-l':
            case '-d':
            case '-q':
            case '-x':
                serverStage = newServerStage;
                serverHost = 'www.enginesis' + serverStage + '.com';
                break;
            default:
                // if it was not a stage match assume it is a full host name, find the stage in it if it exists
                regMatch = /\-[ldqx]\./.exec(newServerStage);
                if (regMatch != null && regMatch.index > 0) {
                    serverStage = newServerStage.substr(regMatch.index, 2);
                } else {
                    serverStage = ''; // anything we do not expect goes to the live instance
                }
                serverHost = newServerStage;
                break;
        }
        submitToURL = (useHTTPS ? 'https://' : 'http://') + serverHost + '/index.php';
        avatarImageURL = (useHTTPS ? 'https://' : 'http://') + serverHost + '/avatar.php';
        return serverStage;
    };

    var setPlatform = function () {
        platform = navigator.platform;
        locale = navigator.language;
        isNativeBuild = document.location.protocol == 'file:';
        if (Modernizr != null && Modernizr.touch != null) {
            isTouchDevice = Modernizr.touch;
        }
    };

    /**
     * Return the current document query string as an object with
     * key/value pairs converted to properties.
     *
     * @method queryStringToObject
     * @param {string} urlParamterString An optional query string to parse as the query string. If not
     *   provided then use window.location.search.
     * @return {object} result The query string converted to an object of key/value pairs.
     */
    var queryStringToObject = function (urlParameterString) {
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
    };

    /**
     * Return the contents fo the cookie indexed by the specified key.
     *
     * @method cookieGet
     * @param {string} key Indicate which cookie to get.
     * @returns {string} value Contents of cookie stored with key.
     */
    var cookieGet = function (key) {
        if (key) {
            return decodeURIComponent(document.cookie.replace(new RegExp("(?:(?:^|.*;)\\s*" + encodeURIComponent(key).replace(/[\-\.\+\*]/g, "\\$&") + "\\s*\\=\\s*([^;]*).*$)|^.*$"), "$1")) || null;
        } else {
            return '';
        }
    };

    /**
     * Get info about the current logged in user, if there is one, from authtok parameter or cookie
     */
    var reviveLoggedInUser = function () {
        var queryParameters = queryStringToObject(),
            authtok = '';

        if (queryParameters.authtok !== undefined) {
            authtok = queryParameters.authtok;
        } else {
            authtok = cookieGet('engsession');
        }
        if (authtok != '') {

        }
    };

    var debugLog = function (message, level) {
        if (debugging) {
            if (level == null) {
                level = 15;
            }
            if ((errorLevel & level) > 0) { // only show this message if the error level is on for the level we are watching
                console.log(message);
            }
            if (level == 9) {
                alert(message);
            }
        }
    };

    setPlatform();
    setProtocolFromCurrentLocation();
    qualifyAndSetServerStage(serverStage);

    // =====================================================================
    // this is the public interface
    //
    return {

        ShareHelper: ShareHelper,
        gameId: gameId,
        gameWidth: gameWidth,
        gameHeight: gameHeight,
        gamePluginId: gamePluginId,
        version: VERSION,

        versionGet: function () {
            return VERSION;
        },

        getLastError: function () {
            return {isError: lastError != '', error: lastError, description: lastErrorMessage};
        },

        getLoggedInUserInfo: function () {
            return {isLoggedIn: loggedInUserId != 0, userId: loggedInUserId, userName: loggedInUserName, siteUserId: siteUserId, networkId: networkId, accessLevel: userAccessLevel};
        },

        isTouchDevice: function () {
            return isTouchDevice;
        },

        isValidUserName: function (userName) {
            return userName.length > 2;
        },

        isValidPassword: function (password) {
            return password.length > 3;
        },

        serverStageSet: function (newServerStage) {
            return qualifyAndSetServerStage(newServerStage);
        },

        serverStageGet: function () {
            return serverStage;
        },

        /**
         * @method: useHTTPS
         * @purpose: get and/or set the use HTTPS flag, allowing the caller to force the protocol. By default we set
         *           useHTTPS from the current document location. This allows the caller to query it and override its value.
         * @param: {boolean} useHTTPSFlag should be either true to force https or false to force http, or undefined to leave it as is
         * @returns: {boolean} the current state of the useHTTPS flag.
         */
        useHTTPS: function (useHTTPSFlag) {
            if (useHTTPSFlag !== undefined) {
                useHTTPS = useHTTPSFlag ? true : false; // force boolean conversion of flag in case we get some value other than true/false
            }
            return useHTTPS;
        },

        serverBaseUrlGet: function () {
            return serverHost;
        },

        gameIdGet: function () {
            return gameId;
        },

        gameIdSet: function (newGameId) {
            return gameId = newGameId;
        },

        gameGroupIdGet: function () {
            return gameGroupId;
        },

        gameGroupIdSet: function (newGameGroupId) {
            return gameGroupId = newGameGroupId;
        },

        siteIdGet: function () {
            return siteId;
        },

        siteIdSet: function (newSiteId) {
            return siteId = newSiteId;
        },

        getGameImageURL: function (gameName, width, height) {
            return (useHTTPS ? 'https://' : 'http://') + serverHost + '/games/' + gameName + '/images/' + width + "x" + height + ".png";
        },

        getDateNow: function () {
            return new Date().toISOString().slice(0, 19).replace('T', ' ');
        },

        sessionBegin: function (gameKey, overRideCallBackFunction) {
            return sendRequest("SessionBegin", {gamekey: gameKey}, overRideCallBackFunction);
        },

        addOrUpdateVoteByURI: function (voteURI, voteGroupURI, voteValue, overRideCallBackFunction) {
            // voteGroupURI = voting group that collects all the items to be voted on
            // voteURI = item voting on
            // voteValue = vote (e.g. 1 to 5)
            return sendRequest("AddOrUpdateVoteByURI", {uri: voteURI, vote_group_uri: voteGroupURI, vote_value: voteValue}, overRideCallBackFunction);
        },

        developerGet: function (developerId, overRideCallBackFunction) {
            return sendRequest("DeveloperGet", {developer_id: developerId}, overRideCallBackFunction);
        },

        gameDataGet: function (gameDataId, overRideCallBackFunction) {
            return sendRequest("GameDataGet", {game_data_id: gameDataId}, overRideCallBackFunction);
        },

        gameDataCreate: function (referrer, fromAddress, fromName, toAddress, toName, userMessage, userFiles, gameData, nameTag, addToGallery, lastScore, overRideCallBackFunction) {
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
        },

        gameTrackingRecord: function (category, action, label, hitData, overRideCallBackFunction) {
            // category = what generated the event
            // action = what happened (LOAD, PLAY, GAMEOVER, EVENT, ZONECHG)
            // label = path in game where event occurred
            // data = a value related to the action, quantifying the action, if any
            if (window.ga != null) {
                // use Google Analytics if it is there (send, event, category, action, label, value)
                ga('send', 'event', category, action, label, hitData);
            }
            return sendRequest("GameTrackingRecord", {hit_type: 'REQUEST', hit_category: category, hit_action: action, hit_label: label, hit_data: hitData}, overRideCallBackFunction);
        },

        getNumberOfVotesPerURIGroup: function (voteGroupURI, overRideCallBackFunction) {
            // voteGroupURI = voting group that collects all the items to be voted on
            return sendRequest("GetNumberOfVotesPerURIGroup", {vote_group_uri: voteGroupURI}, overRideCallBackFunction);
        },

        gameFind: function(game_name_part, overRideCallBackFunction) {
            return sendRequest("GameFind", {game_name_part: game_name_part}, overRideCallBackFunction);
        },

        gameFindByName: function (gameName, overRideCallBackFunction) {
            return sendRequest("GameFindByName", {game_name: gameName}, overRideCallBackFunction);
        },

        gameGet: function (gameId, overRideCallBackFunction) {
            return sendRequest("GameGet", {game_id: gameId}, overRideCallBackFunction);
        },

        gameGetByName: function (gameName, overRideCallBackFunction) {
            return sendRequest("GameGetByName", {game_name: gameName}, overRideCallBackFunction);
        },

        gameListByCategory: function (numItemsPerCategory, gameStatusId, overRideCallBackFunction) {
            return sendRequest("GameListByCategory", {num_items_per_category: numItemsPerCategory, game_status_id: gameStatusId}, overRideCallBackFunction);
        },

        gameListList: function (overRideCallBackFunction) {
            return sendRequest("GameListList", {}, overRideCallBackFunction);
        },

        gameListListGames: function (gameListId, overRideCallBackFunction) {
            return sendRequest("GameListListGames", {game_list_id: gameListId}, overRideCallBackFunction);
        },

        gameListListGamesByName: function (gameListName, overRideCallBackFunction) {
            return sendRequest("GameListListGamesByName", {game_list_name: gameListName}, overRideCallBackFunction);
        },

        gameListByMostPopular: function (startDate, endDate, firstItem, numItems, overRideCallBackFunction) {
            return sendRequest("GameListByMostPopular", {start_date: startDate, end_date: endDate, first_item: firstItem, num_items: numItems}, overRideCallBackFunction);
        },

        gameListCategoryList: function (overRideCallBackFunction) {
            return sendRequest("GameListCategoryList", {}, overRideCallBackFunction);
        },

        gameListListRecommendedGames: function (gameListId, overRideCallBackFunction) {
            return sendRequest("GameListListRecommendedGames", {game_list_id: gameListId}, overRideCallBackFunction);
        },

        gamePlayEventListByMostPlayed: function (startDate, endDate, numItems, overRideCallBackFunction) {
            return sendRequest("GamePlayEventListByMostPlayed", {start_date: startDate, end_date: endDate, num_items: numItems}, overRideCallBackFunction);
        },

        newsletterCategoryList: function (overRideCallBackFunction) {
            return sendRequest("NewsletterCategoryList", {}, overRideCallBackFunction);
        },

        newsletterAddressAssign: function (emailAddress, userName, companyName, categories, overRideCallBackFunction) {
            return sendRequest("NewsletterAddressAssign", {email_address: emailAddress, user_name: userName, company_name: companyName, categories: categories, delimiter: ","}, overRideCallBackFunction);
        },

        newsletterAddressUpdate: function (newsletterAddressId, emailAddress, userName, companyName, active, overRideCallBackFunction) {
            return sendRequest("NewsletterAddressUpdate", {newsletter_address_id: newsletterAddressId, email_address: emailAddress, user_name: userName, company_name: companyName, active: active}, overRideCallBackFunction);
        },

        newsletterAddressDelete: function (emailAddress, overRideCallBackFunction) {
            return sendRequest("NewsletterAddressDelete", {email_address: emailAddress, newsletter_address_id: "NULL"}, overRideCallBackFunction);
        },

        newsletterAddressGet: function (emailAddress, overRideCallBackFunction) {
            return sendRequest("NewsletterAddressGet", {email_address: emailAddress}, overRideCallBackFunction);
        },

        promotionItemList: function (promotionId, queryDate, overRideCallBackFunction) {
            return sendRequest("PromotionItemList", {promotion_id: promotionId, query_date: queryDate}, overRideCallBackFunction);
        },

        promotionList: function (promotionId, queryDate, showItems, overRideCallBackFunction) {
            return sendRequest("PromotionItemList", {promotion_id: promotionId, query_date: queryDate, show_items: showItems}, overRideCallBackFunction);
        },

        recommendedGameList: function (gameId, overRideCallBackFunction) {
            return sendRequest("RecommendedGameList", {game_id: gameId}, overRideCallBackFunction);
        },

        registeredUserCreate: function (userName, password, email, realName, dateOfBirth, gender, city, state, zipcode, countryCode, mobileNumber, imId, tagline, siteUserId, networkId, agreement, securityQuestionId, securityAnswer, imgUrl, aboutMe, additionalInfo, sourceSiteId, captchaId, captchaResponse, overRideCallBackFunction) {
            captchaId = '99999';
            captchaResponse = 'DEADMAN';
            return sendRequest("RegisteredUserCreate",
                {
                    site_id: this.site_id,
                    captcha_id: captchaId,
                    captcha_response: captchaResponse,
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
        },

        registeredUserUpdate: function (userName, password, email, realName, dateOfBirth, gender, city, state, zipcode, countryCode, mobileNumber, imId, tagline, siteUserId, networkId, agreement, securityQuestionId, securityAnswer, imgUrl, aboutMe, additionalInfo, sourceSiteId, captchaId, captchaResponse, overRideCallBackFunction) {
            captchaId = '99999';
            captchaResponse = 'DEADMAN';
            return sendRequest("RegisteredUserUpdate",
                {
                    site_id: this.site_id,
                    captcha_id: captchaId,
                    captcha_response: captchaResponse,
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
        },

        registeredUserSecurityUpdate: function (captcha_id, captcha_response, password, security_question_id, overRideCallBackFunction) {
            return sendRequest("RegisteredUserSecurityUpdate",
                {
                    site_id: this.site_id,
                    captcha_id: captchaId,
                    captcha_response: captchaResponse,
                    password: password,
                    security_question_id: security_question_id
                }, overRideCallBackFunction);
        },

        registeredUserForgotPassword: function (userName, email, overRideCallBackFunction) {
            // this function generates the email that is sent to the email address matching username or email address
            // that email leads to the change password web page
            return sendRequest("RegisteredUserForgotPassword", {user_name: userName, email: email}, overRideCallBackFunction);
        },

        registeredUserGet: function (userId, siteUserId, overRideCallBackFunction) {
            // Return public information about user given id
            return sendRequest("RegisteredUserGet", {get_user_id: userId, site_user_id: siteUserId}, overRideCallBackFunction);
        },

        siteListGames: function(firstItem, numItems, gameStatusId, overRideCallBackFunction) {
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
        },

        siteListGamesRandom: function(numItems, overRideCallBackFunction) {
            if (numItems == null || numItems > 500) {
                numItems = 500;
            }
            return sendRequest("SiteListGamesRandom", {num_items: numItems}, overRideCallBackFunction);
        },

        userGetByName: function (userName, overRideCallBackFunction) {
            // Return public information about user give name
            return sendRequest("UserGetByName", {user_name: userName}, overRideCallBackFunction);
        },

        userLogin: function(userName, password, overRideCallBackFunction) {
            return sendRequest("UserLogin", {user_name: userName, password: password}, overRideCallBackFunction);
        },

        /**
         * Enginesis co-registration accepts validated login from another network and creates a new user or logs in
         * a matching user. site-user-id, user-name, and network-id are mandatory. Everything else is optional.
         * @param registrationParameters {object} registration data values
         * @param networkId {int} we must know which network this registration comes from.
         * @param overRideCallBackFunction {function} called when server replies.
         */
        userLoginCoreg: function (registrationParameters, networkId, overRideCallBackFunction) {
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
            } else if (registrationParameters.gender != 'M' && registrationParameters.gender != 'F') {
                registrationParameters.gender = 'F';
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
        },

        /**
         * Return the proper URL to use to show an avatar for a given user. The default is the default size and the current user.
         * @param size {int} 0 small, 1 medium, 2 large
         * @param userId {int}
         * @return string
         */
        avatarURL: function avatarURL (size, userId) {
            if (userId == 0) {
                userId = loggedInUserId;
            }
            size = 0;
            return avatarImageURL + '?site_id=' + siteId + '&user_id=' + userId + '&size=' + size;
        }
    };
};