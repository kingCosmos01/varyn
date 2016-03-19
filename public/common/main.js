/**
 * @file: main.js
 * @author: jf
 * @date: 7/25/13
 * @version: 2.1.1
 * @summary: The main JavaScript functionality supporting all the sites pages.
 */

// Facebook initialization once FB.js loads
window.fbAsyncInit = function() {
    facebookInit();
};

/* ============== Site Functions ====================== */

function initPage ()
{
    // this is called from head.js once all JS files have been loaded. It is essentially the page initialization function.
    var slider = document.getElementById("slider");
    $('#slider').nivoSlider();
    var id = document.getElementById("page_header_left");
    if (id != null) {
        id.onclick = GoToHomePage;
    }
    id = document.getElementById("popupRegistration");
    if (id != null) {
        $("#register_now,#closePopup,#cancelPopup").click( function () { popupToggle('popupRegistration') });
    }
    Enginesis.init(SiteConfiguration.enginesisSiteId, SiteConfiguration.serverStage, '', SiteConfiguration.enginesisDeveloperKey, SiteConfiguration.languageCode, enginesisResponseHandler);
    Enginesis.debugLog("Enginesis " + Enginesis + " version " + Enginesis.VERSION + " for site_id " + Enginesis.getSiteId());
}

function enginesisResponseHandler (enginesisResult)
{
    Enginesis.debugLog("enginesisResponseHandler " + enginesisResult.fn + " result: " + enginesisResult.results.status.success + ", message: " + enginesisResult.results.status.message + ", info: " + enginesisResult.results.status.extended_info);
    var successful = parseInt(enginesisResult.results.status.success) != 0;
    switch (enginesisResult.fn) {
        case "UserLogin":
            if (successful) {
                loginSuccessful(enginesisResult);
            } else {
                popupErrorMessage("Login", "Invalid name and/or password");
            }
            break;
        case "SessionBegin":
            break;
        case "UserLoginCoreg":
            // if coreg succeeds, log user in, the update the header
            if (successful) {
                Enginesis.debugLog("CoReg successful");
                loginSuccessful(enginesisResult);
            } else {
                Enginesis.debugLog("CoReg Failed");
            }
            break;
        case "RegisteredUserCreate":
            if (successful) {
                Enginesis.debugLog("New registration successful");
                doAutoLogin();
            } else {
                Enginesis.debugLog("Registration Failed");
                popupErrorMessage("Registration", "Could not register at this time: " + enginesisResult.results.status.extendedInfo);
            }
            break;
        case "RegisteredUserForgotPassword":
            break;
        default:
            popupErrorMessage(enginesisResult.fn, "Response to an unhandled command.");
            break;
    }
}


function queryStringAsArray (queryString)
{
    // convert a query string into a keyed array
    var resultArray = {};
    var regexp = /([^&=]+)=([^&]*)/g;
    var item;

    while (item = regexp.exec(queryString)) {
        resultArray[decodeURIComponent(item[1])] = decodeURIComponent(item[2]);
    }
    return resultArray;
}

function arrayAsQueryString (parameterArray)
{
    // turns object properties into a query string format
    var resultString = '';
    if (typeof parameterArray === 'object') {
        for (var key in parameterArray) {
            if (parameterArray.hasOwnProperty(key)) {
                if (resultString.length > 0) {
                    resultString += '&' + key + '=' + parameterArray[key];
                } else {
                    resultString = key + '=' + encodeURIComponent(parameterArray[key]);
                }
            }
        }
    }
    return resultString;
}

function createCookie (cookieName, cookieValue, daysToExpiration)
{
    var expires = ""; // will erase the cookie if not set to a date in the future
    if (daysToExpiration > 0) {
        var today = new Date();
        today.setTime(today.getTime() + (daysToExpiration * 86400000)); // 24 * 60 * 60 * 1000));
        expires = "; expires=" + today.toGMTString();
    }
    document.cookie = cookieName + "=" + cookieValue + expires + "; domain=." + window.location.host.toString() + "; path=/";
}

function readCookie (cookieName)
{
    var nameWithEQ = cookieName + "=";
    var allTokens = document.cookie.split(';');
    var result = null;
    for (var i = 0; i < allTokens.length; i ++) {
        var checkCookie = allTokens[i];
        while (checkCookie.charAt(0) == ' ') {
            checkCookie = checkCookie.substring(1, checkCookie.length);
        }
        if (checkCookie.indexOf(nameWithEQ) == 0) {
            result = checkCookie.substring(nameWithEQ.length, checkCookie.length);
            break;
        }
    }
    return result;
}

