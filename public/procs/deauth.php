<?php
/**
 * SSO/OAuth services come here when a user is asked to deauthorize our app. In this case we should log the user out
 * and also delete all their data. The data deletion is queued, it won't happen right away, but the
 * user should not be able to log in anymore.
 * See https://developers.facebook.com/docs/development/create-an-app/app-dashboard/data-deletion-callback
 * to test this use https://varyn-l.com/procs/deauth.php?debug=1 to generate a request
 * then use https://varyn-l.com/procs/deauth.php?debug=1&ccode=YOUR_CODE to verify queueing.
 * Author: jf
 * Date: 4/26/2021
 */
require_once('../../services/common.php');
require_once('../../services/SocialServices.php');
require_once('../../services/SocialServicesFacebook.php');
setErrorReporting(true);
processSearchRequest();

$page = 'deauth';
$debug = valueToBoolean(getPostOrRequestVar('debug', false));
$showPage = false;
$errorCode = EnginesisErrors::NO_ERROR;
$errorMessage = '';
$ssoFacebook = new SocialServicesFacebook();
$network_id = $ssoFacebook->getNetworkId();
$network_name = $ssoFacebook->getNetworkName();
$origin = getHTTPOrigin();
debugLog('deauth for ' . $network_name . ' called from ' . $origin . ' with ' . implode(',', $_POST));
$pageTitle = 'Delete your ' . $network_name . ' account from Varyn.com';
$pageDescription = 'A request to delete your account data from Varyn.com must be originated from the ' . $network_name . ' website.';
$callbackURL = 'https://www.varyn.com/procs/deauth.php';
$confirmation_code = getPostOrRequestVar('ccode', '');

if ($debug) {
    // debug set up so we can test without invoking Facebook
    if ($confirmation_code == '') {
        $facebook_user_id = '10150077266886694';
        $timeNow = time();
        $expires = $timeNow + (60 * 60 * 24);
        // $jsonPayload = '{"algorithm":"HMAC-SHA256","expires":1291840400,"issued_at":1291836800,"user_id":"57603269"}';
        $jsonPayload = '{"algorithm":"HMAC-SHA256","expires":' . $expires . ',"issued_at":' . $timeNow . ',"user_id":"' . $facebook_user_id . '"}';
        $signed_request = $ssoFacebook->testSignRequest($jsonPayload);
        debugLog('deauth generated user TEST for ' . $facebook_user_id . ' expire at ' . $expires . ' ' . date(DATE_RFC2822, $expires));
    }
} else {
    $signed_request = getPostVar('signed_request', null);
}
if ( ! empty($signed_request)) {
    // if we received a signed request then assume it is Facebook asking to delete a user
    $deletionRequest = $ssoFacebook->parseSignedRequest($signed_request);
    if ($deletionRequest != null) {
        $facebook_user_id = $deletionRequest['user_id'];
        $expires = intval($deletionRequest['expires']);
        $issued_at = intval($deletionRequest['issued_at']);

        // verify $expires is valid
        $timeNow = time();
        if ($expires < $timeNow) {
            $errorMessage = 'User deletion request for ' . $facebook_user_id . ' expired at ' . $expires . ' ' . date(DATE_RFC2822, $expires);
            debugLog($errorMessage);
        }
        // start deletion process
        $enginesis->setSiteUserId($network_id, $facebook_user_id);
        $serverResponse = $enginesis->registeredUserDelete();
        $errorCode = $enginesis->getLastErrorCode();
        if ($serverResponse == null) {
            // deletion failed
            $errorMessage = 'User deletion request failed due to a system error ' . $enginesis->getLastErrorDescription();
            $response = [
                'url' => $callbackURL,
                'confirmation_code' => '',
                'error' => $errorMessage
            ];
        } else {
            if ($errorCode != EnginesisErrors::NO_ERROR && $errorCode != EnginesisErrors::DUPLICATE_ENTRY) {
                $errorMessage = 'User deletion failed for ' . $errorCode . ' ' . $enginesis->getLastErrorDescription();
                debugLog($errorMessage);
                $response = [
                    'url' => $callbackURL,
                    'confirmation_code' => '',
                    'error' => $errorMessage
                ];
            } else {
                // generate a unique code for the deletion request so the user can come back and ask about it
                // page to query status
                $deletionRequest = $serverResponse[0];
                var_dump($deletionRequest);
                $confirmation_code = $deletionRequest->confirmation_code;
                $request_date = $deletionRequest->deletion_date;
                $status_url = $callbackURL . '?ccode=' . $confirmation_code;
                $response = [
                    'url' => $status_url,
                    'confirmation_code' => $confirmation_code
                ];
            }
        }
    } else {
        $errorMessage = 'Invalid deletion request, could not verify request signature.';
        debugLog($errorMessage);
        $response = [
            'url' => $callbackURL,
            'confirmation_code' => '',
            'error' => $errorMessage
        ];
    }
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($response);
} elseif ( ! empty($confirmation_code) && $confirmation_code != '0') {
    // User is asking for deletion status. Look up confirmation code, report status of deletion.
    $showPage = true;
    $request_status = 'UNKNOWN';
    $serverResponse = $enginesis->registeredUserDeleteGet($confirmation_code);
    if ($serverResponse == null) {
        $errorCode = $enginesis->getLastErrorCode();
    } else {
        $deletionRequest = $serverResponse[0];
        switch ($deletionRequest->status_id) {
            case '1':
                $request_status = 'Submitted, pending processing';
                $request_date = MySQLDateToHumanDate($deletionRequest->date_requested);
                break;
            case '2':
                $request_status = 'Processing';
                $request_date = MySQLDateToHumanDate($deletionRequest->date_requested);
                break;
            case '3':
                $request_status = 'Deletion completed';
                $request_date = MySQLDateToHumanDate($deletionRequest->date_completed);
                break;
            default:
                $request_status = 'UNKNOWN';
                break;
        }
    }
} else {
    debugLog('deauth.php was called with incorrect parameters, redirected to 404 page.');
    $redirectTo = 'Location: /missing.php';
    header($redirectTo);
    exit(0);
}

if ($showPage) {
    include_once(VIEWS_ROOT . 'header.php');
?>
<div class="container">
    <div class="row p-4 m-4 justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h1 class="card-title">Delete <?php echo($network_name);?> Account</h1>
                </div>
                <div class="card-body">
                    <?php if ($request_status == 'UNKNOWN') { ?>
                    <p>You did not provide enough information to determine your data deletion status. Review your request and try again.</p>
                    <p><small><?php echo($pageDescription);?></small></p>
                    <?php } else { ?>
                    <p>Your account deletion request <?php echo($confirmation_code);?> is being processed.</p>
                    <p>Request date <?php echo($request_date);?></p>
                    <p>Request status <em><?php echo($request_status);?></em></p>
                    <?php } ?>
                    <hr />
                    <p><a href="login.php">Login</a></p>
                    <p><a href="mailto:support@enginesis.com">Contact Support</a></p>
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
<?php
} else {
}
