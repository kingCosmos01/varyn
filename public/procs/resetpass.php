<?php
    /**
     * Handle reset password from email request. If user, site, token match secondary password lookup and
     * not expired, then accept a new password from a form.
     * @Date: 1/11/16
     */
    require_once('../../services/common.php');
    $user_id = getPostOrRequestVar('u', 0);
    $site_id = getPostOrRequestVar('s', 0);
    $token = getPostOrRequestVar('t', '');
    $newPassword = isset($_POST['newPassword']) ? $_POST['newPassword'] : '';
    $retypePassword = isset($_POST['retypePassword']) ? $_POST['retypePassword'] : '';
    $newPasswordSet = false;
    $hashPassword = '';
    $language_code = sessionGetLanguageCode();
    $redirectTo = '/index.php';
    $errorMessage = '';

    if ($site_id > 0 && isset($site_data[$site_id])) {
        $protocol = getServerHTTPProtocol();
        $serverDomain = serverStageMatch($site_data[$site_id]['site_base_url']);
        $siteLoginURL = $protocol . $serverDomain . $site_data[$site_id]['site_login_url'];
        $siteProfileURL = $protocol . $serverDomain . $site_data[$site_id]['site_profile_url'];
        if ($user_id > 0 && strlen($token) > 0) {
            if ($newPassword == '') {
                // look up token, verify user id, verify expiration date and if OK proceed to a form to accept new password.
                $sql = 'call RegisteredUserVerifyForgotPassword(?, ?, ?, ?, @success, @status_msg)';
                $sqlParameters = array($site_id, $user_id, $token, $language_code);
            } elseif (passwordIsValid($newPassword, $retypePassword) == '') {
                $hashPassword = hashPassword($newPassword); // create a new password from the password
                // then set new password: RegisteredUserPasswordChange (IN _site_id int, IN _logged_in_user_id int, IN _password varchar(128), IN _secondary_password varchar(128), IN _captcha_id int, IN _captcha_response varchar(64), in _language_code char(2), OUT _success boolean, OUT _status_msg varchar(255))
                // select password, secondary_password from users where user_id=9999;
                // call RegisteredUserPasswordChange(100, 9999, "hashedPassword", "token", 99999, "DEADMAN", "en", @, @m);
                $sql = 'call RegisteredUserPasswordChange(?, ?, ?, ?, ?, ?, ?, @success, @status_msg)';
                $sqlParameters = array($site_id, $user_id, $hashPassword, $token, '99999', 'DEADMAN', $language_code);
            } else {
                $sql = '';
                $redirectTo = '';
                $errorMessage = '<p class="errormsg">Invalid password. Your password must match and be at least 4 characters and no more than 20 without leading or trailing space.</p>';
            }
            if ($sql != '') {
                $sqlResults = dbQuery($sql, $sqlParameters);
                if ($sqlResults && dbRowCount($sqlResults) > 0) {
                    $userInfo = dbFetch($sqlResults);
                    if ($userInfo != null) {
                        $redirectTo = $siteProfileURL;
                    } else {
                        $redirectTo = $siteLoginURL; // Anything we don't like just redirect to login
                        $hashPassword = '';
                    }
                    dbClearResults($sqlResults);
                } else {
                    debugLog('Resetpass database error ' . dbError($sqlResults));
                }
                $sql = 'select @success, @status_msg;';
                $sqlResults = dbQuery($sql, null);
                if ($sqlResults && dbRowCount($sqlResults) > 0) {
                    $statusInfo = dbFetch($sqlResults);
                    $success = $statusInfo['@success'];
                    $status_message = $statusInfo['@status_msg'];
                    if ($success == 1) {
                        $redirectTo = ''; // token is valid, stay on this form
                        if ($hashPassword != '') {
                            $newPasswordSet = true; // new password has been set time to tell user what to do next
                        }
                    } else {
                        $redirectTo .= '?resetpass=0&msg=' . $status_message;
                    }
                } else {
                    debugLog('Resetpass database error retrieving status ' . dbError($sqlResults));
                }
            }
        } else {
            $redirectTo = $siteLoginURL; // Anything we don't like just redirect to login
        }
    }
echo("<p>redirect to $redirectTo</p>");
    if ($redirectTo != '') {
//        header('Location: ' . $redirectTo); // Anything we don't like just redirect to the home page
//        return;
    }

    function passwordIsValid($newPassword, $retypePassword) {
        if (strlen(trim($newPassword)) > 3 && $newPassword == $retypePassword) {
            $msg = '';
        } else {
            $msg = 'INVALID_PASSWORD';
        }
        return $msg;
    }
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Reset Password | Enginesis</title>
    <link rel="icon" href="../favicon.ico" type="image/x-icon"/>
    <link rel="stylesheet" type="text/css" href="/css/enginesis.css"/>
</head>

<body>
<table cellpadding="0" cellspacing="1" border="0" width="800" align="center">
    <tr><td height="120"><img src="../images/header3.png" border="0" width="800" height="120" /></td></tr>
    <tr>
        <td>
            <div class="centered">
                <h1>Reset Password</h1>
                <div class="contentAreaBorder centered" style="width: 50%; margin-left: 25%; margin-right: 25%; padding: 24px;">
                    <?php
                    if ($newPasswordSet) {
                    ?>
                        <p>Your password has been reset. Please login now to verify your password.</p>
                        <p><a href="login.php">Login</a></p>
                        <p><a href="mailto:support@enginesis.com">Contact Support</a></p>
                    <?php
                    } else {
                    ?>
                    <form method="POST" action="" onsubmit="return validateForm();">
                        <table>
                            <tr><td colspan="2" width="100%"><div id="messageArea"><?php echo($errorMessage);?></div></td></tr>
                            <tr><td width="50%"><label>New Password</label></td><td width="50%"><input name="newPassword" id="newPassword" type="password" required size="20" maxlength="20" /></td></tr>
                            <tr><td width="50%"><label>Retype New Password</label></td><td width="50%"><input name="retypePassword" id="retypePassword" type="password" required size="20" maxlength="20" /></td></tr>
                            <tr><td colspan="2" width="100%"><input type="submit" value="Reset"/></td></tr>
                        </table>
                    </form>
                    <?php
                    }
                    ?>
                </div>
            </div>
        </td>
    </tr>
</table>
<script src="../js/common.js"></script>
<script>
    function showErrorMessage (message, focusId) {
        var messageArea = document.getElementById('messageArea');

        messageArea.innerHTML = '<p class="errormsg">' + message + '</p>';
        if (focusId != null && focusId != '') {
            var focus = document.getElementById(focusId);
            if (focus != null) {
                focus.focus();
            }
        }
    }

    function validatePassword () {
        var newPassword = document.getElementById('newPassword').value,
            retypePassword = document.getElementById('retypePassword').value,
            passwordOK = false;

        if (enginesisCommon.isValidPassword(newPassword)) {
            if (retypePassword == newPassword) {
                passwordOK = true;
            } else {
                showErrorMessage("Your passwords do not match. We want to be sure you know your password.", "newPassword");
            }
        } else {
            showErrorMessage("Invalid password. Required at least 4 characters and no more than 20 without leading or trailing space.", "newPassword");
        }
        return passwordOK;
    }

    function validateForm () {
        var okToSubmit;
        okToSubmit = validatePassword();
        return okToSubmit;
    }
</script>
</body>
</html>
