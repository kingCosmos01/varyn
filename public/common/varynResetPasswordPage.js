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
                retypePassword = document.getElementById('retypePassword').value,
                isValid = false,
                id = 'password-match',
                button = 'reset-password-button';

            if (enginesisSession.isValidPassword(newPassword) && enginesisSession.isValidPassword(retypePassword) && newPassword == retypePassword) {
                isValid = true;
            }
            if (isValid) {
                $('#' + id).removeClass('password-no-match').addClass('password-match').css('display', 'inline-block');
                $('#' + button).removeClass('disabled');
            } else {
                $('#' + id).removeClass('password-match').addClass('password-no-match').css('display', 'inline-block');
                $('#' + button).addClass('disabled');
            }
        }
    }
};