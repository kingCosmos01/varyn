<?php
/**
 * Handle a request approval.
 *  - Friend request
 *  - Team request
 *  - Quest request
 * @Date: 1/11/16
 */
require_once('../../services/common.php');
$user_id = getPostOrRequestVar('u', 0);
$site_id = getPostOrRequestVar('s', 0);
$token = getPostOrRequestVar('t', '');
$requestId = getPostOrRequestVar('r', '');
$approval = getPostOrRequestVar('a', '');
$language_code = sessionGetLanguageCode();
$errorMessage = '';

?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Approve Request | Enginesis</title>
    <link rel="icon" href="../favicon.ico" type="image/x-icon"/>
    <link rel="stylesheet" type="text/css" href="/css/enginesis.css"/>
</head>

<body>
<table cellpadding="0" cellspacing="1" border="0" width="800" align="center">
    <tr><td height="120"><img src="../images/header3.png" border="0" width="800" height="120" /></td></tr>
    <tr>
        <td>
            <div class="centered">
                <h1>Approve Request</h1>
                <div class="contentAreaBorder centered" style="width: 50%; margin-left: 25%; margin-right: 25%; padding: 24px;">
                    <p>Your request has been approved.</p>
                    <p><a href="profile.php">Profile</a></p>
                    <p><a href="mailto:support@enginesis.com">Contact Support</a></p>
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
