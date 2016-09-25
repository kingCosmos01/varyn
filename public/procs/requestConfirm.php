<?php
    /**
     * Handle a request approval.
     *  - Friend request
     *  - Team request
     *  - Quest request
     * @Date: 1/11/16
     */
    require_once('../../services/common.php');
    $debug = (int) strtolower(getPostOrRequestVar('debug', 0));
    $page = 'requestConfirmation';
    $search = getPostOrRequestVar('q', null);
    if ($search != null) {
        header('location:/allgames.php?q=' . $search);
        exit;
    }
    processTrackBack();
    $user_id = getPostOrRequestVar('u', 0);
    $site_id = getPostOrRequestVar('s', 0);
    $token = getPostOrRequestVar('t', '');
    $requestId = getPostOrRequestVar('r', '');
    $approval = getPostOrRequestVar('a', '');
    $action = getPostOrRequestVar('action', '');
    $errorMessage = '';

    if ($isLoggedIn) {
        $userInfo = getVarynUserCookieObject();
        $authToken = $userInfo->authtok;
        $user_id = $userInfo->user_id; // only use the user_id that is logged in
        $site_id = $userInfo->site_id;
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Process Request | Varyn</title>
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
    <meta name="description" content="Process user request at Varyn.com.">
    <meta name="author" content="Varyn">
    <meta name="google-signin-client_id" content="AIzaSyD22xO1Z71JywxmKfovgRuqZUHRFhZ8i7A.apps.googleusercontent.com">
    <link href="../common/bootstrap.min.css" rel="stylesheet">
    <link href="../common/carousel.css" rel="stylesheet">
    <link href="../common/varyn.css" rel="stylesheet">
    <link rel="icon" href="../favicon.ico">
    <link rel="icon" type="image/png" href="../favicon-48x48.png" sizes="48x48"/>
    <link rel="icon" type="image/png" href="../favicon-196x196.png" sizes="196x196">
    <link rel="icon" type="image/png" href="../favicon-160x160.png" sizes="160x160">
    <link rel="icon" type="image/png" href="../favicon-96x96.png" sizes="96x96">
    <link rel="icon" type="image/png" href="../favicon-16x16.png" sizes="16x16">
    <link rel="icon" type="image/png" href="../favicon-32x32.png" sizes="32x32">
    <link rel="apple-touch-icon" href="../apple-touch-icon-60x60.png" sizes="60x60"/>
    <link rel="apple-touch-icon" href="../apple-touch-icon-72x72.png" sizes="72x72"/>
    <link rel="apple-touch-icon" href="../apple-touch-icon-76x76.png"/>
    <link rel="apple-touch-icon" href="../apple-touch-icon-76x76.png" sizes="76x76"/>
    <link rel="apple-touch-icon" href="../apple-touch-icon-114x114.png" sizes="114x114"/>
    <link rel="apple-touch-icon" href="../apple-touch-icon-120x120.png" sizes="120x120"/>
    <link rel="apple-touch-icon" href="../apple-touch-icon-152x152.png" sizes="152x152"/>
    <link rel="shortcut icon" href="../favicon-196x196.png">
    <meta property="fb:app_id" content="" />
    <meta property="fb:admins" content="726468316" />
    <meta property="og:title" content="Process user request at Varyn.com">
    <meta property="og:url" content="//www.varyn.com/procs/requestConfirm.php">
    <meta property="og:site_name" content="Varyn">
    <meta property="og:description" content="Process user request at Varyn.com.">
    <meta property="og:image" content="//www.varyn.com/images/1200x900.png"/>
    <meta property="og:image" content="//www.varyn.com/images/1024.png"/>
    <meta property="og:image" content="//www.varyn.com/images/1200x600.png"/>
    <meta property="og:image" content="//www.varyn.com/images/600x600.png"/>
    <meta property="og:image" content="//www.varyn.com/images/2048x1536.png"/>
    <meta property="og:type" content="game"/>
    <meta name="twitter:card" content="photo"/>
    <meta name="twitter:site" content="@varyndev"/>
    <meta name="twitter:creator" content="@varyndev"/>
    <meta name="twitter:title" content="Varyn: Great games you can play anytime, anywhere"/>
    <meta name="twitter:image:src" content="//www.varyn.com/images/600x600.png"/>
    <meta name="twitter:domain" content="varyn.com"/>
    <script src="../common/head.min.js"></script>
</head>
<body>
<?php
    include_once('../common/header.php');
?>
<div class="container marketing">
    <div class="panel panel-info panel-padded">
        <h1>Request</h1>
        <p>Your request is being processed.</p>
        <p><a href="/profile.php">Profile</a></p>
        <p><a href="mailto:support@varyn.com">Contact Support</a></p>
    </div>
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
    include_once('../common/footer.php');
?>
<script>

    var varynApp;

    head.ready(function() {
        var siteConfiguration = {
                siteId: <?php echo($siteId);?>,
                gameId: 0,
                gameGroupId: 0,
                serverStage: "<?php echo($stage);?>",
                languageCode: navigator.language || navigator.userLanguage,
                developerKey: '<?php echo($developerKey);?>',
                facebookAppId: '<?php echo($socialServiceKeys[2]['app_id']);?>',
                authToken: '<?php echo($authToken);?>'
            },
            resetPasswordPageParameters = {
                errorFieldId: "<?php echo($errorFieldId);?>",
                inputFocusId: "<?php echo($inputFocusId);?>",
                showSubscribe: "<?php echo($showSubscribe);?>"
            };
        varynApp = varyn(siteConfiguration);
    });

    head.js("/common/modernizr.js", "/common/jquery.min.js", "/common/bootstrap.min.js", "/common/ie10-viewport-bug-workaround.js", "//platform.twitter.com/widgets.js", "https://apis.google.com/js/platform.js", "/common/enginesis.js", "/common/ShareHelper.js", "/common/commonUtilities.js", "/common/varyn.js", "/common/varynResetPasswordPage.js");

</script>
</body>
</html>
