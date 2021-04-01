<?php
require_once('../services/common.php');
processSearchRequest();
$showSubscribe = getPostOrRequestVar('s', '0');
$page = 'template';
$pageTitle = 'Varyn: Great games you can play anytime, anywhere';
$pageDescription = 'Varyn makes games using technology that performs on the most popular platforms. Cross platform friendly technologies have created an opportunity to re-invent online games for an audience that moves seamlessly between desktop, tablet, and smart-phone.';
include_once(VIEWS_ROOT . 'header.php');
?>
<div class="container marketing">
    <div class="panel panel-primary panel-padded">
        <h3>Varyn Template Page</h3>
        <p>This is a template page to be used as a starting point for a new page.</p>
    </div>



    <div id="User-message-about-cookies">
        <h2>TO DO:</h2>
        <p>Fine print we need to show on any webpage. consider adding this to footer.php? then cookie the user if they have seen it.</p>
        <div class="code">
    /* + cookies-notify */
    .jb-slideup-promotion.id_cookies {
    position: fixed;
    left: auto;
    right: 0;
    bottom: 0;
    z-index: 1001;
    width: auto;
    height: auto;
    opacity: 1;
    }
    .jb-slideup-promotion.id_cookies.is_hidden {
    bottom: -450px;
    opacity: 0;
    }
    .id_cookies .jb-slideup-promotion__body {
    position: static;
    -webkit-transform: none;
    transform: none;
    }
    .id_cookies .jb-promotion__close-button {
    z-index: 5;
    background-color: #000;
    }
    .id_cookies .jb-promotion__close-button:before {
    display: inline-block;
    width: 1.4em;
    height: 1.4em;
    background: transparent url("data:image/svg+xml,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%22-388.5%20313.5%2014%2014%22%3E%3Cpath%20fill%3D%22%23fff%22%20d%3D%22M-388.5%20327.281v-1.181l5.709-5.709-5.709-5.709v-1.182h1.181l5.709%205.709%205.708-5.709h1.182v1.182l-5.708%205.709%205.708%205.709v1.182h-1.182l-5.708-5.709-5.709%205.709h-1.181z%22%2F%3E%3C%2Fsvg%3E") no-repeat 50%/.6em;
    content: '';
    color: #fff;
    font-size: 14px;
    line-height: 1.2;
    text-align: center;
    -webkit-transition: background-color .4s;
    transition: background-color .4s;
    }
    .id_cookies .jb-promotion__close-button:hover:before {
    background-color: red;
    }

    /* move close button to the left for iDevices */
    .id_cookies .jb-promotion__body._apple .jb-promotion__close-button {
    right: auto;
    left: 0;
    }

    .cookies-notify {
    overflow-y: auto;
    overflow-x: hidden;
    padding: 23px 23px 0;
    width: 380px;
    height: 110px;
    background: #000;
    color: #ccc;
    font: 12px/1.2 Menlo, Consolas, Monaco, Lucida Console, Liberation Mono, DejaVu Sans Mono, Bitstream Vera Sans Mono, Courier New, monospace, serif;
    -webkit-transition: height .4s;
    transition: height .4s;
    }
    .cookies-notify:before {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 15px;
    box-shadow: inset #000 0 5px 10px;
    content: '';
    }
    .cookies-notify__paragraph {
    margin: 0 0 1em;
    }
    .cookies-notify__link {
    color: inherit;
    text-decoration: underline;
    }
        </div>
        <div class="jb-promotion jb-slideup-promotion id_cookies">
            <div class="jb-promotion__body jb-slideup-promotion__body" style="width: auto; height: 100%; background-image: none;">
                <div class="cookies-notify" id="cookies-terminal">
                    <p class="cookies-notify__paragraph">Cookies help us improve our web content and deliver personalised content. By using this web site, you agree to our use of cookies.</p>
                    <p class="cookies-notify__paragraph">Type `man cookies' to <a href="/company/privacy.html#using-website" class="cookies-notify__link">learn more</a> or `exit' to close.</p>
                    <div class="cookies-notify__content"></div>
                    <div class="jquery-console-inner">
                        <textarea autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" class="jquery-console-typer" style="position: absolute; top: 0px; left: -9999px;"></textarea>
                        <div class="jquery-console-prompt-box">
                            <span class="jquery-console-prompt-label" style="display: inline;">~&nbsp;root# </span>
                            <span class="jquery-console-prompt">
                                <span class="jquery-console-cursor">&nbsp;</span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="jb-promotion__close-button jb-slideup-promotion__close-button" title="Close"></div>
    </div>
    <div>
        <h4>When the user logs in use the fine print:</h4>
        <p>You have Do Not Track enabled, or are browsing privately. Medium respects your request for privacy. To read in stealth mode, stay logged out. If you choose to sign in, we collect some information about your interactions with the site in order to personalize your experience, offer suggested reading, and connect you with your network. More details.
        To use Medium you must have cookies enabled.
        If you sign up with Twitter or Facebook, we’ll start you off with a network by automatically importing any followers/followees or friends already on Medium. Also, we’ll never post to Twitter or Facebook without your permission. For more info, please see Login FAQ.</p>
    </div>
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">Hot Games</h3>
        </div>
    </div>
    <div id="TemplatePageHotGames" class="row">
    </div>
    <div id="bottomAd" class="row">
    <?php
    $adProvider = 'google';
    include_once(VIEWS_ROOT . 'ad-spot.php');
    ?>
    </div>
</div><!-- /.container -->
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
                                varynApp.gameListGamesResponse(enginesisResponse.results.result, "TemplatePageHotGames", 15, "title");
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
                developerKey: "<?php echo($developerKey);?>",
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

    head.js("/common/modernizr.js", "/common/jquery.min.js", "/common/bootstrap.min.js", "/common/ie10-viewport-bug-workaround.js", "//platform.twitter.com/widgets.js", "https://apis.google.com/js/platform.js", "/common/enginesis.js", "/common/ShareHelper.js", "/common/varyn.js");

</script>
</body>
</html>