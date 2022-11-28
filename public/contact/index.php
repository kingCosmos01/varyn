<?php
require_once('../../services/common.php');
processSearchRequest();
$page = 'home';
$pageTitle = 'Contact Varyn';
$pageDescription = 'Contact us if you have something to say, you need help, or if you are interested in more information about what we do.';
$showSubscribe = getPostOrRequestVar('s', '0');

// These variables should only be accepted via POST:
$send = strtolower(getPostVar('send', ''));
$name = getPostVar('name', '');
$emailFrom = getPostVar('email', '');
$message = getPostVar('message', '');
$honeypot = getPostVar('industry', '');
$timestamp = intval(getPostVar('accept', 0));
$errorMessage = '';
$errCode = '';
$messageSent = false;
$serverStage = $enginesis->getServerStage();
$emailTo = $admin_notification_list[0];
$emailSubject= 'Contact form from varyn.com';

/**
 * Validate the info provided by the user and make sure it is something we can accept.
 * @return boolean true if acceptable, otherwise false.
 */
function validateMessageParameters ($name, $emailFrom, $message) {
    $isValidName = (strlen($name) > 0) && (strlen($name) < 51);
    $isValidEmail = checkEmailAddress($emailFrom);
    $isValidMessage = strlen($message) > 0 && strlen($message) < 351;
    return $isValidName && $isValidEmail && $isValidMessage;
}

/**
 * Get information about the logged in user to include in the message.
 * @return string If there is a logged in user, something we know to identify this user, otherwise an empty string.
 */
function getLoggedInUserInfo() {
    global $enginesis;
    $userInfo = '';
    if (isset($enginesis) && $enginesis->isLoggedInUser()) {
        $userInfo = $enginesis->getUserName() . ' {' . $enginesis->getSiteId() . ':' . $enginesis->getUserId() . '}';
    }
    return $userInfo;
}

/**
 * Verify the timestamp is valid and the honeypot is valid.
 * We expect the honeypot to be empty and the timestamp to be within 4 hours.
 * @return boolean true if acceptable, false if unacceptable.
 */
function validateHoneyPot($timestamp, $honeypot) {
    $timeNow = time();
    $timeDifference = $timeNow - $timestamp;
    $isValid = strlen($honeypot) == 0 && $timeDifference < (60*60*4);
    if (! $isValid) {
        debugLog('Contact form validateHoneypot fails with length ' . strlen($honeypot) . ' and time diff ' . $timeDifference);
    }
    return $isValid;
}

