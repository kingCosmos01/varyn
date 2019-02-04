<?php
/**
 * Handle reset password request. Verify the user knows something about their account. Generate the email
 * to lead them back to resetting the password.
 * @Date: 1/11/16
 */
require_once('../../services/common.php');
$search = getPostOrRequestVar('q', null);
if ($search != null) {
    header('location:/games/?q=' . $search);
    exit;
}
$debug = (int) getPostOrRequestVar('debug', 0);
$page = 'forgotpass';
$pageTitle = 'Varyn: Great games you can play anytime, anywhere';
$pageDescription = 'Varyn makes games using technology that performs on the most popular platforms. Cross platform friendly technologies have created an opportunity to re-invent online games for an audience that moves seamlessly between desktop, tablet, and smart-phone.';

$userName = getPostOrRequestVar('userName', '');
$email = getPostOrRequestVar('email', '');
$errorMessage = '';
$reset = false;

if ($userName != '' || $email != '') {
    $errorMessage = $enginesis->userForgotPassword($userName, $email);
    if ($errorMessage == '') {
        $reset = true;
    } else {
        if ($errorMessage == 'INVALID_USER_ID') {
            $errorMessage = '<p class="errormsg">There is no account with the information you supplied.</p>';
        }
    }
}
include_once(VIEWS_ROOT . 'header.php');
?>
<div class="container marketing">
    <div class="row leader-3">
        <div class="col-md-4 col-md-offset-4">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h1 class="panel-title">Forgot Password</h1>
                </div>
                <div class="panel-body">
                    <?php
                    if ($reset) {
                    ?>
                        <p>Email has been sent to the owner of this account. Please follow the instructions in that message to reset the account password.</p>
                        <p><a href="login.php">Login</a></p>
                        <p><a href="mailto:support@enginesis.com">Contact Support</a></p>
                        <?php
                    } else {
                        ?>
                        <form id="forgot-password-form" method="POST" action="/profile/" onsubmit="return varynApp.formForgotPasswordClicked();">
                            <div class="popupMessageArea">
                                This is the response from the server
                            </div>
                            <p>Please identify your account. We will send email to the address set on the account to allow you to reset your password.</p>
                            <div class="form-group">
                                <label for="forgotpassword_username_form">User name:</label>
                                <input type="text" id="forgotpassword_username_form" name="forgotpassword_username_form" tabindex="23" maxlength="20" class="popup-form-input"  placeholder="Your user name" autocorrect="off" autocomplete="name"/>
                            </div>
                            <div class="form-group">
                                <label for="forgotpassword_email_form">Email:</label>
                                <input type="email" id="forgotpassword_email_form" name="forgotpassword_email_form" tabindex="24" maxlength="80" class="popup-form-input required email" placeholder="Your email address" autocapitalize="off" autocorrect="off" autocomplete="email"/>
                            </div>
                            <div class="form-group">
                                <input type="submit" class="btn btn-success" id="forgot-password-button" value="Reset" tabindex="25"/>
                                <input type="hidden" name="action" value="forgotpassword" />
                                <input type="text" name="emailaddress" class="popup-form-address-input" />
                                <input type="hidden" name="all-clear" value="<?php echo($hackerVerification);?>" />
                            </div>
                        </form>
                        <?php
                    }
                    ?>
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
                developerKey: '<?php echo($developerKey);?>',
                facebookAppId: '<?php echo($socialServiceKeys[2]['app_id']);?>',
                authToken: ''
            };
        varynApp = varyn(siteConfiguration);
    });

    head.js("/common/modernizr.js", "/common/jquery.min.js", "/common/bootstrap.min.js", "/common/ie10-viewport-bug-workaround.js", "//platform.twitter.com/widgets.js", "https://apis.google.com/js/platform.js", "/common/enginesis.js", "/common/ShareHelper.js", "/common/commonUtilities.js", "/common/varyn.js", "/common/varynProfilePage.js");

</script>
</body>
</html>
