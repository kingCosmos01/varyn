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
                promoModuleHTML += "</div><a class=\"left carousel-control\" href=\"#PromoCarousel\" role=\"button\" data-slide=\"prev\"><span class=\"iconChevronLeft\"></span><span class=\"sr-only\">Previous</span></a><a class=\"right carousel-control\" href=\"#PromoCarousel\" role=\"button\" data-slide=\"next\"><span class=\"iconChevronRight\"></span><span class=\"sr-only\">Next</span></a>";
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
                            varynApp.gameListGamesResponse(results.result, "AboutPageHotGames", 15, "plays");
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
        "/common/ssoApple.js",
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
    varynApp.initApp(varynBlogPage, pageParameters);
});
