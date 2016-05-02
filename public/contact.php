<?php
    require_once('../services/common.php');
    $page = 'contact';
    $search = getPostOrRequestVar('q', null);
    if ($search != null) {
        header('location:/allgames.php?q=' . $search);
        exit;
    }
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Varyn: Great games you can play anytime, anywhere</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Access-Control-Allow-Origin" content="*">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta http-equiv="cache-control" content="max-age=0" />
    <meta http-equiv="cache-control" content="no-cache" />
    <meta http-equiv="expires" content="0" />
    <meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT" />
    <meta http-equiv="pragma" content="no-cache" />
    <meta name="format-detection" content="telephone=no" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="mobile-web-app-capable" content="yes" />
    <meta name="description" content="Contact Varyn. Varyn makes games using technology that performs on the most popular platforms. Cross platform friendly technologies have created an opportunity to re-invent online games for an audience that moves seamlessly between desktop, tablet, and smart-phone.">
    <meta name="author" content="Varyn">
    <link href="/common/bootstrap.min.css" rel="stylesheet">
    <link href="/common/carousel.css" rel="stylesheet">
    <link href="/common/varyn.css" rel="stylesheet">
    <link rel="icon" href="/favicon.ico">
    <link rel="icon" type="image/png" href="/favicon-48x48.png" sizes="48x48"/>
    <link rel="icon" type="image/png" href="/favicon-196x196.png" sizes="196x196">
    <link rel="icon" type="image/png" href="/favicon-160x160.png" sizes="160x160">
    <link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96">
    <link rel="icon" type="image/png" href="/favicon-16x16.png" sizes="16x16">
    <link rel="icon" type="image/png" href="/favicon-32x32.png" sizes="32x32">
    <link rel="apple-touch-icon" href="/apple-touch-icon-60x60.png" sizes="60x60"/>
    <link rel="apple-touch-icon" href="/apple-touch-icon-72x72.png" sizes="72x72"/>
    <link rel="apple-touch-icon" href="/apple-touch-icon-76x76.png"/>
    <link rel="apple-touch-icon" href="/apple-touch-icon-76x76.png" sizes="76x76"/>
    <link rel="apple-touch-icon" href="/apple-touch-icon-114x114.png" sizes="114x114"/>
    <link rel="apple-touch-icon" href="/apple-touch-icon-120x120.png" sizes="120x120"/>
    <link rel="apple-touch-icon" href="/apple-touch-icon-152x152.png" sizes="152x152"/>
    <link rel="shortcut icon" href="/favicon-196x196.png">
    <meta property="fb:app_id" content="" />
    <meta property="fb:admins" content="726468316" />
    <meta property="og:title" content="Varyn: Great games you can play anytime, anywhere">
    <meta property="og:url" content="http://www.varyn.com">
    <meta property="og:site_name" content="Varyn">
    <meta property="og:description" content="Contact Varyn. Varyn makes games using technology that performs on the most popular platforms. Cross platform friendly technologies have created an opportunity to re-invent online games for an audience that moves seamlessly between desktop, tablet, and smart-phone.">
    <meta property="og:image" content="http://www.varyn.com/images/1200x900.png"/>
    <meta property="og:image" content="http://www.varyn.com/images/1024.png"/>
    <meta property="og:image" content="http://www.varyn.com/images/1200x600.png"/>
    <meta property="og:image" content="http://www.varyn.com/images/600x600.png"/>
    <meta property="og:image" content="http://www.varyn.com/images/2048x1536.png"/>
    <meta property="og:type" content="game"/>
    <meta name="twitter:card" content="photo"/>
    <meta name="twitter:site" content="@varyndev"/>
    <meta name="twitter:creator" content="@varyndev"/>
    <meta name="twitter:title" content="Varyn: Great games you can play anytime, anywhere"/>
    <meta name="twitter:image:src" content="http://www.varyn.com/images/600x600.png"/>
    <meta name="twitter:domain" content="varyn.com"/>
    <script src="/common/head.min.js"></script>
</head>
<body>
<?php
    include_once('common/header.php');
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
    include_once('common/footer.php');
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