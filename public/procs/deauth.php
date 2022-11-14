<?php
/**
 * SSO/OAuth services come here when a user is asked to deauthorize our app. In this case we should log the user out
 * See https://developers.facebook.com/docs/development/create-an-app/app-dashboard/data-deletion-callback
 * and also delete all their data.
 * Author: jf
 * Date: 4/26/2021
 */
require_once('../../services/common.php');
require_once('../../services/SocialServices.php');
require_once('../../services/SocialServicesFacebook.php');
setErrorReporting(true);
processSearchRequest();

$page = 'deauth';
$debug = true;
$showPage = false;
$errorCode = EnginesisErrors::NO_ERROR;
$ssoFacebook = new SocialServicesFacebook();
$network_id = $ssoFacebook->getNetworkId();
$network_name = $ssoFacebook->getNetworkName();
$origin = getHTTPOrigin();
debugLog('deauth for ' . $network_name . ' called from ' . $origin . ' with ' . implode(',', $_POST));
$pageTitle = 'Delete your ' . $network_name . ' account from Varyn.com';
$pageDescription = 'A request to delete your account data from Varyn.com must be originated from the ' . $network_name . ' website.';

if ($debug) {
    // debug set up so we can test without invoking Facebook
    $confirmation_code = getPostOrRequestVar('ccode', '');
    if ($confirmation_code == '') {
        $jsonPayload = '{"algorithm":"HMAC-SHA256","expires":1291840400,"issued_at":1291836800,"user_id":"57603269"}';
        $signed_request = $ssoFacebook->testSignRequest($jsonPayload);
    }
} else {
    $signed_request = getPostVar('signed_request', null);
}
if ( ! empty($signed_request)) {
    // if we received a signed request then assume it is Facebook asking to delete a user
    // TODO: This code belongs in a method on $ssoFacebook.
    $deletionRequest = $ssoFacebook->parseSignedRequest($signed_request);
    if ($deletionRequest != null) {
        $user_id = $deletionRequest['user_id'];
        $expires = $deletionRequest['expires'];
        $issued_at = $deletionRequest['issued_at'];

        // start deletion process
        $enginesis->setSiteUserId($network_id, $user_id);
        $serverResponse = $enginesis->registeredUserDelete();
        if ($serverResponse == null) {
            $errorCode = $enginesis->getLastErrorCode();
            $response = [
                'url' => 'https://www.varyn.com/procs/deauth.php',
                'confirmation_code' => ''
            ];
            echo json_encode($response);
        } else {
            $deletionRequest = $serverResponse[0];
            $confirmation_code = $deletionRequest->confirmation_code;
            $request_date = $deletionRequest->deletion_date;
            // generate a unique code for the deletion request so the user can come back and ask about it
            // page to query status
            $status_url = 'https://www.varyn.com/procs/deauth.php?ccode=' . $confirmation_code;
            $response = [
                'url' => $status_url,
                'confirmation_code' => $confirmation_code
            ];
            echo json_encode($response);
        }
    } else {
        $enginesisLogger->log("Invalid deletion request");
    }
} else {
    $confirmation_code = getPostOrRequestVar('ccode', '');
    $request_status = 'UNKNOWN';
    if ( ! empty($confirmation_code) && $confirmation_code != '0') {
        // look up confirmation code, report status of deletion
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
        // got nothing useful, just generate a page
    }
    $showPage = true;
}

if ($showPage) {
    include_once(VIEWS_ROOT . 'header.php');
?>
<div class="container">
    <div class="row leader-3">
        <div class="col-md-4 col-md-offset-4">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h1 class="panel-title">Delete <?php echo($network_name);?> Account</h1>
                </div>
                <div class="panel-body">
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
}
