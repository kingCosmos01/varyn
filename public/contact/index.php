<?php
require_once('../../services/common.php');
$search = getPostOrRequestVar('q', null);
if ($search != null) {
    header('location:/allgames/?q=' . $search);
    exit;
}
$page = 'home';
$showSubscribe = getPostOrRequestVar('s', '0');

// These variables should only be accepted via POST:
$send = strtolower(getPostVar('send', ''));
$name = getPostVar('name', '');
$email = getPostVar('email', '');
$message = getPostVar('message', '');

// TODO: the form should provide hidden fields hackcheck and timestamp. Timestamp is set
// by the server and provides a number we expect in return to check if someone is hacking or
// botting. hackcheck is a honeypot we expect to be empty so if the we get a value there we
// know it was not a real user.
$hackCheck = getPostVar('captcha', '');
$timestamp = getPostVar('t', 0);

$errorMessage = '';
$errCode = '';
$messageSent = false;

function validateMessageParameters ($name, $email, $message) {
    return '';
}
include_once(VIEWS_ROOT . 'header.php');
if ($send == 'send') {
    if (validateMessageParameters($name, $email, $message) == '') {
        require_once('../services/EnginesisMailer.php');
        $message = "The following message was submitted on the contact form on Varyn.com\n\n$message";
        $enginesisMailer = new EnginesisMailer($email, 'support@varyn.com', 'Contact form from varyn.com', $message);
        $enginesisMailer->setServerStage($enginesis->getServerStage());
        $enginesisMailer->setFromName($name);
        $enginesisMailer->setLogger('debugLog');
        $errCode = $enginesisMailer->send();
        if ($errCode != '') {
            $errorMessage = "There was an issue trying to send your message ($errCode). The issue has been logged with technical support. Please try again later.<br/>" . $enginesisMailer->getExtendedStatusInfo();
            debugLog("enginesisMailer->send failed ($errCode) on " . $enginesis->getServerStage() . ": " . $enginesisMailer->getExtendedStatusInfo());
        }
        $messageSent = true;
    } else {
        $errorMessage = "There was an issue with your message: please check your entry and try again.";
    }
}
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
                        <label for="name">Name:</label><input type="text" name="name" required class="form-control" placeholder="Your name"/><br/>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label><input type="email" required name="email" class="form-control" placeholder="Your email address"/><br/>
                    </div>
                    <div id="email-contact-message" class="form-group">
                        <label for="message">Message:</label><textarea name="message" required class="form-control"></textarea><br/>
                    </div>
                    <div class="form-group" style="float: right;">
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
                serverStage: "<?php echo($stage);?>",
                languageCode: navigator.language || navigator.userLanguage
            },
            pageParameters = {
                showSubscribe: "<?php echo($showSubscribe);?>"
            };

        varynApp = varyn(siteConfiguration);
        varynApp.initApp(varynContactPage, pageParameters);
    });
    head.js("/common/modernizr.js", "/common/jquery.min.js", "/common/bootstrap.min.js", "/common/ie10-viewport-bug-workaround.js", "//platform.twitter.com/widgets.js", "https://apis.google.com/js/platform.js", "/common/enginesis.js", "/common/ShareHelper.js", "/common/varyn.js");
</script>
</body>
</html>