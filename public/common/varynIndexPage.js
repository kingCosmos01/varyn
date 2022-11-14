/**
 * Functionality supporting the index.php page. This script is loaded with the page load then pageLoaded is called
 * from varyn.initApp().
 *
 */
var varynIndexPage = function (varynApp, siteConfiguration) {
    "use strict";

    var enginesisSession = varynApp.getEnginesisSession();

    return {
        pageLoaded: function (pageViewParameters) {
            // Once the page is loaded, take action based on the type of user who is logged in.
        },

        makeCallToActionButton: function(link, callToActionText) {
            var innerHTML;
            if (link.indexOf("showSubscribePopup") >= 0) {
                innerHTML = "<button type=\"button\" class=\"btn btn-md btn-danger\" data-toggle=\"modal\" data-target=\"#modal-subscribe\" onclick=\"" + link + "\">" + callToActionText + "</button>";
            } else {
                innerHTML = "<p><a class=\"btn btn-md btn-primary\" href=\"" + link + "\" role=\"button\">" + callToActionText + "</a></p>";
            }
            return innerHTML;
        },

        /**
         * Callback to handle responses from Enginesis.
         * @param enginesisResponse
         */
        enginesisCallBack: function (enginesisResponse) {
            var succeeded,
                errorMessage,
                results,
                fillDiv,
                listId;

            if (enginesisResponse != null && enginesisResponse.fn != null) {
                results = enginesisResponse.results;
                succeeded = results.status.success;
                errorMessage = results.status.message;
                switch (enginesisResponse.fn) {
                    case "GameListListGames":
                        if (succeeded == 1) {
                            if (results.passthru !== undefined && results.passthru.game_list_id !== undefined) {
                                listId = results.passthru.game_list_id;
                                if (listId == siteConfiguration.gameListIdTop) {
                                    fillDiv = "HomePageTopGames";
                                } else {
                                    fillDiv = "HomePageNewGames";
                                }
                            } else {
                                fillDiv = "HomePageTopGames";
                            }
                            varynApp.gameListGamesResponse(results.result, fillDiv, 30, null);
                        }
                        break;
                    default:
                        break;
                }
            }
        }
    };
};

var varynApp;
var debug = true;
var manifest = [
    "/common/modernizr.js",
    "/common/bootstrap.bundle.min.js",
    "//platform.twitter.com/widgets.js",
    "https://apis.google.com/js/platform.js"
];

if (debug) {
    manifest = manifest.concat([
        "/common/enginesis.js",
        "/common/ShareHelper.js",
        "/common/commonUtilities.js",
        "/common/ssoFacebook.js",
        "/common/ssoGoogle.js",
        "/common/ssoTwitter.js",
        "/common/varyn.js"
    ]);
} else {
    manifest = manifest.concat([
        "/common/enginesis.min.js",
        "/common/ShareHelper.js",
        "/common/varyn.min.js"
    ]);
}

head.load(manifest, function() {
    varynApp = varyn(siteConfiguration);
    varynApp.initApp(varynIndexPage, pageParameters);
});
