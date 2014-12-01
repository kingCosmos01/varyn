/**
 * @file: enginesis.js
 * @author: jf
 * @date: 7/25/13
 * @version: 2.1.1
 * @summary: A JavaScript interface to the Enginesis API. Enginesis is a singleton object.
 */

// Create or return singelton
var Enginesis = Enginesis || {};

Enginesis.VERSION = '2.2.14';
Enginesis.DEBUG = true;
Enginesis.NETWORK_ID_FACEBOOK = 2;

Enginesis.host = '';
Enginesis.site_id = 100;
Enginesis.game_id = 0;
Enginesis.game_key = '';
Enginesis.language_code = 'en';

Enginesis.submitToURL = '';
Enginesis.syncId = 0;
Enginesis.lastCommand = '';
Enginesis.authtoken = '';
Enginesis.developerKey = '';
Enginesis.callBackFunction = null;


Enginesis.init = function (site_id, enginesisServer, authToken, developerKey, language_code, callBackFunction)
{
    this.host = this.qualifyServer(enginesisServer);
    this.site_id = site_id;
    this.language_code = language_code;
    this.syncId = 0;
    this.authtoken = authToken;
    this.developerKey = developerKey;
    this.lastCommand = "";
    this.submitToURL = "http://" + this.host + "/index.php";
    this.callBackFunction = callBackFunction;
}

Enginesis.SessionBegin = function (game_id, game_key)
{
    this.game_id = game_id;
    this.game_key = game_key;
    this.debugLog('Submitting to ' + this.submitToURL, 1);
    this.sendRequest("SessionBegin", {site_id: this.site_id, game_id: this.game_id, gamekey: this.game_key});
}

Enginesis.UserLogin = function (userName, password)
{
    this.debugLog("Enginesis.UserLogin user_name=" + userName + ", password=" + password);
    this.sendRequest("UserLogin", {site_id: this.site_id, user_name: userName, password: password});
}

Enginesis.UserLoginCoreg = function (userName, siteUserId, gender, dob, city, state, countryCode, locale, networkId)
{
    this.debugLog("Enginesis.UserLoginCoreg user_name=" + userName + ", id=" + siteUserId);
    this.sendRequest("UserLoginCoreg",
        {
            site_id: this.site_id,
            site_user_id: siteUserId,
            user_name: userName,
            network_id: networkId
        });
}

Enginesis.RegisteredUserCreate = function (userName, password, email, realName, dateOfBirth, gender, city, state, zipcode, countryCode, mobileNumber, imId, tagline, siteUserId, networkId, agreement, securityQuestionId, securityAnswer, imgUrl, aboutMe, additionalInfo, sourceSiteId, captchaId, captchaResponse)
{
    this.debugLog("Enginesis.RegisteredUserCreate username=" + userName + ", password=" + password);
    captchaId = '99999';
    captchaResponse = 'DEADMAN';
    this.sendRequest("RegisteredUserCreate",
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
        });
}

Enginesis.RegisteredUserForgotPassword = function (userName, email)
{
    // this function generates the email that is sent to the email address matching username or email address
    // that email leads to the change password web page
    this.debugLog("Enginesis.RegisteredUserForgotPassword username=" + userName + ", email=" + email);
}

Enginesis.debugLog = function (message, level)
{
    if (this.DEBUG) {
        if (level === null) {
            level = 1;
        }
        console.log(message);
        if (level == 9) {
            alert(message);
        }
    }
}

Enginesis.qualifyServer = function (server)
{
    var realServer = 'www.enginesis.com';
    if (server == '-l') {
        realServer = 'www.enginesis-l.com';
    } else if (server == '-q') {
        realServer = 'www.enginesis-q.com';
    } else if (server == '-d') {
        realServer = 'www.enginesis-d.com';
    } else if (server == '-x') {
        realServer = 'www.enginesis-x.com';
    }
    return realServer;
}

Enginesis.serverParamObjectMake = function (whichCommand, additionalParameters)
{
    var serverParams = {
        fn: whichCommand,
        language_code: this.language_code,
        site_id: this.site_id,
        state_seq: ++this.syncId,
        response: "json"
    };
    if (additionalParameters != null) {
        for (var key in additionalParameters) {
            if (additionalParameters.hasOwnProperty(key)) {
                serverParams[key] = additionalParameters[key];
            }
        }
    }
    return serverParams;
}

Enginesis.convertParamsToFormData = function (parameterObject)
{
    var formDataObject = new FormData();
    for (var key in parameterObject) {
        if (parameterObject.hasOwnProperty(key)) {
            formDataObject.append(key, parameterObject[key]);
            Enginesis.debugLog("convert " + key + "=" + parameterObject[key]);
        }
    }
    return formDataObject;
}

Enginesis.requestComplete = function (enginesisResponseData)
{
    Enginesis.debugLog("CORS request complete " + enginesisResponseData);
    var enginesisResponseObject = JSON.parse(enginesisResponseData);
    enginesisResponseObject.fn = enginesisResponseObject.results.passthru.fn;
    if (this.callBackFunction != null) {
        this.callBackFunction(enginesisResponseObject);
    }
}

Enginesis.sendRequest = function (fn, params)
{
    var enginesisParameters = this.serverParamObjectMake(fn, params);
    var crossOriginRequest = new XMLHttpRequest();
    if (typeof crossOriginRequest.withCredentials === undefined) {
        Enginesis.debugLog("CORS is not supported");
    } else {
        crossOriginRequest.onload = function(e) {
            Enginesis.requestComplete(this.responseText);
        }

        crossOriginRequest.onerror = function(e) {
            Enginesis.debugLog("CORS request error " + e);
            // TODO: Enginesis.requestError(errorMessage); generate a canned error response (see PHP code)
        }

        crossOriginRequest.open("POST", this.submitToURL, true);
        crossOriginRequest.send(this.convertParamsToFormData(enginesisParameters));
        this.lastCommand = fn;
    }
}

Enginesis.getSiteId = function ()
{
    return this.site_id;
}

Enginesis.getGameId = function ()
{
    return this.game_id;
}
