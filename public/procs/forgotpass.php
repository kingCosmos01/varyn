<?php
    /**
     * Handle reset password request. Verify the user knows something about their account. Generate the email
     * to lead them back to resetting the password.
     * @Date: 1/11/16
     */
    require_once('../../services/common.php');
    $debug = (int) getPostOrRequestVar('debug', 0);
    $page = 'forgotpass';
    $search = getPostOrRequestVar('q', null);
    if ($search != null) {
        header('location:/allgames.php?q=' . $search);
        exit;
    }
    $userName = getPostOrRequestVar('userName', '');
    $email = getPostOrRequestVar('email', '');
    $errorMessage = '';
    $reset = false;

    if ($userName != '' || $email != '') {
        $errorMessage = resetUserPassword($userName, $email);
        if ($errorMessage == '') {
            $reset = true;
        } else {
            if ($errorMessage == 'INVALID_USER_ID') {
                $errorMessage = '<p class="errormsg">There is no account with the information you supplied.</p>';
            }
        }
    }

    /**
     * Start the process to reset a users password. We require one of the user_id, user_name, or email_address
     * of the user to reset. Once valid, the account is marked with a temporary password. Then an email is
     * sent to the email_address that belongs to the account. Responding to that email brings the user back
     * with a token to match the account record and the password can be changed.
     *
     * @param $userName
     * @param $emailAddress
     * @return null|string
     */
    function resetUserPassword ($userName, $emailAddress) {
        global $serverName;
        global $emailNotificationTypeIds;

        $site_id = 100;
        $language_code = 'en';
        $dbOpenedHere = false;
        if ( ! dbIsActiveConnection()) {
            dbConnect();
            $dbOpenedHere = true;
        }
        $success = 0;
        $status_msg = 'ERROR';
        $sql ='call RegisteredUserForgotPassword(?, null, ?, ?, ?, @success, @status_msg); select @success, @status_msg;';
        $params = array($site_id, $userName, $emailAddress, $language_code);
        $queryResults = dbQuery($sql, $params);
        if (dbError($queryResults)) {
            $errorMessage = dbError($queryResults);
        } else {
            do {
                $rowSet = dbFetchAll($queryResults); // should be temp password and email address.
                if (isset($rowSet[0])) {
                    if (isset($rowSet[0]['@success'])) {
                        $success = $rowSet[0]['@success'] == 1;
                        $status_msg = $rowSet[0]['@status_msg'];
                    } elseif (isset($rowSet[0]['email_address'])) {
                        $emailAddress = $rowSet[0]['email_address'];
                        $user_id = $rowSet[0]['user_id'];
                        $user_name = $rowSet[0]['user_name'];
                        $token = $rowSet[0]['secondary_password'];
                    }
                }
            } while (dbNextResult($queryResults));
            dbClearResults($queryResults);
            if ($success) {
                $errorMessage = '';
                // Reset succeeded, now send the email prompting the user to reset their password.
                $parameters = array(
                    'site_id' => $site_id,
                    'user_id' => $user_id,
                    'user_name' => $user_name,
                    'token' => $token,
                    'date' => date('F j, Y'),
                    'domain' => $serverName
                );
                $game_id = 0;
                SendUserEmailNotification($emailAddress, $site_id, $user_id, $game_id, $emailNotificationTypeIds['ForgotPassword'], $language_code, $parameters);
            } else {
                $errorMessage = $status_msg;
            }
        }
        if ($dbOpenedHere) {
            dbClose();
        }
        return $errorMessage;
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Varyn: Great games you can play anytime, anywhere</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Access-Control-Allow-Origin" content="*">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta http-equiv="cache-control" content="max-age=0" />
    <meta http-equiv="cache-control" content="no-cache" />
    <meta http-equiv="expires" content="0" />
    <meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT" />
    <meta http-equiv="pragma" content="no-cache" />
    <meta name="format-detection" content="telephone=no" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="mobile-web-app-capable" content="yes" />
    <meta name="description" content="Varyn makes games using technology that performs on the most popular platforms. Cross platform friendly technologies have created an opportunity to re-invent online games for an audience that moves seamlessly between desktop, tablet, and smart-phone.">
    <meta name="author" content="Varyn">
    <meta name="google-signin-client_id" content="AIzaSyD22xO1Z71JywxmKfovgRuqZUHRFhZ8i7A.apps.googleusercontent.com">
    <link href="/common/bootstrap.min.css" rel="stylesheet">
    <link href="/common/carousel.css" rel="stylesheet">
    <link href="/common/varyn.css" rel="stylesheet">
    <link rel="icon" href="/favicon.ico">
    <link rel="icon" type="image/png" href="/favicon-48x48.png" sizes="48x48"/>
    <link rel="icon" type="image/png" href="/favicon-196x196.png" sizes="196x196">
    <link rel="icon" type="image/png" href="/favicon-160x160.png" sizes="160x160">
    <link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96">
    <link rel="icon" type="image/png" href="/favicon-16x16.png" sizes="16x16">
    <link rel="icon" type="image/png" href="/favicon-32x32.png" sizes="32x32">
    <link rel="apple-touch-icon" href="/apple-touch-icon-60x60.png" sizes="60x60"/>
    <link rel="apple-touch-icon" href="/apple-touch-icon-72x72.png" sizes="72x72"/>
    <link rel="apple-touch-icon" href="/apple-touch-icon-76x76.png"/>
    <link rel="apple-touch-icon" href="/apple-touch-icon-76x76.png" sizes="76x76"/>
    <link rel="apple-touch-icon" href="/apple-touch-icon-114x114.png" sizes="114x114"/>
    <link rel="apple-touch-icon" href="/apple-touch-icon-120x120.png" sizes="120x120"/>
    <link rel="apple-touch-icon" href="/apple-touch-icon-152x152.png" sizes="152x152"/>
    <link rel="shortcut icon" href="/favicon-196x196.png">
    <meta property="fb:app_id" content="" />
    <meta property="fb:admins" content="726468316" />
    <meta property="og:title" content="Varyn: Great games you can play anytime, anywhere">
    <meta property="og:url" content="http://www.varyn.com">
    <meta property="og:site_name" content="Varyn">
    <meta property="og:description" content="Varyn makes games using technology that performs on the most popular platforms. Cross platform friendly technologies have created an opportunity to re-invent online games for an audience that moves seamlessly between desktop, tablet, and smart-phone.">
    <meta property="og:image" content="http://www.varyn.com/images/1200x900.png"/>
    <meta property="og:image" content="http://www.varyn.com/images/1024.png"/>
    <meta property="og:image" content="http://www.varyn.com/images/1200x600.png"/>
    <meta property="og:image" content="http://www.varyn.com/images/600x600.png"/>
    <meta property="og:image" content="http://www.varyn.com/images/2048x1536.png"/>
    <meta property="og:type" content="game"/>
    <meta name="twitter:card" content="photo"/>
    <meta name="twitter:site" content="@varyndev"/>
    <meta name="twitter:creator" content="@varyndev"/>
    <meta name="twitter:title" content="Varyn: Great games you can play anytime, anywhere"/>
    <meta name="twitter:image:src" content="http://www.varyn.com/images/600x600.png"/>
    <meta name="twitter:domain" content="varyn.com"/>
    <script src="/common/head.min.js"></script>
</head>
<body>
<?php
include_once('../common/header.php');
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
                        <form id="forgot-password-form" method="POST" action="../profile.php" onsubmit="return varynApp.formForgotPasswordClicked();">
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
include_once('../common/footer.php');
?>
<script type="text/javascript">

    var varynApp;

    head.ready(function() {
        var siteConfiguration = {
                siteId: <?php echo($siteId);?>,
                gameId: 0,
                gameGroupId: 0,
                serverStage: "<?php echo($stage);?>",
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
