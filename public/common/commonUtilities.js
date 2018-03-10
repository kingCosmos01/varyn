/**  commonUtilities.js
 *
 * @module commonUtilities
 * @classdesc
 *   A static object of utility functions for handling common problems
 *   found in JavaScript and web development. I find on every JS project I work
 *   on I need most of these functions, so I pulled them all together in one place.
 *
 *   This module includes many function utilities for data transformations such as
 *   base64, url and query string processing, data validation, and storage handling.
 *
 * @since 1.0
 */

(function commonUtilities (global) {
    'use strict';

    var commonUtilities = {
        version: "1.2.9"
    },
    _base64KeyStr = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",
    _testNumber = 0;

    /**
     * Private function to validate HTML5 local or session storage.
     * @param storageType - either localStorage or sessionStorage, default is localStorage
     * @param robustCheck - true for the more robust but un-performant test
     * @returns {boolean} - true if supported.
     */
    function hasLocalStorage (storageType, robustCheck) {
        var hasSupport = false,
            storage,
            testKey;

        if (typeof storageType === "undefined" || storageType == null || storageType == '') {
            storageType = 'localStorage';
        }
        try {
            hasSupport = storageType in global && global[storageType] !== null;
            if (hasSupport && robustCheck) { // even if "supported" make sure we can write and read from it
                storage = global[storageType];
                testKey = 'commonUtilities';
                storage.setItem(testKey, '1');
                storage.removeItem(testKey);
            }
        } catch (exception) {
            hasSupport = false;
        }
        return hasSupport;
    }

    /**
     * Return the provided object as a string in key: value; format.
     *
     * @method objectToString
     * @param {object} obj The object to convert to a string representation.
     * @return {string} string The object converted to a string representation.
     */
    commonUtilities.objectToString = function (obj) {
        var result,
            prop;
        if (obj) {
            result = '';
            for (prop in obj) {
                if (obj.hasOwnProperty(prop)) {
                    result += (result.length > 0 ? ' ' : '') + prop + ': ' + obj[prop] + ';';
                }
            }
        } else {
            result = 'null;';
        }
        return result;
    };

    /**
     * Return the provided array as a string in key: value; format.
     *
     * @method arrayToString
     * @param {array} array The array to convert to a string representation.
     * @return {string} string The array converted to a string representation.
     */
    commonUtilities.arrayToString = function (array) {
        var result,
            key,
            value;
        if (array && array instanceof Array) {
            result = '[';
            for (key in array) {
                value = array[key];
                if (typeof(value) == "undefined") {
                    value = "undefined";
                } else if (typeof(value) == "object") {
                    value = this.objectStringify(value);
                } else if (typeof(value) == "array") {
                    value = this.arrayToString(value);
                }
                result += (result.length > 1 ? ', ' : '') + key + ': ' + value;
            }
            result += ']';
        } else {
            result = 'null';
        }
        return result;
    };

    /**
     * Return the provided object as a string in key: value; format. This version handles
     * functions but is slower than objectToString.
     *
     * @method objectStringify
     * @param {object} object The object to convert to a string representation.
     * @return {string} string The object converted to a string representation.
     */
    commonUtilities.objectStringify = function (object) {
        var subObjects = [], // An array of sub-objects that will later be joined into a string.
            property;

        if (object === undefined || object === null) {
            return String(object);
        } else if (typeof(object) == "function") {
            subObjects.push(object.toString());
        } else if (typeof(object) == "object") {
            // is object (or array):
            //    Both arrays and objects seem to return "object" when typeof(obj)
            //    is applied to them. So instead we check if they have the property
            //    join, a function of the array prototype. Unless the object actually
            //    defines its own join property!
            if (object.join === undefined) {
                for (property in object) {
                    if (object.hasOwnProperty(property)) {
                        subObjects.push(property + ": " + this.objectStringify(object[property]));
                    }
                }
                return "{" + subObjects.join(", ") + "}";
            } else {
                for (property in object) {
                    subObjects.push(this.objectStringify(object[property]));
                }
                return "[" + subObjects.join(", ") + "]";
            }
        } else {
            // all other value types can be represented with JSON.stringify
            subObjects.push(JSON.stringify(object))
        }
        return subObjects.join(", ");
    };

    /**
     * Return the current document query string as an object with
     * key/value pairs converted to properties.
     *
     * @method queryStringToObject
     * @param {string} urlParamterString An optional query string to parse as the query string. If not
     *   provided then use window.location.search.
     * @return {object} result The query string converted to an object of key/value pairs.
     */
    commonUtilities.queryStringToObject = function (urlParameterString) {
        var match,
            search = /([^&=]+)=?([^&]*)/g,
            decode = function (s) {
                return decodeURIComponent(s.replace(/\+/g, " "));
                },
            result = {};
        if ( ! urlParameterString) {
            urlParameterString = window.location.search.substring(1);
        }
        while (match = search.exec(urlParameterString)) {
            result[decode(match[1])] = decode(match[2]);
        }
        return result;
    };

    /**
     * Extend an object with properties copied from other objects. Takes a variable number of arguments:
     *  If no arguments, and empty object is returned.
     *  If one argument, that object is returned unchanged.
     *  If more than one argument, each object is copied to the first object one property at a time. When
     *    properties conflict the last property is the one retained.
     * @returns {object}
     */
    commonUtilities.extendObject = function() {
        var key,
            value,
            extendedObject,
            object,
            objects,
            index,
            objectCount;

        if (arguments.length > 0) {
            extendedObject = arguments[0];
            if (arguments.length > 1) {
                objects = arguments.slice(1);
                for (index = 0, objectCount = objects.length; index < objectCount; index ++) {
                    object = objects[index];
                    for (key in object) {
                        value = object[key];
                        extendedObject[key] = value;
                    }
                }
            }
        } else {
            extendedObject = {};
        }
        return extendedObject;
    };

    /**
     * Determine if at least one string in the array matches the pattern. Since we are using regex pattern
     * to match we cannot use Array.indexOf(). If the pattern were a simple string, use Array.indexOf().
     * @param pattern a regex pattern to match.
     * @param arrayOfStrings strings to test each against the pattern.
     * @returns {number} index of first string in the array that matches the pattern, -1 when no match.
     */
    commonUtilities.matchInArray = function (pattern, arrayOfStrings) {
        var i = 0,
            numberOfTokens;

        if (pattern && arrayOfStrings && arrayOfStrings.constructor === Array) {
            numberOfTokens = arrayOfStrings.length;
            for (i; i < numberOfTokens; i ++) {
                if (pattern.match(arrayOfStrings[i])) {
                    return i;
                }
            }
        }
        return -1;
    };

    /**
     * Given a path make sure it represents a full path with a leading and trailing /.
     *
     * @method makeFullPath
     * @param {string} path URI path to check.
     * @return {string} path Full URI path.
     */
    commonUtilities.makeFullPath = function (path) {
        if (path) {
            if (path[path.length - 1] !== '/') {
                path += '/';
            }
            if (path[0] !== '/') {
                path = '/' + path;
            }
        } else {
            path = '/';
        }
        return path;
    };

    /**
     * Append a folder or file name to the end of an existing path string.
     *
     * @method
     * @param {string} path URI path to append to.
     * @param {string} file folder or file to append.
     * @return {string} path Full URI path.
     */
    commonUtilities.appendFileToPath = function (path, file) {
        if (path && file) {
            if (path[path.length - 1] !== '/' && file[0] !== '/') {
                path += '/' + file;
            } else if (path[path.length - 1] == '/' && file[0] == '/') {
                path += file.substr(1);
            } else {
                path += file;
            }
        } else if (file) {
            path = file;
        }
        return path;
    };

    /**
     * Replace occurrences of {token} with matching keyed values from parameters array.
     *
     * @method tokenReplace
     * @param {string} text text containing tokens to be replaced.
     * @param {Array} parameters array/object of key/value pairs to match keys as tokens in text and replace with value.
     * @return {string} text replaced string.
     */
    commonUtilities.tokenReplace = function (text, parameters) {
        var token,
            regexMatch;

        for (token in parameters) {
            if (parameters.hasOwnProperty(token)) {
                regexMatch = new RegExp("\{" + token + "\}", 'g');
                text = text.replace(regexMatch, parameters[token]);
            }
        }
        return text;
    };

    /**
     * Determine if a variable is "empty", which could depend on what type it is:
     *   any variant when null or undefined
     *   if a boolean, then when false
     *   if a number, then when 0
     *   if a string, then 0 length
     *   if an array, then 0 length
     * Given boolean logic expression order of precedence, we should arrange the return
     *   statement with the most likely case first, the least likely case last.
     * @param field var any variable
     * @returns {boolean} returns true if empty, false if not empty.
     */
    commonUtilities.isEmpty = function (field) {
        return (typeof field === 'undefined') || field === null || field === "" || (field instanceof Array && field.length == 0) || field === false || field === 0;
    };

    /**
     * Convert a string into one that has no HTML vunerabilities such that it can be rendered inside an HTML tag.
     * @param string
     * @returns {string}
     */
    commonUtilities.safeForHTML = function (string) {
        var htmlEscapeMap = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#x27;',
                '/': '&#x2F;'
            },
            htmlEscaper = /[&<>"'\/]/g;
        return ('' + string).replace(htmlEscaper, function (match) {
            return htmlEscapeMap[match]
        });
    };

    /* ----------------------------------------------------------------------------------
     * Platform and feature detection
     * ----------------------------------------------------------------------------------*/
    /**
     * Determine if the current invokation environment is a mobile device.
     * TODO: Really would rather use modernizr.js as you really do not want isMobile(), you want isTouchDevice()
     *
     * @method isMobile
     * @return {bool} true if we think this is a mobile device, false if we think otherwise.
     *
     */
    commonUtilities.isMobile = function () {
        return (this.isMobileAndroid() || this.isMobileBlackberry() || this.isMobileIos() || this.isMobileWindows());
    };

    commonUtilities.isMobileAndroid = function () {
        return navigator.userAgent.match(/Android/i);
        // NOTE: tolower+indexof is about 10% slower than regex
        // return navigator.userAgent.toLowerCase().indexOf("android") != -1;
    };

    commonUtilities.isMobileBlackberry = function () {
        return navigator.userAgent.match(/BlackBerry/i) ? true : false;
    };

    commonUtilities.isMobileIos = function () {
        return navigator.userAgent.match(/iPhone|iPad|iPod/i) ? true : false;
    };

    commonUtilities.isMobileWindows = function () {
        return navigator.userAgent.match(/IEMobile/i) ? true : false;
    };

    /* ----------------------------------------------------------------------------------
     * Various conversion utilities - UTF-8, Base 64
     * ----------------------------------------------------------------------------------*/

    /**
     * Encode a Unicode string in UTF-8 character encoding.
     *
     * @method utf8Encode
     * @param {string} input string in Unicode to convert to UTF-8.
     * @return {string} result UTF-8 encoded input string.
     */
    commonUtilities.utf8Encode = function (input) {
        var result = "",
            inputLength = input.length,
            index,
            charCode;
        input = input.replace(/\r\n/g,"\n");

        for (index = 0; index < inputLength; index ++) {
            charCode = input.charCodeAt(index);
            if (charCode < 128) {
                result += String.fromCharCode(charCode);
            } else if((charCode > 127) && (charCode < 2048)) {
                result += String.fromCharCode((charCode >> 6) | 192);
                result += String.fromCharCode((charCode & 63) | 128);
            } else {
                result += String.fromCharCode((charCode >> 12) | 224);
                result += String.fromCharCode(((charCode >> 6) & 63) | 128);
                result += String.fromCharCode((charCode & 63) | 128);
            }
        }
        return result;
    };

    /**
     * Decode a UTF-8 encoded string into a Unicode character coding format.
     *
     * @method utf8Decode
     * @param {string} utfText string in UTF-8 to convert to Unicode.
     * @return {string} result Unicode representation of input string.
     */
    commonUtilities.utf8Decode = function (utfText) {
        var result = "",
            utfTextLength = utfText.length,
            index = 0,
            charCode1,
            charCode2,
            charCode3;

        while (index < utfTextLength) {
            charCode1 = utfText.charCodeAt(index);
            if (charCode1 < 128) {
                result += String.fromCharCode(charCode1);
                index ++;
            } else if((charCode1 > 191) && (charCode1 < 224)) {
                charCode2 = utfText.charCodeAt(index + 1);
                result += String.fromCharCode(((charCode1 & 31) << 6) | (charCode2 & 63));
                index += 2;
            } else {
                charCode2 = utfText.charCodeAt(index + 1);
                charCode3 = utfText.charCodeAt(index + 2);
                result += String.fromCharCode(((charCode1 & 15) << 12) | ((charCode2 & 63) << 6) | (charCode3 & 63));
                index += 3;
            }
        }
        return result;
    };

    /**
     * Convert an image located at the URL specified into its Base 64 representation.
     * Because the image is loaded asynchronously over the network a callback function
     * will be called once the image is loaded and encoded.
     *
     * @method base64FromImageUrl
     * @param {string} url URL to an image.
     * @param {function} callback Called when image is loaded. This function takes one parameter,
     *         a string that represents the Base 64 encoded image.
     * @return void
     */
    commonUtilities.base64FromImageUrl = function(url, callback) {
        var img = new Image();
        img.src = url;
        img.onload = function() {
            var canvas = document.createElement("canvas"),
                ctx = canvas.getContext("2d"),
                dataURL;

            canvas.width = this.width;
            canvas.height = this.height;
            ctx.drawImage(this, 0, 0);
            dataURL = canvas.toDataURL("image/png");
            callback(dataURL);
        }
    };

    /**
     * Encode a string into its base 64 representation.
     *
     * @method base64Encode
     * @param {string} input string to encode in base 64.
     * @return {string} output encoded string.
     */
    commonUtilities.base64Encode = function (input) {
        var output = "",
            inputLength = input.length,
            chr1, chr2, chr3, enc1, enc2, enc3, enc4,
            i = 0;

        input = this.utf8Encode(input);
        while (i < inputLength) {
            chr1 = input.charCodeAt(i ++);
            chr2 = input.charCodeAt(i ++);
            chr3 = input.charCodeAt(i ++);
            enc1 = chr1 >> 2;
            enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
            enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
            enc4 = chr3 & 63;
            if (isNaN(chr2)) {
                enc3 = enc4 = 64;
            } else if (isNaN(chr3)) {
                enc4 = 64;
            }
            output = output +
                _base64KeyStr.charAt(enc1) + _base64KeyStr.charAt(enc2) +
                _base64KeyStr.charAt(enc3) + _base64KeyStr.charAt(enc4);
        }
        return output;
    };

    /**
     * Convert a base 64 encoded string to its UTF-8 character coding.
     *
     * @method base64Decode
     * @param {string} input string in base 64 to convert to UTF-8.
     * @return {string} result UTF-8 string.
     */
    commonUtilities.base64Decode = function (input) {
        var output = "",
            inputLength = input.length,
            chr1, chr2, chr3, enc1, enc2, enc3, enc4,
            i = 0;

        input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");
        while (i < inputLength) {
            enc1 = _base64KeyStr.indexOf(input.charAt(i ++));
            enc2 = _base64KeyStr.indexOf(input.charAt(i ++));
            enc3 = _base64KeyStr.indexOf(input.charAt(i ++));
            enc4 = _base64KeyStr.indexOf(input.charAt(i ++));
            chr1 = (enc1 << 2) | (enc2 >> 4);
            chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
            chr3 = ((enc3 & 3) << 6) | enc4;
            output = output + String.fromCharCode(chr1);
            if (enc3 != 64) {
                output = output + String.fromCharCode(chr2);
            }
            if (enc4 != 64) {
                output = output + String.fromCharCode(chr3);
            }
        }
        return this.utf8Decode(output);
    };

    /**
     * Round a number to the requested number of decimal places.
     * @param value {number} the number to round.
     * @param decimalPlaces {number} the number of decimal places.
     * @returns {number} Rounded value.
     */
    commonUtilities.roundTo = function (value, decimalPlaces) {
        var orderOfMagnitude = Math.pow(10, decimalPlaces);
        return Math.round(value * orderOfMagnitude) / orderOfMagnitude;
    };

    /* ----------------------------------------------------------------------------------
     * Cookie handling functions
     * ----------------------------------------------------------------------------------*/

    /**
     * Return the contents fo the cookie indexed by the specified key.
     *
     * @method cookieGet
     * @param {string} key Indicate which cookie to get.
     * @return {string} value Contents of cookie stored with key.
     */
    commonUtilities.cookieGet = function (key) {
        if (key) {
            return decodeURIComponent(document.cookie.replace(new RegExp("(?:(?:^|.*;)\\s*" + encodeURIComponent(key).replace(/[\-\.\+\*]/g, "\\$&") + "\\s*\\=\\s*([^;]*).*$)|^.*$"), "$1")) || null;
        } else {
            return null;
        }
    };

    /**
     * Set a cookie indexed by the specified key.
     *
     * @method cookieSet
     * @param key {string} Indicate which cookie to set.
     * @param value {string} Value to store under key.
     * @param expiration {object} When the cookie should expire.
     * @param path {string} Cookie URL path.
     * @param domain {string} Cookie domain.
     * @param isSecure {boolean} Set cookie secure flag.
     * @return {boolean} true if set, false if error.
     */
    commonUtilities.cookieSet = function (key, value, expiration, path, domain, isSecure) {
        var expires = "";

        if ( ! key || /^(?:expires|max\-age|path|domain|secure)$/i.test(key)) {
            return false;
        } else {
            if (expiration) {
              switch (expiration.constructor) {
                case Number:
                  expires = expiration === Infinity ? "; expires=Fri, 31 Dec 9999 23:59:59 GMT" : "; max-age=" + expiration;
                  break;
                case String:
                  expires = "; expires=" + expiration;
                  break;
                case Date:
                  expires = "; expires=" + expiration.toUTCString();
                  break;
              }
            }
            document.cookie = encodeURIComponent(key) + "=" + encodeURIComponent(value) + expires + (domain ? "; domain=" + domain : "") + (path ? "; path=" + path : "") + (isSecure ? "; secure" : "");
            return true;
        }
    };

    /**
     * Remove a cookie indexed by the specified key.
     *
     * @method cookieRemove
     * @param key {string} Indicate which cookie to remove.
     * @param path {string} Cookie URL path.
     * @param domain {string} Cookie domain.
     * @return {boolean} true if removed, false if doesn't exist.
     */
    commonUtilities.cookieRemove = function (key, path, domain) {
        if (this.cookieExists(key)) {
            document.cookie = encodeURIComponent(key) + "=; expires=Thu, 01 Jan 1970 00:00:00 GMT" + (domain ? "; domain=" + domain : "") + (path ? "; path=" + path : "");
            return true;
        } else {
            return false;
        }
    };

    /**
     * Determine if the cookie exists.
     *
     * @method cookieExists
     * @param key {string} Key to test if exists.
     * @return {boolean} true if exists, false if doesn't exist.
     */
    commonUtilities.cookieExists = function (key) {
        if (key) {
            return (new RegExp("(?:^|;\\s*)" + encodeURIComponent(key).replace(/[\-\.\+\*]/g, "\\$&") + "\\s*\\=")).test(document.cookie);
        } else {
            return false;
        }
    };

    /**
     * Return an array of all cookie keys.
     *
     * @method cookieGetKeys
     * @return {Array} Array of all stored cookie keys.
     */
    commonUtilities.cookieGetKeys = function () {
        var allKeys = document.cookie.replace(/((?:^|\s*;)[^\=]+)(?=;|$)|^\s*|\s*(?:\=[^;]*)?(?:\1|$)/g, "").split(/\s*(?:\=[^;]*)?;\s*/),
            count = allKeys.length,
            index = 0;

        for (; index < count; index ++) {
            allKeys[index] = decodeURIComponent(allKeys[index]);
        }
        return allKeys;
    };

    /* ----------------------------------------------------------------------------------
     * Local storage helper functions
     * ----------------------------------------------------------------------------------*/

    /**
     * Look up an item's value in a local or session storage and return it. If it is
     * stored as JSON then we parse it and return an object.
     *
     * @param key {string} the key to look up and return its respective value from the storage object indicated. The expectation
     * is you previously saved it with commonUtilities.storageSave(key, value);
     * @param storageObject {object|null} use either localStorage, sessionStorage, or null will default to 'localStorage'
     * @returns {string|*}
     */
    commonUtilities.storageGet = function (key, storageObject) {
        var itemValueRaw,
            itemValueParsed;

        if (typeof storageObject === "undefined" || storageObject == null) {
            storageObject = global.localStorage;
        }
        itemValueRaw = storageObject.getItem(key);
        if (itemValueRaw != null) {
            itemValueParsed = JSON.parse(itemValueRaw);
            if (itemValueParsed == null) {
                itemValueParsed = itemValueRaw;
            }
        }
        return itemValueParsed;
    };

    /**
     * Save an item in local storage. If the value is null, it will attempt to remove the item if it was
     * previously saved.
     * @param key {string} the key to store a respective value in the storage object indicated.
     * @param object {*} any data you want to store. Note Objects and Arrays are saved as JSON and loadObjectWithKey will
     * re-hydrate the object. Other types are converted to string so loadObjectWithKey will return a string.
     * @return {boolean} true if saved or removed. false for an error.
     */
    commonUtilities.saveObjectWithKey = function (key, object) {
        var storageObject,
            itemValueRaw,
            saved = false;

        if (hasLocalStorage() && key != null) {
            try {
                storageObject = global.localStorage;
                if (object != null) {
                    if (typeof object === 'object') {
                        itemValueRaw = JSON.stringify(object);
                    } else {
                        itemValueRaw = object.toString();
                    }
                    storageObject.setItem(key, itemValueRaw);
                } else {
                    storageObject.removeItem(key);
                }
                saved = true;
            } catch (exception) {
                saved = false;
            }
        }
        return saved;
    };

    /**
     * Return object from local storage that was saved with saveObjectWithKey.
     * @param key {string}
     * @returns {*} object that was saved with saveObjectWithKey
     */
    commonUtilities.loadObjectWithKey = function (key) {
        var maybeJsonData,
            storageObject,
            object = null;

        if (hasLocalStorage() && key != null) {
            try {
                storageObject = global.localStorage;
                maybeJsonData = storageObject[key];
                if (maybeJsonData != null) {
                    if (maybeJsonData[0] == '{' || maybeJsonData[0] == ']') {
                        object = JSON.parse(maybeJsonData);
                    } else {
                        object = maybeJsonData;
                    }
                }
            } catch (exception) {
                object = null;
            }
        }
        return object;
    };

    /**
     * Remove the given key from local storage.
     * @param key
     */
    commonUtilities.removeObjectWithKey = function (key) {
        var removed = false;

        if (hasLocalStorage() && key != null) {
            try {
                global.localStorage.removeItem(key);
                removed = true;
            } catch (exception) {
                removed = false;
            }
        }
        return removed;
    };

    /* ----------------------------------------------------------------------------------
     * Very basic social network sharing utilities
     * ----------------------------------------------------------------------------------*/
    // 	<i tabindex="-1" class="shareIcon share_facebook socialIcon-facebook-squared-1"></i>
    //	<i tabindex="-1" class="socialIcon-twitter-1 shareIcon share_twitter"></i>
    // 	$(".share_facebook").click(shareFacebook);
    //	$(".share_twitter").click(shareTwitter);
    // G+: https://developers.google.com/+/web/share/

    commonUtilities.shareOnFacebook = function (title, summary, url, image) {
        var options = '&p[title]=' + encodeURIComponent(title)
                        + '&p[summary]=' + encodeURIComponent(summary)
                        + '&p[url]=' + encodeURIComponent(url)
                        + '&p[images][0]=' + encodeURIComponent(image);

        window.open(
            'http://www.facebook.com/sharer.php?s=100' + options,
            'Share on Facebook',
            'toolbar=no,status=0,width=626,height=436'
        );
    };

    commonUtilities.shareOnTwitter = function (message, url, related, hashTags) {
        var options = 'text=' + encodeURIComponent(message)
                        + '&url=' + encodeURIComponent(url)
                        + '&related=' + related
                        + '&hashtags=' + hashTags;

        window.open(
            'https://twitter.com/intent/tweet?' + options,
            'Tweet',
            'toolbar=no,status=0,width=626,height=436'
        );
    };

    commonUtilities.shareOnGoogle = function (url) {
        window.open(
            'https://plus.google.com/share?url=' + encodeURIComponent(url),
            'Share on Google+',
            'toolbar=no,status=0,width=626,height=436'
        );
    };

    commonUtilities.shareByEmail = function (title, message, url) {
        if (url) {
            message = message + '\n\n' + url;
        }
        window.open(
            'mailto:?subject=' + encodeURIComponent(title) + '&body=' + encodeURIComponent(message),
            'Share by Email',
            'toolbar=no,status=0,width=626,height=436'
        );
    };

    /**
     * A very basic function performance tester. Will track the time it takes to run the
     *        function for the specified number of iterations.
     *
     * @method performanceTest
     * @param testFunction {function} a function to test. This function takes no parameters. If you
     *        require parameters then wrap into a function that takes no parameters.
     * @param testId {string} any id you want to assign to the test. Not used, but returned.
     * @param totalIterations {int} number of times to call this function.
     * @return {object} test results object including test number, test function id, duration,
     *         duration units, and total iterations.
     */
    commonUtilities.performanceTest = function (testFunction, testId, totalIterations) {
        var start,
            duration,
            i,
            results;

        _testNumber ++;
        start = global.performance.now();
        for (i = 0; i < totalIterations; i ++) {
            testFunction();
        }
        duration = global.performance.now() - start;
        results = {
            testNumber: _testNumber,
            testFunction: testId,
            duration: duration,
            durationUnits: 'ms',
            totalIterations: i
        };
        return results;
    };

    /**
     * Convert a date into a MySQL compatible date string.
     * If the date provided is a string we will attempt to convert it to a date object using the available
     * Date() constructor. If no date is provided we will use the current date. If none of these conditions
     * then we expect the date provided to be a valid Date object.
     * @param date one of null, a string, or a Date object
     * @returns {string}
     */
    commonUtilities.MySQLDate = function (date) {
        var mysqlDateString;
        if (date == undefined) {
            date = new Date();
        } else if (! (date instanceof Date)) {
            date = new Date(date);
        }
        mysqlDateString = date.toISOString().slice(0, 10);
        return mysqlDateString;
    };

    /**
     * Return the date it was years from today.
     * @param years {int} number of years before today.
     * @returns {Date}
     */
    commonUtilities.subtractYearsFromNow = function (years) {
        var date = new Date();
        date.setFullYear(date.getFullYear() - years);
        return date;
    };

    /**
     * Inserts a script element to the DOM on the indicated tag.
     * @param src {string} The src attribute, usually a file reference or URL to a script to load.
     * @param tagName {string} optional tag you want to insert this script to. Defaults to 'script'
     */
    commonUtilities.insertScriptElement = function (src, tagName) {
        var script = document.createElement('script');

        if (tagName === undefined || tagName == null || tagName == '') {
            tagName = 'script';
        }
        script.src = src;
        script.type = 'text/javascript';
        script.async = true;
        document.getElementsByTagName(tagName)[0].appendChild(script);
    };

    /**
     * Parse a string of tags into individual tags array, making sure each tag is properly formatted.
     * A tag must be at least 1 character and no more than 50, without any leading or trailing whitespace,
     * and without any HTML tags (entities should be OK.)
     * @param tags {string} string of delimited tags.
     * @param delimiter {string} how the tags are separated, default is ;.
     * @returns {*} array of individual tag strings, or null if nothing to parse or an error occurred.
     */
    commonUtilities.tagParse = function (tags, delimiter) {
        var tagList,
            i;

        if (typeof tags === 'undefined' || tags === null || tags.length < 1) {
            tagList = null;
        } else {
            if (typeof delimiter === 'undefined' || delimiter === null || delimiter == '') {
                delimiter = ';';
            }
            tagList = tags.split(delimiter);
            for (i = tagList.length - 1; i >= 0; i --) {
                tagList[i] = this.stripTags(tagList[i].trim(), '').substr(0, 50).trim();
                if (tagList[i].length < 2) {
                    tagList.splice(i, 1);
                }
            }
            if (tagList.length < 1) {
                tagList = null;
            }
        }
        return tagList;
    };

    /**
     * Strip HTML tags from a string. Credit to http://locutus.io/php/strings/strip_tags/
     * @param input {string} input string to clean.
     * @param allowed {string} list of tags to accept.
     * @returns {string} the stripped result.
     */
    commonUtilities.stripTags = function (input, allowed) {
        allowed = (((allowed || '') + '').toLowerCase().match(/<[a-z][a-z0-9]*>/g) || []).join('');
        var tags = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi;
        var commentsAndPhpTags = /<!--[\s\S]*?-->|<\?(?:php)?[\s\S]*?\?>/gi;
        return input.replace(commentsAndPhpTags, '').replace(tags, function ($0, $1) {
            return allowed.indexOf('<' + $1.toLowerCase() + '>') > -1 ? $0 : ''
        });
    };

    /**
     * Determine if a string looks like a valid email address.
     * @param email {string}
     * @returns {boolean}
     */
    commonUtilities.isValidEmail = function(email) {
        return /^(([^<>()\[\]\.,;:\s@\"]+(\.[^<>()\[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()\.,;\s@\"]+\.{0,1})+([^<>()\.,;:\s@\"]{2,}|[\d\.]+))$/.test(email);
    };

    /**
     * Validate an array of fields, such as user form inputs, by using a matching array of
     * field definitions. The result is an array of fields that failed the validation and
     * the reason for failure. It is important to note the logic is driven from the
     * keyValueArrayOfDefinitions for-each key in that array the key/value is looked up
     * in keyValueArrayOfFields. This way missing fields are handled. Conversely, any
     * keys in keyValueArrayOfFields that do not appear in keyValueArrayOfDefinitions are
     * ignored.
     *
     * When using the date range check, all dates (min, max, and the value) must be JavaScript
     * date objects.
     *
     * @param keyValueArrayOfFields array A key-value array of fields to validate. The key
     *   is the name of the field. The value is the value assigned to that field that will be
     *   validated using the rules defined in keyValueArrayOfDefinitions.
     *
     * @param keyValueArrayOfDefinitions array A key-value array of field rules where the
     *   key must match the field key in keyValueArrayOfFields. The value of that key is the
     *   set of rules. The rule set itself is defined as a key/value array of mandatory and
     *   optional keys, as follows:
     *   type: string defining the data type expected. Optional, the default is "string".
     *         Valid types are string, number, bool, array, date, email.
     *   optional: boolean indicates if the field value is optional. When true, the key
     *         does not have to exist in keyValueArrayOfFields. If it does exist we accept
     *         no value for the field (null, "", or any valid empty value.) If it does
     *         exist and it is not empty it must then pass the validation test. When false
     *         the key must exist and pass the validation test. Default is false.
     *   min: The minimum value for the field. For strings this is the minimum length. For
     *         dates the earliest date. For sets the minimum number of items. Does not
     *         apply to bool. Default is - infinity.
     *   max: The maximum value for the field. For strings this is the maximum length. For
     *         dates the latest date. For sets the maximum number of items. Does not
     *         apply to bool. Default is infinity.
     *   options: an array of allowed values. Optional, default is empty.
     *   validator: A function you can pass to perform the validation. This function takes
     *         two arguments, the field name and the field value. It must return true if
     *         the value is valid and false if the value is invalid.
     * @return Array A key/value array of fields that failed their test. when empty, all
     *   tests passed. When not empty, each key in this array is the field name key.
     *   The value is an object constructed as follows:
     *   code: integer An error code, can be used to look up an error in a string table.
     *   message: string the error message.
     */
    commonUtilities.validateFields = function (keyValueArrayOfFields, keyValueArrayOfDefinitions) {
        var result = [],
            field,
            fieldDefinition,
            fieldValue,
            fieldTime,
            options,
            i;

        if (keyValueArrayOfFields != null && keyValueArrayOfDefinitions != null) {
            for (field in keyValueArrayOfDefinitions) {
                if (keyValueArrayOfDefinitions.hasOwnProperty(field)) {
                    fieldDefinition = keyValueArrayOfDefinitions[field];
                    fieldValue = keyValueArrayOfFields[field];
                    if ( ! fieldDefinition.hasOwnProperty('optional')) {
                        fieldDefinition.optional = false;
                    }
                    if ( ! fieldDefinition.optional && this.isEmpty(fieldValue)) {
                        result[field] = {code: "required", message: "This field is required."};
                    } else if (fieldDefinition.hasOwnProperty('validator')) {
                        if ( ! fieldDefinition.validator(field, fieldValue)) {
                            result[field] = {code: "validator", message: "This field failed validation."};
                        }
                    } else if ( ! (fieldDefinition.optional && this.isEmpty(fieldValue))) {
                        if ( ! fieldDefinition.hasOwnProperty('type')) {
                            fieldDefinition.type = 'string';
                        }
                        if ( ! fieldDefinition.hasOwnProperty('min')) {
                            fieldDefinition.min = fieldDefinition.type == 'number' ? Number.MIN_SAFE_INTEGER : 0;
                        }
                        if ( ! fieldDefinition.hasOwnProperty('max')) {
                            fieldDefinition.max = Number.MAX_SAFE_INTEGER;
                        }
                        if (fieldDefinition.hasOwnProperty('options')) {
                            options = fieldDefinition.options;
                        } else {
                            options = [];
                        }
                        switch (fieldDefinition.type) {
                            case "string":
                                if (fieldValue.length < fieldDefinition.min) {
                                    result[field] = {code: "min", message: "The field length is less than the minimum number of characters."};
                                } else if (fieldValue.length > fieldDefinition.max) {
                                    result[field] = {code: "max", message: "The field length is more than the maximum number of characters."};
                                } else if (options.length > 0) {
                                    if (options.indexOf(fieldValue) < 0) {
                                        result[field] = {code: "options", message: "The field value is not an option."};
                                    }
                                }
                                break;
                            case "number":
                                if (fieldValue < fieldDefinition.min) {
                                    result[field] = {code: "min", message: "The field is less than the minimum value allowed."};
                                } else if (fieldValue > fieldDefinition.max) {
                                    result[field] = {code: "max", message: "The field is more than the maximum value allowed."};
                                } else if (options.length > 0) {
                                    if (options.indexOf(fieldValue) < 0) {
                                        result[field] = {code: "options", message: "The field value is not an option."};
                                    }
                                }
                                break;
                            case "bool":
                            case "boolean":
                                if (options.length > 0) {
                                    if (options.indexOf(fieldValue) < 0) {
                                        result[field] = {code: "options", message: "The field value is not an option."};
                                    }
                                }
                                break;
                            case "date":
                                if (fieldValue instanceof Date) {
                                    fieldTime = fieldValue.getTime();
                                    if (fieldTime < fieldDefinition.min) {
                                        result[field] = {code: "min", message: "The date field is before the minimum date allowed."};
                                    } else if (fieldTime > fieldDefinition.max) {
                                        result[field] = {code: "max", message: "The date field is after the maximum date allowed."};
                                    } else if (options.length > 0) {
                                        if (options.indexOf(fieldTime) < 0) {
                                            result[field] = {code: "options", message: "The field value is not a valid option."};
                                        }
                                    }
                                }
                                break;
                            case "array":
                                if (fieldValue.length < fieldDefinition.min) {
                                    result[field] = {code: "min", message: "The field contains less than the minimum number of items."};
                                } else if (fieldValue.length > fieldDefinition.max) {
                                    result[field] = {code: "max", message: "The field contains more than the maximum number of items."};
                                } else if (options.length > 0) {
                                    for (i = 0; i < fieldValue.length; i ++) {
                                        if (options.indexOf(fieldValue[i]) < 0) {
                                            result[field] = {code: "options", message: "A field value is not a valid option."};
                                            break;
                                        }
                                    }
                                }
                                break;
                            case "email":
                                if ( ! this.isEmpty(fieldValue) && ! this.isValidEmail(fieldValue)) {
                                    result[field] = {code: "invalid", message: "The email address is not valid."};
                                }
                                break;
                        }
                    }
                }
            }
        }
        return result;
    };

    /**
     * Compute MD5 checksum for the given string.
     * @param s {string}
     * @returns {string} MD5 checksum.
     */
    commonUtilities.MD5 = function (s) {
        function L(k,d) {
            return(k<<d)|(k>>>(32-d))
        }
        function K(G,k) {
            var I,d,F,H,x;
            F=(G&2147483648);H=(k&2147483648);I=(G&1073741824);d=(k&1073741824);x=(G&1073741823)+(k&1073741823);
            if(I&d){return(x^2147483648^F^H);}
            if(I|d){if(x&1073741824){return(x^3221225472^F^H);}else{return(x^1073741824^F^H);}}else{return(x^F^H);}
        }
        function r(d,F,k){
            return(d&F)|((~d)&k);
        }
        function q(d,F,k){
            return(d&k)|(F&(~k));
        }
        function p(d,F,k){return(d^F^k)}
        function n(d,F,k){return(F^(d|(~k)))}
        function u(G,F,aa,Z,k,H,I){G=K(G,K(K(r(F,aa,Z),k),I));return K(L(G,H),F)}
        function f(G,F,aa,Z,k,H,I){G=K(G,K(K(q(F,aa,Z),k),I));return K(L(G,H),F)}
        function D(G,F,aa,Z,k,H,I){G=K(G,K(K(p(F,aa,Z),k),I));return K(L(G,H),F)}
        function t(G,F,aa,Z,k,H,I){G=K(G,K(K(n(F,aa,Z),k),I));return K(L(G,H),F)}
        function e(G){
            var Z;var F=G.length;var x=F+8;var k=(x-(x%64))/64;var I=(k+1)*16;var aa=Array(I-1);var d=0;var H=0;
            while(H<F){
                Z=(H-(H%4))/4;d=(H%4)*8;aa[Z]=(aa[Z]|(G.charCodeAt(H)<<d));H++;
            }
            Z=(H-(H%4))/4;d=(H%4)*8;aa[Z]=aa[Z]|(128<<d);aa[I-2]=F<<3;aa[I-1]=F>>>29;
            return aa;
        }
        function B(x){
            var k="",F="",G,d;
            for(d=0;d<=3;d++){
                G=(x>>>(d*8))&255;F="0"+G.toString(16);k=k+F.substr(F.length-2,2);
            }
            return k;
        }
        function J(k){
            k=k.replace(/rn/g,"n");var d="";
            for(var F=0;F<k.length;F++){
                var x=k.charCodeAt(F);
                if(x<128){
                    d+=String.fromCharCode(x);
                }else{
                    if((x>127)&&(x<2048)){
                        d+=String.fromCharCode((x>>6)|192);d+=String.fromCharCode((x&63)|128);
                    }else{
                        d+=String.fromCharCode((x>>12)|224);d+=String.fromCharCode(((x>>6)&63)|128);d+=String.fromCharCode((x&63)|128);
                    }
                }
            }
            return d;
        }
        var C;var P,h,E,v,g,Y,X,W,V;var S=7,Q=12,N=17,M=22;var A=5,z=9,y=14,w=20;var o=4,m=11,l=16,j=23;var U=6,T=10,R=15,O=21;
        s=J(s);C=e(s);Y=1732584193;X=4023233417;W=2562383102;V=271733878;
        for(P=0;P<C.length;P+=16){
            h=Y;E=X;v=W;g=V;Y=u(Y,X,W,V,C[P+0],S,3614090360);V=u(V,Y,X,W,C[P+1],Q,3905402710);W=u(W,V,Y,X,C[P+2],N,606105819);X=u(X,W,V,Y,C[P+3],M,3250441966);Y=u(Y,X,W,V,C[P+4],S,4118548399);V=u(V,Y,X,W,C[P+5],Q,1200080426);W=u(W,V,Y,X,C[P+6],N,2821735955);X=u(X,W,V,Y,C[P+7],M,4249261313);Y=u(Y,X,W,V,C[P+8],S,1770035416);V=u(V,Y,X,W,C[P+9],Q,2336552879);W=u(W,V,Y,X,C[P+10],N,4294925233);X=u(X,W,V,Y,C[P+11],M,2304563134);Y=u(Y,X,W,V,C[P+12],S,1804603682);V=u(V,Y,X,W,C[P+13],Q,4254626195);W=u(W,V,Y,X,C[P+14],N,2792965006);X=u(X,W,V,Y,C[P+15],M,1236535329);Y=f(Y,X,W,V,C[P+1],A,4129170786);V=f(V,Y,X,W,C[P+6],z,3225465664);W=f(W,V,Y,X,C[P+11],y,643717713);X=f(X,W,V,Y,C[P+0],w,3921069994);Y=f(Y,X,W,V,C[P+5],A,3593408605);V=f(V,Y,X,W,C[P+10],z,38016083);W=f(W,V,Y,X,C[P+15],y,3634488961);X=f(X,W,V,Y,C[P+4],w,3889429448);Y=f(Y,X,W,V,C[P+9],A,568446438);V=f(V,Y,X,W,C[P+14],z,3275163606);W=f(W,V,Y,X,C[P+3],y,4107603335);X=f(X,W,V,Y,C[P+8],w,1163531501);Y=f(Y,X,W,V,C[P+13],A,2850285829);V=f(V,Y,X,W,C[P+2],z,4243563512);W=f(W,V,Y,X,C[P+7],y,1735328473);X=f(X,W,V,Y,C[P+12],w,2368359562);Y=D(Y,X,W,V,C[P+5],o,4294588738);V=D(V,Y,X,W,C[P+8],m,2272392833);W=D(W,V,Y,X,C[P+11],l,1839030562);X=D(X,W,V,Y,C[P+14],j,4259657740);Y=D(Y,X,W,V,C[P+1],o,2763975236);V=D(V,Y,X,W,C[P+4],m,1272893353);W=D(W,V,Y,X,C[P+7],l,4139469664);X=D(X,W,V,Y,C[P+10],j,3200236656);Y=D(Y,X,W,V,C[P+13],o,681279174);V=D(V,Y,X,W,C[P+0],m,3936430074);W=D(W,V,Y,X,C[P+3],l,3572445317);X=D(X,W,V,Y,C[P+6],j,76029189);Y=D(Y,X,W,V,C[P+9],o,3654602809);V=D(V,Y,X,W,C[P+12],m,3873151461);W=D(W,V,Y,X,C[P+15],l,530742520);X=D(X,W,V,Y,C[P+2],j,3299628645);Y=t(Y,X,W,V,C[P+0],U,4096336452);V=t(V,Y,X,W,C[P+7],T,1126891415);W=t(W,V,Y,X,C[P+14],R,2878612391);X=t(X,W,V,Y,C[P+5],O,4237533241);Y=t(Y,X,W,V,C[P+12],U,1700485571);V=t(V,Y,X,W,C[P+3],T,2399980690);W=t(W,V,Y,X,C[P+10],R,4293915773);X=t(X,W,V,Y,C[P+1],O,2240044497);Y=t(Y,X,W,V,C[P+8],U,1873313359);V=t(V,Y,X,W,C[P+15],T,4264355552);W=t(W,V,Y,X,C[P+6],R,2734768916);X=t(X,W,V,Y,C[P+13],O,1309151649);Y=t(Y,X,W,V,C[P+4],U,4149444226);V=t(V,Y,X,W,C[P+11],T,3174756917);W=t(W,V,Y,X,C[P+2],R,718787259);X=t(X,W,V,Y,C[P+9],O,3951481745);Y=K(Y,h);X=K(X,E);W=K(W,v);V=K(V,g);
        }
        var i=B(Y)+B(X)+B(W)+B(V);
        return i.toLowerCase();
    };

    /**
     * Given a user email, generate the Gravatar URL for the image.
     * @param email
     * @param size
     * @returns {string} - URL.
     */
    commonUtilities.getGravatarURL = function (email, size) {
        var size = size || 80;
        return 'https://www.gravatar.com/avatar/' + this.MD5(email) + '.jpg?s=' + size;
    };

    /* ----------------------------------------------------------------------------------
     * Setup for AMD, node, or standalone reference the commonUtilities object.
     * ----------------------------------------------------------------------------------*/

    if (typeof define === 'function' && define.amd) {
        define(function () { return commonUtilities; });
    } else if (typeof exports === 'object') {
        module.exports = commonUtilities;
    } else {
        var existingUtilityFunctions = global.commonUtilities;
        commonUtilities.existingUtilityFunctions = function () {
            global.commonUtilities = existingUtilityFunctions;
            return this;
        };
        global.commonUtilities = commonUtilities;
    }
})(this);
