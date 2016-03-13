<?php
    /**
     * Handle reset password request. Verify the user knows something about their account. Generate the email
     * to lead them back to resetting the password.
     * @Date: 1/11/16
     */
    require_once('../../services/common.php');
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
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Forgot Password | Enginesis</title>
    <link rel="icon" href="../favicon.ico" type="image/x-icon"/>
    <link rel="stylesheet" type="text/css" href="/css/enginesis.css"/>
</head>

<body>
<table cellpadding="0" cellspacing="1" border="0" width="800" align="center">
    <tr><td height="120"><img src="../images/header3.png" border="0" width="800" height="120" /></td></tr>
    <tr>
        <td>
            <div class="centered">
                <h1>Forgot Password</h1>
                <div class="contentAreaBorder centered" style="width: 50%; margin-left: 25%; margin-right: 25%; padding: 24px;">
                    <?php
                    if ($reset) {
                    ?>
                        <p>Email has been sent to the owner of this account. Please follow the instructions in that message to reset the account password.</p>
                        <p><a href="login.php">Login</a></p>
                        <p><a href="mailto:support@enginesis.com">Contact Support</a></p>
                    <?php
                    } else {
                    ?>
                    <p>Please identify your account. We will send email to the address set on the account to allow you to reset your password.</p>
                    <form id="forgotPasswordForm" method="POST" action="">
                        <table>
                            <tr><td width="30%" align="right"><label>User Name</label></td><td width="50%"><input name="userName" id="userName" type="text" size="20" maxlength="20" value="<?php echo($userName);?>" /></td></tr>
                            <tr><td width="30%" align="right"><label>Email Address</label></td><td width="50%"><input name="email" id="email" type="email" size="20" maxlength="80" value="<?php echo($email);?>" /></td></tr>
                            <tr><td colspan="2" width="100%"><div id="messageArea"><?php echo($errorMessage);?></div></td></tr>
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
</body>
</html>
