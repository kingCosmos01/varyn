<?php
/**
 * Handle reset password from email request. If user, site, token match secondary password lookup and
 * not expired, then accept a new password from a form. This page is intended to be called from a link such as
 * https://%domain%/procs/resetpass.php?id=10093&e=1e6&s=%site_id%&u=%user_id%&t=%token%
 * @Date: 6/18/2016
 *
 * TEST: https://varyn-l.com/procs/resetpass.php?id=10093&e=1e6&s=106&u=10239&t=1234
 * https://varyn-l.com/procs/resetpass.php?id=10093&e=1e6&s=106&u=10244&t=ff4825a984337fccb18d6757d3fa5558c75a082b
 * 
 */
require_once('../../services/common.php');
$debug = (int) strtolower(getPostOrRequestVar('debug', 0));
$page = 'resetpass';
processSearchRequest();
processTrackBack();
$showSubscribe = 0;
$user_id = getPostOrRequestVar('u', 0);
$site_id = getPostOrRequestVar('s', 0);
$newPasswordSet = false;

// User may not be logged in to call this page (forgot password vs. change password)
$authToken = '';
$token = '';
$action = getPostOrRequestVar('action', '');
$newPassword = '';
$hackerHoneyPot = '';
$hackerToken = '';
$isValidRequest = true;