function eraseCookie(cookieName)
{
    createCookie(cookieName, "", -1);
}

function getLoginCookieName ()
{
    return SiteConfiguration.varynLoginCookieName;
}

function getUserExtendedInfoCookieName ()
{
    return SiteConfiguration.varynUserInfoCookieName;
}

function getEnginesisSessionCookieName ()
{
    return SiteConfiguration.enginesisSessionCookieName;
}

function GoToHomePage ()
{
    window.location = '/';
}

function GoToProfilePage (withParameters)
{
    var queryString = '';
    if (withParameters.length > 0) {
        queryString = '?' + withParameters;
    }
    window.location = '/services/MyProfile.php' + queryString;
}

function setLoginCookie (userId, siteUserId, userName, sessionId, authtoken)
{
    // store Varyn login info in localstorage and the Enginesis session in the cookie

    var cookieName = getLoginCookieName();
    Enginesis.debugLog("setLoginCookie c=" + cookieName + "; id=" + userId + "; siteUserId=" + siteUserId + "; name=" + userName + "; session=" + sessionId + "; tok=" + authtoken);

    var userInfoObject = {
        userId: userId,
        userName: userName,
        siteUserId: siteUserId,
        sessionId: sessionId,
        authtok: authtoken
    };
    // remember this user is logged in
    var cookieValue = arrayAsQueryString(userInfoObject);
    var cr = makeHash(cookieValue);
    Enginesis.debugLog("setLoginCookie createCookie c=" + cookieName + "; v=" + cookieValue + "; cr=" + cr);
    userInfoObject.cr = cr;
    cookieValue = arrayAsQueryString(userInfoObject);
    window.localStorage.setItem(cookieName, cookieValue);

    cookieName = getEnginesisSessionCookieName();
    cookieValue = authtoken;
    Enginesis.debugLog("setLoginCookie for enginesis cookie c=" + cookieName + "; value=" + cookieValue);
    createCookie(cookieName, cookieValue, 2);
}

function saveExtendedUserInfo (enginesisResultsRow)
{
    // cache users login info
    if (enginesisResultsRow != null) {
        var userInfoObject = {
            user_id: enginesisResultsRow.user_id,
            user_name: enginesisResultsRow.user_name,
            real_name: enginesisResultsRow.real_name,
            site_user_id: enginesisResultsRow.site_user_id,
            dob: enginesisResultsRow.dob,
            gender: enginesisResultsRow.gender,
            city: enginesisResultsRow.city,
            state: enginesisResultsRow.state,
            zipcode: enginesisResultsRow.zipcode,
            country_code: enginesisResultsRow.country_code,
            email_address: enginesisResultsRow.email_address,
            mobile_number: enginesisResultsRow.mobile_number,
            im_id: enginesisResultsRow.im_id,
            about_me: enginesisResultsRow.about_me,
            date_created: enginesisResultsRow.date_created,
            last_login: enginesisResultsRow.last_login,
            login_count: enginesisResultsRow.login_count,
            tagline: enginesisResultsRow.tagline,
            additional_info: enginesisResultsRow.additional_info,
            site_currency_value: enginesisResultsRow.site_currency_value,
            site_experience_points: enginesisResultsRow.site_experience_points,
            view_count: enginesisResultsRow.view_count,
            site_rank:  enginesisResultsRow.user_rank
        };
        window.localStorage.setItem(getUserExtendedInfoCookieName(), arrayAsQueryString(userInfoObject));
    }
}

function getExtendedUserInfo ()
{
    var userInfoObject = null;
    var userInfo = window.localStorage.getItem(getUserExtendedInfoCookieName());
    if (userInfo != null) {
        userInfoObject = queryStringAsArray(userInfo);
    }
    return userInfoObject;
}

function makeHash (value)
{
    // TODO: create a hash to make sure the cookie was not altered; return sha1(value + salt)
    return '12345';
}

