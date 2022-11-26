/**
 * Page specific functionality on the Varyn Profile page. This mostly handles user login and user profile edit.
 */
var varynProfilePage = function (varynApp, siteConfiguration) {
    "use strict";

    var enginesisSession = varynApp.getEnginesisSession();
    var errorFieldId = "";
    var inputFocusId = "";

    function setElementValue(domId, value) {
        const element = document.getElementById(domId);
        if (element != null) {
            element.value = value;
        }
    }

    function getElementValue(domId) {
        const element = document.getElementById(domId);
        if (element != null) {
            return element.value;
        }
        return null;
    }

    function toggleClass(domId, classToRemove, classToAdd) {
        const domElement = document.getElementById(domId);
        if (domElement != null) {
            if (classToRemove) {
                domElement.classList.remove(classToRemove);
            }
            if (classToAdd) {
                domElement.classList.add(classToAdd);
            }
        }
    }

    /**
     * Setup the security input fields only the first time the tab is visited
     */
    function securityFieldsPopulate () {
        var securityInfo = commonUtilities.loadObjectWithKey('VarynSecurityInfo');
        if (securityInfo == null) {
            enginesisSession.registeredUserSecurityGet(enginesisCallBack);
        } else {
            setElementValue("register-form-new-password", "");
            setElementValue("register-form-question", securityInfo['security_question']);
            setElementValue("register-form-answer", securityInfo['security_answer']);
            // @todo: show tab $('a[data-toggle="tab"]').off('shown.bs.tab');
        }
    }

    /**
     * Callback to handle responses from Enginesis.
     * @param enginesisResponse
     */
    function enginesisCallBack (enginesisResponse) {
        var succeeded,
            errorMessage,
            results;

        if (enginesisResponse != null && enginesisResponse.fn != null) {
            results = enginesisResponse.results;
            succeeded = results.status.success;
            errorMessage = results.status.message;
            switch (enginesisResponse.fn) {
                case 'GameListListGames':
                    if (succeeded == 1) {
                        varynApp.gameListGamesResponse(results.result, "ProfilePageTopGames", null, "title");
                    }
                    break;

                case 'RegisteredUserSecurityGet':
                    if (succeeded == 1) {
                        commonUtilities.saveObjectWithKey('VarynSecurityInfo', results.result[0]);
                        securityFieldsPopulate();
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

                case 'UserFavoriteGamesList':
                    if (succeeded == 1) {
                        var gamesShown = varynApp.gameListFavoriteGamesResponse(results.result, "FavoriteGames", null, "title");
                        var favoriteGamesDiv = document.getElementById("FavoriteGamesContainer");
                        if (favoriteGamesDiv != null) {
                            favoriteGamesDiv.style.display = gamesShown > 0 ? "block" : "none";
                        }
                    }
                    break;

                default:
                    break;
            }
        }
    }

    return {
        pageLoaded: function (pageViewParameters) {
            if (pageViewParameters.errorFieldId !== undefined) {
                errorFieldId = pageViewParameters.errorFieldId;
            }
            if (pageViewParameters.inputFocusId !== undefined) {
                inputFocusId = pageViewParameters.inputFocusId;
            }
            var pageParams = commonUtilities.queryStringToObject(null);
            if (pageParams != null && pageParams.action != undefined) {
                if (pageParams.action == 'update') {
                    // TODO: Make a copy of the user data so we can identify which fields changed, save in localStorage.
                    this.onPageLoadSetTabEvents();
                }
            }
            if (!enginesisSession.isUserLoggedIn()) {
                this.setupUserLogin();
            } else {
                enginesisSession.userFavoriteGamesList(this.enginesisCallBack);
            }
            if (varynApp.isLogout()) {
                this.enableLoginButtons(false);
            }
            this.setupUserNameChangeHandler();
            enginesisSession.gameListListGames(siteConfiguration.gameListIdTop, this.enginesisCallBack);
            this.onPageLoadSetFocus();
            window.onunload = this.updateCleanup.bind(this);
        },

        setupUserLogin: function() {
            if (document.getElementById('profile-forgot-password')) {
                document.getElementById('profile-forgot-password').addEventListener('click', this.forgotPassword.bind(this));
            }
            if (document.getElementById('facebook-connect-button')) {
                document.getElementById('facebook-connect-button').addEventListener('click', this.loginFacebook.bind(this));
            }
            if (document.getElementById('twitter-signin-button')) {
                document.getElementById('twitter-signin-button').addEventListener('click', this.loginTwitter.bind(this));
            }
            ssoGoogle.setLoginCallback(varynApp.registerSSO.bind(varynApp)); // Google button is attached in ssoGoogle.init()
        },

        /**
         * Use this function to enable/disable all the login buttons. Useful to block or unblock the buttons
         * as a group while some process that takes time is working or completes.
         * @param enableFlag
         * @returns {*}
         */
        enableLoginButtons: function(enableFlag) {
            if (typeof enableFlag === 'undefined' || enableFlag === null) {
                enableFlag = true;
            }
            var isDisabled = ! enableFlag;
            if (document.getElementById('profile-register-now')) {
                document.getElementById('profile-register-now').disabled = isDisabled;
            }
            if (document.getElementById('login-button')) {
                document.getElementById('login-button').disabled = isDisabled;
            }
            if (document.getElementById('facebook-connect-button')) {
                document.getElementById('facebook-connect-button').disabled = isDisabled;
            }
            if (document.getElementById('twitter-signin-button')) {
                document.getElementById('twitter-signin-button').disabled = isDisabled;
            }
            if (document.getElementById('gapi-signin-button')) {
                document.getElementById('gapi-signin-button').disabled = isDisabled;
            }
            return enableFlag;
        },

        setupUserNameChangeHandler: function () {
            var registerFormUserName = document.getElementById("register-form-username");
            if (registerFormUserName != null) {
                registerFormUserName.addEventListener("change", varynApp.onChangeRegisterUserName.bind(varynApp));
                registerFormUserName.addEventListener("input", varynApp.onChangeRegisterUserName.bind(varynApp));
                registerFormUserName.addEventListener("propertychange", varynApp.onChangeRegisterUserName.bind(varynApp));
                varynApp.setupRegisterUserNameOnChangeHandler();
                varynApp.onChangeRegisterUserName(registerFormUserName, 'register-username-unique'); // in case field is pre-populated
                var emailFormField = document.getElementById("register-email");
                if (emailFormField != null) {
                    emailFormField.addEventListener("change", varynApp.onChangeEmail.bind(varynApp));
                    emailFormField.addEventListener("input", varynApp.onChangeEmail.bind(varynApp));
                }
            }
        },

        onClickRegisterShowPassword: function(event) {
            var passwordInput = document.getElementById("register-form-password");
            var icon = document.getElementById("register-form-show-password-icon");
            var text = document.getElementById("register-form-show-password-text");
            var show = icon.classList.contains("iconEyeSlash");

            if (show) {
                passwordInput.type = 'password';
                icon.className = 'iconEye';
                text.innerText = 'Show';
            } else {
                passwordInput.type = 'text';
                icon.className = 'iconEyeSlash';
                text.innerText = 'Hide';
            }
        },

        onClickLoginShowPassword: function(event) {
            var passwordInput = document.getElementById("login-form-password");
            var icon = document.getElementById("login-form-show-password-icon");
            var text = document.getElementById("login-form-show-password-text");
            var show = icon.classList.contains("iconEyeSlash");

            if (show) {
                passwordInput.type = 'password';
                icon.className = 'iconEye';
                text.innerText = 'Show';
            } else {
                passwordInput.type = 'text';
                icon.className = 'iconEyeSlash';
                text.innerText = 'Hide';
            }
        },

        onPageLoadSetFocus: function () {
            var element;

            if (inputFocusId != '') {
                element = document.getElementById(inputFocusId);
                if (element != null) {
                    element.focus();
                }
            }
            if (errorFieldId != '') {
                toggleClass(errorFieldId, "popup-form-input", "popup-form-input-error");
            }
        },

        onPageLoadSetTabEvents: function () {
            // @todo: learn how to set BS tabs from JavaScript
            return;
            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                var whichTab = e.target.id;
                if (whichTab == 'secure-info') {
                    // the first time we show this tab we need to get the secure info
                    securityFieldsPopulate();
                }
            })
        },

        loginValidation: function () {
            var userName = getElementValue("login-form-username"); // $("input[name=login-form-username]").val(),
            var password = getElementValue("login-form-password");
            var errorMessage = "";
            var errorField = "";

            toggleClass("login-form-username", "popup-form-input-error", "popup-form-input");
            toggleClass("login-form-password", "popup-form-input-error", "popup-form-input");
            varynApp.showErrorMessage("", "");
            if (errorMessage == "" && ! varynApp.isValidUserName(userName)) {
                errorMessage = "User name is not acceptable. Please enter your user name.";
                errorField = "login-form-username";
            }
            if (errorMessage == "" && ! varynApp.isValidPassword(password)) {
                errorMessage = "Password is not acceptable. Please retry your password.";
                errorField = "login-form-password";
            }
            if (errorMessage == "") {
                // good enough to send to the server for more validation
                document.getElementById('login').submit();
            } else {
                varynApp.showErrorMessage(errorMessage, errorField);
            }
        },

        /**
         * Use this method to validate a new registration. When there is an error this function
         * will update the error form field and set the focus.
         * @returns {boolean} Returns true when form is acceptable, false if there is an error.
         */
        registerFormValidation: function () {
            var userName = getElementValue("register-form-username");
            var password = getElementValue("register-form-password");
            var email = getElementValue("register-form-email");
            var dob = getElementValue("register-form-dob");
            var agreement = getElementValue("register-form-agreement") > 1;
            var errorMessage = "";
            var errorField = "";

            toggleClass("register-form-username", "popup-form-input-error", "popup-form-input");
            toggleClass("register-form-password", "popup-form-input-error", "popup-form-input");
            toggleClass("register-form-email", "popup-form-input-error", "popup-form-input");
            varynApp.showErrorMessage("", "");
            if (errorMessage == "" && ! varynApp.isValidUserName(userName)) {
                errorMessage = "User name is not acceptable. Please enter your user name.";
                errorField = "register-form-username";
            }
            if (errorMessage == "" && ! varynApp.isValidPassword(password)) {
                errorMessage = "Password is not acceptable. Please retry your password.";
                errorField = "register-form-password";
            }
            if (errorMessage == "" && ! varynApp.isValidEmail(email)) {
                errorMessage = "Email " + email + " doesn't look right. Please enter a proper email address.";
                errorField = "register-form-email";
            }
            if (errorField == "" && ! varynApp.isValidDateOfBirth(dob)) {
                errorMessage = "You must be at least 13 years of age to register an account on this site.";
                errorField = "register-form-dob";
            }
            if (errorField == "" && ! agreement) {
                errorMessage = "You must agree with the terms of service or you cannot register.";
                errorField = "register-form-agreement";
            }
            if (errorMessage != "") {
                varynApp.showErrorMessage(errorMessage, errorField);
            }
            return errorMessage == "";
        },

        /**
         * Use this method to validate an existing registration. When there is an error this function
         * will update the error form field and set the focus.
         * @returns {boolean} Returns true when form is acceptable, false if there is an error.
         */
        updateFormValidation: function () {
            var userName = getElementValue("register-form-username");
            var email = getElementValue("register-form-email");
            var dob = getElementValue("register-form-dob");
            var errorMessage = "";
            var errorField = "";

            toggleClass("register-form-username", "popup-form-input-error", "popup-form-input");
            toggleClass("register-form-email", "popup-form-input-error", "popup-form-input");
            varynApp.showErrorMessage("", "");
            if (errorMessage == "" && ! varynApp.isValidUserName(userName)) {
                errorMessage = "User name is not acceptable. Please enter your user name.";
                errorField = "register-form-username";
            }
            if (errorMessage == "" && varynApp.isChangedUserName(userName) && ! varynApp.testUserNameIsUnique('register-user-name-unique')) {
                errorMessage = "User name is in use by someone else. Please select a unique user name.";
                errorField = "register-form-username";
            }
            if (errorMessage == "" && ! varynApp.isValidEmail(email)) {
                errorMessage = "Email " + email + " doesn't look right. Please enter a proper email address.";
                errorField = "register-form-email";
            }
            if (errorField == "" && ! varynApp.isValidDateOfBirth(dob)) {
                errorMessage = "You must be at least 13 years of age to register an account on this site.";
                errorField = "register-form-dob";
            }
            // TODO:
            // Location, tagline, about-me all are valid strings, no crazy html crap (b/i/strong emojis are ok)
            if (errorMessage != "") {
                varynApp.showErrorMessage(errorMessage, errorField);
            }
            return errorMessage == "";
        },

        logout: function () {
            window.location.href = "/profile/?action=logout";
        },

        logoutComplete: function() {
            this.enableLoginButtons(true);
        },

        cancelUpdate: function (event) {
            this.updateCleanup();
            window.location.href = "/profile/?action=cancel";
            event.preventDefault();
            return false;
        },

        startUpdate: function () {
            this.updateCleanup();
            window.location.href = "/profile/?action=update";
        },

        updateCleanup: function () {
            commonUtilities.removeObjectWithKey('VarynSecurityInfo');
        },

        sendPasswordResetRequest: function () {
            var sent = enginesisSession.registeredUserRequestPasswordChange(this.enginesisCallBack);
            return ! sent;
        },

        forgotPassword: function () {
            varynApp.showForgotPasswordPopup(true);
        },

        /**
         * Call enginesis API to reset the password for the current logged in user.
         */
        changePassword: function () {
            var userInfo = varynApp.getVarynUserInfo(),
                errorMessage = '',
                error = false;

            if (userInfo != null) {
                error = this.sendPasswordResetRequest();
                if (error) {
                    errorMessage = 'We are not able to generate a password reset on your behalf. Please contact our support channel.';
                }
            } else {
                error = true;
                errorMessage = 'You must be logged in to reset your password.';
            }
            if (error) {
                varynApp.showInfoMessagePopup('Change Password', errorMessage, 0);
            }
        },

        showRegistrationPopup: function () {
            varynApp.showRegistrationPopup(true);
        },

        popupRegistrationClicked: function () {
            varynApp.showRegistrationPopup(false);
        },

        /**
         * When you request a Facebook login (e.g. click the Login to Facebook button) we use Facebook's SDK to
         * determine if we have a logged in user. If the user is logged in we need to refresh the page so the
         * Enginesis/PHP code can pick it up. If the user does not complete a login then do nothing.
         * @returns {boolean}
         */
        loginFacebook: function () {
            varynApp.trackEvent('login', 'sso', 'facebook');
            ssoFacebook.login(varynApp.ssoFacebookParameters(), varynApp.registerSSO.bind(varynApp));
            return true;
        },

        /**
         * When you request a Twitter login (e.g. click the Login to Twitter button) we redirect to our
         * oauth page and handle it with PHP. When the user returns a cookie should be available to pick up
         * the Twitter access token.
         * @returns {boolean}
         */
        loginTwitter: function () {
            varynApp.trackEvent('login', 'sso', 'twitter');
            ssoTwitter.login(varynApp.ssoTwitterParameters());
            return true;
        },

        /**
         * When you request Apple login (e.g. click the Sign in with Apple button) we redirect to our
         * oauth page and handle it with PHP. When the user returns a cookie should be available to pick up
         * the Apple access token.
         * @returns {boolean}
         */
        loginApple: function () {
            varynApp.trackEvent('login', 'sso', 'apple');
            ssoApple.login(varynApp.ssoAppleParameters());
            return true;
        }
    }
};