include_once(VIEWS_ROOT . 'header.php');
if ($send == 'send') {
    $message = cleanString(strip_tags($message));
    if (validateMessageParameters($name, $emailFrom, $message)) {
        if (validateHoneyPot($timestamp, $honeypot)) {
            require_once('../../services/EnginesisMailer.php');
            $message = "The following message was submitted on the contact form on Varyn.com $serverStage\n\n$message";
            $userInfo = getLoggedInUserInfo();
            if ($userInfo != '') {
                $message .= "\n\nUser logged in as $userInfo";
            }
            $enginesisMailer = new EnginesisMailer($siteId, 0, $emailFrom, $emailTo, $emailSubject, $message);
            $enginesisMailer->setServerStage($serverStage);
            $enginesisMailer->setFromName($name);
            $errCode = $enginesisMailer->send();
            if ($errCode != '') {
                $errorMessage = "There was an issue trying to send your message ($errCode). The issue has been logged with technical support. Please try again later.<br/>" . $enginesisMailer->getExtendedStatusInfo();
                debugLog("enginesisMailer->send failed ($errCode) on " . $serverStage . ": " . $enginesisMailer->getExtendedStatusInfo());
            }
            $messageSent = true;
        } else {
            $messageSent = true;
            debugLog("Contact form fails on honeypot/timestamp by hacker " . $emailFrom);
        }
    } else {
        $errorMessage = "There was an issue with your message: please check your entry and try again.";
        debugLog("Contact form fails on name/email/message by hacker " . $emailFrom);
    }
}
$timestamp = time();
?>
<div class="container">
    <div class="row">
        <div class="card card-dark mt-4 mx-4 pt-4 pb-2 px-4">
            <h2>Contact Varyn</h2>
            <p>Use one of these ways to contact us:</p>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <div class="card card-light m-4">
                <div class="card-header">
                    <h5><span class="email-small"></span>&nbsp;Email us</h5>
                </div>
                <div class="card-body email-contact-form">
                    <?php if ($messageSent) {
                            if ($errorMessage == '') { ?>
                        <p>Your message has been sent.</p>
                        <?php } else {
                                echo("<p>$errorMessage</p>");
                            } ?>
                    <?php } else { ?>
                    <form class="form-inline" method="POST">
                        <div class="form-group">
                            <label for="name">Name:</label><input type="text" name="name" id="name" required class="form-control" placeholder="Your name"/>
                        </div>
                        <div class="form-group">
                            <label for="email">Email:</label><input type="email" required name="email" id="email" class="form-control" placeholder="Your email address"/>
                        </div>
                        <div id="email-contact-message" class="form-group">
                            <label for="message" class="form-label">Message:</label><textarea name="message" id="message" required class="form-control" rows="4"></textarea>
                        </div>
                        <div class="form-group" style="float: right;">
                            <label for="industry" id="label-industry">Industry:</label><input type="text" name="industry" id="industry" class="form-control" placeholder="Industry"/><br/>
                            <input type="text" name="accept" id="accept" placeholder="Do you agree" value="<?php echo($timestamp);?>"/><br/>
                            <br/><input type="submit" class="btn btn-lg btn-primary" name="send" value="Send"/>
                        </div>
                    </form>
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card card-light m-4">
                <div class="card-header">
                    <h5><span class="twitter-small"></span>&nbsp;Twitter Direct Message</h5>
                </div>
                <div class="card-body text-center">
                    <button type="button" class="btn btn-lg btn-info" name="twitter-dm" onclick="onClickedTwitterButton();">@varyndev</button>
                </div>
            </div>
            <div class="card card-light m-4">
                <div class="card-header">
                    <h5><span class="facebook-small"></span>&nbsp;Facebook Message</h5>
                </div>
                <div class="card-body text-center">
                    <button type="button" class="btn btn-lg btn-info" name="facebook-message" onclick="onClickedFacebookButton();">VarynDev</button>
                </div>
            </div>
            <div class="card card-light m-4">
                <div class="card-header">
                    <h5><span class="linkedin-small"></span>&nbsp;Linked-In Message</h5>
                </div>
                <div class="card-body text-center">
                    <button type="button" class="btn btn-lg btn-info" name="linkedin-message" onclick="onClickedLinkedInButton();">VarynDev</button>
                </div>
            </div>
            <div class="card card-light m-4">
                <div class="card-header">
                    <span class="varyn-shield-icon"></span>&nbsp;<a href="mailto:support@varyn.com&subject=Support request from Varyn.com">Contact Varyn support</a>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container">
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
    function onClickedFacebookButton () {
        window.open("http://facebook.com");
    }

    function onClickedTwitterButton () {
        window.open("https://twitter.com/messages/compose?recipient_id=1184539699");
    }

    function onClickedLinkedInButton () {
        window.open("http://linkedin.com/company/varyn-inc-/about/");
    }

    var varynApp;
    var varynContactPage = function (varynApp, siteConfiguration) {
        "use strict";

        var enginesisSession = varynApp.getEnginesisSession();

        return {
            pageLoaded: function (pageViewParameters) {
                // nothing to do on this page but we need this function definition.
            }
        };
    };

    head.ready(function() {
        var siteConfiguration = {
                siteId: <?php echo($siteId);?>,
                developerKey: "<?php echo(ENGINESIS_DEVELOPER_API_KEY);?>",
                serverStage: "<?php echo($serverStage);?>",
                authToken: "<?php echo($authToken);?>",
                languageCode: navigator.language || navigator.userLanguage
            },
            pageParameters = {
                showSubscribe: "<?php echo($showSubscribe);?>"
            };

        varynApp = varyn(siteConfiguration);
        varynApp.initApp(varynContactPage, pageParameters);
    });
    head.js("/common/modernizr.js", "/common/bootstrap.bundle.min.js", "/common/enginesis.js", "/common/ShareHelper.js", "/common/commonUtilities.js", "/common/varyn.js");
</script>
</body>
</html>