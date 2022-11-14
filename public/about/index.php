<?php
require_once('../../services/common.php');
require_once('../../views/sections.php');
processSearchRequest();
$page = 'home';
$pageTitle = 'About Varyn';
$pageDescription = 'Learn more about who is Varyn, our mission, and what we stand for.';
$showSubscribe = getPostOrRequestVar('s', '0');
$topGamesListId = 5;

include_once(VIEWS_ROOT . 'header.php');
?>
    <div class="container top-promo-area">
        <div class="row">
            <div id="about-varyn" class="col-sm-8">
                <img src="/images/VarynCardLogo_md.png" width="200" height="200" alt="Varyn card logo" align="left" style="margin-right: 20px;"/>
                <h1>About Varyn</h1><br />
                <p>Varyn is all about fun! We want to bring you the best games we can make and the best games we can find, and we want you to be able to play these games with very little getting in your way.
                We are dedicated to bringing you fine games of any type or style as long as it is a good game that is fun to play.</p>
                <p>Varyn is a company of experienced game industry professionals who have been doing this for a long time. We love games: we love making them, we love playing them, we love talking about them!</p>
                <p>So join us on our quest, play some games, and while you are at it tell us what you like or do not like so we can help make this even better for you.</p>
                <h3>Our Mission</h3>
                <p>We have been playing games and building gaming websites and services for many years. Over the years we have seen a lot of other games sites use questionable practices to exploit their users. We do not believe in this approach.</p>
                <p>Our mission is to provide games and services that are fun to play and fair to our users. We will not exploit or take advantage of our users. While we may display ads and IAP our promise is always to be low-key and fair about it. Fun comes first!</p>
            </div>
            <div id="ad300" class="col-sm-4 col-md-2">
                <div id="boxAd300" class="ad300">
                <?php
                $adProvider = 'cpmstar';
                include_once(VIEWS_ROOT . 'ad-spot.php');
                ?>
                </div>
                <p id="ad300-subtitle" class="text-right"><small>Advertisement</small></p>
            </div>
        </div>
    </div>
    <div class="container">
        <?php buildGamesSection($topGamesListId, 'Hot games'); ?>
        <div id="bottomAd" class="row">
        <?php
        $adProvider = 'google';
        include_once(VIEWS_ROOT . 'ad-spot.php');
        ?>
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
                    // Load remaining page features
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
                    developerKey: "<?php echo(ENGINESIS_DEVELOPER_API_KEY);?>",
                    serverStage: "<?php echo($serverStage);?>",
                    authToken: "<?php echo($authToken);?>",
                    languageCode: navigator.language || navigator.userLanguage
                },
                pageParameters = {
                    showSubscribe: "<?php echo($showSubscribe);?>"
                };

            varynApp = varyn(siteConfiguration);
            varynApp.initApp(varynAboutPage, pageParameters);
        });

        head.js("/common/modernizr.js", "/common/bootstrap.bundle.min.js", "/common/enginesis.js", "/common/ShareHelper.js", "/common/commonUtilities.js", "/common/varyn.js");

    </script>
</div>
</body>
</html>
