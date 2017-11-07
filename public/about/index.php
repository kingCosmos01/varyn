<?php
require_once('../../services/common.php');
$search = getPostOrRequestVar('q', null);
if ($search != null) {
    header('location:/allgames/?q=' . $search);
    exit;
}
$page = 'home';
$pageTitle = 'About Varyn';
$showSubscribe = getPostOrRequestVar('s', '0');
include_once(VIEWS_ROOT . 'header.php');
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
    include_once(VIEWS_ROOT . 'footer.php');
    ?>
    <script type="text/javascript">

        var varynApp;
        var varynAboutPage = function (varynApp, siteConfiguration) {
            "use strict";

            var enginesisSession = varynApp.getEnginesisSession();

            return {
                pageLoaded: function (pageViewParameters) {
                    // Load Hot Games
                    enginesisSession.gameListListGames(siteConfiguration.gameListIdTop, this.enginesisCallBack);
                },

                /**
                 * Callback to handle responses from Enginesis.
                 * @param enginesisResponse
                 */
                enginesisCallBack: function (enginesisResponse) {
                    var succeeded,
                        errorMessage,
                        results;

                    if (enginesisResponse != null && enginesisResponse.fn != null) {
                        results = enginesisResponse.results;
                        succeeded = results.status.success;
                        errorMessage = results.status.message;
                        switch (enginesisResponse.fn) {
                            case "NewsletterAddressAssign":
                                varynApp.handleNewsletterServerResponse(succeeded);
                                break;
                            case "GameListListGames":
                                if (succeeded == 1) {
                                    varynApp.gameListGamesResponse(enginesisResponse.results.result, "AboutPageHotGames", 15, false);
                                }
                                break;
                            default:
                                break;
                        }
                    }
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
            varynApp.initApp(varynAboutPage, pageParameters);
        });

        head.js("/common/modernizr.js", "/common/jquery.min.js", "/common/bootstrap.min.js", "/common/ie10-viewport-bug-workaround.js", "//platform.twitter.com/widgets.js", "https://apis.google.com/js/platform.js", "/common/enginesis.js", "/common/ShareHelper.js", "/common/varyn.js");

    </script>
</div>
</body>
</html>