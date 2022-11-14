<?php
require_once('../services/common.php');
require_once('../views/sections.php');
processSearchRequest();
$page = 'home';
$showSubscribe = getPostOrRequestVar('s', '0');
$extendedMessage = getPostOrRequestVar('m', null);
$topGamesListId = 5;
include_once(VIEWS_ROOT . 'header.php');
?>
    <div class="container top-promo-area">
        <div class="row">
            <div id="Missing" class="col-sm-8">
                <h2>Not Found Here</h2>
                <?php
                if ($extendedMessage) {
                    echo('<p class="text-danger">' . $extendedMessage . '</p>');
                }
                ?>
                <p class="text-danger">The content you are looking for is not at this location. The link may be incorrectly entered or the content you are looking for was moved to a new location.</p>
                <p>Please check it, or use our search field, or use one of our other links to find the content you are looking for.</p>
                <p><strong>But wait!</strong> While you are here, why not try one of these awesome games:</p>
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
</div>
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