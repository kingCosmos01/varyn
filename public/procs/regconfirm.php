<?php
/**
 * Handle registration confirmation from email request. The link in teh email redirects to here, we use the parameters
 * u (user-id), s (site-id), and t (token, or secondary-password) to verify this is the user. Once confirmed the
 * user is logged in.
 * @Date: 1/5/16
 */
require_once('../../services/common.php');
$page = 'profile';
$search = getPostOrRequestVar('q', null);
if ($search != null) {
    header('location:/allgames.php?q=' . $search);
    exit;
}
processTrackBack();
$user_id = getPostOrRequestVar('u', 0);
$site_id = getPostOrRequestVar('s', 0);
$token = getPostOrRequestVar('t', '');
$redirectTo = '/profile.php?action=regconfirm&code=';

if ($site_id > 0 && $user_id > 0 && $token != '') {
    $errorCode = '';
    $errorMessage = 'Testing';
    $serverResponse = $enginesis->registeredUserConfirm($user_id, $token);
    if ($serverResponse == null) {
        $errorCode = $enginesis->getLastErrorCode();
        if ($errorCode == 'INVALID_SECONDARY_PASSWORD') {
            $errorMessage = "Your registration request is invalid or it has expired.";
        } elseif ($errorCode == 'PASSWORD_EXPIRED') {
            $errorMessage = "Your registration request has expired.";
        } else {
            $errorMessage = "There was a system error servicing this request (" . $enginesis->getLastErrorDescription() . ")";
        }
        $errorMessage = "<p class=\"error-text\">$errorMessage Please <a href=\"/profile.php\">begin the request again</a>.</p>";
    } else {
        $errorCode = 'SUCCESS';
        $errorMessage = 'Your registration has been confirmed! Welcome to Varyn. Now let\'s play some games!';
        setVarynUserCookie($serverResponse, $enginesis->getServerName());
    }
} else {
    $errorCode = 'INVALID_PARAM';
    $errorMessage = 'The information supplied to confirm your registration does not appear to be correct.';
}
header('Location: ' . $redirectTo . $errorCode);
return;
