<?php
    require_once('../services/common.php');
    $page = 'home';
    $search = getPostOrRequestVar('q', null);
    if ($search != null) {
        header('location:/allgames.php?q=' . $search);
        exit;
    }
    $showSubscribe = getPostOrRequestVar('s', '0');
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
    <meta name="description" content="Varyn makes games using technology that performs on the most popular platforms. Cross platform friendly technologies have created an opportunity to re-invent online games for an audience that moves seamlessly between desktop, tablet, and smart-phone.">
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
    <meta property="og:description" content="Varyn makes games using technology that performs on the most popular platforms. Cross platform friendly technologies have created an opportunity to re-invent online games for an audience that moves seamlessly between desktop, tablet, and smart-phone.">
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
    <script type="text/javascript">

        var enginesisSiteId = <?php echo($siteId);?>,
            serverStage = "<?php echo($stage);?>",
            enginesisGameListId = 6,
            enginesisHomePagePromoId = 2;

        function initApp() {
            var serverHostDomain = 'varyn' + serverStage + '.com',
                showSubscribe = '<?php echo($showSubscribe);?>';

            document.domain = serverHostDomain;
            window.EnginesisSession = enginesis(enginesisSiteId, 0, 0, 'enginesis.' + serverHostDomain, '', '', 'en', enginesisCallBack);
            EnginesisSession.gameListListGames(enginesisGameListId, null);
            EnginesisSession.promotionItemList(enginesisHomePagePromoId, EnginesisSession.getDateNow(), null);
            if (showSubscribe == '1') {
                showSubscribePopup();
            }
        }

        function enginesisCallBack (enginesisResponse) {
            var succeeded,
                errorMessage;

            if (enginesisResponse != null && enginesisResponse.fn != null) {
                succeeded = enginesisResponse.results.status.success;
                errorMessage = enginesisResponse.results.status.message;
                switch (enginesisResponse.fn) {
                    case "NewsletterAddressAssign":
                        handleNewsletterServerResponse(succeeded);
                        break;
                    case "PromotionItemList":
                        if (succeeded == 1) {
                            promotionItemListResponse(enginesisResponse.results.result);
                        }
                        break;
                    case "GameListListGames":
                        if (succeeded == 1) {
                            gameListGamesResponse(enginesisResponse.results.result, "HomePageGamesArea", null, false);
                        }
                        break;
                    default:
                        break;
                }
            }
        }
    </script>
</head>
<body>
<?php
    include_once('common/header.php');
?>
<div class="container marketing">
    <div class="panel panel-primary panel-padded">
        <h2>Frequently Asked Questions</h2>
        <p>Need help? Here are the answers to the most common questions asked by our users.</p>
        <ul class="faq">
            <li class="faq-question">Do I need to Use Facebook to Login?<p class="faq-answer">We support login via Facebook, Twitter, Google Plus, or you can create an account with us using your email address.</p></li>
            <li class="faq-question">I forgot my password, how do I get it?<p class="faq-answer">You must use the forgot password form located on the logged out <a href="/profile.php">profile page</a>.</p></li>
            <li class="faq-question">Can I Block Another User From Contacting Me?<p class="faq-answer">Not at this time but that is a feature we are working on.</p></li>
            <li class="faq-question">I Earned Coins, Where Are They?<p class="faq-answer">Your coins are stored in your account. Make sure you are logged in.</p></li>
            <li class="faq-question">My Score Was On The Leader board, Why Is It No Longer There?<p class="faq-answer">Our leader boards refresh every week. You can view the all-time leader board to see how you rank against all players.</p></li>
            <li class="faq-question">There was a problem with a game I played! How do I report it?<p class="faq-answer">Send us email at support@varyn.com and include the game id or URL from where you played it along with any details about what happened.</p></li>
        </ul>
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
</div><!-- /.marketing -->
<?php
    include_once('common/footer.php');
?>
</body>
</html>