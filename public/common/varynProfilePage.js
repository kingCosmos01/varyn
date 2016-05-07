/**
 * Page specific functionality on the Varyn Profile page.
 */
var varynProfilePage = function (varynApp, siteConfiguration) {
    "use strict";

    var enginesisSession = varynApp.getEnginesisSession(),
        errorFieldId = "",
        inputFocusId = "";


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
                case "GameListListGames":
                    if (succeeded == 1) {
                        varynApp.gameListGamesResponse(results.result, "ProfilePageTopGames", null, false);
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
            $('#register_form_username').on('change', varynApp.onChangeRegisterUserName);
            $('#register_form_username').on('input', varynApp.onChangeRegisterUserName);
            $('#register_form_username').on('propertychange', varynApp.onChangeRegisterUserName);
            varynApp.setupRegisterUserNameOnChangeHandler();
            $('#profile_forgot_password').click(this.forgotPassword);
            $('#facebook-connect-button').click(this.loginFacebook);
            $('#gapi-signin-button').click(this.loginGoogle);
            $('#twitter-signin-button').click(this.loginTwitter);
            varynApp.onChangeRegisterUserName($('#register_form_username').get(0), 'register_user_name_unique'); // in case field is pre-populated
            enginesisSession.gameListListGames(siteConfiguration.gameListIdTop, this.enginesisCallBack);
            this.onPageLoadSetFocus();
            // Google+ login button support
/*            gapi.signin2.render('g-signin2', {
                'scope': 'https://www.googleapis.com/auth/plus.login',
                'width': 200,
                'height': 50,
                'longtitle': true,
                'theme': 'dark',
                'onsuccess': this.onGapiSuccess,
                'onfailure': this.onGapiFailure
            });
            */
        },

        onPageLoadSetFocus: function () {
            if (inputFocusId != "") {
                document.getElementById(inputFocusId).focus();
            }
            if (errorFieldId != "") {
                $('#' + errorFieldId).removeClass("popup-form-input").addClass("popup-form-input-error");
            }
        },

        loginValidation: function () {
            var userName = $("input[name=login_form_username]").val(),
                password = $("input[name=login_form_password]").val(),
                errorMessage = "",
                errorField = "";

            $("#login_form_username").removeClass("popup-form-input-error").addClass("popup-form-input");
            $("#login_form_password").removeClass("popup-form-input-error").addClass("popup-form-input");
            varynApp.showErrorMessage("", "");
            if (errorMessage == "" && ! varynApp.isValidUserName(userName)) {
                errorMessage = "User name is not acceptable. Please enter your user name.";
                errorField = "login_form_username";
            }
            if (errorMessage == "" && ! varynApp.isValidPassword(password)) {
                errorMessage = "Password is not acceptable, at least 4 characters. Please retry your password.";
                errorField = "login_form_password";
            }
            if (errorMessage == "") {
                // good enough to send to the server for more validation
                document.getElementById('login').submit();
            } else {
                varynApp.showErrorMessage(errorMessage, errorField);
            }
        },

        registerFormValidation: function () {
            var userName = $("input[name=register_form_username]").val(),
                password = $("input[name=register_form_password]").val(),
                email = $("input[name=register_form_email]").val(),
                dob = $("input[name=register_form_dob]").val(),
                agreement = $("input[name=register_form_agreement]").prop('checked'),
                captcha = $("input[name=register_form_captcha]").val(),
                errorMessage = "",
                errorField = "";

            $("#register_form_username").removeClass("popup-form-input-error").addClass("popup-form-input");
            $("#register_form_password").removeClass("popup-form-input-error").addClass("popup-form-input");
            $("#register_form_email").removeClass("popup-form-input-error").addClass("popup-form-input");
            varynApp.showErrorMessage("", "");
            if (errorMessage == "" && ! isValidUserName(userName)) {
                errorMessage = "User name is not acceptable. Please enter your user name.";
                errorField = "register_form_username";
            }
            if (errorMessage == "" && ! isValidPassword(password)) {
                errorMessage = "Password is not acceptable, at least 4 characters. Please retry your password.";
                errorField = "register_form_password";
            }
            if (errorMessage == "" && ! isValidEmail(email)) {
                errorMessage = "Email " + email + " doesn't look right. Please enter a proper email address.";
                errorField = "register_form_email";
            }
            if (errorField == "" && ! isValidDateOfBirth(dob)) {
                errorMessage = "You must be at least 13 years of age to register an account on this site.";
                errorField = "register_form_dob";
            }
            if (errorField == "" && ! agreement) {
                errorMessage = "You must agree with the terms of service or you cannot register.";
                errorField = "register_form_agreement";
            }
            if (errorField == "" && captcha.trim().length < 3) {
                errorMessage = "Please answer the human test. Can you try again?";
                errorField = "register_form_captcha";
            }
            if (errorMessage != "") {
                varynApp.showErrorMessage(errorMessage, errorField);
            }
            return errorMessage == "";
        },

        logout: function () {
            window.location.href = "/profile.php?action=logout";
        },

        startUpdate: function () {
            window.location.href = "/profile.php?action=update";
        },

        forgotPassword: function () {
            varynApp.showForgotPasswordPopup(true);
        },

        showRegistrationPopup: function () {
            varynApp.showRegistrationPopup(true);
        },

        popupRegistrationClicked: function () {
            varynApp.showRegistrationPopup(false);
        },

        onGapiSuccess: function (googleUser) {

        },

        onGapiFailure: function (error) {

        },

        loginFacebook: function () {
            FB.login(function(response) {
                var registrationParameters = {};
                if (response.authResponse) {
                    FB.api('/me', 'get', {fields: 'id,name,email,gender'}, function(response) {
                        registrationParameters.networkId = 2;
                        registrationParameters.userName = '';
                        registrationParameters.realName = response.name;
                        registrationParameters.emailAddress = response.email;
                        registrationParameters.siteUserId = response.id;
                        registrationParameters.gender = response.gender.substring(0, 1) == 'm' ? 'M' : 'F';
                        registrationParameters.dob = commonUtilities.MySQLDate();
                        registrationParameters.scope = '';
                        varynApp.registerSSO(registrationParameters, registrationParameters.networkId);
                    });
                } else {
                    console.log('User cancelled login or did not fully authorize.');
                }
            }, {scope: 'email', return_scopes: true});
            return false;
        },

        /**
         * Using this function to fake a login-response from the network service only so we can test our code.
         * @returns {boolean}
         */
        loginFacebookFake: function () {
            var response = {id: "726468316", name: "John Foster", email: "jfoster@acm.org", gender: "male"};
            var registrationParameters = {};

            registrationParameters.networkId = 2;
            registrationParameters.userName = '';
            registrationParameters.realName = response.name;
            registrationParameters.emailAddress = response.email;
            registrationParameters.siteUserId = response.id;
            registrationParameters.gender = response.gender.substring(0, 1) == 'm' ? 'M' : 'F';
            registrationParameters.dob = commonUtilities.MySQLDate();
            registrationParameters.scope = 'email';
            varynApp.registerSSO(registrationParameters, registrationParameters.networkId);
            return false;
        },

        loginGoogle: function () {
            var registrationParameters = {};
            registrationParameters.networkId = 7;
            registrationParameters.userName = '';
            registrationParameters.realName = 'Google User';
            registrationParameters.emailAddress = 'Google email';
            registrationParameters.siteUserId = 'Google user-id';
            registrationParameters.gender = 'F';
            registrationParameters.dob = commonUtilities.MySQLDate();
            registrationParameters.scope = '';
            alert('We are working on Google + login');
            varynApp.registerSSO(registrationParameters, registrationParameters.networkId);
            return false;
        },

        loginTwitter: function () {
            var registrationParameters = {};
            registrationParameters.networkId = 11;
            registrationParameters.userName = '';
            registrationParameters.realName = 'twitter User';
            registrationParameters.emailAddress = 'twitter email';
            registrationParameters.siteUserId = 'twitter user-id';
            registrationParameters.gender = 'F';
            registrationParameters.dob = commonUtilities.MySQLDate();
            registrationParameters.scope = '';
            alert('We are working on Twitter login');
            varynApp.registerSSO(registrationParameters, registrationParameters.networkId);
            return false;
        }
    }
};