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
            // Load Hot Games, New Games, and Promotions
            enginesisSession.gameListListGames(siteConfiguration.gameListIdTop, this.enginesisCallBack.bind(this));
            enginesisSession.gameListListGames(siteConfiguration.gameListIdNew, this.enginesisCallBack.bind(this));
            enginesisSession.promotionItemList(siteConfiguration.homePagePromoId, enginesisSession.getDateNow(), this.enginesisCallBack.bind(this));
        },

        showHomePagePromotionModule: function(enginesisResponse) {
            var innerHTML;
            var domElement;

            domElement = document.getElementById("PromoCarousel");
            if (domElement != null) {

            }
        },

        /**
         * makePromoModule will generate the HTML for a single standard promo module for the carousel.
         * @param isActive bool the active module. The first module should be active.
         * @param backgroundImg
         * @param titleText
         * @param altText
         * @param promoText
         * @param link
         * @param callToActionText
         * @returns {string}
         */
        makePromoModule: function (isActive, backgroundImg, titleText, altText, promoText, link, callToActionText) {
            var innerHtml,
                isActiveItem;

            if (isActive) {
                isActiveItem = " active";
            } else {
                isActiveItem = "";
            }
            innerHtml = "<div class=\"item" + isActiveItem + "\">";
            innerHtml += "<div class=\"sliderContainer\" style=\"background:url(" + backgroundImg + ") center center; background-size:cover;\">";
            innerHtml += "<div class=\"carousel-caption\"><h3>" + titleText + "</h3>";
            innerHtml += "<p class=\"sliderCaption\">" + promoText + "</p>";
            if (this.isURL(link)) {
                innerHtml += "<p><a class=\"btn btn-md btn-primary\" href=\"" + link + "\" role=\"button\">" + callToActionText + "</a></p>";
            } else {
                innerHtml += "<p>" + callToActionText + "</p>";
            }
            innerHtml += "</div></div></div>";
            return innerHtml;
        },

        /**
         * makePromoIndicators generates the HTML for all promo indicators used in the carousel.
         * @param numberOfPromos
         * @param activeIndicator
         * @returns {string}
         */
        makePromoIndicators: function (numberOfPromos, activeIndicator) {
            var innerHtml = "<ol class=\"carousel-indicators\">",
                activeClass,
                i;

            if (activeIndicator === undefined || activeIndicator == null || activeIndicator < 0 || activeIndicator >= numberOfPromos) {
                activeIndicator = 0;
            }
            for (i = 0; i < numberOfPromos; i ++) {
                if (i == activeIndicator) {
                    activeClass = " class=\"active\""
                }
                innerHtml += "<li data-target=\"#PromoCarousel\" data-slide-to=\"" + i + "\"" + activeClass + "></li>";
            }
            innerHtml += "</ol>";
            return innerHtml;
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
                    case "PromotionItemList":
                        if (succeeded == 1) {
                            this.showHomePagePromotionModule(results.result.row);
                        }
                        break;
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
                            varynApp.gameListGamesResponse(results.result, fillDiv, 30, false);
                        }
                        break;
                    default:
                        break;
                }
            }
        }

    };
};
