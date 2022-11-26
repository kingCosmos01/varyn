<?php
/**
 * Handle unsubscribe from newsletter. This would only be for an anonymous user.
 * @created: 25-Jul-2020
 */
require_once('../../services/common.php');
$email = getPostOrRequestVar(['e', 'email'], null);
$debug = (int) getPostOrRequestVar('debug', 0);
$action = getPostOrRequestVar('action', null);
$page = 'unsubscribe';
$pageTitle = 'Unsubscribe from Varyn newsletter';
$pageDescription = 'Varyn makes games using technology that performs on the most popular platforms. Cross platform friendly technologies have created an opportunity to re-invent online games for an audience that moves seamlessly between desktop, tablet, and smart-phone.';

$errorMessage = '';
$reset = false;

if ($action == 'unsubscribe') {
    $emailClean = safeForHTML($email);
    // process request
    if (verifyFormHacks(['emailaddress', 'all-clear'])) {
        if (checkEmailAddress($email)) {
            $results = $enginesis->newsletterAddressDelete($email);
            $errorCode = $enginesis->getLastErrorCode();
            if ($errorCode == EnginesisErrors::NOT_SUBSCRIBED) {
                $errorMessage = "<p class=\"text-error\">Email $emailClean was not subscribed.</p>";
            } elseif ($errorCode == EnginesisErrors::NO_ERROR) {
                $errorMessage = "<p class=\"text-success\">Email $emailClean has been unsubscribed.</p>";
            } else {
                $errorMessage = "<p class=\"text-error\">Email $emailClean has issues, please check your entry. $errorCode</p>";
            }
        } else {
            $errorMessage = "<p class=\"text-error\">Email '$emailClean' doesn't appear to be subscribed. Try again?</p>";
        }
    } else {
        // attempted hack?
        $errorMessage = "<p class=\"text-error\">Email $emailClean doesn't appear to be subscribed. Try again?</p>";
    }
} else {
    $hackerVerification = makeInputFormHackerToken();
}
include_once(VIEWS_ROOT . 'header.php');
?>
<div class="container">
    <div class="row p-4 justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h1 class="card-title">Unsubscribe</h1>
                </div>
                <div class="card-body">
                    <form id="unsubscribe-form" method="POST" action="/procs/unsubscribe.php" onsubmit="return varynApp.formUnsubscribeClicked();">
                        <div class="popupMessageArea">
                            <?php echo($errorMessage);?>
                        </div>
                        <?php echo($errorMessage);?>
                        <p>Please identify your account by entering your email address. We will unsubscribe you from all Varyn communications.</p>
                        <div class="p-2">
                            <label for="unsubscribe_email_form">Email:</label>
                            <input type="email" id="unsubscribe_email_form" name="email" tabindex="1" maxlength="80" class="popup-form-input required email" placeholder="Your email address" autocapitalize="off" autocorrect="off" autocomplete="email" value="<?php echo($email);?>"/>
                        </div>
                        <div class="text-center p-2">
                            <input type="submit" class="btn btn-success" id="unsubscribe-button" value="Unsubscribe" tabindex="25"/>
                            <input type="hidden" name="action" value="unsubscribe" />
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