function loginSuccessful (enginesisResult)
{
    var userId = enginesisResult.results.result.row.user_id;
    var userName = enginesisResult.results.result.row.user_name;
    var cr = enginesisResult.results.result.row.cr;
    var authtoken = enginesisResult.results.result.row.authtok;
    var sessionId = enginesisResult.results.result.row.session_id;

    // TODO: verify cr hash
    // cr_check = md5(???) == cr

    setLoginCookie(userId, userName, sessionId, authtoken);
    saveExtendedUserInfo(enginesisResult.results.result.row);

    // if returning from registration with auto-login we need to refresh the page to update the header and cookie
    var isReturningFromRegistration = window.localStorage.getItem("userName");
    if (isReturningFromRegistration != null) {
        window.localStorage.removeItem("userName");
        window.localStorage.removeItem("password");
        window.location.reload(true);
    } else {
        setHeaderProfileLoggedinUser();
    }
}

function loginSuccessfulWithCookie ()
{
    setHeaderProfileLoggedinUser();
}

function getLoggedInUserInfo ()
{
    // return user info object
    var userInfoObject = null;
    if (isLoggedInUser()) { // this call will verify the data is valid
        var localCookie = window.localStorage.getItem(getLoginCookieName());
        userInfoObject = queryStringAsArray(localCookie);
    }
    return userInfoObject;
}

function isLoggedInUser ()
{
    // check if login cookie is set and it is valid AND we have the enginesis token
    var isLoggedIn = false;
    var haveUserInfo = false;
    var localCookie = window.localStorage.getItem(getLoginCookieName());
    if (localCookie != null && localCookie.length > 0) {
        var userInfoObject = queryStringAsArray(localCookie);
        var cr = userInfoObject.cr;
        // TODO: verify cr hash
        // cr_check = md5(???) == cr

        haveUserInfo = true;
        Enginesis.debugLog("isLoggedInUser c=" + getLoginCookieName() + "; id=" + userInfoObject.userId + "; name=" + userInfoObject.userName + "; session=" + userInfoObject.sessionId + "; tok=" + userInfoObject.authtoken);
    }
    var enginesisSessionToken = readCookie(getEnginesisSessionCookieName());
    if (enginesisSessionToken != null && haveUserInfo) {
        isLoggedIn = true;
    }
    return isLoggedIn;
}

function logOutUser ()
{
    // clear login state
    window.localStorage.removeItem(getLoginCookieName());
    eraseCookie(getEnginesisSessionCookieName());
    FB.logout(function(response) {Enginesis.debugLog("Facebook user is logged out");});
    window.localStorage.removeItem("userName");
    window.localStorage.removeItem("password");
    window.location.reload(true);
}

function rememberCredentialsForAutologin (userName, password)
{
    // save these credentials in a cookie so when registration completes we can auto login
    window.localStorage.setItem("userName", userName);
    window.localStorage.setItem("password", password);
}

function registrationFormSubmit ()
{
    Enginesis.debugLog("registrationFormSubmit");
    if (document.forms["registration-form"] != null) {
        var fieldWithError = validateRegistrationParameters();
        if (fieldWithError == '') {
//        document.forms["registration-form"].submit();
            var locationParts = parseLocation(document.forms["registration-form"]["location"].value);
            var city = locationParts.city;
            var state = locationParts.state;
            var countryCode = locationParts.countryCode;
            var dob = getCorrectedDate(document.forms["registration-form"]["dob"].value);
            var userName = document.forms["registration-form"]["username"].value;
            var password = document.forms["registration-form"]["password"].value;

            Enginesis.RegisteredUserCreate(
                userName,
                password,
                document.forms["registration-form"]["email"].value,
                document.forms["registration-form"]["fullname"].value,
                dob,
                getFormGenderSetting(),
                city, state, "", countryCode, "", "",
                document.forms["registration-form"]["tagline"].value,
                "", 106,
                document.forms["registration-form"]["agreement"].value == "on" ? "1" : "0",
                1, "", "", "", "", 106, 1, "");
            rememberCredentialsForAutologin(userName, password);
        } else {
            showErrorPopup('Registration', 'Registration incomplete, something is wrong with ' + fieldWithError + '. Name, password, and email are required.');
            // TODO: Focus form on fieldWithError
        }
    } else {
        showErrorPopup('Registration', 'Please provide the required fields to register your account. Name, password, and email are required.');
    }
}

