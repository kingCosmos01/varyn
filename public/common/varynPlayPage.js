/**
 * Functionality supporting the play.php page. This script is loaded with the page load then pageLoaded is called
 * from varyn.initApp().
 *
 */
var varynPlayPage = function (varynApp, siteConfiguration) {
    "use strict";

    var enginesisSession = varynApp.getEnginesisSession();

    function setGameDeveloper (developerInfo) {
        var developerInfoDiv = document.getElementById("gameDeveloper");
        if (developerInfoDiv != null) {
            if (developerInfo.logo_img_url != null && developerInfo.logo_img_url != '') {
                developerInfoDiv.innerHTML = "<h4>Developed By:</h4><p><a href=\"" + developerInfo.web_site_url + "\" target=\"_new\"><img src=\"//www.enginesis.com" + developerInfo.logo_img_url + "\" width=100 height=50 style=\"margin-right: 20px;\"/></a></p>";
            } else {
                developerInfoDiv.innerHTML = "<h4>Developed By:</h4><p><a href=\"" + developerInfo.web_site_url + "\" target=\"_new\">" + developerInfo.organization_name + "</a></p>";
            }
        }
    }

    function fitGameToFullScreen () {
        // Resize the game containers to the full extent of the available window, then let the game deal with that stage size.
        var elementDiv,
            width,
            height;

        width = window.innerWidth;
        height = window.innerHeight;
        elementDiv = document.getElementById("gameContainer-iframe");
        if (elementDiv != null) {
            elementDiv.style.width = width + "px";
            elementDiv.style.maxWidth = width + "px";
            elementDiv.style.height = height + "px";
            elementDiv.style.maxHeight = height + "px";
        }
        elementDiv = document.getElementById("gameContainer");
        if (elementDiv != null) {
            elementDiv.style.width = width + "px";
            elementDiv.style.maxWidth = width + "px";
            elementDiv.style.height = height + "px";
            elementDiv.style.maxHeight = height + "px";
        }
        elementDiv = document.getElementById("topContainer");
        if (elementDiv != null) {
            elementDiv.style.margin = "0";
            elementDiv.style.padding = "0";
            elementDiv.style.top = "0";
            elementDiv.style.left = "0";
            elementDiv.style.width = width + "px";
            elementDiv.style.maxWidth = width + "px";
            elementDiv.style.height = height + "px";
            elementDiv.style.maxHeight = height + "px";
        }
        elementDiv = document.body;
        if (elementDiv != null) {
            elementDiv.style.margin = "0";
            elementDiv.style.padding = "0";
            elementDiv.style.top = "0";
            elementDiv.style.left = "0";
            elementDiv.style.width = width + "px";
            elementDiv.style.maxWidth = width + "px";
            elementDiv.style.height = height + "px";
            elementDiv.style.maxHeight = height + "px";
        }
    }

    function resetGameFrameSize () {
        fitGameToFullScreen();
        window.location.reload();
    }

    function setGameContainer (pageViewParameters) {
        var gameContainerDiv = document.getElementById("gameContainer"),
            gameContainerIframe,
            requiredAspectRatio,
            width = pageViewParameters.width,
            height = pageViewParameters.height,
            pluginId = pageViewParameters.pluginId,
            isTouchDevice = enginesisSession.isTouchDevice(),
            embedOnTouchDevice = false;

        // we want to size the container to the size of the game and center it in the panel div.

        enginesisSession.gameWidth = width;
        enginesisSession.gameHeight = height;
        if (height < width) {
            requiredAspectRatio = height / width;
        } else {
            requiredAspectRatio = width / height;
        }
        enginesisSession.gameAspectRatio = requiredAspectRatio; // cache the ideal aspect ratio to use when the frame is resized
        enginesisSession.gamePluginId = pluginId;
        if (gameContainerDiv != null) {
            embedOnTouchDevice = isTouchDevice && enginesisSession.gamePluginId == 9;
            if (enginesisSession.gamePluginId == 9) {
                gameContainerDiv.style['overflow-y'] = "scroll";
                gameContainerDiv.style['overflow-x'] = "hidden";
                gameContainerDiv.style['-webkit-overflow-scrolling'] = "touch";
                if (isTouchDevice) {
                    // if we are on mobile and this is an Embed type game, just embed the game link directly into the div.
                    showOnlyTheGame(true);
                }
                //                    gameContainerDiv.style.width = width + "px";
                //                    gameContainerDiv.style.maxWidth = width + "px";
                //                    gameContainerDiv.style.height = height + "px";
                //                    gameContainerDiv.style.maxHeight = height + "px";
                gameContainerDiv.style.paddingTop = "0";
                // insertAndExecute("gameContainer", gameData.game_link);
            } else {
                //                    if (EnginesisSession.gamePluginId == 10) {
                //                        if (gameData.game_link.indexOf('://') > 0) {
                //                            gameLink = gameData.game_link;
                //                        } else {
                //                            gameLink = enginesisHost + "/games/" + gameData.game_name + "/" + gameData.game_link;
                //                        }
                //                    } else {
                //                        gameLink = enginesisHost + "/games/play.php?site_id=<?php //echo($siteId);?>//&game_id=<?php //echo($gameId);?>//";
                //                    }
                //                    gameContainerDiv.innerHTML = "<iframe id=\"gameContainer-iframe\" src=\"" + gameLink + "\" allowfullscreen scrolling=\"" + allowScroll + "\" width=\"" + width + "\" height=\"" + height + "\"/>";
            }
            if (isTouchDevice && enginesisSession.gamePluginId == 10) {
                showOnlyTheGame(true);
            } else if ( ! embedOnTouchDevice) {
                if (gameContainerDiv.clientWidth >= width) { // the game will fit the available space
                    gameContainerIframe = document.getElementById("gameContainer-iframe");
                    if (gameContainerIframe != null) {
                        gameContainerIframe.style.width = width + "px";
                        gameContainerIframe.style.maxWidth = width + "px";
                        gameContainerIframe.style.height = height + "px";
                        gameContainerIframe.style.maxHeight = height + "px";
                    }
                    gameContainerDiv.style.width = width + "px";
                    gameContainerDiv.style.maxWidth = width + "px";
                    gameContainerDiv.style.height = height + "px";
                    gameContainerDiv.style.maxHeight = height + "px";
                } else if ( ! (enginesisSession.gamePluginId == 9)) { // iframe game does not fit
                    gameContainerDiv.style.paddingTop = (requiredAspectRatio * 100) + "%";
                }
            }
        }
    }

    function showOnlyTheGame (showFlag) {
        // show/hide all divs except the gameContainer
        var hideTheseElements = ['varyn-navbar', 'playgame-InfoPanel', 'playgame-BottomPanel'],
            unwantedElement,
            elementDiv,
            showStyle;

        if (showFlag) {
            showStyle = 'none';
        } else {
            showStyle = 'block';
        }
        for (unwantedElement in hideTheseElements) {
            elementDiv = document.getElementById(hideTheseElements[unwantedElement]);
            if (elementDiv != null) {
                elementDiv.style.display = showStyle;
            }
        }
        elementDiv = document.getElementById('varyn-body');
        if (elementDiv != null) {
            elementDiv.style.padding = "0";
            elementDiv.style.margin = "0";
        }
        fitGameToFullScreen();
    }


    /**
     * Callback to handle responses from Enginesis.
     * @param enginesisResponse
     */
    function enginesisCallBack (enginesisResponse) {
        var succeeded,
            errorMessage,
            results;

        if (enginesisResponse != null && enginesisResponse.fn != null) {
            results = enginesisResponse.results;
            succeeded = results.status.success;
            errorMessage = results.status.message;
            switch (enginesisResponse.fn) {
                case "NewsletterAddressAssign":
                    handleNewsletterServerResponse(succeeded);
                    break;
                case "DeveloperGet":
                    setGameDeveloper(enginesisResponse.results.result[0]);
                    break;
                case "SiteListGamesRandom":
                    if (succeeded == 1) {
                        varynApp.gameListGamesResponse(enginesisResponse.results.result, "PlayPageGamesArea", 9, false);
                    }
                    break;
                default:
                    break;
            }
        }
    }

    return {
        pageLoaded: function (pageViewParameters) {
            var gameId = siteConfiguration.gameId,
                serverStage = siteConfiguration.serverStage,
                serverHostDomain = 'varyn' + serverStage + '.com';

            gtag('game', 'request', gameId);
            // document.domain = serverHostDomain;
            enginesisSession.siteListGamesRandom(24, enginesisCallBack);
            enginesisSession.developerGet(pageViewParameters.developerId, enginesisCallBack);
            setGameContainer(pageViewParameters);
            if (enginesisSession.isTouchDevice()) {
                window.addEventListener('orientationchange', resetGameFrameSize, false);
            }
            // window.addEventListener('resize', resetGameFrameSize, false); // we are hoping the responsive page will handle this for us
        }
    };
};
