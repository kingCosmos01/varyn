<?php
/**
 * Handle reset password request. Verify the user knows something about their account, either the
 * user name or the email address. If successful, this process sends the email to lead them
 * back to resetting the password. This page redirects to /profile/?action=forgotpassword to
 * complete the process if the form is filled out successfully.
 * @Date: 1/11/16
 */
require_once('../../services/common.php');
processSearchRequest();
$debug = (int) getPostOrRequestVar('debug', 0);
$page = 'forgotpass';
$pageTitle = 'Varyn: Great games you can play anytime, anywhere';
$pageDescription = 'Varyn makes games using technology that performs on the most popular platforms. Cross platform friendly technologies have created an opportunity to re-invent online games for an audience that moves seamlessly between desktop, tablet, and smart-phone.';
$hackerVerification = makeInputFormHackerToken();

include_once(VIEWS_ROOT . 'header.php');
?>
<div class="container">
    <div class="row p-4 justify-content-center">
        <div class="col-md-6 align-self-center">
            <div class="card">
                <div class="card-header">
                    <h1 class="card-title">Forgot Password</h1>
                </div>
                <div class="card-body p-4">
                    <form id="forgot-password-form" method="POST" action="/profile/" onsubmit="return varynApp.formForgotPasswordClicked();">
                        <div id="formMessageArea" class="popupMessageArea">
                            This is the response from the server
                        </div>
                        <p>Please identify your account. We will send email to the address set on the account to allow you to reset your password.</p>
                        <div class="form-group">
                            <label for="forgot-password-username-form">User name:</label>
                            <input type="text" id="forgot-password-username-form" name="forgot-password-username-form" maxlength="20" class="popup-form-input" placeholder="Your user name" autocorrect="off" autocomplete="username"/>
                        </div>
                        <div class="form-group">
                            <label for="forgot-password-email-form">Email:</label>
                            <input type="email" id="forgot-password-email-form" name="forgot-password-email-form" maxlength="80" class="popup-form-input required email" placeholder="Your email address" autocapitalize="off" autocorrect="off" autocomplete="email"/>
                        </div>
                        <div class="form-group">
                            <input type="submit" class="btn btn-success" id="forgot-password-button" value="Request"/>
                            <input type="hidden" name="action" value="forgotpassword" />
                            <input type="text" name="emailaddress" class="popup-form-address-input" />
                            <input type="hidden" name="all-clear" value="<?php echo($hackerVerification);?>" />
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
include_once(VIEWS_ROOT . 'footer.php');
?>
<script type="text/javascript">

    var varynApp;

    head.ready(function() {
        var siteConfiguration = {
                siteId: <?php echo($siteId);?>,
                gameId: 0,
                gameGroupId: 0,
                serverStage: "<?php echo($serverStage);?>",
                languageCode: navigator.language || navigator.userLanguage,
                developerKey: '<?php echo(ENGINESIS_DEVELOPER_API_KEY);?>',
                facebookAppId: '<?php echo($socialServiceKeys[2]['app_id']);?>',
                googleAppId: '<?php echo($socialServiceKeys[7]['app_id']);?>',
                twitterAppId: '<?php echo($socialServiceKeys[11]['app_id']);?>',
                appleAppId: '<?php echo($socialServiceKeys[14]['app_id']);?>',
                authToken: ''
            };
        varynApp = varyn(siteConfiguration);
    });

    head.js("/common/modernizr.js", "/common/bootstrap.bundle.min.js", "//platform.twitter.com/widgets.js", "https://apis.google.com/js/platform.js", "/common/enginesis.js", "/common/ShareHelper.js", "/common/commonUtilities.js", "/common/varyn.js", "/common/varynProfilePage.js");

</script>
</body>
</html>