function loginSubmit ()
{
    Enginesis.debugLog("LoginSubmit");
    if (document.forms["login-form"] != null && validateLoginParameters()) {
        Enginesis.UserLogin(document.forms["login-form"]["username"].value, document.forms["login-form"]["password"].value);
    } else {
        var username = document.forms["login-form"]["username"].value;
        var password = document.forms["login-form"]["password"].value;
        showErrorPopup('Login', 'You must provide your user name and password to login. ' + username + ' with ' + password + ' just wont do.');
    }
}

function doAutoLogin ()
{
    Enginesis.debugLog("now doing autoLogin");
    var userName = window.localStorage.getItem("userName");
    var password = window.localStorage.getItem("password");
    if (userName != null && password != null) {
        Enginesis.UserLogin(userName, password);
    }
}

function validateLoginParameters ()
{
    Enginesis.debugLog("validateLoginParameters");
    // make sure we have name and password
    var goodEnough = false;
    if (document.forms["login-form"] != null) {
        var username = document.forms["login-form"]["username"].value;
        var password = document.forms["login-form"]["password"].value;
        if (username != null && username.length > 1 && password != null && password.length > 2) {
            goodEnough = true;
        }
    }
    return goodEnough;
}

function validateRegistrationParameters ()
{
    // make sure we have enough filled out to continue.
    // TODO: Show a real error message indicating which field is incorrect
    var fieldWithError = '';
    if (document.forms["registration-form"] != null) {
        var username = document.forms["registration-form"]["username"].value;
        var password = document.forms["registration-form"]["password"].value;
        var email = document.forms["registration-form"]["email"].value;
        var agreed = document.forms["registration-form"]["agreement"].checked;
        if (username == null || username.length < 2) {
            fieldWithError = 'username';
        } else if (password == null || password.length < 3) {
            fieldWithError = 'password';
        } else if (email == null || email.length < 5 || ! isValidEmail(email)) {
            fieldWithError = 'email';
        } else if ( ! agreed) {
            fieldWithError = 'agreement';
        }
        if (fieldWithError != '') {
            Enginesis.debugLog("validateRegistrationParameters: Something is wrong with " + fieldWithError + "(" + document.forms["registration-form"][fieldWithError].value + ")");
        }
    }
    return fieldWithError;
}

function isValidEmail (email)
{
    // verify the email address appears correct
    var regExExp = /\S+@\S+\.\S+/; // validate anything *@*.*
    var isValid = regExExp.test(email);
    Enginesis.debugLog("Email " + email + (isValid ? " is " : " is not ") + "valid.");
    return isValid;
}

function isAvailableUsername (userName)
{
    // TODO: verify the user name is unique, must query Enginesis to get matching user names
    return true;
}

function getCorrectedDate (datevalue)
{
    if (datevalue == null || datevalue == '') {
        // your DOB will be today - 10 years
        var dateNow = new Date();
        datevalue = (dateNow.getUTCFullYear() - 10) + '-' +
            ('00' + (dateNow.getUTCMonth()+1)).slice(-2) + '-' +
            ('00' + dateNow.getUTCDate()).slice(-2);
    } else {
        // TODO: verify this is a valid date in form yyyy-mm-dd
    }
    return datevalue;
}

function parseLocation (locationString)
{
    // TODO: Convert location into City/State, CC
    // try to get city, state, country from locationString, we expect to see "New York, NY", "US", New York, or NY
    var resultParts = {};
    resultParts.city = "";
    resultParts.state = "";
    resultParts.countryCode = "us";
    if (locationString != null && locationString.length > 1) {
        var commaPos = locationString.indexOf(',');
        if (commaPos > 0) {
            resultParts.city = locationString.substring(0, commaPos).trim();
            resultParts.state = locationString.substring(commaPos+1).trim();
        } else {
            if (locationString.length > 2) {
                resultParts.city = locationString.trim();
            } else {
                resultParts.state = locationString.trim();
            }
        }
    }
    return resultParts;
}

function getCheckedRadioButtonValueFromGroup (groupName, defaultValue)
{
    var value = defaultValue;
    var radios = document.getElementsByName(groupName);
    if (radios != null) {
        for (var i = 0, numButtons = radios.length; i < numButtons; i ++) {
            if (radios[i].checked) {
                value = radios[i].value;
                break;
            }
        }
    }
    return value;
}

function getFormGenderSetting ()
{
    return getCheckedRadioButtonValueFromGroup("gender", "F");
}

