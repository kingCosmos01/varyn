/**
 * Page specific functionality on the Varyn procs/resetpass page.
 */
var varynResetPasswordPage = function (varynApp, siteConfiguration) {
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
            this.setupPasswordChangeHandler();
            this.onPageLoadSetFocus();
        },

        setupPasswordChangeHandler: function () {
            var newPasswordElement = document.getElementById("newPassword");
            if (newPasswordElement != null) {
                newPasswordElement.addEventListener("change", this.passwordChangeHandler.bind(this));
                newPasswordElement.addEventListener("input", this.passwordChangeHandler.bind(this));
                newPasswordElement.addEventListener("propertychange", this.passwordChangeHandler.bind(this));
                this.passwordChangeHandler();
            }
        },

        onPageLoadSetFocus: function () {
            var pageElement;
            if (inputFocusId != "") {
                pageElement = document.getElementById(inputFocusId);
                if (pageElement != null) {
                    pageElement.focus();
                }
            } else {
                pageElement = document.getElementById("newPassword");
                if (pageElement != null) {
                    pageElement.focus();
                }
            }
            if (errorFieldId != "") {
                pageElement = document.getElementById(errorFieldId);
                if (pageElement != null) {
                    pageElement.classList.remove("popup-form-input");
                    pageElement.classList.add("popup-form-input-error");
                }
            }
        },

        passwordChangeHandler: function () {
            var newPassword = document.getElementById('newPassword').value,
                isValid,
                passwordElement = document.getElementById('password-match'),
                buttonElement = document.getElementById('reset-password-button');

            isValid = enginesisSession.isValidPassword(newPassword);
            if (isValid) {
                passwordElement.classList.remove('password-no-match');
                passwordElement.classList.add('password-match');
                passwordElement.style.display = "inline-block";
                buttonElement.classList.remove('disabled');
            } else {
                passwordElement.classList.remove('password-match');
                passwordElement.classList.add('password-no-match');
                passwordElement.style.display = "inline-block";
                buttonElement.classList.add('disabled');
            }
        },

        onClickShowNewPassword: function(event) {
            var passwordInput = document.getElementById("newPassword");
            var icon = document.getElementById("reset-show-password-icon");
            var text = document.getElementById("reset-show-password-text");
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
        }
    }
};