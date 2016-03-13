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
    <title>Varyn | About Varyn</title>
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
            enginesisGameListId = 7;

        function initApp() {
            var serverHostDomain = 'varyn' + serverStage + '.com',
                showSubscribe = '<?php echo($showSubscribe);?>';

            document.domain = serverHostDomain;
            window.EnginesisSession = enginesis(enginesisSiteId, 0, 0, 'enginesis.' + serverHostDomain, '', '', 'en', enginesisCallBack);
            EnginesisSession.gameListListGames(enginesisGameListId, null);
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
                    case "GameListListGames":
                        if (succeeded == 1) {
                            gameListGamesResponse(enginesisResponse.results.result, "AboutPageHotGames", null, false);
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
<div id="page_container">
    <?php
    include_once('common/header.php');
    ?>
    <div class="container top-promo-area">
        <div class="row">
            <div id="about-varyn" class="col-sm-8">
                <img src="/images/VarynCardLogo_md.png" width="200" height="200" border="0" align="left" style="margin-right: 20px;"/>
                <h1>About Varyn</h1><br />
                <p>Varyn is all about the fun! We want to bring you the best games we can make and the best games we can find, and we want you to be able to play these games with very little getting in your way.
                We are dedicated to bringing you fine games of any type or style as long as it is a good game that is fun to play.</p>
                <p>Varyn is a company of experienced game industry professionals who have been doing this for a long time. We love games: we love making them, we love playing them, we love talking about them!</p>
                <p>So join us on our quest, play some games, and while you are at it tell us what you like or do not like so we can help make this even better for you.</p>
                <h3>Our Mission</h3>
                <p>We have been playing games and building gaming websites and services for many years. Over the years we have seen a lot of other games sites use questionable practices to exploit their users. We do not believe in this approach.</p>
                <p>Our mission is to provide games and services that are fun to play and fair to our users. We will not exploit or take advantage of our users. While we may display ads and IAP our promise is always to be low-key and fair about it. Fun comes first!</p>
            </div>
            <div id="ad300" class="col-sm-4 col-md-2">
                <div id="boxAd300" class="ad300">
                    <iframe src="<?php echo($webServer);?>/common/ad300.html" frameborder="0" scrolling="no" style="width: 300px; height: 250px; overflow: hidden; z-index: 9999; left: 0px; bottom: 0px; display: inline-block;"></iframe>
                </div>
                <p id="ad300-subtitle" class="text-right"><small>Advertisement</small></p>
            </div>
        </div>
    </div>
    <div class="container marketing">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">Hot Games</h3>
            </div>
        </div>
        <div id="AboutPageHotGames" class="row">
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
    include_once('common/footer.php');
    ?>
</div>
</body>
</html>