function submitSearch ()
{
    var searchId = document.getElementById('search-query');
    if (searchId != null) {
        var query = searchId.value;
        if (query != null && query.length > 0) {
            showErrorPopup("Search", "We are going to look for " + query);
            window.location = '/services/AllGames.php?q=' + query;
        }
    }
}

/*
          Facebook SDK
 */

function facebookInit ()
{
    FB.init({
        appId      : SiteConfiguration.varynFacebookAppId,
        channelUrl : '//www.varyn.com/common/channel.html', // Channel File
        status     : true, // check login status
        cookie     : true, // enable cookies to allow the server to access the session
        xfbml      : true  // parse XFBML
    });

    // Here we subscribe to the auth.authResponseChange JavaScript event. This event is fired
    // for any authentication related change, such as login, logout or session refresh. This means that
    // whenever someone who was previously logged out tries to log in again, the correct case below
    // will be handled.

    FB.Event.subscribe('auth.authResponseChange', function(response) {
        // Here we specify what we do with the response anytime this event occurs.
        if (response.status === 'connected') {
            // The response object is returned with a status field that lets the app know the current
            // login status of the person. In this case, we're handling the situation where they
            // have logged in to the app.
            FacebookUserIsLoggedIn();
        } else if (response.status === 'not_authorized') {
            // In this case, the person is logged into Facebook, but not into the app, so we call
            // FB.login() to prompt them to do so.
            // In real-life usage, you wouldn't want to immediately prompt someone to login
            // like this, for two reasons:
            // (1) JavaScript created popup windows are blocked by most browsers unless they
            // result from direct interaction from people using the app (such as a mouse click)
            // (2) it is a bad experience to be continually prompted to login upon page load.
            FB.login();
        } else {
            // In this case, the person is not logged into Facebook, so we call the login()
            // function to prompt them to do so. Note that at this stage there is no indication
            // of whether they are logged into the app. If they aren't then they'll see the Login
            // dialog right after they log in to Facebook.
            // The same caveats as above apply to the FB.login() call here.
            FB.login();
        }
    });
}

function FacebookUserIsLoggedIn ()
{
    // Our user is logged in with Facebook now auto-login with Enginesis

    FB.api('/me', function(response) {
        Enginesis.debugLog('Facebook login for ' + response.name + ' (' + response.id + ').');
        if ( ! facebookUserIsStillValid(response.name, response.id)) {
            var gender = response.gender;
            var locale = response.locale; // en_US
            var city = '';
            var state = '';
            var countryCode = '';
            var dob = '';
            if (response.location != null && response.location.name != null) {
                var location = parseLocation(response.location.name);
                if (location != null) {
                    city = location.city;
                    state = location.state;
                }
            }
            var locale_parts = locale.split('_');
            if (locale_parts.count > 1) {
                locale = locale_parts[0];
                countryCode = locale_parts[1];
            } else {
                locale = 'en';
                countryCode = 'US';
            }
            Enginesis.UserLoginCoreg(response.name, response.id, gender, dob, city, state, countryCode, locale, Enginesis.NETWORK_ID_FACEBOOK);
        } else {
            loginSuccessfulWithCookie();
        }
    });
}

function loginFacebook ()
{
    FB.login(function(response) {
        if (response.authResponse) {
            FacebookUserIsLoggedIn();
        } else {
            Enginesis.debugLog('User cancelled login or did not fully authorize.');
        }
    });
}

function facebookUserIsStillValid (facebookName, facebookId)
{
    // verify our cookie is still valid and it matches this user
    var isValid = false;
    var userInfoObject = getLoggedInUserInfo();
    if (userInfoObject != null && userInfoObject['siteUserId'] == facebookId && userInfoObject['userName'] == facebookName) {
        isValid = true;
    }
    return isValid;
}

function setHeaderProfileLoggedinUser ()
{
    var headerProfileDiv = document.getElementById("header_profile");
    var userInfoObject = getExtendedUserInfo();
    if (headerProfileDiv != null && userInfoObject != null) {
        var setDocElement = document.getElementById("header_user_profile_img");
        if (setDocElement != null) {
            setDocElement.title = userInfoObject['user_name'];
            setDocElement.src = "";
        }
        setDocElement = document.getElementById("header_username");
        if (setDocElement != null) {
            setDocElement.innerText = userInfoObject['user_name'];
        }
        setDocElement = document.getElementById("header_siterank");
        if (setDocElement != null) {
            setDocElement.innerText = userInfoObject['site_rank'];
        }
    }
}

