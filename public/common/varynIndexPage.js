/**
 * Functionality supporting the index.php page. This script is loaded with the page load then pageLoaded is called
 * from varyn.initApp().
 *
 */
var varynIndexPage = function (varynApp, siteConfiguration) {
    "use strict";

    var enginesisSession = varynApp.getEnginesisSession();

    return {
        pageLoaded: function () {
            // Load Hot Games, New Games, and Promotions
            enginesisSession.gameListListGames(siteConfiguration.gameListIdTop, this.enginesisCallBack);
            enginesisSession.gameListListGames(siteConfiguration.gameListIdNew, this.enginesisCallBack);
            enginesisSession.promotionItemList(siteConfiguration.homePagePromoId, enginesisSession.getDateNow(), this.enginesisCallBack);
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
                            varynApp.promotionItemListResponse(results.result);
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
                            varynApp.gameListGamesResponse(enginesisResponse.results.result, fillDiv, null, false);
                        }
                        break;
                    default:
                        break;
                }
            }
        }

    };
};
