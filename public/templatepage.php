<?php
require_once('../services/common.php');
require_once('../views/sections.php');
processSearchRequest();
$showSubscribe = getPostOrRequestVar('s', '0');
$page = 'template';
$pageTitle = 'Varyn: Great games you can play anytime, anywhere';
$pageDescription = 'Varyn makes games using technology that performs on the most popular platforms. Cross platform friendly technologies have created an opportunity to re-invent online games for an audience that moves seamlessly between desktop, tablet, and smart-phone.';
$topGamesListId = 5;
include_once(VIEWS_ROOT . 'header.php');
?>
<div class="container">
    <div class="card m-2 p-4">
        <h3>Varyn Template Page</h3>
        <p>This is a template page to be used as a starting point for a new page.</p>
    </div>
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

    /**
     * This is the template page, so the page script may require design before just including this version. This is
     * the most simple version, is there is limited function on the page then define the page object here, otherwise
     * you should put it inside its own JS file in /common (see index.php for example).
     */
    var varynApp;
    var varynTemplatePage = function (varynApp, siteConfiguration) {
        "use strict";

        var enginesisSession = varynApp.getEnginesisSession();

        return {
            pageLoaded: function (pageViewParameters) {
                // Load the remaining page features
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
        varynApp.initApp(varynTemplatePage, pageParameters);
    });

    head.js("/common/modernizr.js", "/common/bootstrap.bundle.min.js", "//platform.twitter.com/widgets.js", "https://apis.google.com/js/platform.js", "/common/enginesis.js", "/common/ShareHelper.js", "/common/varyn.js");

</script>
</body>
</html>