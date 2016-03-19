/**
 * Common JavaScript and utility functions used across Varyn.com. Should be loaded on every page.
 *
 *
 */
var varyn = function (parameters) {
    "use strict";

    var siteConfiguration = {
            debug: true,
            originWhiteList: ["www.enginesis.com", "games.enginesis.com", "metrics.enginesis.com", "www.enginesis-l.com", "games.enginesis-l.com", "metrics.enginesis-l.com", "www.enginesis-q.com", "games.enginesis-q.com", "metrics.enginesis-q.com"],
            enginesisSessionCookieName: 'engsession',
            varynLoginCookieName: 'varynsession',
            varynUserInfoCookieName: 'varynuserinfo',
            varynFacebookAppId: '489296364486097',
            developerKey: 'deaddeaddeaddead',
            siteId: parameters.siteId,
            serverStage: parameters.serverStage,
            serverHostDomain: 'varyn' + parameters.serverStage + '.com',
            languageCode: parameters.languageCode,
            gameListIdTop: parameters.gameListIdTop || 4,
            gameListIdNew: parameters.gameListIdNew || 5,
            homePagePromoId: parameters.homePagePromoId || 3,
            gameListState: 1,

            minPasswordLength: 4,
            minUserNameLength: 3,
            minimumAge: 13
        },
        currentPage = '',
        waitingForUserNameReply = false,
        enginesisSession = null;

    return {

        /**
         * Call this to initialize the varyn app, get the Enginesis instance, and begin the page operations.
         */
        initApp: function(pageView) {

            var enginesisParameters = {
                siteId: siteConfiguration.siteId,
                gameId: siteConfiguration.gameId || 0,
                gameGroupId: siteConfiguration.gameGroupId || 0,
                serverStage: 'enginesis.' + siteConfiguration.serverHostDomain,
                authToken: siteConfiguration.authToken || '',
                developerKey: siteConfiguration.developerKey,
                languageCode: this.parseLanguageCode(siteConfiguration.languageCode),
                callBackFunction: this.enginesisCallBack
            };
            currentPage = this.getCurrentPage();
            document.domain = siteConfiguration.serverHostDomain;
            enginesisSession = enginesis(enginesisParameters);
            var showSubscribe = '<?php echo($showSubscribe);?>';
            if (showSubscribe == '1') {
                showSubscribePopup();
            }
            if (pageView !== undefined) {
                var pageViewTemplate = pageView(varynApp, siteConfiguration);
                pageViewTemplate.pageLoaded();
            }
        },

        getEnginesisSession: function () {
            return enginesisSession;
        },

        getSiteConfiguration: function () {
            return siteConfiguration;
        },

        parseLanguageCode: function (languageCode) {
            return languageCode.substr(0, 2);
        },

        /**
         * Determines if an email address appears to be a valid format.
         * @param {string} email address to check.
         * @returns {boolean}
         */
        isValidEmail: function (email) {
            return /\S+@\S+\.\S+/.test(email);
        },

        /**
         * Determines if a user name appears to be a valid format. A user name must be
         * 3 to 20 characters.
         * @param {string} user name to check.
         * @returns {boolean}
         */
        isValidUserName: function (userName) {
            return /^[a-zA-Z0-9_@!~\$\.\-\|\s?]{3,20}$/.test(userName);
        },

        /**
         * Determines if a password appears to be a valid format.
         * @param {string} password to check.
         * @returns {boolean}
         */
        isValidPassword: function (password) {
            var trimmed = password.trim().length;
            return trimmed >= siteConfiguration.minPasswordLength && trimmed == password.length;
        },

        /**
         * Compute Age given date of birth.
         * @param {string} Date of birth is a string date format from an input type=date control.
         * @returns {number}
         */
        ageInYearsFromNow: function (dob) {
            var today = new Date(),
                dateOfBirth = new Date(dob),
                millisecondsOneYear = 31536000000, // 1000 * 60 * 60 * 24 * 365,
                utc1,
                utc2;

            if (isNaN(dateOfBirth.getFullYear())) {
                dateOfBirth = today;
            }
            utc1 = Date.UTC(dateOfBirth.getFullYear(), dateOfBirth.getMonth(), dateOfBirth.getDate());
            utc2 = Date.UTC(today.getFullYear(), today.getMonth(), today.getDate());
            return Math.floor((utc2 - utc1) / millisecondsOneYear);
        },

        /**
         * Determines if a date of birth appears to be a valid. For this site users must be 13 years of age.
         * @param {string} Date of birth is a string date format from an input type=date control.
         * @returns {boolean}
         */
        isValidDateOfBirth: function (dob) {
            return this.ageInYearsFromNow(dob) >= siteConfiguration.minimumAge;
        },

        /**
         * Return the current web page file name without extension.
         * @returns {string}
         */
        getCurrentPage: function () {
            var pageName = location.href.split('/').slice(-1);
            if (pageName.indexOf('.') > 0) {
                pageName = pageName.split('.').slice(0);
            } else if (pageName == '') {
                pageName = 'index';
            }
            return pageName.toString();
        },

        /**
         * We expect a standard errorContent div to appear on any page that will display an error message
         * resulting from a user interaction.
         * @param errorMessage
         * @param fieldWithError
         */
        showErrorMessage: function (errorMessage, fieldWithError) {
            var errorContent = document.getElementById('errorContent'),
                errorFieldElement = document.getElementById(fieldWithError);

            if (errorMessage == "") {
                errorContent.innerHTML = '<p>&nbsp;</p>';
            } else if (errorContent != null) {
                errorContent.innerHTML = '<p class="error-text">' + errorMessage + '</p>';
            }
            if (errorFieldElement != null) {
                $(errorFieldElement).removeClass("popup-form-input").addClass("popup-form-input-error");
                errorFieldElement.focus();
            }
        },

        /**
         * setElementSizeAndColor of DOM element
         * @param DOM element
         * @param requiredWidth
         * @param requiredHeight
         * @param bgcolor
         */
        setElementSizeAndColor: function (elementDiv, requiredWidth, requiredHeight, bgcolor) {
            var style = "margin: 0; padding: 0; left: 0; top: 0; width: " + requiredWidth + "px; height: " + requiredHeight + "px; min-height: " + requiredHeight + "px !important; overflow: hidden;";
            if (bgcolor != null && bgcolor != "") {
                style += " background-color: " + bgcolor + ";";
            }
            elementDiv.setAttribute("style", style);
        },

        /**
         * This is a debug function to dump out the width&height of all children of the target div.
         * @param elementDiv
         */
        checkChildLayout: function (elementDiv) {
            var i,
                e,
                nodeName,
                childNodes = elementDiv.children;

            for (i = 0; i < childNodes.length; i ++) {
                e = childNodes[i];
                if (e.name != null) {
                    nodeName = e.name;
                } else if (e.id != null) {
                    nodeName = e.id;
                } else {
                    nodeName = e.localName;
                }
                console.log(elementDiv.localName + ": Child " + nodeName + " (" + e.style.width + "," + e.style.height + ")");
            }
        },

        /**
         * compareTitle is an array sort function to alphabetize an array by title
         * @param a
         * @param b
         * @returns {number} 0 if equal, 1 if  is greater, -1 if b is greater
         */
        compareTitle: function (a, b) {
            if (a.title < b.title) {
                return -1;
            } else if (a.title > b.title) {
                return 1;
            } else {
                return 0;
            }
        },

        /**
         * insertAndExecute inserts HTML text into the provided <div id="id"> and if that new HTML includes
         * any script tags they will get evaluated.
         * @param id: element to insert new HTML text into.
         * @param text: the new HTML text to insert into id.
         */
        insertAndExecute: function (id, text) {
            document.getElementById(id).innerHTML = text;
            var scripts = document.getElementById(id).getElementsByTagName("script");
            for (var i = 0; i < scripts.length; i ++) {
                if (scripts[i].src != '') {
                    var tag = document.createElement('script');
                    tag.src = scripts[i].src;
                    document.getElementsByTagName('head')[0].appendChild(tag);
                } else {
                    eval(scripts[i].innerHTML);
                }
            }
        },

        /**
         * Check if this browser supports CORS.
         * @returns {boolean}
         */
        checkBrowserCORSCompatibility: function () {
            var supported = (typeof window.postMessage !== "undefined");
            return supported;
        },

        /**
         * Verify the originating request is coming from a trusted source.
         * @param origin
         * @returns {boolean}
         */
        verifyCORSWhiteList: function (origin) {
            var ok = false;
            for (var i=0; i < SiteConfiguration.originWhiteList.length; i++) {
                if (origin === SiteConfiguration.originWhiteList[i]) {
                    ok = true;
                    break;
                }
            }
            return ok;
        },

        /**
         * showSubscribePopup show the popup form to capture an email address to subscribe to the newsletter.
         */
        showSubscribePopup: function (showFlag) {
            showCommonFormPopup(document.getElementById("popupCover"), document.getElementById("subscribePopup"), showFlag);
        },

        /**
         * showRegistrationPopup show the popup form to capture an new quick registration. This is a short form,
         * for the long form go to the profile.php page.
         */
        showRegistrationPopup: function (showFlag) {
            showCommonFormPopup(document.getElementById("popupCover"), document.getElementById("registrationPopup"), showFlag);
            onChangeRegisterUserName(document.getElementById('register-username'), 'popup_user_name_unique');
        },

        /**
         * showLoginPopup show the popup form to capture an new quick registration. This is a short form,
         * for the long form go to the profile.php page.
         */
        showLoginPopup: function (showFlag) {
            showCommonFormPopup(document.getElementById("popupCover"), document.getElementById("loginPopup"), showFlag);
        },

        /**
         * showForgotPasswordPopup show the popup form initiate forgot password flow.
         */
        showForgotPasswordPopup: function (showFlag) {
            showCommonFormPopup(document.getElementById("popupCover"), document.getElementById("forgotPasswordPopup"), showFlag);
        },

        /**
         * showCommonFormPopup shows the popup div given the correct DOM elements.
         */
        showCommonFormPopup: function (popupCover, popupFrame, showFlag) {
            if (showFlag === undefined) {
                showFlag = true;
            }
            if (popupCover != null && popupFrame != null) {
                if (showFlag) {
                    popupCover.style.display = 'block';
                    popupFrame.style.display = 'block';
                } else {
                    popupCover.style.display = 'none';
                    popupFrame.style.display = 'none';
                }
            }
        },

        setPopupMessage: function (popupId, message, className) {
            var messageElement = $('#' + popupId + ' .popupMessageArea'),
                messageArea = $('#' + popupId + ' .popupMessageResponseError');

            if (messageElement != null && messageArea != null) {
                messageElement.css('display', 'block');
                messageArea.css('display', 'block');
                messageArea.text(message);
                if (className != null) {
                    messageArea.attr("class", className);
                }
            }
        },

        /**
         * Close all popups. Being not so smart, we set all popups we know of to display:none.
         */
        popupCloseClicked: function () {
            showSubscribePopup(false);
            showLoginPopup(false);
            showRegistrationPopup(false);
        },

        /**
         * The submit button was clicked on the subscribe popup. Validate user inputs before we
         * attempt to submit the request with the server. Will set focus to a field in error.
         *
         * @return boolean true if ok to submit the form
         */
        popupSubscribeClicked: function () {
            var email = document.getElementById("emailInput").value,
                errorField = "";

            if (isValidEmail(email)) {
                setPopupMessage("subscribePopup", "Subscribing " + email + " with the service...", "popupMessageResponseOK");
                enginesisSession.newsletterAddressAssign(email, '', '', '2', null); // the newsletter category id for Varyn/General is 2
            } else {
                errorField = "emailInput";
                setPopupMessage("subscribePopup", "Your email " + email + " looks bad. Can you try again?", "popupMessageResponseError");
                document.getElementById(errorField).focus();
            }
            return errorField == "";
        },

        /**
         * The submit button was clicked on the registration popup. Validate user inputs on the quick registration form before we
         * attempt to submit the request with the server. Will set focus to a field in error.
         *
         * @returns {bool} true if ok to submit the form
         */
        popupRegistrationClicked: function () {
            var email = document.getElementById("register-email").value,
                password = document.getElementById("register-password").value,
                userName = document.getElementById("register-username").value,
                captcha = document.getElementById("register-captcha").value,
                agreement = document.getElementById("register-agreement").checked,
                errorField = "";

            if (errorField == "" && ! isValidEmail(email)) {
                setPopupMessage("registrationPopup", "Your email " + email + " looks bad. Can you try again?", "popupMessageResponseError");
                errorField = "register-email";
            }
            if (errorField == "" && ! isValidUserName(userName)) {
                setPopupMessage("registrationPopup", "Your user name " + userName + " looks bad. Can you try again?", "popupMessageResponseError");
                errorField = "register-username";
            }
            if (errorField == "" && ! isValidPassword(password)) {
                setPopupMessage("registrationPopup", "Your password looks bad. Can you try again?", "popupMessageResponseError");
                errorField = "register-password";
            }
            if (errorField == "" && ! agreement) {
                setPopupMessage("registrationPopup", "You must agree with the terms of use or you cannot register.", "popupMessageResponseError");
                errorField = "register-agreement";
            }
            if (errorField == "" && captcha.trim().length < 3) {
                setPopupMessage("registrationPopup", "Please answer the human test. Can you try again?", "popupMessageResponseError");
                errorField = "register-captcha";
            }
            if (errorField != "") {
                $(errorField).removeClass("popup-form-input").addClass("popup-form-input-error");
                document.getElementById(errorField).focus();
            }
            return errorField == ""; // return true to submit form
        },

        /**
         * The submit button on the login popup was clicked. Validate user inputs on the login form before we
         * attempt to submit the request with the server. Will set focus to a field in error.
         *
         * @returns {bool} true if ok to submit the form
         */
        popupLoginClicked: function () {
            var password = document.getElementById("login_password").value.toString(),
                userName = document.getElementById("login_username").value.toString(),
                errorField = "";

            if (errorField == "" && ! isValidUserName(userName)) {
                setPopupMessage("loginPopup", "Your user name " + userName + " looks bad. Can you try again?", "popupMessageResponseError");
                errorField = "login_username";
            }
            if (errorField == "" && ! isValidPassword(password)) {
                setPopupMessage("loginPopup", "Your password looks bad. Can you try again?", "popupMessageResponseError");
                errorField = "login_password";
            }
            if (errorField != "") {
                $(errorField).removeClass("popup-form-input").addClass("popup-form-input-error");
                document.getElementById(errorField).focus();
            }
            return errorField == ""; // return true to submit form
        },

        /**
         * The submit button on the forgot password popup was clicked. Validate user inputs on the
         * forgot password form before we attempt to submit the request with the server. Will
         * set focus to a field in error.
         *
         * @returns {bool} true if ok to submit the form
         */
        popupForgotPasswordClicked: function () {
            var email = document.getElementById("forgotpassword_email").value.toString(),
                userName = document.getElementById("forgotpassword_username").value.toString(),
                errorField = "";

            if (errorField == "" && ! isValidUserName(userName)) {
                setPopupMessage("forgotPasswordPopup", "Your user name '" + userName + "' looks bad. Can you try again?", "popupMessageResponseError");
                errorField = "forgotpassword_username";
            }
            if (errorField == "" && ! isValidEmail(email)) {
                setPopupMessage("forgotPasswordPopup", "Your email " + email + " looks bad. Can you try again?", "popupMessageResponseError");
                errorField = "forgotpassword_email";
            }
            if (errorField != "") {
                $(errorField).removeClass("popup-form-input").addClass("popup-form-input-error");
                document.getElementById(errorField).focus();
            }
            return errorField == ""; // return true to submit form
        },

        setupRegisterUserNameOnChangeHandler: function () {
            $('#register-username').on('change', onChangeRegisterUserName);
            $('#register-username').on('input', onChangeRegisterUserName);
            $('#register-username').on('propertychange', onChangeRegisterUserName);
        },

        /**
         * On change handler for the user name field on a registration form.
         * Try to make sure the user name is unique.
         * @param {object} DOM element that is changing.
         * @param {string} DOM id that will receive update of name status either acceptable or unacceptable.
         */
        onChangeRegisterUserName: function (element, domIdImage) {
            if ( ! waitingForUserNameReply && element != null) {
                if (element.target != null) {
                    element = element.target;
                }
                if (domIdImage == null) {
                    domIdImage = $(this).data("target");
                }
                var userName = element.value.toString();
                if (userName && isValidUserName(userName)) {
                    waitingForUserNameReply = true;
                    enginesisSession.userGetByName(userName, function (enginesisResponse) {
                        var userNameAlreadyExists = false;
                        waitingForUserNameReply = false;
                        if (enginesisResponse != null && enginesisResponse.fn != null) {
                            userNameAlreadyExists = enginesisResponse.results.status.success == "1";
                        }
                        setUserNameIsUnique(domIdImage, ! userNameAlreadyExists);
                    });
                } else {
                    setUserNameIsUnique(domIdImage, false);
                }
            }
        },

        /**
         * When we dynamically query the server to determine if the user name is a unique selection
         * use this function to indicate uniqueness result on the form.
         * @param (id} which DOM id we wish to manipulate.
         * @param {bool} true if the name is unique, false if it is taken.
         */
        setUserNameIsUnique: function (id, isUnique) {
            if (id) {
                if (isUnique) {
                    $('#' + id).removeClass('username-is-not-unique').addClass('username-is-unique').css('display', 'inline-block');
                } else {
                    $('#' + id).removeClass('username-is-unique').addClass('username-is-not-unique').css('display', 'inline-block');
                }
            }
        },

        /**
         * When a response to one of our form submissions returns from the server we handle it here.
         * If the result is a success we close the popup after a delay to confirm with the user the
         * successful status. If the result is an error we display the error message.
         */
        handleNewsletterServerResponse: function (succeeded) {
            if (succeeded == 1) {
                setPopupMessage("subscribePopup", "You are subscribed - Thank you!", "popupMessageResponseOK");
                window.setTimeout(hideSubscribePopup, 2000);
            } else {
                setPopupMessage("subscribePopup", "Service reports an error: " + errorMessage, "popupMessageResponseError");
            }
        },

        /**
         * makeGameModule will generate the HTML for a standard game promo module.
         * @param gameId
         * @param gameName
         * @param gameDescription
         * @param gameImg
         * @param gameLink
         * @returns {string} the HTML
         */
        makeGameModule: function (gameId, gameName, gameDescription, gameImg, gameLink) {
            var innerHtml,
                title;

            title = "Play " + gameName + " Now!";
            innerHtml = "<div class=\"gameModule thumbnail\">";
            innerHtml += "<a href=\"" + gameLink + "\" title=\"" + title + "\"><img class=\"thumbnail-img\" src=\"" + gameImg + "\" alt=\"" + gameName + "\"/></a>";
            innerHtml += "<div class=\"gameModuleInfo\"><a href=\"" + gameLink + "\" class=\"btn btn-md btn-success\" role=\"button\" title=\"" + title + "\" alt=\"" + title + "\">Play Now!</a></div>";
            innerHtml += "<div class=\"caption\"><a class=\"gameTitle\" href=\"" + gameLink + "\" title=\"" + title + "\"><h3>" + gameName + "</h3></a><p class=\"gamedescription\">" + gameDescription + "</p>";
            innerHtml += "</div></div>";
            return innerHtml;
        },

        /**
         * makeAdModule will generate the HTML for a standard ad module.
         * @returns {string} the HTML
         */
        makeAdModule: function () {
            var innerHtml;

            innerHtml = '<div class="gameModule thumbnail"><div class="row"><div class="col-sm-4 col-md-2 adContainer412"><div id="boxAd300" class="ad300x412">';
            innerHtml += '<iframe src="/common/adModule.html" frameborder="0" scrolling="no" style="width: 300px; height: 412px; overflow: hidden; z-index: 9999; left: 0px; bottom: 0px; display: inline-block;"></iframe></div></div></div></div>';
            return innerHtml;
        },

        /**
         * makeCouponModule will generate the HTML for a standard Coupons.com module.
         * @returns {string} the HTML
         */
        makeCouponModule: function () {
            var innerHtml;

            innerHtml = '<div class="gameModule thumbnail"><div class="row"><div class="col-sm-4 col-md-2 adContainer412"><div id="boxAd300" class="ad300x412">';
            innerHtml += '<iframe src="/common/couponModule.html" frameborder="0" scrolling="no" style="width: 300px; height: 412px; overflow: hidden; z-index: 9999; left: 0px; bottom: 0px; display: inline-block;"></iframe></div></div></div></div>';
            return innerHtml;
        },

        /**
         * makePromoModule will generate the HTML for a single standard promo module for the carousel.
         * @param isActive
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
            innerHtml = "<div class=\"carousel-inner\" role=\"listbox\"><div class=\"item" + isActiveItem + "\">";
            innerHtml += "<img src=\"" + backgroundImg + +"\" alt=\"" + altText + "\">";
            innerHtml += "<div class=\"container\"><div class=\"carousel-caption\"><h1>" + titleText + "</h1>";
            innerHtml += "<p>" + promoText + "</p>";
            innerHtml += "<p><a class=\"btn btn-lg btn-primary\" href=\"" + link + "\" role=\"button\">" + callToActionText + "</a></p>";
            innerHtml += "</div></div></div>"
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
         * gameListGamesResponse handles the server reply from GameListListGames and generates the game modules.
         * @param results {object}: the sever response object
         * @param elementId {string}: element to insert game modules HTML
         * @param maxItems {int}: no more than this number of games
         * @param sortList {bool}: true to sort the list of games alphabetically by title
         */
        gameListGamesResponse: function (results, elementId, maxItems, sortList) {
            // results is an array of games
            var i,
                adsShownCounter,
                gameItem,
                gamesContainer = document.getElementById(elementId),
                newDiv,
                itemHtml,
                countOfGamesShown,
                baseURL = document.location.protocol + "//" + enginesisSession.serverBaseUrlGet() + "/games/",
                isTouchDevice = enginesisSession.isTouchDevice(),
                adsDisplayPositions = new Array(3, 21, 41, 60, 80, 100),
                numberOfAdSpots;

            if (results != null && results.length > 0 && gamesContainer != null) {
                if (sortList == null) {
                    sortList = false;
                }
                if (sortList) {
                    results.sort(compareTitle);
                }
                if (maxItems == null || maxItems < 1) {
                    maxItems = results.length;
                }
                countOfGamesShown = 0;
                adsShownCounter = 0;
                numberOfAdSpots = adsDisplayPositions.length;
                for (i = 0; i < results.length && countOfGamesShown < maxItems; i ++) {
                    gameItem = results[i];
                    if (isTouchDevice && ! (gameItem.game_plugin_id == "10" || gameItem.game_plugin_id == "9")) {
                        continue; // only show HTML5 or embed games on touch devices
                    }
                    countOfGamesShown ++;
                    itemHtml = this.makeGameModule(gameItem.game_id, gameItem.title, gameItem.short_desc, baseURL + gameItem.game_name + "/images/300x225.png", "/play.php?gameid=" + gameItem.game_id);
                    newDiv = document.createElement('div');
                    newDiv.className = "col-sm-6 col-md-4";
                    newDiv.innerHTML = itemHtml;
                    gamesContainer.appendChild(newDiv);
                    if (adsShownCounter < numberOfAdSpots && i + 1 == adsDisplayPositions[adsShownCounter]) {
                        // Time to show an ad module
                        adsShownCounter ++;
                        newDiv = document.createElement('div');
                        newDiv.className = "col-sm-6 col-md-4";
                        if (adsShownCounter == 1) {
                            newDiv.innerHTML = this.makeCouponModule();
                        } else {
                            newDiv.innerHTML = this.makeAdModule();
                        }
                        newDiv.id = 'AdSpot' + adsShownCounter;
                        gamesContainer.appendChild(newDiv);
                    }
                }
            } else {
                // no games!
            }
        },

        /**
         *
         * @param results
         */
        promotionItemListResponse: function (results) {
            // results is an array of promoted items
            var i;
            if (results != null && results.length > 0) {
                for (i = 0; i < results.length; i ++) {

                }
            } else {
                // no promotions!
            }
        },

        /**
         * Callback to handle responses from Enginesis.
         * @param enginesisResponse
         */
        enginesisCallBack: function (enginesisResponse) {
            var succeeded,
                errorMessage;

            if (enginesisResponse != null && enginesisResponse.fn != null) {
                succeeded = enginesisResponse.results.status.success;
                errorMessage = enginesisResponse.results.status.message;
                switch (enginesisResponse.fn) {
                    case "NewsletterAddressAssign":
                        handleNewsletterServerResponse(succeeded);
                        break;
                    case "PromotionItemList":
                        if (succeeded == 1) {
                            promotionItemListResponse(enginesisResponse.results.result);
                        }
                        break;
                    case "GameListListGames":
                        if (succeeded == 1) {
                            if (gameListState == 1) {
                                gameListGamesResponse(enginesisResponse.results.result, "HomePageTopGames", null, false);
                                this.gameListState = 2;
                                enginesisSession.gameListListGames(enginesisGameListIdNew, null);
                            } else if (gameListState == 2) {
                                gameListGamesResponse(enginesisResponse.results.result, "HomePageHotGames", null, false);
                                this.gameListState = 0;
                            }
                        }
                        break;
                    default:
                        break;
                }
            }
        }
    }
};

/**
 * Determine full extent of the window available to the application
 * Extra Warning: this function must be global (on window object) because we will refer to it globally later.
 * @param container {object} DOM element that extends the full width and height of the page (use body unless you have a
 * full size div container.)
 * @returns {object} {fullWidth, fullHeight}
 */
// container = "gameContainer";
function getDocumentSize (container) {
    var gameContainerDiv = document.getElementById(container),
        result = {fullWidth: document.documentElement.clientWidth, fullHeight: document.documentElement.clientHeight},
        enginesisSession = varyn.getEnginesisSession();

    if (gameContainerDiv != null) {
        result.containerWidth = gameContainerDiv.clientWidth;
        result.containerHeight = gameContainerDiv.clientHeight;
    }
    if (enginesisSession != null) {
        result.gameWidth = enginesisSession.gameWidth;
        result.gameHeight = enginesisSession.gameHeight;
        result.gameAspectRatio = enginesisSession.gameAspectRatio;
    }
    return result;
}
