<?php
    require_once('../services/common.php');
    $page = 'home';
    $search = getPostOrRequestVar('q', null);
    if ($search != null) {
        header('location:/allgames.php?q=' . $search);
        exit;
    }
    processTrackBack();
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
    </head>
  <body>
  <?php
      include_once('common/header.php');
  ?>
    <div class="container top-promo-area">
        <div class="row">
            <div id="PromoCarousel" class="carousel slide carousel-fade col-sm-8" data-ride="carousel">
              <ol class="carousel-indicators">
                  <li data-target="#PromoCarousel" data-slide-to="0" class="active"></li>
                  <li data-target="#PromoCarousel" data-slide-to="1"></li>
                  <li data-target="#PromoCarousel" data-slide-to="2"></li>
              </ol>
              <div class="carousel-inner" role="listbox">
                <div class="item active">
                  <div class="sliderContainer" style="background:url(/images/promos/VarynPromoHome.jpg) center center; background-size:cover;">
                    <div class="carousel-caption">
                      <h3>Welcome to Varyn!</h3>
                      <p class="sliderCaption">We have games for all ages and the most popular platforms. Follow us for updates.</p>
                      <p><a class="btn btn-md btn-danger" href="JavaScript:showSubscribePopup();" role="button">Sign up now</a></p>
                    </div>
                  </div>
                </div>
                <div class="item">
                  <div class="sliderContainer" style="background:url(/images/promos/MatchMasterPromoHome.jpg) center center; background-size:cover;">
                    <div class="carousel-caption">
                      <h3>Match Master 3000</h3>
                      <p class="sliderCaption">A match game like no other: 8 different play patterns organized into a quest for the Match Master crown.</p>
                      <p><a class="btn btn-md btn-danger" href="/play.php?gameid=MatchMaster3000" role="button">Play Now &gt;</a></p>
                    </div>
                  </div>
                </div>
                <div class="item">
                  <div class="sliderContainer" style="background:url(/images/promos/TopChefPromoHome.jpg) center center; background-size:cover;">
                    <div class="carousel-caption">
                      <h3>Top Chef Memory Challenge</h3>
                      <p class="sliderCaption">A Top Chef knows the recipe for success. Test your skills in the ultimate memory challenge.</p>
                      <p><a class="btn btn-md btn-danger" href="http://www.bravotv.com/media/games/top-chef-memory-challenge/index.html" role="button">Play Now &gt;</a></p>
                    </div>
                  </div>
                </div>
              </div>
              <a class="left carousel-control" href="#PromoCarousel" role="button" data-slide="prev">
                <span class="glyphicon glyphicon-chevron-left"></span>
                <span class="sr-only">Previous</span>
              </a>
              <a class="right carousel-control" href="#PromoCarousel" role="button" data-slide="next">
                <span class="glyphicon glyphicon-chevron-right"></span>
                <span class="sr-only">Next</span>
              </a>
            </div><!-- /.carousel -->
            <div id="ad300" class="col-sm-4 col-md-2">
                <div id="boxAd300" class="ad300">
                    <iframe src="<?php echo($webServer);?>/common/ad300.html" frameborder="0" scrolling="no" style="width: 300px; height: 250px; overflow: hidden; z-index: 9999; left: 0px; bottom: 0px; display: inline-block;"></iframe>
                </div>
                <p id="ad300-subtitle" class="text-right"><small>Advertisement</small></p>
            </div>
        </div><!-- row -->
    </div><!-- /top-promo-area -->
    <div class="container marketing">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">Hot Games</h3>
            </div>
        </div>
        <div id="HomePageTopGames" class="row">
        </div>
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">New Games</h3>
            </div>
        </div>
        <div id="HomePageNewGames" class="row">
        </div>
        <div id="bottomAd" class="row">
            <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
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
  <script type="text/javascript">

      var varynApp;

      head.ready(function() {
          var siteConfiguration = {
              siteId: <?php echo($siteId);?>,
              serverStage: "<?php echo($stage);?>",
              languageCode: navigator.language || navigator.userLanguage
          };
          varynApp = varyn(siteConfiguration);
          varynApp.initApp(varynIndexPage);
      });

      head.js("/common/modernizr.js", "/common/jquery.min.js", "/common/bootstrap.min.js", "/common/ie10-viewport-bug-workaround.js", "//connect.facebook.net/en_US/all.js", "//platform.linkedin.com/in.js", "//platform.twitter.com/widgets.js", "https://apis.google.com/js/platform.js", "/common/enginesis.js", "/common/ShareHelper.js", "common/varyn.js", "common/varynIndexPage.js");

      (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
      ga('create', 'UA-41765479-1', 'varyn.com');
      ga('send', 'pageview');
      <?php if (strlen($search) > 0) { ?>
      ga('send', 'event', 'game', 'search', '<?php echo($search);?>', 1);
      <?php } ?>
  </script>
  </body>
</html>