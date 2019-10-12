<?php
require_once('../../services/common.php');
$page = 'games';
$pageTitle = 'All games at Varyn.com';
$pageDescription = 'Discover the games we offer or search for the game you are looking for.';
$search = getPostOrRequestVar('q', '');
$showSubscribe = getPostOrRequestVar('s', '0');
include_once(VIEWS_ROOT . 'header.php');
?>
    <div class="container marketing">
        <?php
            if ($search != '') {
         ?>
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">Search for:</strong> <?php echo($search);?></h3>
            </div>
        </div>
        <?php
            }
         ?>
        <div id="AllGamesArea" class="row">
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
    include_once(VIEWS_ROOT . 'footer.php');
    ?>
    <script type="text/javascript">

        var varynApp;

        head.ready(function() {
            varynApp = varyn(siteConfiguration);
            varynApp.initApp(varynAllGamesPage, pageParameters);
        });

        head.js("/common/modernizr.js", "/common/jquery.min.js", "/common/bootstrap.min.js", "/common/ie10-viewport-bug-workaround.js", "//platform.twitter.com/widgets.js", "https://apis.google.com/js/platform.js", "/common/enginesis.js", "/common/ShareHelper.js", "/common/commonUtilities.js", "/common/varyn.js", "/common/varynAllGamesPage.js");

    </script>
</body>
</html>