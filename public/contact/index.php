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
$email = getPostVar('email', '');
$message = getPostVar('message', '');
$honeypot = getPostVar('industry', '');
$timestamp = getPostVar('accept', 0);
$errorMessage = '';
$errCode = '';
$messageSent = false;
$serverStage = $enginesis->getServerStage();

/**
 * Validate the info provided by the user and make sure it is something we can accept.
 * @return {boolean} true if acceptable, otherwise false.
 */
function validateMessageParameters ($name, $email, $message) {
    $isValidName = (strlen($name) > 0) && (strlen($name) < 51);
    $isValidEmail = checkEmailAddress($email);
    $isValidMessage = strlen($message) > 0 && strlen($message) < 351;
    return $isValidName && $isValidEmail && $isValidMessage;
}

/**
 * Verify the timestamp is valid and the honeypot is valid.
 * We expect the honeypot to be empty and the timestamp to be within 4 hours.
 * @return {boolean} true if acceptable, false if unacceptable.
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
    if (validateMessageParameters($name, $email, $message)) {
        if (validateHoneyPot($timestamp, $honeypot)) {
            require_once('../../services/EnginesisMailer.php');
            $message = "The following message was submitted on the contact form on Varyn.com $serverStage\n\n$message";
            $enginesisMailer = new EnginesisMailer($email, 'support@varyn.com', 'Contact form from varyn.com', $message);
            $enginesisMailer->setServerStage($serverStage);
            $enginesisMailer->setFromName($name);
            $enginesisMailer->setLogger('debugLog');
            $errCode = $enginesisMailer->send();
            if ($errCode != '') {
                $errorMessage = "There was an issue trying to send your message ($errCode). The issue has been logged with technical support. Please try again later.<br/>" . $enginesisMailer->getExtendedStatusInfo();
                debugLog("enginesisMailer->send failed ($errCode) on " . $serverStage . ": " . $enginesisMailer->getExtendedStatusInfo());
            }
            $messageSent = true;
        } else {
            $messageSent = true;
            debugLog("Contact form fails on honeypot/timestamp by hacker " . $email);
        }
    } else {
        $errorMessage = "There was an issue with your message: please check your entry and try again.";
        debugLog("Contact form fails on name/email/message by hacker " . $email);
    }
}
$timestamp = time();
?>
<div class="container marketing">
    <h2>Contact Varyn</h2>
    <p>Use one of these ways to contact us:</p>
    <div class="col-sm-8">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h5><span class="email-small"></span>&nbsp;Email Form</h5>
            </div>
            <div class="panel-body email-contact-form">
                <?php if ($messageSent) {
                        if ($errorMessage == '') { ?>
                    <p>Your message has been sent.</p>
                    <?php } else {
                            echo("<p>$errorMessage</p>");
                        } ?>
                <?php } else { ?>
                <form class="form-inline" method="POST">
                    <div class="form-group">
                        <label for="name">Name:</label><input type="text" name="name" required class="form-control" placeholder="Your name" maxlength="50"/><br/>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label><input type="email" required name="email" class="form-control" placeholder="Your email address" maxlength="80"/><br/>
                    </div>
                    <div id="email-contact-message" class="form-group">
                        <label for="message">Message:</label><textarea name="message" required class="form-control" maxlength="250"></textarea><br/>
                    </div>
                    <div class="form-group" style="float: right;">
                        <label for="name" id="label_industry">Industry:</label><input type="text" name="industry" id="industry" class="form-control" placeholder="Industry"/><br/>
                        <input type="text" name="accept" id="accept" placeholder="Do you agree" value="<?php echo($timestamp);?>"/><br/>
                        <br/><input type="submit" class="btn btn-lg btn-primary" name="send" value="Send"/>
                    </div>
                </form>
                <?php } ?>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <span class="varyn-shield-icon"></span>&nbsp;<a href="mailto:support@varyn.com&subject=Support request from Varyn.com">Contact Support</a>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h5><span class="twitter-small"></span>&nbsp;Twitter Direct Message</h5>
            </div>
            <div class="panel-body text-center">
                <button type="button" class="btn btn-lg btn-info" name="twitter-dm" onclick="onClickedTwitterButton();">@varyndev</button>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h5><span class="facebook-small"></span>&nbsp;Facebook Message</h5>
            </div>
            <div class="panel-body text-center">
                <button type="button" class="btn btn-lg btn-info" name="facebook-message" onclick="onClickedFacebookButton();">VarynDev</button>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h5><span class="linkedin-small"></span>&nbsp;Linked-In Message</h5>
            </div>
            <div class="panel-body text-center">
                <button type="button" class="btn btn-lg btn-info" name="linkedin-message" onclick="onClickedLinkedInButton();">VarynDev</button>
            </div>
        </div>
    </div>
</div><!-- /.container -->
<div class="container marketing">
    <div id="bottomAd" class="row">
        <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
        <!-- Varyn Responsive -->
        <ins class="adsbygoogle"
             style="display:block"
             data-ad-client="ca-pub-9118730651662049"
             data-ad-slot="5571172619"
             data-ad-format="auto"></ins>
        <script>
            (adsbygoogle = window.adsbygoogle || []).push({});
        </script>
    </div>
</div>
<?php
include_once(VIEWS_ROOT . 'footer.php');
?>
<script>
    function onClickedFacebookButton () {
        window.open('http://facebook.com');
    }

    function onClickedTwitterButton () {
        window.open('http://twitter.com');
    }

    function onClickedLinkedInButton () {
        window.open('http://linkedin.com');
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
                developerKey: "<?php echo($developerKey);?>",
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
    head.js("/common/modernizr.js", "/common/jquery.min.js", "/common/bootstrap.min.js", "/common/ie10-viewport-bug-workaround.js", "/common/enginesis.js", "/common/ShareHelper.js", "/common/varyn.js");
</script>
</body>
</html>