// ==========================================
// Various UI/GUI handlers and helpers
//

function toggleDiv (popUpDivId)
{
    var id = document.getElementById(popUpDivId);
    if (id != null) {
        var newStyle;
        if (id.style.display != 'block' && id.style.display != 'inline-block') {
            newStyle = 'block';
        } else {
            newStyle = 'none';
        }
        id.style.display = newStyle;
    }
}

function hideDiv (popUpDivId)
{
    var id = document.getElementById(popUpDivId);
    if (id != null && id.style.display != 'none') {
        id.style.display = 'none';
    }
}

function setCoverSize (id)
{
    var cover = document.getElementById(id);
    if (cover != null) {
        var coverHeight = 0;
        var viewportHeight = 0;
        if (typeof window.innerWidth != 'undefined') {
            viewportHeight = window.innerHeight;
        } else {
            viewportHeight = document.documentElement.clientHeight;
        }
        if ((viewportHeight > document.body.parentNode.scrollHeight) && (viewportHeight > document.body.parentNode.clientHeight)) {
            coverHeight = viewportHeight;
        } else {
            if (document.body.parentNode.clientHeight > document.body.parentNode.scrollHeight) {
                coverHeight = document.body.parentNode.clientHeight;
            } else {
                coverHeight = document.body.parentNode.scrollHeight;
            }
        }
        cover.style.height = coverHeight + 'px';
    }
}

function setWindowPosition (popUpDivId, popupWidth, popupHeight)
{
    var windowWidth = 0;
    var viewportWidth = 0;
    if (typeof window.innerWidth != 'undefined') {
        viewportWidth = window.innerHeight;
    } else {
        viewportWidth = document.documentElement.clientHeight;
    }
    if ((viewportWidth > document.body.parentNode.scrollWidth) && (viewportWidth > document.body.parentNode.clientWidth)) {
        windowWidth = viewportwidth;
    } else {
        if (document.body.parentNode.clientWidth > document.body.parentNode.scrollWidth) {
            windowWidth = document.body.parentNode.clientWidth;
        } else {
            windowWidth = document.body.parentNode.scrollWidth;
        }
    }
    var popUpDiv = document.getElementById(popUpDivId);
    if (popUpDiv != null) {
        popUpDiv.style.left = ((windowWidth / 2) - (popupWidth / 2)) + 'px';
        popUpDiv.style.top = '10px';
    }
}

function popupToggle (popUpDivId)
{
    toggleDiv('popupCover');
    setCoverSize('popupCover');
    toggleDiv(popUpDivId);
    setWindowPosition(popUpDivId, 320, 400);
}

function setErrorMessage (title, message)
{
    var popupDiv = document.getElementById('popupErrorMessage');
    if (popupDiv != null) {
        popupDiv.innerHTML = '<div class="dialogHeader"><h2 class="errorTitle">' + title + '</h2></div><p class="errorMessage">' + message + '</p><br /><a href="#" id="cancelError">Continue</a>';
        $("#cancelError").click( function () { popupToggle('popupErrorMessage') });
    }
}

function popupErrorMessage (title, message)
{
    // build a popup error div with background cover
    toggleDiv('popupCover');
    setCoverSize('popupCover');
    toggleDiv('popupErrorMessage');
    setWindowPosition('popupErrorMessage', 300, 240);
    showErrorPopup(title, message);
}

function showErrorPopup (title, message)
{
    var id = document.getElementById('popupCover');
    if (id != null) {
        var newStyle;
        if (id.style.display != 'block' && id.style.display != 'inline-block') {
            // not already showing so do the whole routine
            popupErrorMessage(title, message);
        } else {
            // already showing a popup, we need to hide that one
            hideDiv('popupRegistration');
            toggleDiv('popupErrorMessage');
            setWindowPosition('popupErrorMessage', 300, 240);
            var popupDiv = document.getElementById('popupErrorMessage');
            if (popupDiv != null) {
                popupDiv.innerHTML = '<div class="dialogHeader"><h2 class="errorTitle">' + title + '</h2></div><p class="errorMessage">' + message + '</p><br /><a href="#" id="cancelError">Continue</a>';
                $("#cancelError").click( function () { popupToggle('popupErrorMessage') });
            }
        }
    }
}