if ($site_id > 0 && $user_id > 0) { // make sure page is called with correct parameters
    $token = getPostOrRequestVar('t', ''); // this is the password reset token generated by Enginesis and sent in the email.
}
if ($isLoggedIn) {
    $userInfo = $enginesis->getLoggedInUserInfo();
    if ($userInfo != null) {
        $authToken = $userInfo->authtok;
        $user_id = $userInfo->user_id; // only use the user_id that is logged in
        $site_id = $userInfo->site_id;
    } else {
        $userInfo = $enginesis->sessionUserInfoGet();
        if ($userInfo == null) {
            $isLoggedIn = false;
        } else {
            $authToken = $userInfo->authtok;
            $user_id = $userInfo->user_id; // only use the user_id that is logged in
            $site_id = $userInfo->site_id;
        }
    }
}
debugLog("loading registeredUserPasswordChange with " . json_encode([$user_id, $token]));
if ($site_id > 0 && $user_id > 0 && strlen($token) > 0) {
    if ($action == 'resetpassword') {
        $hackerToken = getPostVar('clearall', '');        // must match token when page loaded
        $hackerHoneyPot = getPostVar('emailaddress', ''); // must be empty
        $newPassword = getPostVar('newPassword', '');
    }
    $language_code = $enginesis->getLanguageCode();
    $networkId = $enginesis->getNetworkId();
    $redirectTo = '';
    $errorMessage = '';
    $errorFieldId = '';
    $inputFocusId = '';
    $isValidRequest = $action == 'resetpassword' && empty($hackerHoneyPot) && validateInputFormHackerToken($hackerToken);
    if ($newPassword == '') {
        // First time in: prompt for a new password
        $hackerToken = makeInputFormHackerToken();
    } elseif ( ! $isValidRequest) {
        $redirectTo = '/profile/';
        debugLog("resetpass not a valid request");
    } elseif ($enginesis->isValidPassword($newPassword)) {
        if ($enginesis->isLoggedInUser()) {
            debugLog("Calling registeredUserPasswordChange with " . json_encode([$newPassword, $token]));
            $serverResponse = $enginesis->registeredUserPasswordChange($newPassword, $token);
        } else {
            debugLog("Calling registeredUserPasswordChangeUnauth with " . json_encode([$user_id, $newPassword, $token]));
            $serverResponse = $enginesis->registeredUserPasswordChangeUnauth($user_id, $newPassword, $token);
        }
        debugLog("registeredUserPasswordChange says " . json_encode($serverResponse));
        if ($serverResponse == null) {
            $errorCode = $enginesis->getLastErrorCode();
            if ($errorCode == 'INVALID_SECONDARY_PASSWORD') {
                $errorMessage = "Your password change request is invalid or it has expired.";
            } elseif ($errorCode == 'PASSWORD_EXPIRED') {
                $errorMessage = "Your password change request has expired.";
            } elseif ($errorCode == 'NOT_AUTHENTICATED') {
                $errorMessage = "Your must be logged in to change your password.";
            } else {
                $errorMessage = "There was a system error saving your information (" . $enginesis->getLastErrorDescription() . ")";
            }
            $errorMessage = "<p class=\"text-error\">$errorMessage Please <a href=\"/profile/\">begin the request again</a>.</p>";
            $hackerToken = makeInputFormHackerToken();
        } else {
            $newPasswordSet = true;
        }
    } else {
        $sql = '';
        $errorMessage = '<p class="errormsg">Invalid password. Your password must be between 4 and 20 characters without leading or trailing space.</p>';
    }
} else { // not a valid request
    $redirectTo = '/profile/';
}
if ( ! $debug && $redirectTo != '') {
    header('Location: ' . $redirectTo); // Anything we don't like just redirect to the home page
    return;
}
$pageTitle = 'Reset Password | Varyn';
$pageDescription = 'Reset user password at Varyn.com.';
include_once(VIEWS_ROOT . 'header.php');
?>
<div class="container">
    <div class="row p-4 m-4 justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Change Password</h2>
                </div>
                <div class="card-body">
                    <?php
                    if ($newPasswordSet) {
                    ?>
                        <p>Your password has been changed. To verify, please logout and then log in with your new password.</p>
                        <ul>
                            <li><a href="/">Home</a></li>
                            <li><a href="/profile/">Your profile</a></li>
                            <li><a href="/profile/?action=logout">Login again</a></li>
                            <li><a href="mailto:support@varyn.com">Contact Support</a></li>
                        </ul>
                    <?php
                    } else {
                    ?>
                    <form id="forgot-password-form" method="POST" action="" onsubmit="return varynApp.formResetPasswordClicked();">
                        <div class="popupMessageArea">
                            This is the response from the server
                        </div>
                        <?php
                        if (empty($errorMessage)) {
                        ?>
                            <p>Please enter a new password, and then select Change to change your password.</p>
                        <?php
                        } else {
                            echo($errorMessage);
                        }
                        ?>
                        <div class="form-group">
                            <label for="newPassword">New password:</label><br/>
                            <input type="password" id="newPassword" name="newPassword" maxlength="20" required class="form-control"  placeholder="New password" autocorrect="off" autocapitalize="off" autocomplete="off"/>
                            <div id="optional-small-label" class="checkbox optional-small"><label for="ShowPassword" onclick="resetPasswordPage.onClickShowNewPassword(this);"><input type="checkbox" name="ShowPassword" id="reset-show-password"> <span id="reset-show-password-text">Show</span> <span id="reset-show-password-icon" class="iconEye" aria-hidden="true"></span></label></div>
                        </div>
                        <div class="form-group">
                            <input type="submit" class="btn btn-success disabled" id="reset-password-button" value="Change"/><span id="password-match" class="password-match"></span>
                            <input type="hidden" name="action" value="resetpassword" />
                            <input type="text" name="emailaddress" class="popup-form-address-input" autocorrect="off" autocapitalize="off" autocomplete="off"/>
                            <input type="hidden" name="clearall" value="<?php echo($hackerToken);?>" />
                            <input type="hidden" name="s" value="<?php echo($site_id);?>" />
                            <input type="hidden" name="u" value="<?php echo($user_id);?>" />
                            <input type="hidden" name="t" value="<?php echo($token);?>" />
                        </div>
                    </form>
                    <p class="text-info text-small">Password security is something we take very seriously. Please use a password that is at least 12 characters and does not conform to any common patterns.</p>
                    <?php
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <?php
    if ($debug == 1) {
        echo("<div class=\"card card-info p-4\"><h3>Debug info:</h3><p>Is valid request? " . boolToString($isValidRequest) . "</p><p>Page called with action $action; User id $user_id; site id: $site_id; token: $token; password $newPassword;</p><p>redirect to $redirectTo</p><p>Honeypot: $hackerHoneyPot</p><p>Hacker token: $hackerToken; Current token: " . makeInputFormHackerToken() . "</p></div>");
    }
    ?>
    <div class="card card-info p-4">
        <h4>Password Security</h4>
        <p>We take password security very seriously and you should to. You may think there is little for a hacker to gain by cracking your account
        but that could be the tip of the iceberg, giving away valuable secrets leading to a crack on your other accounts. Please keep these ideas in mind when choosing your password:</p>
        <ul>
            <li>More important than anything else is the length of the password. Longer is better. Use at least 12 characters. The way most brute force attacks work they become less effective the more characters there are to try.</li>
            <li>Never use the same password on multiple websites or any other security system. When one is cracked you are leaving the door open to crack others.</li>
            <li>Avoid using personally identifiable information as a cracker can derive information about your password through social engineering. If you post your dog's name in your social media a hacker could use that against you if it were also part of your password.</li>
            <li>Avoid using common passwords and word combinations. Dictionary look-ups are usually the first method of attack.</li>
        </ul>
    </div>
    <div id="bottomAd" class="row">
    <?php
    $adProvider = 'google';
    include_once(VIEWS_ROOT . 'ad-spot.php');
    ?>
    </div>
</div>
<?php
include_once(VIEWS_ROOT . 'footer.php');
?>
<script>

    var varynApp;
    var resetPasswordPage;

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
                authToken: '<?php echo($authToken);?>'
            },
            resetPasswordPageParameters = {
                errorFieldId: "<?php echo($errorFieldId);?>",
                inputFocusId: "<?php echo($inputFocusId);?>",
                showSubscribe: "<?php echo($showSubscribe);?>"
            };
        varynApp = varyn(siteConfiguration);
        resetPasswordPage = varynApp.initApp(varynResetPasswordPage, resetPasswordPageParameters);
    });

    head.js("/common/modernizr.js", "/common/bootstrap.bundle.min.js", "//platform.twitter.com/widgets.js", "https://apis.google.com/js/platform.js", "/common/enginesis.js", "/common/ShareHelper.js", "/common/commonUtilities.js", "/common/varyn.js", "/common/varynResetPasswordPage.js");

</script>
</body>
</html>