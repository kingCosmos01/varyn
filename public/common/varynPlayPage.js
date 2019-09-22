/**
 * Functionality supporting the /play/index.php page. This script is loaded with the page load then pageLoaded is called
 * from varyn.initApp().
 *
 */
var varynPlayPage = function (varynApp, siteConfiguration) {
    "use strict";

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

    function setGameContainer (pageViewParameters, enginesisSession) {
        var gameContainerDiv = document.getElementById("gameContainer"),
            gameContainerIframe = document.getElementById("gameContainer-iframe"),
            requiredAspectRatio,
            requestedWidth = pageViewParameters.width,
            requestedHeight = pageViewParameters.height,
            pluginId = pageViewParameters.pluginId,
            isTouchDevice = enginesisSession.isTouchDevice(),
            boundingRect = gameContainerDiv.getBoundingClientRect();

        // we want to size the container to the size of the game and center it in the panel div.

        if (requestedHeight < requestedWidth) {
            requiredAspectRatio = requestedHeight / requestedWidth;
        } else {
            requiredAspectRatio = requestedWidth / requestedHeight;
        }
        enginesisSession.gameWidth = requestedWidth;
        enginesisSession.gameHeight = requestedHeight;
        enginesisSession.gameAspectRatio = requiredAspectRatio; // cache the ideal aspect ratio to use when the frame is resized
        enginesisSession.gamePluginId = pluginId;
        if (gameContainerDiv != null) {
            if (isTouchDevice) {
                // if we are on mobile and this is an Embed or Canvas game, force the container 
                // to full width/height and let the game size itself inside the container.
                showOnlyTheGame(true);
            } else {
                if (enginesisSession.gamePluginId == 9) {
                    // For Embed games, setup the requested width and height.
                    // if no requested w/h, then set the height to the remaining client
                    // height. clientHeight - boundingRect
                    gameContainerDiv.style['overflow-y'] = "scroll";
                    gameContainerDiv.style['overflow-x'] = "hidden";
                    gameContainerDiv.style['-webkit-overflow-scrolling'] = "touch";
                    // gameContainerDiv.style.width = width + "px";
                    // gameContainerDiv.style.maxWidth = width + "px";
                    // gameContainerDiv.style.height = height + "px";
                    // gameContainerDiv.style.maxHeight = height + "px";
                    // insertAndExecute("gameContainer", gameData.game_link);
                } else if (enginesisSession.gamePluginId == 10) {
                    // for Canvas games, fit the game to the container or shrink the
                    // container to the game size. Best case is using all available width/height.
                    var width = gameContainerDiv.width;
                    var height = window.innerHeight - boundingRect.top;

                    // the game will fit the available space
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
                } else {
                    gameContainerDiv.style.height = requestedHeight + "px";
                    gameContainerDiv.style.maxHeight = requestedHeight + "px";
                }
            }
        }
        // Set focus on the iframe so that the keyboard works for the game, not the parent website.
        if (gameContainerIframe != null) {
            window.setTimeout(function() {
                gameContainerIframe.contentWindow.focus();
            }, 150);
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
            succeeded = results.status.success == "1";
            if (succeeded) {
                switch (enginesisResponse.fn) {
                    case "NewsletterAddressAssign":
                        handleNewsletterServerResponse(succeeded);
                        break;
                    case "DeveloperGet":
                        setGameDeveloper(enginesisResponse.results.result[0]);
                        break;
                    case "SiteListGamesRandom":
                        varynApp.gameListGamesResponse(enginesisResponse.results.result, "PlayPageGamesArea", 9, false);
                        break;
                    default:
                        break;
                }
            } else {
                errorMessage = results.status.message + ": " + results.status.extended_info;
                // display the errors
                console.log("Error " + errorMessage + " from Enginesis service call " + enginesisResponse.fn);
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
            var enginesisSession = varynApp.getEnginesisSession();
            enginesisSession.siteListGamesRandom(24, enginesisCallBack);
            enginesisSession.developerGet(pageViewParameters.developerId, enginesisCallBack);
            setGameContainer(pageViewParameters, enginesisSession);
            if (enginesisSession.isTouchDevice()) {
                window.addEventListener('orientationchange', resetGameFrameSize, false);
            }
            // window.addEventListener('resize', resetGameFrameSize, false); // we are hoping the responsive page will handle this for us
        }
    };
};
