/**
 * Page specific functionality on the Varyn Profile page.
 */
var varynProfilePage = function (varynApp, siteConfiguration) {
    "use strict";

    var enginesisSession = varynApp.getEnginesisSession(),
        errorFieldId = "",
        inputFocusId = "";


    return {
        pageLoaded: function (pageViewParameters) {
            if (pageViewParameters.errorFieldId !== undefined) {
                this.errorFieldId = pageViewParameters.errorFieldId;
            }
            if (pageViewParameters.inputFocusId !== undefined) {
                this.inputFocusId = pageViewParameters.inputFocusId;
            }
            $('#register_form_username').on('change', varynApp.onChangeRegisterUserName);
            $('#register_form_username').on('input', varynApp.onChangeRegisterUserName);
            $('#register_form_username').on('propertychange', varynApp.onChangeRegisterUserName);
            varynApp.setupRegisterUserNameOnChangeHandler();
            gapi.signin2.render('g-signin2', {
                'scope': 'https://www.googleapis.com/auth/plus.login',
                'width': 200,
                'height': 50,
                'longtitle': true,
                'theme': 'dark',
                'onsuccess': this.onGapiSuccess,
                'onfailure': this.onGapiFailure
            });
            $('#profile_forgot_password').click(this.forgotPassword);
            $('#facebook-connect-button').click(this.loginFacebook);
            varynApp.onChangeRegisterUserName($('#register_form_username').get(0), 'register_user_name_unique'); // in case field is pre-populated
            this.onPageLoadSetFocus();
//        showSubscribePopup();
//        showLoginPopup(true);
//        showRegistrationPopup(true);
        },

        onPageLoadSetFocus: function () {
            if (this.inputFocusId != "") {
                document.getElementById(this.inputFocusId).focus();
            }
            if (this.errorFieldId != "") {
                $('#' + this.errorFieldId).removeClass("popup-form-input").addClass("popup-form-input-error");
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

        onGapiSuccess: function (googleUser) {

        },

        onGapiFailure: function (error) {

        },

        logout: function () {
            alert("You are logged OUT");
        },

        forgotPassword: function () {
            showForgotPasswordPopup(true);
        },

        showRegistrationForm: function () {
            showRegistrationPopup(true);
        },

        popupRegistrationClicked: function () {
            showRegistrationPopup(false);
        },

        loginFacebook: function () {
            fbLogin('/facebook/endpoints/connect_fb.php');
            return false;
        },

        register: function () {

        }
    }
};