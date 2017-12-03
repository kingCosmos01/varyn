/**
 * Functions on the Blog page.
 */

var varynBlogPage = function (varynApp, siteConfiguration) {
    "use strict";

    var enginesisSession = varynApp.getEnginesisSession();

    return {
        pageLoaded: function (pageViewParameters) {
            // Load Hot Games
            enginesisSession.gameListListGames(siteConfiguration.gameListIdTop, this.enginesisCallBack.bind(this));
            enginesisSession.promotionItemList(siteConfiguration.blogPagePromoId, enginesisSession.getDateNow(), this.enginesisCallBack.bind(this));
        },

        showBlogPagePromotionModule: function(enginesisResponse) {
            var promoModuleHTML;
            var promoIndicatorHTML;
            var domElement;
            var numberOfPromos;
            var promotionItem;
            var i;

            domElement = document.getElementById("PromoCarousel");
            if (domElement != null && enginesisResponse != null && enginesisResponse.length > 0) {
                numberOfPromos = enginesisResponse.length;
                promoIndicatorHTML = this.makePromoIndicators(numberOfPromos, 0);
                promoModuleHTML = "<div id=\"PromoCarouselInner\" class=\"carousel-inner\" role=\"listbox\">";
                for (i = 0; i < numberOfPromos; i ++) {
                    promotionItem = enginesisResponse[i];
                    promoModuleHTML += this.makePromoModule(i == 0, promotionItem);
                }
                promoModuleHTML += "</div><a class=\"left carousel-control\" href=\"#PromoCarousel\" role=\"button\" data-slide=\"prev\"><span class=\"glyphicon glyphicon-chevron-left\"></span><span class=\"sr-only\">Previous</span></a><a class=\"right carousel-control\" href=\"#PromoCarousel\" role=\"button\" data-slide=\"next\"><span class=\"glyphicon glyphicon-chevron-right\"></span><span class=\"sr-only\">Next</span></a>";
                domElement.innerHTML = promoIndicatorHTML + promoModuleHTML;
            } else if (domElement != null) {
                domElement.innerText = "There are no promotions today.";
            }
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
                    case "PromotionItemList":
                        if (succeeded == 1) {
                            this.showBlogPagePromotionModule(results.result);
                        }
                        break;
                    case "GameListListGames":
                        if (succeeded == 1) {
                            varynApp.gameListGamesResponse(results.result, "AboutPageHotGames", 15, false);
                        }
                        break;
                    default:
                        break;
                }
            }
        }
    };
};

var debug = true;
var varynApp;
var manifest = [
    "/common/modernizr.js",
    "/common/jquery.min.js",
    "/common/bootstrap.min.js",
    "/common/ie10-viewport-bug-workaround.js",
//    "//platform.twitter.com/widgets.js",
//    "https://apis.google.com/js/platform.js"
];

if (debug) {
    manifest.push("/common/varyn.js");
    manifest.push("/common/ShareHelper.js");
    manifest.push("/common/ssoFacebook.js");
    manifest.push("/common/ssoGooglePlus.js");
    manifest.push("/common/ssoTwitter.js");
    manifest.push("/common/enginesis.js");
    manifest.push("/common/commonUtilities.js");

    head.js("/common/modernizr.js", "/common/jquery.min.js", "/common/bootstrap.min.js", "/common/ie10-viewport-bug-workaround.js", "//platform.twitter.com/widgets.js", "https://apis.google.com/js/platform.js", "/common/varyn.js", "/common/ShareHelper.js", "/common/ssoFacebook.js", "/common/ssoGooglePlus.js", "/common/ssoTwitter.js", "/common/enginesis.js", "/common/commonUtilities.js");
} else {
    manifest.push("/common/enginesis.min.js");
    manifest.push("/common/ShareHelper.js");
    manifest.push("/common/varyn.min.js");

    head.js("/common/modernizr.js", "/common/jquery.min.js", "/common/bootstrap.min.js", "/common/ie10-viewport-bug-workaround.js", "//platform.twitter.com/widgets.js", "https://apis.google.com/js/platform.js", "/common/enginesis.min.js", "/common/ShareHelper.js", "/common/varyn.min.js");
}

head.ready(function() {
    varynApp = varyn(siteConfiguration);
    varynApp.initApp(varynBlogPage, pageParameters);
});
