/**
 * Page specific functionality on the Varyn procs/resetpass page.
 */
var varynResetPasswordPage = function (varynApp, siteConfiguration) {
    "use strict";

    var enginesisSession = varynApp.getEnginesisSession(),
        errorFieldId = "",
        inputFocusId = "",
        varynResetPasswordPageReference = this;

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
            $('#newPassword').on('change', this.passwordChangeHandler.bind(this));
            $('#newPassword').on('input', this.passwordChangeHandler.bind(this));
            $('#newPassword').on('propertychange', this.passwordChangeHandler.bind(this));
            $('#retypePassword').on('change', this.passwordChangeHandler.bind(this));
            $('#retypePassword').on('input', this.passwordChangeHandler.bind(this));
            $('#retypePassword').on('propertychange', this.passwordChangeHandler.bind(this));
            this.passwordChangeHandler();
        },

        onPageLoadSetFocus: function () {
            if (inputFocusId != "") {
                document.getElementById(inputFocusId).focus();
            }
            if (errorFieldId != "") {
                $('#' + errorFieldId).removeClass("popup-form-input").addClass("popup-form-input-error");
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
        }
    }
};