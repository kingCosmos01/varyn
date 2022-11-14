<?php
require_once('../services/common.php');
require_once('../views/sections.php');
$page = 'home';
processSearchRequest();
$showSubscribe = getPostOrRequestVar('s', '0');
$topGamesListId = 5;
include_once(VIEWS_ROOT . 'header.php');
?>
    <div class="container top-promo-area">
        <div class="row">
            <div id="Missing" class="col-sm-8">
                <h2>System Error</h2>
                <p class="text-danger">Something went wrong and our code could not handle your request. It could be a bug or it could be a system fault. We should check our logs soon.</p>
                <p>Please feel free to try again, but if the issue persists then give us some time to fix the error. If you like please send us an email explaining what you did so we can expedite our corrective action.</p>
                <p><strong>But wait!</strong> While you are here, why not try one of these awesome games:</p>
            </div><!-- /.Missing -->
            <div id="ad300" class="col-sm-4 col-md-2">
                <div id="boxAd300" class="ad300">
                <?php
                $adProvider = 'cpmstar';
                include_once(VIEWS_ROOT . 'ad-spot.php');
                ?>
                </div>
                <p id="ad300-subtitle" class="text-right"><small>Advertisement</small></p>
            </div>
        </div><!-- row -->
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
</div><!-- page_container -->
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
                                varynApp.gameListGamesResponse(enginesisResponse.results.result, "AboutPageHotGames", 15, "title");
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

    head.js("/common/modernizr.js", "/common/bootstrap.bundle.min.js", "//platform.twitter.com/widgets.js", "https://apis.google.com/js/platform.js", "/common/enginesis.js", "/common/ShareHelper.js", "/common/varyn.js");

</script>
</body>
</html>