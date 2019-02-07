<?php
/**
 * Handle registration confirmation from email request. The link in the email redirects to here, we use the parameters
 * u (user-id), s (site-id), and t (token, or secondary-password) to verify this is the user. Once confirmed the
 * user is logged in. If not confirmed a reason message is displayed. All cases redirect to profile.php with the
 * error code and the message is diplayed there.
 * @Date: 1/5/16
 */
require_once('../../services/common.php');
$page = 'profile';
$search = getPostOrRequestVar('q', null);
if ($search != null) {
    header('location:/games/?q=' . $search);
    exit;
}
processTrackBack();
$user_id = getPostOrRequestVar('u', 0);
$site_id = getPostOrRequestVar('s', 0);
$token = getPostOrRequestVar('t', '');
$redirectTo = '/profile/?action=regconfirm&code=';
$errorCode = '';

if ($site_id > 0 && $user_id > 0 && $token != '') {
    $errorCode = '';
    $serverResponse = $enginesis->registeredUserConfirm($user_id, $token);
    if ($serverResponse == null) {
        $errorCode = $enginesis->getLastErrorCode();
    } else {
        $errorCode = 'SUCCESS';
        setSiteUserCookie($serverResponse, $enginesis->getServerName());
        $errorCode .= '&u=' . $user_id . '&t=' . $token;
    }
} else {
    $errorCode = 'INVALID_PARAM';
}
header('Location: ' . $redirectTo . $errorCode);
return;
