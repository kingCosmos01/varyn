/**
 * Common JavaScript and utility functions used across Varyn.com. This script should be loaded on every page.
 * The initApp function requires a page-view object that is responsible for implementing page-specific
 * functionality.
 */
var varyn = function (parameters) {
    "use strict";

    var siteConfiguration = {
            debug: true,
            originWhiteList: ["www.enginesis.com", "games.enginesis.com", "metrics.enginesis.com", "www.enginesis-l.com", "games.enginesis-l.com", "metrics.enginesis-l.com", "www.enginesis-q.com", "games.enginesis-q.com", "metrics.enginesis-q.com"],
            enginesisSessionCookieName: 'engsession',
            varynLoginCookieName: 'varynsession',
            varynUserInfoCookieName: 'varynuser',
            varynFacebookAppId: parameters.facebookAppId,
            developerKey: parameters.developerKey,
            siteId: parameters.siteId,
            gameId: parameters.gameId,
            gameGroupId: parameters.gameGroupId,
            serverStage: parameters.serverStage,
            serverHostDomain: 'varyn' + parameters.serverStage + '.com',
            languageCode: parameters.languageCode,
            gameListIdTop: parameters.gameListIdTop || 4,
            gameListIdNew: parameters.gameListIdNew || 5,
            homePagePromoId: parameters.homePagePromoId || 3,
            gameListState: 1,
            userInfo: parameters.userInfo,
            authToken: parameters.authToken,

            minPasswordLength: 4,
            minUserNameLength: 3,
            minimumAge: 13
        },
        userInfoKey = 'VarynAppUserInfo',
        unconfirmedNetworkId = 1,
        currentPage = '',
        waitingForUserNameReply = false,
        domImage,
        enginesisSession = window.enginesis,
        pageViewParameters = null;

    /**
     * Network id is set by the Enginesis server based on what type of user login was performed.
     * @returns {number}
     */
    function getNetworkId () {
        var resultNetworkId;
        if (siteConfiguration.userInfo !== undefined && siteConfiguration.userInfo != null && siteConfiguration.userInfo.networkId !== undefined) {
            resultNetworkId = siteConfiguration.userInfo.networkId;
        } else {
            resultNetworkId = unconfirmedNetworkId;
        }
        return resultNetworkId;
    }

    function getVarynUserInfoFromCookie () {
        var userInfoJSON = commonUtilities.cookieGet(siteConfiguration.varynUserInfoCookieName);
        if (userInfoJSON != null && userInfoJSON != '') {
            return JSON.parse(userInfoJSON);
        }
        return null;
    }

    return {

        /**
         * Call this to initialize the varyn app, get the Enginesis instance, and begin the page operations.
         */
        initApp: function(pageView, pageViewParameterObject) {

            var enginesisParameters = {
                    siteId: siteConfiguration.siteId,
                    gameId: siteConfiguration.gameId || 0,
                    gameGroupId: siteConfiguration.gameGroupId || 0,
                    serverStage: 'enginesis.' + siteConfiguration.serverHostDomain,
                    authToken: siteConfiguration.authToken || '',
                    developerKey: siteConfiguration.developerKey,
                    languageCode: this.parseLanguageCode(siteConfiguration.languageCode),
                    callBackFunction: this.enginesisCallBack.bind(this)
               },
               pageViewTemplate = null;

            currentPage = this.getCurrentPage();
            pageViewParameters = pageViewParameterObject;
            // document.domain = siteConfiguration.serverHostDomain;
            enginesisSession.init(enginesisParameters);
            this.checkLoggedInSSO(getNetworkId());
            if (enginesisSession.isUserLoggedIn()) {
                if (pageViewParameters != null && pageViewParameters['userInfo'] !== undefined && pageViewParameters.userInfo != '') {
                    siteConfiguration.userInfo = JSON.parse(pageViewParameters.userInfo); // when user logs in first time this is passed from PHP
                    commonUtilities.saveObjectWithKey(userInfoKey, siteConfiguration.userInfo);
                } else {
                    siteConfiguration.userInfo = commonUtilities.loadObjectWithKey(userInfoKey); // when user already logged in this is saved locally
                    if (siteConfiguration.userInfo == null) {
                        // TODO: This is a critical error, we expect the userInfo object to be available if the user is logged in.
                    }
                }
            }
            if (pageViewParameters != null && pageViewParameters.showSubscribe !== undefined && pageViewParameters.showSubscribe == '1') {
                varynApp.showSubscribePopup();
            }
            if (pageView !== undefined && pageView != null) {
                pageViewTemplate = pageView(varynApp, siteConfiguration);
                pageViewTemplate.pageLoaded(pageViewParameters);
            }
            return pageViewTemplate;
        },

        /**
         * Save the refresh token client-side. This means that the token is saved only on the device
         * the user successfully logs in in from. The app can use this token when the auth-token is
         * rejected due to TOKEN_EXPIRED error in order to ask for a new token.
         * @param refreshToken
         */
        saveRefreshToken: function(refreshToken) {
            if (enginesisSession != null) {
                enginesisSession.saveRefreshToken(refreshToken);
            }
        },

        /**
         * If we think we should be logged in on a certain network then verify that network also agrees
         * @param networkId
         */
        checkLoggedInSSO: function (networkId) {
            switch (networkId) {
                case 2: // Facebook
                    if (FB !== undefined) {
                        FB.getLoginStatus(varynApp.facebookStatusChangeCallback);
                    }
                    break;
                case 7: // Google
                    break;
                case 11: // Twitter
                    break;
                default:
                    break;
            }
        },

        /**
         * Return the current logged in user info object.
         * TODO: Verify the user is in fact logged in and token is valid.
         * @returns {*}
         */
        getVarynUserInfo: function () {
            // user info could come from authtok or cookie.
            var userInfo = siteConfiguration.userInfo;
            if (userInfo == null) {
                userInfo = commonUtilities.loadObjectWithKey(userInfoKey);
                if (userInfo == null) {
                    userInfo = getVarynUserInfoFromCookie();
                }
            }
            return userInfo;
        },

        getEnginesisSession: function () {
            return enginesisSession;
        },

        getSiteConfiguration: function () {
            return siteConfiguration;
        },

        parseLanguageCode: function (languageCode) {
            return languageCode.substr(0, 2);
        },

        /**
         * Determines if an email address appears to be a valid format.
         * @param {string} email address to check.
         * @returns {boolean}
         */
        isValidEmail: function (email) {
            return /\S+@\S+\.\S+/.test(email);
        },

        /**
         * Determines if a user name appears to be a valid format. A user name must be
         * 3 to 20 characters.
         * @param {string} user name to check.
         * @returns {boolean}
         */
        isValidUserName: function (userName) {
            return /^[a-zA-Z0-9_@!~\$\.\-\|\s?]{3,20}$/.test(userName);
        },

        /**
         * Test if user name has changed from teh value we have in the userInfo object.
         * @param newUserName
         * @returns {boolean} Returns true if the provided user name is different from the cached value.
         */
        isChangedUserName: function (newUserName) {
            var userInfo = siteConfiguration.userInfo;
            return ! (userInfo != null && userInfo.user_name == newUserName);
        },

        /**
         * Use this function to determine if a given form field contains a value that is different than the
         * corresponding value in the userInfo cache. Returns true if the values are different.
         * @param fieldKey String the key in the userInfo object to check.
         * @param formId String the id on the DOM form to read.
         * @returns {boolean} true if the two values are different. false if they are the same.
         */
        isChangedUserInfoField: function (fieldKey, formId) {
            var result = false,
                fieldValue = null,
                formElement,
                formValue = null;

            if (siteConfiguration.userInfo !== undefined && siteConfiguration.userInfo != null && siteConfiguration.userInfo[fieldKey] !== undefined) {
                fieldValue = siteConfiguration.userInfo[fieldKey];
            }
            formElement = document.getElementById(formId);
            if (formElement != null) {
                formValue = formElement.value;
            }
            if (fieldValue != null && formValue != null) {
                result = fieldValue != formValue;
            }
            return result;
        },

        /**
         * Determines if a password appears to be a valid format.
         * @param {string} password to check.
         * @returns {boolean}
         */
        isValidPassword: function (password) {
            var trimmed = password.trim().length;
            return trimmed >= siteConfiguration.minPasswordLength && trimmed == password.length;
        },

        /**
         * Compute Age given date of birth. Actually, compute number of years since date provided.
         * @param {string} Date of birth is a string date format from an input type=date control, we expect yyyy-mm-dd.
         * @returns {number} Number of years.
         */
        ageInYearsFromNow: function (dob) {
            var today = new Date(),
                dateOfBirth = new Date(dob),
                millisecondsOneYear = 31536000000, // 1000 * 60 * 60 * 24 * 365,
                utc1,
                utc2;

            if (isNaN(dateOfBirth.getFullYear())) {
                dateOfBirth = today;
            }
            utc1 = Date.UTC(dateOfBirth.getFullYear(), dateOfBirth.getMonth(), dateOfBirth.getDate());
            utc2 = Date.UTC(today.getFullYear(), today.getMonth(), today.getDate());
            return Math.floor((utc2 - utc1) / millisecondsOneYear);
        },

        /**
         * Determines if a date of birth appears to be a valid. For this site users must be 13 years of age.
         * @param {string} Date of birth is a string date format from an input type=date control.
         * @returns {boolean}
         */
        isValidDateOfBirth: function (dob) {
            return this.ageInYearsFromNow(dob) >= siteConfiguration.minimumAge;
        },

        /**
         * Return the current web page file name without extension.
         * @returns {string}
         */
        getCurrentPage: function () {
            var pageName = location.href.split('/').slice(-1);
            if (pageName.indexOf('.') > 0) {
                pageName = pageName.split('.').slice(0);
            } else if (pageName == '') {
                pageName = 'index';
            }
            return pageName.toString();
        },

        /**
         * We expect a standard errorContent div to appear on any page that will display an error message
         * resulting from a user interaction.
         * @param errorMessage
         * @param fieldWithError
         */
        showErrorMessage: function (errorMessage, fieldWithError) {
            var errorContent = document.getElementById('errorContent'),
                errorFieldElement = document.getElementById(fieldWithError);

            if (errorMessage == "") {
                errorContent.innerHTML = '<p>&nbsp;</p>';
            } else if (errorContent != null) {
                errorContent.innerHTML = '<p class="error-text">' + errorMessage + '</p>';
            }
            if (errorFieldElement != null) {
                $(errorFieldElement).removeClass("popup-form-input").addClass("popup-form-input-error");
                errorFieldElement.focus();
            }
        },

        /**
         * setElementSizeAndColor of DOM element
         * @param DOM element
         * @param requiredWidth
         * @param requiredHeight
         * @param bgcolor
         */
        setElementSizeAndColor: function (elementDiv, requiredWidth, requiredHeight, bgcolor) {
            var style = "margin: 0; padding: 0; left: 0; top: 0; width: " + requiredWidth + "px; height: " + requiredHeight + "px; min-height: " + requiredHeight + "px !important; overflow: hidden;";
            if (bgcolor != null && bgcolor != "") {
                style += " background-color: " + bgcolor + ";";
            }
            elementDiv.setAttribute("style", style);
        },

        /**
         * This is a debug function to dump out the width&height of all children of the target div.
         * @param elementDiv
         */
        checkChildLayout: function (elementDiv) {
            var i,
                e,
                nodeName,
                childNodes = elementDiv.children;

            for (i = 0; i < childNodes.length; i ++) {
                e = childNodes[i];
                if (e.name != null) {
                    nodeName = e.name;
                } else if (e.id != null) {
                    nodeName = e.id;
                } else {
                    nodeName = e.localName;
                }
                console.log(elementDiv.localName + ": Child " + nodeName + " (" + e.style.width + "," + e.style.height + ")");
            }
        },

        /**
         * compareTitle is an array sort function to alphabetize an array by title
         * @param a
         * @param b
         * @returns {number} 0 if equal, 1 if  is greater, -1 if b is greater
         */
        compareTitle: function (a, b) {
            if (a.title < b.title) {
                return -1;
            } else if (a.title > b.title) {
                return 1;
            } else {
                return 0;
            }
        },

        /**
         * insertAndExecute inserts HTML text into the provided <div id="id"> and if that new HTML includes
         * any script tags they will get evaluated.
         * @param id: element to insert new HTML text into.
         * @param text: the new HTML text to insert into id.
         */
        insertAndExecute: function (id, text) {
            document.getElementById(id).innerHTML = text;
            var scripts = document.getElementById(id).getElementsByTagName("script");
            for (var i = 0; i < scripts.length; i ++) {
                if (scripts[i].src != '') {
                    var tag = document.createElement('script');
                    tag.src = scripts[i].src;
                    document.getElementsByTagName('head')[0].appendChild(tag);
                } else {
                    eval(scripts[i].innerHTML);
                }
            }
        },

        /**
         * Check if this browser supports CORS.
         * @returns {boolean}
         */
        checkBrowserCORSCompatibility: function () {
            var supported = (typeof window.postMessage !== "undefined");
            return supported;
        },

        /**
         * Verify the originating request is coming from a trusted source.
         * @param origin
         * @returns {boolean}
         */
        verifyCORSWhiteList: function (origin) {
            var ok = false;
            for (var i=0; i < siteConfiguration.originWhiteList.length; i++) {
                if (origin === siteConfiguration.originWhiteList[i]) {
                    ok = true;
                    break;
                }
            }
            return ok;
        },

        /**
         * showSubscribePopup show the popup form to capture an email address to subscribe to the newsletter.
         */
        hideSubscribePopup: function () {
            this.showSubscribePopup(false);
        },

        /**
         * showSubscribePopup show the popup form to capture an email address to subscribe to the newsletter.
         * TODO: track if user already signed up?
         */
        showSubscribePopup: function (showFlag) {
            if (showFlag) {
                document.getElementById("subscribe-email").value = enginesisSession.anonymousUserGetSubscriberEmail();
                // $('#modal-subscribe').modal('show');
                this.setPopupMessage('modal-subscribe', '', null);
            } else {
                $('#modal-subscribe').modal('hide');
            }
        },

        /**
         * showRegistrationPopup show the popup form to capture an new quick registration. This is a short form,
         * for the long form go to the profile.php page.
         */
        showRegistrationPopup: function (showFlag) {
            if (showFlag) {
                $('#modal-register').modal('show');
                this.setPopupMessage('modal-register', '', null);
                this.onChangeRegisterUserName(document.getElementById('register-username'), 'popup_user_name_unique');
            } else {
                $('#modal-register').modal('hide');
            }
        },

        /**
         * showLoginPopup show the popup form to capture an new quick registration. This is a short form,
         * for the long form go to the profile.php page.
         */
        showLoginPopup: function (showFlag) {
            if (showFlag) {
                $('#modal-login').modal('show');
                this.setPopupMessage('modal-login', '', null);
            } else {
                $('#modal-login').modal('hide');
            }
        },

        /**
         * showForgotPasswordPopup show the popup form initiate forgot password flow.
         */
        showForgotPasswordPopup: function (showFlag) {
            if (showFlag) {
                $('#modal-forgot-password').modal('show');
                this.setPopupMessage('modal-forgot-password', '', null);
            } else {
                $('#modal-forgot-password').modal('hide');
            }
        },

        setPopupMessage: function (popupId, message, className) {
            var messageClass = 'modalMessageArea',
                messageElement = $('#' + popupId).find('.' + messageClass);

            if (messageElement != null) {
                messageElement.css('display', 'block');
                messageElement.text(message);
                if (className != null) {
                    messageElement.attr('class', messageClass + ' ' + className);
                }
            }
        },

        /**
         * Close all popups. Being not so smart, we set all popups we know of to display:none.
         * TODO: Smarter approach would be to take all .popupFrame elements and set them to display:none.
         */
        popupCloseClicked: function () {
            this.closeInfoMessagePopup();
            this.showSubscribePopup(false);
            this.showLoginPopup(false);
            this.showRegistrationPopup(false);
            this.showForgotPasswordPopup(false);
            // $('.popupFrame').attr('display', 'none');
        },

        /**
         * Display a take-over popup with a title and message. Use this as a general informational popup on any page.
         * @param title - title text of popup.
         * @param message - message HTML shown inside popup body.
         * @param timeToClose - number of milliseoncds to auto-close the popup. 0 to never close automatically.
         */
        showInfoMessagePopup: function (title, message, timeToClose) {
            var popupTitle = document.getElementById("infoMessageTitle"),
                popupMessage = document.getElementById("infoMessageArea");

            popupTitle.innerText = title;
            popupMessage.innerHTML = message;
            $('#modal-message').modal('show');
            if (timeToClose > 0) {
                window.setTimeout(this.closeInfoMessagePopup.bind(this), timeToClose);
            }
        },

        /**
         * Closes the popup that was opened with showInfoMesssagePopup.
         * TODO: Maybe smart to cancel the close interval if this was closed from the close button.
         */
        closeInfoMessagePopup: function () {
            $('#modal-message').modal('hide');
        },


        /**
         * The submit button was clicked on the subscribe popup. Validate user inputs before we
         * attempt to submit the request with the server. Will set focus to a field in error. This
         * function also auto-submits the request ajax style so the form submit is not used. If
         * successful the popup will dismiss automatically after a timer.
         *
         * @return boolean true if ok to submit the form
         */
        popupSubscribeClicked: function () {
            var email = document.getElementById("subscribe-email").value,
                errorField = "";

            if (this.isValidEmail(email)) {
                this.setPopupMessage("modal-subscribe", "Subscribing " + email + " with the service...", "popupMessageResponseOK");
                enginesisSession.anonymousUserSetSubscriberEmail(email);
                enginesisSession.newsletterAddressAssign(email, '', '', '2', null); // the newsletter category id for Varyn/General is 2
            } else {
                errorField = "subscribe-email";
                this.setPopupMessage("modal-subscribe", "Your email " + email + " looks bad. Can you try again?", "popupMessageResponseError");
                document.getElementById(errorField).focus();
            }
            return errorField == "";
        },

        /**
         * The submit button was clicked on the registration popup. Validate user inputs on the quick registration form before we
         * attempt to submit the request with the server. Will set focus to a field in error.
         *
         * @returns {boolean} true if ok to submit the form
         */
        popupRegistrationClicked: function () {
            var email = document.getElementById("register-email").value,
                password = document.getElementById("register-password").value,
                userName = document.getElementById("register-username").value,
                agreement = document.getElementById("register-agreement").value,
                errorField = "";

            if (errorField == "" && ! this.isValidEmail(email)) {
                this.setPopupMessage("modal-register", "Your email " + email + " looks bad. Can you try again?", "popupMessageResponseError");
                errorField = "register-email";
            }
            if (errorField == "" && ! this.isValidUserName(userName)) {
                this.setPopupMessage("modal-register", "Your user name " + userName + " looks bad. Can you try again?", "popupMessageResponseError");
                errorField = "register-username";
            }
            if (errorField == "" && ! this.testUserNameIsUnique('popup_user_name_unique')) {
                this.setPopupMessage("modal-register", "Your user name " + userName + " is in use by another user. Please pick a unique user name.", "popupMessageResponseError");
                errorField = "register-username";
            }
            if (errorField == "" && ! this.isValidPassword(password)) {
                this.setPopupMessage("modal-register", "Your password looks bad. Can you try again?", "popupMessageResponseError");
                errorField = "register-password";
            }
            if (errorField == "" && agreement < 2) {
                this.setPopupMessage("modal-register", "You must agree with the terms of use or you cannot register.", "popupMessageResponseError");
                errorField = "register-agreement";
            }
            if (errorField != "") {
                $(errorField).removeClass("popup-form-input").addClass("popup-form-input-error");
                document.getElementById(errorField).focus();
            }
            if (errorField == "") {
                document.getElementById("registration-form").submit();
            }
            return errorField == ""; // return true to submit form
        },

        /**
         * The submit button on the login popup was clicked. Validate user inputs on the login form before we
         * attempt to submit the request with the server. Will set focus to a field in error.
         *
         * @returns {boolean} true if ok to submit the form
         */
        popupLoginClicked: function () {
            var password = document.getElementById("login_password").value.toString(),
                userName = document.getElementById("login_username").value.toString(),
                errorField = "";

            if (errorField == "" && ! this.isValidUserName(userName)) {
                this.setPopupMessage("modal-login", "Your user name " + userName + " looks bad. Can you try again?", "popupMessageResponseError");
                errorField = "login_username";
            }
            if (errorField == "" && ! this.isValidPassword(password)) {
                this.setPopupMessage("modal-login", "Your password looks bad. Can you try again?", "popupMessageResponseError");
                errorField = "login_password";
            }
            if (errorField != "") {
                $(errorField).removeClass("popup-form-input").addClass("popup-form-input-error");
                document.getElementById(errorField).focus();
            }
            if (errorField == "") {
                document.getElementById("login-form").submit();
            }
            return errorField == ""; // return true to submit form
        },

        /**
         * The submit button on the forgot password popup was clicked. Validate user inputs on the
         * forgot password form before we attempt to submit the request with the server. Will
         * set focus to a field in error.
         *
         * @returns {boolean} true if ok to submit the form
         */
        popupForgotPasswordClicked: function () {
            var email = document.getElementById("forgotpassword_email").value.toString(),
                userName = document.getElementById("forgotpassword_username").value.toString(),
                errorField = "";

            if (errorField == "" && ! this.isValidUserName(userName)) {
                this.setPopupMessage("modal-forgot-password", "Your user name '" + userName + "' looks bad. Can you try again?", "popupMessageResponseError");
                errorField = "forgotpassword_username";
            }
            if (errorField == "" && ! this.isValidEmail(email)) {
                this.setPopupMessage("modal-forgot-password", "Your email " + email + " looks bad. Can you try again?", "popupMessageResponseError");
                errorField = "forgotpassword_email";
            }
            if (errorField != "") {
                $(errorField).removeClass("popup-form-input").addClass("popup-form-input-error");
                document.getElementById(errorField).focus();
            }
            if (errorField == "") {
                document.getElementById("forgot-password-form").submit();
            }
            return errorField == ""; // return true to submit form
        },

        /**
         * The submit button on the forgot password form was clicked. Validate user inputs on the
         * forgot password form before we attempt to submit the request with the server. Will
         * set focus to a field in error.
         *
         * @returns {boolean} true if ok to submit the form
         */
        formForgotPasswordClicked: function () {
            var email = document.getElementById("forgotpassword_email_form").value.toString(),
                userName = document.getElementById("forgotpassword_username_form").value.toString(),
                errorField = "";

            if (errorField == "" && ! this.isValidUserName(userName)) {
                this.setPopupMessage("forgot-password-form", "Your user name '" + userName + "' looks bad. Can you try again?", "popupMessageResponseError");
                errorField = "forgotpassword_username";
            }
            if (errorField == "" && ! this.isValidEmail(email)) {
                this.setPopupMessage("forgot-password-form", "Your email " + email + " looks bad. Can you try again?", "popupMessageResponseError");
                errorField = "forgotpassword_email";
            }
            if (errorField != "") {
                $(errorField).removeClass("popup-form-input").addClass("popup-form-input-error");
                document.getElementById(errorField).focus();
            }
            return errorField == ""; // return true to submit form
        },

        onClickShowPassword: function(element) {
            var showPasswordCheckbox = document.getElementById('register-showpassword'),
                passwordInput = document.getElementById('register-password'),
                icon = document.getElementById('register-showpassword-icon'),
                text = document.getElementById('register-showpassword-text'),
                show = false;

            if (showPasswordCheckbox != null) {
                if (passwordInput == null) {
                    passwordInput = document.getElementById('newPassword');
                }
                show = passwordInput.type == 'text';
                if (show) {
                    showPasswordCheckbox.checked = false;
                    passwordInput.type = 'password';
                    icon.className = 'glyphicon glyphicon-eye-open';
                    text.innerText = 'Show';
                } else {
                    showPasswordCheckbox.checked = true;
                    passwordInput.type = 'text';
                    icon.className = 'glyphicon glyphicon-eye-close';
                    text.innerText = 'Hide';
                }
            }
        },

        /**
         * Single sign-on registration. In this case, the user id comes from a 3rd party network and we need to map that
         * to an new Enginesis user_id. Additional processing/error checking must be handled in the Enginesis callback.
         * @param {object} registrationParameters is a KV object. The keys must match the Enginesis UserLoginCoreg API
         * @param {int} networkId is the network identifier, see Enginesis documentation
         */
        registerSSO: function (registrationParameters, networkId) {
            if (registrationParameters != undefined && registrationParameters != null) {
                unconfirmedNetworkId = networkId;
                enginesisSession.userLoginCoreg(registrationParameters, networkId, null);
            }
        },

        /**
         * Single sign-on login. In this case, the user id comes from a 3rd party network and we need to map that
         * to an existing Enginesis user_id.
         * @param {object} registrationParameters is a KV object. THe keys must match the Enginesis UserLoginCoreg API
         * @param {int} networkId is the network identifier, see Enginesis documentation
         */
        loginSSO: function (registrationParameters, networkId) {
            if (registrationParameters != undefined && registrationParameters != null) {
                unconfirmedNetworkId = networkId;
                enginesisSession.userLoginCoreg(registrationParameters, networkId, null);
            }
        },

        /**
         * This callback supports Facebook's initialization and auto-login, when a page loads and Facebook SDK
         * initializes we end up here to determine if we have a properly logged in user.
         * @param response
         */
        facebookStatusChangeCallback: function (response) {
            if (response.status === 'connected') {
                // Logged into your app and Facebook.
                FB.api('/me', function (response) {
                    // if we get here, the user has approved our app AND they are logged in.
                    // We need to check this state IF a user is not currently logged in, this would indicate they should be logged in
                    // automatically with Facebook
                    unconfirmedNetworkId = 2;
                    console.log('VARYNAPP Successful Facebook login for: ' + response.name + ' (' + response.id + ')');
                    // this.loginSSO(); ???
                });
            }
        },

        /**
         * Function provided to check the login state of the user on a given network.
         * @param networkId
         */
        checkLoginStateSSO: function (networkId) {
            switch (networkId) {
                case 2: // Facebook
                    FB.getLoginStatus(varynApp.facebookStatusChangeCallback);
                    break;
                case 7: // Google
                    break;
                case 11: // Twitter
                    break;
                default:
                    console.log("varynApp.checkLoginStateSSO unsupported network " + networkId);
                    break;
            }
        },

        /**
         * This function sets up the events to monitor a change to the user name in a registration input form so we
         * can ask the server to test if the user name is already in use.
         */
        setupRegisterUserNameOnChangeHandler: function () {
            $('#register-username').on('change', this.onChangeRegisterUserName.bind(this));
            $('#register-username').on('input', this.onChangeRegisterUserName.bind(this));
            $('#register-username').on('propertychange', this.onChangeRegisterUserName.bind(this));
        },

        /**
         * On change handler for the user name field on a registration form.
         * Try to make sure the user name is unique.
         * @param {object} DOM element that is changing.
         * @param {string} DOM id that will receive update of name status either acceptable or unacceptable.
         */
        onChangeRegisterUserName: function (element, domIdImage) {
            var userName;
            if ( ! waitingForUserNameReply && element != null) {
                if (element.target != null) {
                    element = element.target;
                }
                if (domIdImage == null) {
                    domIdImage = $(element).data("target");
                }
                userName = element.value.toString();
                if (varynApp.isChangedUserName(userName)) {
                    if (userName && varynApp.isValidUserName(userName)) {
                        waitingForUserNameReply = true;
                        domImage = domIdImage;
                        enginesisSession.userGetByName(userName, varynApp.onChangeRegisteredUserNameResponse.bind(varynApp));
                    } else {
                        this.setUserNameIsUnique(domIdImage, false);
                    }
                } else {
                    this.setUserNameIsUnique(domIdImage, true);
                }
            }
        },

        onChangeRegisteredUserNameResponse: function (enginesisResponse) {
            var userNameAlreadyExists = false;
            waitingForUserNameReply = false;
            if (enginesisResponse != null && enginesisResponse.fn != null) {
                userNameAlreadyExists = enginesisResponse.results.status.success == "1";
            }
            this.setUserNameIsUnique(domImage, ! userNameAlreadyExists);
            domImage = null;
        },

        /**
         * When we dynamically query the server to determine if the user name is a unique selection
         * use this function to indicate uniqueness result on the form.
         * @param (id} which DOM id we wish to manipulate.
         * @param {bool} true if the name is unique, false if it is taken.
         */
        setUserNameIsUnique: function (id, isUnique) {
            if (id) {
                if (isUnique) {
                    $('#' + id).removeClass('username-is-not-unique').addClass('username-is-unique').css('display', 'inline-block');
                } else {
                    $('#' + id).removeClass('username-is-unique').addClass('username-is-not-unique').css('display', 'inline-block');
                }
            }
        },

        /**
         * Test to check the last status of the user is unique attribute on the registration form. Take care because
         * this depends on that element being properly set and I really don't like this particular solution but
         * going with it for now. TODO: better solution?
         * @param id {string} the element to check (because it is a different id on different forms.)
         * @returns {boolean} true if the name is unique, false if it is taken.
         */
        testUserNameIsUnique: function (id) {
            var isUnique = $('#' + id).hasClass('username-is-unique');
            return isUnique;
        },

        /**
         * When a response to one of our form submissions returns from the server we handle it here.
         * If the result is a success we close the popup after a delay to confirm with the user the
         * successful status. If the result is an error we display the error message.
         */
        handleNewsletterServerResponse: function (succeeded, errorMessage) {
            if (succeeded == 1) {
                this.setPopupMessage("modal-subscribe", "You are subscribed - Thank you!", "popupMessageResponseOK");
                window.setTimeout(this.hideSubscribePopup.bind(this), 2500);
            } else {
                this.setPopupMessage("modal-subscribe", "Service reports an error: " + errorMessage, "popupMessageResponseError");
            }
        },

        /**
         * makeGameModule will generate the HTML for a standard game promo module.
         * @param gameId
         * @param gameName
         * @param gameDescription
         * @param gameImg
         * @param gameLink
         * @returns {string} the HTML
         */
        makeGameModule: function (gameId, gameName, gameDescription, gameImg, gameLink) {
            var innerHtml,
                title;

            title = "Play " + gameName + " Now!";
            innerHtml = "<div class=\"gameModule thumbnail\">";
            innerHtml += "<a href=\"" + gameLink + "\" title=\"" + title + "\"><img class=\"thumbnail-img\" src=\"" + gameImg + "\" alt=\"" + gameName + "\"/></a>";
            innerHtml += "<div class=\"gameModuleInfo\"><a href=\"" + gameLink + "\" class=\"btn btn-md btn-success\" role=\"button\" title=\"" + title + "\" alt=\"" + title + "\">Play Now!</a></div>";
            innerHtml += "<div class=\"caption\"><a class=\"gameTitle\" href=\"" + gameLink + "\" title=\"" + title + "\"><h3>" + gameName + "</h3></a><p class=\"gamedescription\">" + gameDescription + "</p>";
            innerHtml += "</div></div>";
            return innerHtml;
        },

        /**
         * makeAdModule will generate the HTML for a standard ad module.
         * @returns {string} the HTML
         */
        makeAdModule: function () {
            var innerHtml;

            innerHtml = '<div class="gameModule thumbnail"><div class="row"><div class="col-sm-4 col-md-2 adContainer412"><div id="boxAd300" class="ad300x412">';
            innerHtml += '<iframe src="/common/adModule.html" frameborder="0" scrolling="no" style="width: 300px; height: 412px; overflow: hidden; z-index: 9999; left: 0px; bottom: 0px; display: inline-block;"></iframe></div></div></div></div>';
            return innerHtml;
        },

        /**
         * makeCouponModule will generate the HTML for a standard Coupons.com module.
         * @returns {string} the HTML
         */
        makeCouponModule: function () {
            var innerHtml;

            innerHtml = '<div class="gameModule thumbnail"><div class="row"><div class="col-sm-4 col-md-2 adContainer412"><div id="boxAd300" class="ad300x412">';
            innerHtml += '<iframe src="/common/couponModule.html" frameborder="0" scrolling="no" style="width: 300px; height: 412px; overflow: hidden; z-index: 9999; left: 0px; bottom: 0px; display: inline-block;"></iframe></div></div></div></div>';
            return innerHtml;
        },

        /**
         * makePromoModule will generate the HTML for a single standard promo module for the carousel.
         * @param isActive
         * @param backgroundImg
         * @param titleText
         * @param altText
         * @param promoText
         * @param link
         * @param callToActionText
         * @returns {string}
         */
        makePromoModule: function (isActive, backgroundImg, titleText, altText, promoText, link, callToActionText) {
            var innerHtml,
                isActiveItem;

            if (isActive) {
                isActiveItem = " active";
            } else {
                isActiveItem = "";
            }
            innerHtml = "<div class=\"carousel-inner\" role=\"listbox\"><div class=\"item" + isActiveItem + "\">";
            innerHtml += "<img src=\"" + backgroundImg + +"\" alt=\"" + altText + "\">";
            innerHtml += "<div class=\"container\"><div class=\"carousel-caption\"><h1>" + titleText + "</h1>";
            innerHtml += "<p>" + promoText + "</p>";
            innerHtml += "<p><a class=\"btn btn-lg btn-primary\" href=\"" + link + "\" role=\"button\">" + callToActionText + "</a></p>";
            innerHtml += "</div></div></div>"
            return innerHtml;
        },

        /**
         * makePromoIndicators generates the HTML for all promo indicators used in the carousel.
         * @param numberOfPromos
         * @param activeIndicator
         * @returns {string}
         */
        makePromoIndicators: function (numberOfPromos, activeIndicator) {
            var innerHtml = "<ol class=\"carousel-indicators\">",
                activeClass,
                i;

            if (activeIndicator === undefined || activeIndicator == null || activeIndicator < 0 || activeIndicator >= numberOfPromos) {
                activeIndicator = 0;
            }
            for (i = 0; i < numberOfPromos; i ++) {
                if (i == activeIndicator) {
                    activeClass = " class=\"active\""
                }
                innerHtml += "<li data-target=\"#PromoCarousel\" data-slide-to=\"" + i + "\"" + activeClass + "></li>";
            }
            innerHtml += "</ol>";
            return innerHtml;
        },

        /**
         * gameListGamesResponse handles the server reply from GameListListGames and generates the game modules.
         * @param results {object}: the sever response object
         * @param elementId {string}: element to insert game modules HTML
         * @param maxItems {int}: no more than this number of games
         * @param sortList {boolean}: true to sort the list of games alphabetically by title
         */
        gameListGamesResponse: function (results, elementId, maxItems, sortList) {
            // results is an array of games
            var i,
                adsShownCounter,
                gameItem,
                gamesContainer = document.getElementById(elementId),
                newDiv,
                itemHtml,
                countOfGamesShown,
                baseURL = document.location.protocol + "//" + enginesisSession.serverBaseUrlGet() + "/games/",
                isTouchDevice = enginesisSession.isTouchDevice(),
                adsDisplayPositions = new Array(3, 21, 41, 60, 80, 100),
                numberOfAdSpots;

            if (results != null && results.length > 0 && gamesContainer != null) {
                if (sortList == null) {
                    sortList = false;
                }
                if (sortList) {
                    results.sort(compareTitle);
                }
                if (maxItems == null || maxItems < 1) {
                    maxItems = results.length;
                }
                countOfGamesShown = 0;
                adsShownCounter = 0;
                numberOfAdSpots = adsDisplayPositions.length;
                for (i = 0; i < results.length && countOfGamesShown < maxItems; i ++) {
                    gameItem = results[i];
                    if (isTouchDevice && ! (gameItem.game_plugin_id == "10" || gameItem.game_plugin_id == "9")) {
                        continue; // only show HTML5 or embed games on touch devices
                    }
                    countOfGamesShown ++;
                    itemHtml = this.makeGameModule(gameItem.game_id, gameItem.title, gameItem.short_desc, baseURL + gameItem.game_name + "/images/300x225.png", "/play.php?gameid=" + gameItem.game_id);
                    newDiv = document.createElement('div');
                    newDiv.className = "col-sm-6 col-md-4";
                    newDiv.innerHTML = itemHtml;
                    gamesContainer.appendChild(newDiv);
                    if (adsShownCounter < numberOfAdSpots && i + 1 == adsDisplayPositions[adsShownCounter]) {
                        // Time to show an ad module
                        adsShownCounter ++;
                        newDiv = document.createElement('div');
                        newDiv.className = "col-sm-6 col-md-4";
                        if (adsShownCounter == 1) {
                            newDiv.innerHTML = this.makeCouponModule();
                        } else {
                            newDiv.innerHTML = this.makeAdModule();
                        }
                        newDiv.id = 'AdSpot' + adsShownCounter;
                        gamesContainer.appendChild(newDiv);
                    }
                }
            } else {
                // no games!
            }
        },

        /**
         *
         * @param results
         */
        promotionItemListResponse: function (results) {
            // results is an array of promoted items
            var i;
            if (results != null && results.length > 0) {
                for (i = 0; i < results.length; i ++) {

                }
            } else {
                // no promotions!
            }
        },

        /**
         * Callback to handle responses from Enginesis.
         * @param enginesisResponse
         */
        enginesisCallBack: function (enginesisResponse) {
            var succeeded,
                errorMessage,
                results,
                fillDiv,
                listId;

            if (enginesisResponse != null && enginesisResponse.fn != null) {
                results = enginesisResponse.results;
                succeeded = results.status.success;
                errorMessage = results.status.message;
                if (succeeded == 0) {
                    console.log("Enginesis service error " + errorMessage + " from fn " + enginesisResponse.fn);
                }
                switch (enginesisResponse.fn) {
                    case "NewsletterAddressAssign":
                        this.handleNewsletterServerResponse(succeeded, errorMessage);
                        break;

                    case "PromotionItemList":
                        if (succeeded == 1) {
                            this.promotionItemListResponse(results.result);
                        }
                        break;

                    case "GameListListGames":
                        if (succeeded == 1) {
                            if (results.passthru !== undefined && results.passthru.game_list_id !== undefined) {
                                listId = results.passthru.game_list_id;
                                if (listId == siteConfiguration.gameListIdTop) {
                                    fillDiv = "HomePageTopGames";
                                } else {
                                    fillDiv = "HomePageNewGames";
                                }
                            } else {
                                fillDiv = "HomePageTopGames";
                            }
                            this.gameListGamesResponse(results.result, fillDiv, null, false);
                        }
                        break;

                    case "UserLoginCoreg":
                        var userInfo = results.result.row;
                        if (userInfo) {
                            // TODO: User is now logged in, refresh the page and the page refresh should be able to pick up the logged in state.
                            document.location.href = "/profile.php?network_id=" + getNetworkId();
                        } else {
                            // TODO: User is not logged in, we should display an error message.
                        }
                        break;

                    case 'RegisteredUserRequestPasswordChange':
                        if (succeeded == 1) {
                            varynApp.showInfoMessagePopup("Change Password", "A request to change your password has been sent to the email address on file. Please continue the password reset process from the link provided there.", 0);
                        } else {
                            if (results.status.extended_info != undefined) {
                                errorMessage += ' ' + results.status.extended_info;
                            }
                            varynApp.showInfoMessagePopup("Change Password", "There was a system issue while trying to reset your password: " + errorMessage, 0);
                        }
                        break;

                    default:
                        console.log("Unhandled Enginesis reply for " + enginesisResponse.fn);
                        break;
                }
            }
        },

        runUnitTests: function() {
            console.log('enginesisSession.versionGet: ' + enginesisSession.versionGet());
            console.log('enginesisSession.getRefreshToken: ' + enginesisSession.getRefreshToken());
            console.log('enginesisSession.getGameImageURL: ' + enginesisSession.getGameImageURL('MatchMaster3000', 0, 0, null));
            console.log('enginesisSession.getDateNow: ' + enginesisSession.getDateNow());
        }
    }
};

/**
 * Determine full extent of the window available to the application
 * Extra Warning: this function must be global (on window object) because we will refer to it globally later.
 * @param container {object} DOM element that extends the full width and height of the page (use body unless you have a
 * full size div container.)
 * @returns {object} {fullWidth, fullHeight}
 */
// container = "gameContainer";
function getDocumentSize (container) {
    var gameContainerDiv = document.getElementById(container),
        result = {fullWidth: document.documentElement.clientWidth, fullHeight: document.documentElement.clientHeight},
        enginesisSession = varyn.getEnginesisSession();

    if (gameContainerDiv != null) {
        result.containerWidth = gameContainerDiv.clientWidth;
        result.containerHeight = gameContainerDiv.clientHeight;
    }
    if (enginesisSession != null) {
        result.gameWidth = enginesisSession.gameWidth;
        result.gameHeight = enginesisSession.gameHeight;
        result.gameAspectRatio = enginesisSession.gameAspectRatio;
    }
    return result;
}
