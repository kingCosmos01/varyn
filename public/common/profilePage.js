/**
 * Page specific functionality on the Profile page.
 */
define(function () {
    return {
        init: function () {
            $('#register_form_username').on('change', onChangeRegisterUserName);
            $('#register_form_username').on('input', onChangeRegisterUserName);
            $('#register_form_username').on('propertychange', onChangeRegisterUserName);
            setupRegisterUserNameOnChangeHandler();
            gapi.signin2.render('g-signin2', {
                'scope': 'https://www.googleapis.com/auth/plus.login',
                'width': 200,
                'height': 50,
                'longtitle': true,
                'theme': 'dark',
                'onsuccess': onGapiSuccess,
                'onfailure': onGapiFailure
            });
            $('#profile_forgot_password').click(forgotPassword);
            $('#facebook-connect-button').click(loginFacebook);
            onPageLoadSetFocus();
            onChangeRegisterUserName($('#register_form_username').get(0), 'register_user_name_unique'); // in case field is pre-populated
//        showSubscribePopup();
//        showLoginPopup(true);
//        showRegistrationPopup(true);
        },

        onPageLoadSetFocus: function () {
            var errorFieldId = "<?php echo($errorFieldId);?>",
                inputFocusId = "<?php echo($inputFocusId);?>";

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
            showErrorMessage("", "");
            if (errorMessage == "" && ! isValidUserName(userName)) {
                errorMessage = "User name is not acceptable. Please enter your user name.";
                errorField = "login_form_username";
            }
            if (errorMessage == "" && ! isValidPassword(password)) {
                errorMessage = "Password is not acceptable, at least 4 characters. Please retry your password.";
                errorField = "login_form_password";
            }
            if (errorMessage == "") {
                // good enough to send to the server for more validation
                document.getElementById('login').submit();
            } else {
                showErrorMessage(errorMessage, errorField);
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
            showErrorMessage("", "");
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
                showErrorMessage(errorMessage, errorField);
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
});