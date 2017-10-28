<?php
require_once('../../services/common.php');
require_once('../../services/blog.php');
$page = 'blog';
$search = getPostOrRequestVar('q', null);
if ($search != null) {
    header('location:/allgames.php?q=' . $search);
    exit;
}
$showSubscribe = getPostOrRequestVar('s', '0');
require_once('conf-page-header.php');
include_once('../common/header.php');

$topicId = getPostOrRequestVar('tid', 0);
// Get 3 most recent topics
$topicList = $blog->getTopicList('', null, null, 1, 3);
if (empty($topicList)) {
    $errorCode = $enginesis->getLastErrorCode();
    $errorMessage = $enginesis->getLastErrorDescription();
} elseif ($topicId == 0) {
    $topicId = $topicList[0]->topic_id;
}
$blog->setConferenceTopic($topicId);
?>
    <div class="container">
        <div class="row conf-topic-container">
            <div id="conf-topic" class="col-sm-8">
                <?php echo($blog->getTopicContentAsHTML($topicId));?>
            </div>
            <div id="conf-sidebar" class="col-md-4">
                <div id="boxAd300" class="ad300">
                    <iframe src="<?php echo($webServer);?>/common/ad300.html" frameborder="0" scrolling="no" style="width: 300px; height: 250px; overflow: hidden; z-index: 9999; left: 0px; bottom: 0px; display: inline-block;"></iframe>
                </div>
                <p id="ad300-subtitle">Advertisement</p>
                <?php echo($blog->getCurrentPromo());?>
                <div id="conf-nav" class="conf-nav">
                    <?php echo($blog->getCurrentTopicListPreview($topicList, $topicId, 2, [$topicId]));?>
                </div>
            </div>
        </div>
        <?php echo($blog->getCurrentTopicRepliesPanel());?>
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
    include_once('../common/footer.php');
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
