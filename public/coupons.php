<?php
    require_once('../services/common.php');
    $page = 'offers';
    $search = getPostOrRequestVar('q', '');
 ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Varyn | Coupons and Offers</title>
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
        <meta name="description" content="Get Coupons and Offers at Varyn! Varyn makes games using technology that performs on the most popular platforms. Cross platform friendly technologies have created an opportunity to re-invent online games for an audience that moves seamlessly between desktop, tablet, and smart-phone.">
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
        <meta property="og:title" content="Varyn | Coupons and Offers">
        <meta property="og:url" content="http://www.varyn.com">
        <meta property="og:site_name" content="Varyn">
        <meta property="og:description" content="Get Coupons and Offers at Varyn! Varyn makes games using technology that performs on the most popular platforms. Cross platform friendly technologies have created an opportunity to re-invent online games for an audience that moves seamlessly between desktop, tablet, and smart-phone.">
        <meta property="og:image" content="http://www.varyn.com/images/1200x900.png"/>
        <meta property="og:image" content="http://www.varyn.com/images/1024.png"/>
        <meta property="og:image" content="http://www.varyn.com/images/1200x600.png"/>
        <meta property="og:image" content="http://www.varyn.com/images/600x600.png"/>
        <meta property="og:image" content="http://www.varyn.com/images/2048x1536.png"/>
        <meta property="og:type" content="game"/>
        <meta name="twitter:card" content="photo"/>
        <meta name="twitter:site" content="@varyndev"/>
        <meta name="twitter:creator" content="@varyndev"/>
        <meta name="twitter:title" content="Varyn | Coupons and Offers"/>
        <meta name="twitter:image:src" content="http://www.varyn.com/images/600x600.png"/>
        <meta name="twitter:domain" content="varyn.com"/>
        <script src="/common/head.min.js"></script>
        <script type="text/javascript">

        var enginesisSiteId = <?php echo($siteId);?>,
            serverStage = "<?php echo($stage);?>";

        function initApp() {
            var searchString = "<?php echo($search);?>",
                serverHostDomain = 'jumpydot' + serverStage + '.com';

            document.domain = serverHostDomain;
            window.EnginesisSession = enginesis(enginesisSiteId, 0, 0, 'enginesis.' + serverHostDomain, '', '', 'en', enginesisCallBack);
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
                    case "SiteListGames":
                    case "GameFind":
                        if (succeeded == 1) {
                            gameListGamesResponse(enginesisResponse.results.result, "AllGamesArea", null, true);
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
    <div id="AllGamesArea" class="row">
        <div class="col-xs-12" style="min-height: 1190px;">
            <noscript>
                <p>Coupons powered by <a href="http://www.coupons.com?pid=13903&nid=10&zid=xh20&bid=1379910001">Coupons.com</a></p>
            </noscript>
            <script id="scriptId_718x940_117571" type="text/javascript" src="//bcg.coupons.com/?scriptId=117571&bid=1379910001&format=718x940&bannerType=3&channel=Coupon%20Page">
            </script>
        </div>
    </div>
    <div id="bottomAd" class="row">
        <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
        <!-- JumpyDot Responsive -->
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
</div><!-- /.container -->
</body>
</html>