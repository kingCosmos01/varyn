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
 * @exports commonUtilities
 */

(function commonUtilities (global) {
    "use strict";

    var commonUtilities = {
        version: "1.4.3"
    };
    var _base64KeyStr = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
    var _testNumber = 0;

    /**
     * Determine if HTML5 local or session storage is available.
     * @param {string} storageType - either "localStorage" or "sessionStorage", default is "localStorage".
     * @param {boolean} robustCheck - true for the more robust but un-performant test.
     * @returns {boolean} True if the storage type is supported.
     */
    commonUtilities.browserStorageAvailable = function(storageType, robustCheck) {
        var hasSupport = false,
            storage,
            testKey;

        if (storageType === undefined || storageType == null || storageType == "") {
            storageType = "localStorage";
        }
        try {
            hasSupport = storageType in global && global[storageType] !== null;
            if (hasSupport && robustCheck) { // even if "supported" make sure we can write and read from it
                storage = global[storageType];
                testKey = "commonUtilities";
                storage.setItem(testKey, "1");
                storage.removeItem(testKey);
            }
        } catch (exception) {
            hasSupport = false;
        }
        return hasSupport;
    }

    /**
     * Coerce a boolean value to its string representation, either "true" or "false". The input
     * parameter is expected to be a boolean but if it isn't it is coerced to its boolean representation.
     * @param {boolean} value Expected boolean value to be converted to a printable string, either "true" or "false".
     * @returns {string} Either "true" or "false".
     */
    commonUtilities.booleanToString = function(value) {
        return ( ! ! value) ? "true" : "false";
    }

    /**
     * Return the provided object represented as a string in "key: value;" format. Typically
     * used for debug and user display. For serialization it is preferred to convert
     * objects to JSON.
     *
     * @param {object} object The object to convert to a string representation.
     * @return {string} string The object converted to a string representation.
     */
    commonUtilities.objectToString = function (object) {
        var result,
            prop;
        if (object) {
            result = "";
            for (prop in object) {
                if (object.hasOwnProperty(prop)) {
                    result += (result.length > 0 ? " " : "") + prop + ": " + object[prop] + ";";
                }
            }
        } else {
            result = "null;";
        }
        return result;
    };

    /**
     * Return the provided array as a string in key: value; format.
     *
     * @param {array} array The array to convert to a string representation.
     * @return {string} string The array converted to a string representation.
     */
    commonUtilities.arrayToString = function (array) {
        var result,
            key,
            value;
        if (array && array instanceof Array) {
            result = "[";
            for (key in array) {
                value = array[key];
                if (typeof(value) == "undefined") {
                    value = "undefined";
                } else if (Array.isArray(value)) {
                    value = this.arrayToString(value);
                } else if (typeof(value) == "object") {
                    value = this.objectStringify(value);
                }
                result += (result.length > 1 ? ", " : "") + key + ": " + value;
            }
            result += "]";
        } else {
            result = "null";
        }
        return result;
    };

    /**
     * Return the provided object as a string in key: value; format. This version handles
     * functions but is slower than objectToString.
     *
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
     * Append an existing URL with additional query paremters.
     * @param {String} url A well-formed URL. It may or may not have "?" query parameter(s).
     * @param {Object} parameters Expected object of key/value properties. Does not work for nested objects.
     * @returns {String} The url with query string parameters appended.
     */
    commonUtilities.appendQueryParametersToURL = function (url, parameters) {
        var queryPos = url.indexOf("?");
        var safeParameters = [];
        for (var parameter in parameters) {
            if (parameters.hasOwnProperty(parameter)) {
                safeParameters.push(encodeURIComponent(parameter) + "=" + encodeURIComponent(parameters[parameter]));
            }
        }
        if (queryPos > 0 && queryPos < url.length - 1) {
            url += "&";
        } else if (queryPos == -1) {
            url += "?";
        }
        url += safeParameters.join("&");
        return url;
    };

    /**
     * Extend an object with properties copied from other objects. Takes a variable number of arguments:
     * @param {any} ...arguments
     *  If no arguments, an empty object is returned.
     *  If one argument, that object is returned unchanged.
     *  If more than one argument, each object in l-2-r order is copied to the first object one property at a time. When
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
     * @param {Regex} pattern a regex pattern to match.
     * @param {Array} arrayOfStrings strings to test each against the pattern.
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
     * @param {string} path URI path to check.
     * @return {string} path Full URI path.
     */
    commonUtilities.makeFullPath = function (path) {
        if (path) {
            if (path[path.length - 1] !== "/") {
                path += "/";
            }
            if (path[0] !== "/") {
                path = "/" + path;
            }
        } else {
            path = "/";
        }
        return path;
    };

    /**
     * Append a folder or file name to the end of an existing path string.
     *
     * @param {string} path URI path to append to.
     * @param {string} file folder or file to append.
     * @return {string} path Full URI path.
     */
    commonUtilities.appendFileToPath = function (path, file) {
        if (path && file) {
            if (path[path.length - 1] !== "/" && file[0] !== "/") {
                path += "/" + file;
            } else if (path[path.length - 1] == "/" && file[0] == "/") {
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
     * @param {string} text text containing tokens to be replaced.
     * @param {Array} parameters array/object of key/value pairs to match keys as tokens in text and replace with value.
     * @return {string} text replaced string.
     */
    commonUtilities.tokenReplace = function (text, parameters) {
        var token,
            regexMatch;

        for (token in parameters) {
            if (parameters.hasOwnProperty(token)) {
                regexMatch = new RegExp("\{" + token + "\}", "g");
                text = text.replace(regexMatch, parameters[token]);
            }
        }
        return text;
    };

    /**
     * Translate single characters of an input string.
     *
     * @param {String} string to translate. It is not mutated.
     * @param {Array} undesired characters to translate from in string.
     * @param {Array} desired characters to translate to in string.
     * @returns {String} the translated string.
     */
    commonUtilities.stringTranslate = function(string, undesired, desired) {
        var i;
        var char;
        var found;
        var length;
        var result = "";
        if (typeof string !== "string" || string.length < 1 || ! Array.isArray(undesired) || ! Array.isArray(desired) || undesired.length != desired.length) {
            return string;
        }
        length = string.length;
        for (i = 0; i < length; i ++) {
            char = string.charAt(i);
            found = undesired.indexOf(char);
            if (found >= 0) {
                char = desired[found];
            }
            result += char;
        }
        return result;
    }

    /**
     * Determine if a given variable is considered an empty value. A value is considered empty if it is any one of
     * `null`, `undefined`, `false`, `NaN`, an empty string, an empty array, or 0. Note this does not consider an
     * empty object `{}` to be empty.
     * @param {any} field The parameter to be tested for emptiness.
     * @returns {boolean} `true` if `field` is considered empty.
     */
    commonUtilities.isEmpty = function (field) {
        return field === undefined
            || field === null
            || field === false
            || (typeof field === "string" && (field === "" || field === "null" || field === "NULL"))
            || (field instanceof Array && field.length == 0)
            || (typeof field === "number" && (isNaN(field) || field === 0));
    }

    /**
     * Determine if a given variable is considered null (either null or undefined).
     * At the moment this will not check for "null"/"NULL" values, as when using SQL.
     * @param {any} field A value to consider.
     * @returns {boolean} `true` if `value` is considered null.
     */
    commonUtilities.isNull = function(field) {
        return field === undefined || field === null;
    }

    /**
     * Coerce a value to its boolean equivalent, causing the value to be interpreted as its
     * boolean intention. This works very different that the JavaScript coercion. For example,
     * "0" == true and "false" == true in JavaScript but here "0" == false and "false" == false.
     * @param {*} value A value to test.
     * @returns {boolean} `true` if `value` is considered a coercible true value.
     */
    commonUtilities.coerceBoolean = function(value) {
        if (typeof value === "string") {
            value = value.toLowerCase();
            return value === "1" || value === "true" || value === "t" || value === "checked" || value === "yes" || value === "y";
        } else {
            return value === true || value === 1;
        }
    }

    /**
     * Coerce a value to the first non-empty value of a given set of parameters. It is expected the last
     * parameter is a non-empty value and is the expected result when all arguments are empty values. If
     * for some reason this function is called with an unexpected number of parameters it returns `null`.
     * See `isEmpty()` for the meaning of "empty".
     * @param {any} arguments Any number of parameters, at least the last one is expected to be not empty.
     * @returns {any} The first parameter encountered, in order, that is not an empty value.
     */
    commonUtilities.coerceNotEmpty = function() {
        var result;
        var numberOfArguments = arguments.length;
        if (numberOfArguments == 0) {
            result = null;
        } else if (numberOfArguments == 1) {
            result = arguments[0];
        } else {
            for (var i = 0; i < numberOfArguments; i++) {
                if (! commonUtilities.isEmpty(arguments[i])) {
                    result = arguments[i];
                    break;
                }
            }
            if (result === undefined) {
                result = arguments[numberOfArguments - 1];
            }
        }
        return result;
    }

    /**
     * Coerce a value to the first non-null value of a given set of parameters. It is expected the last
     * parameter is a non-null value and is the expected result when all arguments are null values. If
     * for some reason this function is called with an unexpected number of parameters it returns `null`.
     * See `isNull()` for the meaning of "null".
     * @param {any} arguments Any number of parameters, at least the last one is expected to be not null.
     * @returns {any} The first parameter encountered, in order, that is not a null value.
     */
    commonUtilities.coerceNotNull = function() {
        var result;
        var numberOfArguments = arguments.length;
        if (numberOfArguments == 0) {
            result = null;
        } else if (numberOfArguments == 1) {
            result = arguments[0] === undefined ? null : arguments[0];
        } else {
            for (var i = 0; i < numberOfArguments; i++) {
                if (! commonUtilities.isNull(arguments[i])) {
                    result = arguments[i];
                    break;
                }
            }
            if (result === undefined) {
                result = arguments[numberOfArguments - 1];
            }
        }
        return result;
    }

    /**
     * Convert a string into one that has no HTML vunerabilities such that it can be rendered inside an HTML tag.
     * @param {string} string A string to check for HTML vunerabilities.
     * @returns {string} A copy of the input string with any HTML vunerabilities removed.
     */
    commonUtilities.safeForHTML = function (string) {
        var htmlEscapeMap = {
                "&": "&amp;",
                "<": "&lt;",
                ">": "&gt;",
                '"': "&quot;",
                "'": "&#x27;",
                "/": "&#x2F;"
            },
            htmlEscaper = /[&<>"'\/]/g;
        return ("" + string).replace(htmlEscaper, function (match) {
            return htmlEscapeMap[match]
        });
    };

    /**
     * Convert any string into a string that can be used as a DOM id (aka slug). Rules:
     *   * Only allow A-Z, a-z, 0-9, dash, space.
     *   * Trim any leading or trailing space.
     *   * Only lowercase characters.
     *   * Max length 50.
     *
     * For example, the string
     *    "This is   +a TEST" is changed to "this-is-a-test". Spaces and multiple spaces change
     *    to -, special chars are removed, and the string is all lowercase.
     *
     * @param {string} label A string to consider.
     * @returns {string} The converted string.
     */
    commonUtilities.makeSafeForId = function (label) {
        if (typeof label !== "string") {
            if (label !== undefined && label !== null) {
                label = label.toString();
            } else {
                label = "id";
            }
        }
        label = label.trim();
        if (label.length > 0) {
            return label.replace(/-/g, " ").replace(/[^\w\s]/g, "").replace(/\s\s+/g, " ").replace(/\s/g, "-").toLowerCase().substr(0, 50);
        } else {
            return "id";
        }
    };

    /* ----------------------------------------------------------------------------------
     * Platform and feature detection
     * ----------------------------------------------------------------------------------*/
    /**
     * Determine if the current invokation environment is a mobile device.
     * @todo: Really would rather use modernizr.js as you really do not want isMobile(), you want isTouchDevice()
     *
     * @return {bool} true if we think this is a mobile device, false if we think otherwise.
     *
     */
    commonUtilities.isMobile = function () {
        return (commonUtilities.isMobileAndroid() || commonUtilities.isMobileBlackberry() || commonUtilities.isMobileIos() || commonUtilities.isMobileWindows());
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

    /**
     * On some platforms, web audio doesn't work until a user-initiated event occurs. This function
     * plays a short silent clip in order to unlock the audio capabilities. This function returns a
     * HTMLAudio element that you must call the play method on once you detected the user interacted
     * (e.g. a tap event) with your app the very first time.
     * @returns HTMLAudio An audio element that you call .play() on in order to unlock audio.
     */
    commonUtilities.unlockWebAudio = function () {
        var silence = "data:audio/mpeg;base64,//uQxAAAAAAAAAAAAAAAAAAAAAAASW5mbwAAAA8AAAADAAAGhgBVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVWqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqr///////////////////////////////////////////8AAAA5TEFNRTMuOThyAc0AAAAAAAAAABSAJAiqQgAAgAAABobxtI73AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA//uQxAACFEII9ACZ/sJZwWEoEb8w/////N//////JcxjHjf+7/v/H2PzCCFAiDtGeyBCIx7bJJ1mmEEMy6g8mm2c8nrGABB4h2Mkmn//4z/73u773R5qHHu/j/w7Kxkzh5lWRWdsifCkNAnY9Zc1HvDAhjhSHdFkHFzLmabt/AQxSg2wwzLhHIJOBnAWwVY4zrhIYhhc2kvhYDfQ4hDi2Gmh5KyFn8EcGIrHAngNgIwVIEMf5bzbAiTRoAD///8z/KVhkkWEle6IX+d/z4fvH3BShK1e5kmjkCMoxVmXhd4ROlTKo3iipasvTilY21q19ta30/v/0/idPX1v8PNxJL6ramnOVsdvMv2akO0iSYIzdJFirtzWXCZicS9vHqvSKyqm5XJBdqBwPxyfJdykhWTZ0G0ZyTZGpLKxsNwwoRhsx3tZfhwmeOBVISm3impAC/IT/8hP/EKEM1KMdVdVKM2rHV4x7HVXZvbVVKN/qq8CiV9VL9jjH/6l6qf7MBCjZmOqsAibjcP+qqqv0oxqpa/NVW286hPo1nz2L/h8+jXt//uSxCmDU2IK/ECN98KKtE5IYzNoCfbw+u9i5r8PoadUMFPKqWL4LK3T/LCraMSHGkW4bpLXR/E6LlHOVQxmslKVJ8IULktMN06N0FKCpHCoYsjC4F+Z0NVqdNFoGSTjSiyjzLdnZ2fNqTi2eHKONONKLMPMKLONKLMPQRJGlFxZRoKcJFAYEeIFiRQkUWUeYfef//Ko04soswso40UJAgMw8wosososy0EalnZyjQUGBRQGIFggOWUacWUeYmuadrZziQKKEgQsQLAhQkUJAgMQDghltLO1onp0cpkNInSFMqlYeSEJ5AHsqFdOwy1DA2sRmRJKxdKRfLhfLw5BzUxBTUUzLjk4LjJVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVUxBTUUzLjk4LjJVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVf/7ksRRA8AAAaQAAAAgAAA0gAAABFVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVU=";
        var audioTag = document.createElement("audio");
        audioTag.controls = false;
        audioTag.preload = "auto";
        audioTag.loop = false;
        audioTag.src = silence;
        document.addEventListener("visibilitychange", function () {
            if (document.visibilityState == 'visible') {
                audioTag.play();
            }
        });
        return audioTag;
    };

    /* ----------------------------------------------------------------------------------
     * Various conversion utilities - UTF-8, Base 64
     * ----------------------------------------------------------------------------------*/

    /**
     * Encode a Unicode string in UTF-8 character encoding.
     *
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
     * @param {string} input string to encode in base 64.
     * @return {string} output encoded string.
     */
    commonUtilities.base64Encode = function (input) {
        var output = "",
            inputLength = input.length,
            chr1, chr2, chr3, enc1, enc2, enc3, enc4,
            i = 0;

        input = commonUtilities.utf8Encode(input);
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
        return commonUtilities.utf8Decode(output);
    };

    /**
     * Round a number to the requested number of decimal places.
     * @param {number} value the number to round.
     * @param {integer} decimalPlaces the number of decimal places.
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
     * Return the contents of the cookie indexed by the specified key.
     *
     * @param {string} key Indicate which cookie to get.
     * @return {string|null} Contents of cookie stored with key.
     */
    commonUtilities.cookieGet = function (key) {
        if (key && document && document.cookie) {
            return decodeURIComponent(document.cookie.replace(new RegExp("(?:(?:^|.*;)\\s*" + encodeURIComponent(key).replace(/[\-\.\+\*]/g, "\\$&") + "\\s*\\=\\s*([^;]*).*$)|^.*$"), "$1")) || null;
        } else {
            return null;
        }
    };

    /**
     * Set a cookie indexed by the specified key.
     *
     * @param {string} key Indicate which cookie to set.
     * @param {object} value Value to store under key. If null, expire the prior cookie.
     * @param {Number|String|Date} expiration When the cookie should expire. Number indicates
     *   max age, in seconds. String indicates GMT date. Date is converted to GMT date.
     * @param {string} path Cookie URL path.
     * @param {string} domain Cookie domain.
     * @param {boolean} isSecure Set cookie secure flag. Default is true.
     * @return {boolean|string} true if set, false if error. Returns string if not running in
     *   a browser environment, such as Node.
     */
     commonUtilities.cookieSet = function (key, value, expiration, path, domain, isSecure) {
        var expires;
        var neverExpires;
        var sameSite;

        if ( ! key || /^(?:expires|max\-age|path|domain|secure)$/i.test(key)) {
            // This is an invalid cookie key.
            return false;
        }
        if (value === null || typeof value === "undefined") {
            return commonUtilities.cookieRemove(key, path, domain);
        }
        expires = "";
        neverExpires = "expires=Fri, 31 Dec 9999 23:59:59 GMT";
        sameSite = "samesite=LAX";
        if (typeof isSecure === "undefined") {
            isSecure = true;
        }
        if (typeof value === "object") {
            value = JSON.stringify(value);
        }
        if (expiration) {
            switch (expiration.constructor) {
            case Number:
                expires = expiration === Infinity ? neverExpires : "; max-age=" + expiration;
                break;
            case String:
                expires = "expires=" + expiration;
                break;
            case Date:
                expires = "expires=" + expiration.toUTCString();
                break;
            default:
                expires = neverExpires;
                break;
            }
        } else {
            expires = neverExpires;
        }
        var cookieData = encodeURIComponent(value) + "; "
            + expires + "; "
            + (domain ? ("domain=" + domain + "; ") : "")
            + (path ? ("path=" + path + "; ") : "")
            + sameSite + "; "
            + (isSecure ? "Secure;" : "");
        if (typeof global.document === "undefined" || typeof global.document.cookie === "undefined") {
            // If the document object is undefined then we are running in Node.
            return cookieData;
        }
        global.document.cookie = encodeURIComponent(key) + "=" + cookieData;
        return true;
    };

    /**
     * Remove a cookie indexed by the specified key.
     *
     * @param {string} key Indicate which cookie to remove.
     * @param {string} path Cookie URL path.
     * @param {string} domain Cookie domain.
     * @return {boolean} true if removed, false if doesn't exist.
     */
    commonUtilities.cookieRemove = function (key, path, domain) {
        if (commonUtilities.cookieExists(key)) {
            document.cookie = encodeURIComponent(key) + "=; expires=Thu, 01 Jan 1970 00:00:00 GMT" + (domain ? "; domain=" + domain : "") + (path ? "; path=" + path : "");
            return true;
        } else {
            return false;
        }
    };

    /**
     * Determine if the cookie exists.
     *
     * @param {string} key Key to test if exists.
     * @return {boolean} true if exists, false if doesn't exist.
     */
    commonUtilities.cookieExists = function (key) {
        if (key && global.document) {
            return (new RegExp("(?:^|;\\s*)" + encodeURIComponent(key).replace(/[\-\.\+\*]/g, "\\$&") + "\\s*\\=")).test(document.cookie);
        } else {
            return false;
        }
    };

    /**
     * Return an array of all cookie keys.
     *
     * @return {Array} Array of all stored cookie keys.
     */
    commonUtilities.cookieGetKeys = function () {
        if (! global.document) {
            return [];
        }
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
     * Determine if we have sessionStorage available.
     * @returns {boolean}
     */
    commonUtilities.haveSessionStorage = function () {
        return this.browserStorageAvailable("sessionStorage", true);
    };

    /**
     * Determine if we have localStorage available.
     * @returns {boolean}
     */
    commonUtilities.haveLocalStorage = function () {
        return this.browserStorageAvailable("localStorage", true);
    };

    /**
     * Look up an item's value in a local or session storage and return it. If it is
     * stored as JSON then we parse it and return an object.
     *
     * @param {string} key the key to look up and return its respective value from the storage object indicated. The expectation
     * is you previously saved it with commonUtilities.storageSave(key, value);
     * @param {Object} storageObject use either localStorage, sessionStorage, or null will default to 'localStorage'
     * @returns {string|*}
     */
    commonUtilities.storageGet = function (key, storageObject) {
        var itemValueRaw,
            itemValueParsed;

        if (storageObject === undefined || storageObject == null) {
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
     * @param {string} key the key to store a respective value in the storage object indicated.
     * @param {any} object any data you want to store. Note Objects and Arrays are saved as JSON and loadObjectWithKey will
     * re-hydrate the object. Other types are converted to string so loadObjectWithKey will return a string.
     * @return {boolean} true if saved or removed. false for an error.
     */
    commonUtilities.saveObjectWithKey = function (key, object) {
        var storageObject,
            itemValueRaw,
            saved = false;

        if (this.browserStorageAvailable("localStorage", false) && key != null) {
            try {
                storageObject = global.localStorage;
                if (object != null) {
                    if (typeof object === "object") {
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
     * @param {string} key The key property name to look up.
     * @returns {any} object that was saved with saveObjectWithKey().
     */
    commonUtilities.loadObjectWithKey = function (key) {
        var maybeJsonData,
            storageObject,
            object = null;

        if (this.browserStorageAvailable("localStorage", false) && key != null) {
            try {
                storageObject = global.localStorage;
                maybeJsonData = storageObject[key];
                if (maybeJsonData != null) {
                    if (maybeJsonData[0] == "{" || maybeJsonData[0] == "]") {
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

        if (this.browserStorageAvailable("localStorage", false) && key != null) {
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
     * @param {function} testFunction a function to test. This function takes no parameters. If you
     *        require parameters then wrap into a function that takes no parameters.
     * @param {string} testId any id you want to assign to the test. Not used, but returned.
     * @param {integer} totalIterations number of times to call this function.
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
            durationUnits: "ms",
            totalIterations: i
        };
        return results;
    };

    /**
     * Convert a date into a MySQL compatible date string (YYYY-MM-DD).
     * If the date provided is a string we will attempt to convert it to a date object using the available
     * Date() constructor. If no date is provided we will use the current date. If none of these conditions
     * then we expect the date provided to be a valid Date object.
     * @param {null|string|Date} date one of null, a string, or a Date object
     * @returns {string}
     */
    commonUtilities.MySQLDate = function (date) {
        var mysqlDateString;
        if (date == undefined || date == null) {
            date = new Date();
        } else if (! (date instanceof Date)) {
            date = new Date(date);
        }
        mysqlDateString = date.toISOString().slice(0, 10);
        return mysqlDateString;
    };

    /**
     * Return the date it was years from today.
     * @param {integer} years Number of years before today.
     * @returns {Date}
     */
    commonUtilities.subtractYearsFromNow = function (years) {
        var date = new Date();
        date.setFullYear(date.getFullYear() - years);
        return date;
    };

    /**
     * Inserts a new script element into the DOM on the indicated tag.
     *
     * @param id {string} The id attribute, so that the script element can be referenced.
     * @param src {string} The src attribute, usually a file reference or URL to a script to load.
     * @param tagName {string} optional tag you want to insert this script to. Defaults to "script"
     */
    commonUtilities.insertScriptElement = function (id, src, tagName) {
        if (document.getElementById(id)) {
            // script already exists.
            return;
        }
        var scriptElement = document.createElement("script");
        if (tagName === undefined || tagName == null || tagName == "") {
            tagName = "script";
        }
        var fjs = document.getElementsByTagName(tagName)[0];
        if (fjs == null) {
            fjs = document.getElementsByTagName("div")[0];
        }
        scriptElement.id = id;
        scriptElement.src = src;
        script.type = "text/javascript";
        script.async = true;
        fjs.appendChild(script);
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

        if (typeof tags === "undefined" || tags === null || tags.length < 1) {
            tagList = null;
        } else {
            if (typeof delimiter === "undefined" || delimiter === null || delimiter == "") {
                delimiter = ";";
            }
            tagList = tags.split(delimiter);
            for (i = tagList.length - 1; i >= 0; i --) {
                tagList[i] = commonUtilities.stripTags(tagList[i].trim(), '').substr(0, 50).trim();
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
        allowed = (((allowed || "") + "").toLowerCase().match(/<[a-z][a-z0-9]*>/g) || []).join("");
        var tags = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi;
        var commentsAndPhpTags = /<!--[\s\S]*?-->|<\?(?:php)?[\s\S]*?\?>/gi;
        return input.replace(commentsAndPhpTags, "").replace(tags, function ($0, $1) {
            return allowed.indexOf("<" + $1.toLowerCase() + ">") > -1 ? $0 : ""
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
                    if ( ! fieldDefinition.hasOwnProperty("optional")) {
                        fieldDefinition.optional = false;
                    }
                    if ( ! fieldDefinition.optional && this.isEmpty(fieldValue)) {
                        result[field] = {code: "required", message: "This field is required."};
                    } else if (fieldDefinition.hasOwnProperty("validator")) {
                        if ( ! fieldDefinition.validator(field, fieldValue)) {
                            result[field] = {code: "validator", message: "This field failed validation."};
                        }
                    } else if ( ! (fieldDefinition.optional && this.isEmpty(fieldValue))) {
                        if ( ! fieldDefinition.hasOwnProperty("type")) {
                            fieldDefinition.type = "string";
                        }
                        if ( ! fieldDefinition.hasOwnProperty("min")) {
                            fieldDefinition.min = fieldDefinition.type == "number" ? Number.MIN_SAFE_INTEGER : 0;
                        }
                        if ( ! fieldDefinition.hasOwnProperty("max")) {
                            fieldDefinition.max = Number.MAX_SAFE_INTEGER;
                        }
                        if (fieldDefinition.hasOwnProperty("options")) {
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
     * Parse a domain or a URL to return the domain with the server dropped.
     * Works on either a domain name (e.g. www.host.com) or a URL (e.g.
     * https://www.host.com/path). In either case this function should return
     * the domain the server is a member of, e.g. `host.com`.
     *
     * @param {String} proposedHost A proposed URL or domain name to parse.
     * @returns {String} The proposed host domain with the server removed.
     */
    commonUtilities.domainDropServer = function(proposedHost) {
        var targetHost = proposedHost ? proposedHost.toString() : "";
        var pos = targetHost.indexOf("://"); // remove the protocol
        if (pos > 0) {
            targetHost = targetHost.substring(pos + 3);
        }
        pos = targetHost.indexOf("//"); // remove the neutral protocol
        if (pos == 0) {
            targetHost = targetHost.substring(2);
        }
        pos = targetHost.indexOf("/"); // remove everything after the domain
        if (pos > 0) {
            targetHost = targetHost.substring(0, pos);
        }
        pos = targetHost.indexOf(":"); // remove everything after the port
        if (pos > 0) {
            targetHost = targetHost.substring(0, pos);
        }
        var domainParts = targetHost.split(".");
        if (domainParts.length > 2) {
            domainParts.shift();
        }
        targetHost = domainParts.join(".")
        return targetHost;
    }

    /**
     * Compute MD5 checksum for the given string.
     * @param s {string}
     * @returns {string} MD5 checksum.
     */
    commonUtilities.md5 = function (s) {
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
     * Varyn URL safe version of blowfish encrypt, decrypt
     * commonUtilities.blowfish.encryptString(data, key)
     * commonUtilities.blowfish.decryptString(data, key)
     * Encrypted string is the URL safe ecsaped version of base-64, translates +/= to -_~
     * Clear text must be string.
     * Key must be hex digits represented as string "0123456789abcdef"
     * Uses ECB mode only.
     */
    commonUtilities.blowfish = (function () {
        var crypto={};
        var base64={};
        var p="=";
        var tab="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";

        base64.encode=function(ba){
            var s=[], l=ba.length;
            var rm=l%3;
            var x=l-rm;
            for (var i=0; i<x;){
                var t=ba[i++]<<16|ba[i++]<<8|ba[i++];
                s.push(tab.charAt((t>>>18)&0x3f));
                s.push(tab.charAt((t>>>12)&0x3f));
                s.push(tab.charAt((t>>>6)&0x3f));
                s.push(tab.charAt(t&0x3f));
            }
            switch(rm){
                case 2:{
                    var t=ba[i++]<<16|ba[i++]<<8;
                    s.push(tab.charAt((t>>>18)&0x3f));
                    s.push(tab.charAt((t>>>12)&0x3f));
                    s.push(tab.charAt((t>>>6)&0x3f));
                    s.push(p);
                    break;
                }
                case 1:{
                    var t=ba[i++]<<16;
                    s.push(tab.charAt((t>>>18)&0x3f));
                    s.push(tab.charAt((t>>>12)&0x3f));
                    s.push(p);
                    s.push(p);
                    break;
                }
            }
            return s.join("");
        };

        base64.decode=function(str){
            var s=str.split(""), out=[];
            var l=s.length;
            while(s[--l]==p){ }
            for (var i=0; i<l;){
                var t=tab.indexOf(s[i++])<<18;
                if(i<=l){ t|=tab.indexOf(s[i++])<<12 };
                if(i<=l){ t|=tab.indexOf(s[i++])<<6 };
                if(i<=l){ t|=tab.indexOf(s[i++]) };
                out.push((t>>>16)&0xff);
                out.push((t>>>8)&0xff);
                out.push(t&0xff);
            }
            while(out[out.length-1]==0){ out.pop(); }
            return out;
        };

        function arrayMapWithHoles(arr, callback, thisObject, Ctr){
            var i = 0, l = arr && arr.length || 0, out = new (Ctr || Array)(l);
            if(l && typeof arr == "string") arr = arr.split("");
            if(typeof callback == "string") callback = cache[callback] || buildFn(callback);
            if(thisObject){
                for(; i < l; ++i){
                    out[i] = callback.call(thisObject, arr[i], i, arr);
                }
            }else{
                for(; i < l; ++i){
                    out[i] = callback(arr[i], i, arr);
                }
            }
            return out;
        };

        function stringTranslate(string, undesired, desired) {
            var i, char, found, length, result = "";
            if (typeof string !== "string" || string.length < 1 || ! Array.isArray(undesired) || ! Array.isArray(desired) || undesired.length != desired.length) {
                return string;
            }
            length = string.length;
            for (i = 0; i < length; i ++) {
                char = string.charAt(i);
                found = undesired.indexOf(char);
                if (found >= 0) {
                    char = desired[found];
                }
                result += char;
            }
            return result;
        }

       crypto.blowfish = new function(){
            var POW8=Math.pow(2,8);
            var POW16=Math.pow(2,16);
            var POW24=Math.pow(2,24);
            var iv=null;
            var boxes={
                p:[
                    0x243f6a88, 0x85a308d3, 0x13198a2e, 0x03707344, 0xa4093822, 0x299f31d0, 0x082efa98, 0xec4e6c89,
                    0x452821e6, 0x38d01377, 0xbe5466cf, 0x34e90c6c, 0xc0ac29b7, 0xc97c50dd, 0x3f84d5b5, 0xb5470917,
                    0x9216d5d9, 0x8979fb1b
                ],
                s0:[
                    0xd1310ba6, 0x98dfb5ac, 0x2ffd72db, 0xd01adfb7, 0xb8e1afed, 0x6a267e96, 0xba7c9045, 0xf12c7f99,
                    0x24a19947, 0xb3916cf7, 0x0801f2e2, 0x858efc16, 0x636920d8, 0x71574e69, 0xa458fea3, 0xf4933d7e,
                    0x0d95748f, 0x728eb658, 0x718bcd58, 0x82154aee, 0x7b54a41d, 0xc25a59b5, 0x9c30d539, 0x2af26013,
                    0xc5d1b023, 0x286085f0, 0xca417918, 0xb8db38ef, 0x8e79dcb0, 0x603a180e, 0x6c9e0e8b, 0xb01e8a3e,
                    0xd71577c1, 0xbd314b27, 0x78af2fda, 0x55605c60, 0xe65525f3, 0xaa55ab94, 0x57489862, 0x63e81440,
                    0x55ca396a, 0x2aab10b6, 0xb4cc5c34, 0x1141e8ce, 0xa15486af, 0x7c72e993, 0xb3ee1411, 0x636fbc2a,
                    0x2ba9c55d, 0x741831f6, 0xce5c3e16, 0x9b87931e, 0xafd6ba33, 0x6c24cf5c, 0x7a325381, 0x28958677,
                    0x3b8f4898, 0x6b4bb9af, 0xc4bfe81b, 0x66282193, 0x61d809cc, 0xfb21a991, 0x487cac60, 0x5dec8032,
                    0xef845d5d, 0xe98575b1, 0xdc262302, 0xeb651b88, 0x23893e81, 0xd396acc5, 0x0f6d6ff3, 0x83f44239,
                    0x2e0b4482, 0xa4842004, 0x69c8f04a, 0x9e1f9b5e, 0x21c66842, 0xf6e96c9a, 0x670c9c61, 0xabd388f0,
                    0x6a51a0d2, 0xd8542f68, 0x960fa728, 0xab5133a3, 0x6eef0b6c, 0x137a3be4, 0xba3bf050, 0x7efb2a98,
                    0xa1f1651d, 0x39af0176, 0x66ca593e, 0x82430e88, 0x8cee8619, 0x456f9fb4, 0x7d84a5c3, 0x3b8b5ebe,
                    0xe06f75d8, 0x85c12073, 0x401a449f, 0x56c16aa6, 0x4ed3aa62, 0x363f7706, 0x1bfedf72, 0x429b023d,
                    0x37d0d724, 0xd00a1248, 0xdb0fead3, 0x49f1c09b, 0x075372c9, 0x80991b7b, 0x25d479d8, 0xf6e8def7,
                    0xe3fe501a, 0xb6794c3b, 0x976ce0bd, 0x04c006ba, 0xc1a94fb6, 0x409f60c4, 0x5e5c9ec2, 0x196a2463,
                    0x68fb6faf, 0x3e6c53b5, 0x1339b2eb, 0x3b52ec6f, 0x6dfc511f, 0x9b30952c, 0xcc814544, 0xaf5ebd09,
                    0xbee3d004, 0xde334afd, 0x660f2807, 0x192e4bb3, 0xc0cba857, 0x45c8740f, 0xd20b5f39, 0xb9d3fbdb,
                    0x5579c0bd, 0x1a60320a, 0xd6a100c6, 0x402c7279, 0x679f25fe, 0xfb1fa3cc, 0x8ea5e9f8, 0xdb3222f8,
                    0x3c7516df, 0xfd616b15, 0x2f501ec8, 0xad0552ab, 0x323db5fa, 0xfd238760, 0x53317b48, 0x3e00df82,
                    0x9e5c57bb, 0xca6f8ca0, 0x1a87562e, 0xdf1769db, 0xd542a8f6, 0x287effc3, 0xac6732c6, 0x8c4f5573,
                    0x695b27b0, 0xbbca58c8, 0xe1ffa35d, 0xb8f011a0, 0x10fa3d98, 0xfd2183b8, 0x4afcb56c, 0x2dd1d35b,
                    0x9a53e479, 0xb6f84565, 0xd28e49bc, 0x4bfb9790, 0xe1ddf2da, 0xa4cb7e33, 0x62fb1341, 0xcee4c6e8,
                    0xef20cada, 0x36774c01, 0xd07e9efe, 0x2bf11fb4, 0x95dbda4d, 0xae909198, 0xeaad8e71, 0x6b93d5a0,
                    0xd08ed1d0, 0xafc725e0, 0x8e3c5b2f, 0x8e7594b7, 0x8ff6e2fb, 0xf2122b64, 0x8888b812, 0x900df01c,
                    0x4fad5ea0, 0x688fc31c, 0xd1cff191, 0xb3a8c1ad, 0x2f2f2218, 0xbe0e1777, 0xea752dfe, 0x8b021fa1,
                    0xe5a0cc0f, 0xb56f74e8, 0x18acf3d6, 0xce89e299, 0xb4a84fe0, 0xfd13e0b7, 0x7cc43b81, 0xd2ada8d9,
                    0x165fa266, 0x80957705, 0x93cc7314, 0x211a1477, 0xe6ad2065, 0x77b5fa86, 0xc75442f5, 0xfb9d35cf,
                    0xebcdaf0c, 0x7b3e89a0, 0xd6411bd3, 0xae1e7e49, 0x00250e2d, 0x2071b35e, 0x226800bb, 0x57b8e0af,
                    0x2464369b, 0xf009b91e, 0x5563911d, 0x59dfa6aa, 0x78c14389, 0xd95a537f, 0x207d5ba2, 0x02e5b9c5,
                    0x83260376, 0x6295cfa9, 0x11c81968, 0x4e734a41, 0xb3472dca, 0x7b14a94a, 0x1b510052, 0x9a532915,
                    0xd60f573f, 0xbc9bc6e4, 0x2b60a476, 0x81e67400, 0x08ba6fb5, 0x571be91f, 0xf296ec6b, 0x2a0dd915,
                    0xb6636521, 0xe7b9f9b6, 0xff34052e, 0xc5855664, 0x53b02d5d, 0xa99f8fa1, 0x08ba4799, 0x6e85076a
                ],
                s1:[
                    0x4b7a70e9, 0xb5b32944, 0xdb75092e, 0xc4192623, 0xad6ea6b0, 0x49a7df7d, 0x9cee60b8, 0x8fedb266,
                    0xecaa8c71, 0x699a17ff, 0x5664526c, 0xc2b19ee1, 0x193602a5, 0x75094c29, 0xa0591340, 0xe4183a3e,
                    0x3f54989a, 0x5b429d65, 0x6b8fe4d6, 0x99f73fd6, 0xa1d29c07, 0xefe830f5, 0x4d2d38e6, 0xf0255dc1,
                    0x4cdd2086, 0x8470eb26, 0x6382e9c6, 0x021ecc5e, 0x09686b3f, 0x3ebaefc9, 0x3c971814, 0x6b6a70a1,
                    0x687f3584, 0x52a0e286, 0xb79c5305, 0xaa500737, 0x3e07841c, 0x7fdeae5c, 0x8e7d44ec, 0x5716f2b8,
                    0xb03ada37, 0xf0500c0d, 0xf01c1f04, 0x0200b3ff, 0xae0cf51a, 0x3cb574b2, 0x25837a58, 0xdc0921bd,
                    0xd19113f9, 0x7ca92ff6, 0x94324773, 0x22f54701, 0x3ae5e581, 0x37c2dadc, 0xc8b57634, 0x9af3dda7,
                    0xa9446146, 0x0fd0030e, 0xecc8c73e, 0xa4751e41, 0xe238cd99, 0x3bea0e2f, 0x3280bba1, 0x183eb331,
                    0x4e548b38, 0x4f6db908, 0x6f420d03, 0xf60a04bf, 0x2cb81290, 0x24977c79, 0x5679b072, 0xbcaf89af,
                    0xde9a771f, 0xd9930810, 0xb38bae12, 0xdccf3f2e, 0x5512721f, 0x2e6b7124, 0x501adde6, 0x9f84cd87,
                    0x7a584718, 0x7408da17, 0xbc9f9abc, 0xe94b7d8c, 0xec7aec3a, 0xdb851dfa, 0x63094366, 0xc464c3d2,
                    0xef1c1847, 0x3215d908, 0xdd433b37, 0x24c2ba16, 0x12a14d43, 0x2a65c451, 0x50940002, 0x133ae4dd,
                    0x71dff89e, 0x10314e55, 0x81ac77d6, 0x5f11199b, 0x043556f1, 0xd7a3c76b, 0x3c11183b, 0x5924a509,
                    0xf28fe6ed, 0x97f1fbfa, 0x9ebabf2c, 0x1e153c6e, 0x86e34570, 0xeae96fb1, 0x860e5e0a, 0x5a3e2ab3,
                    0x771fe71c, 0x4e3d06fa, 0x2965dcb9, 0x99e71d0f, 0x803e89d6, 0x5266c825, 0x2e4cc978, 0x9c10b36a,
                    0xc6150eba, 0x94e2ea78, 0xa5fc3c53, 0x1e0a2df4, 0xf2f74ea7, 0x361d2b3d, 0x1939260f, 0x19c27960,
                    0x5223a708, 0xf71312b6, 0xebadfe6e, 0xeac31f66, 0xe3bc4595, 0xa67bc883, 0xb17f37d1, 0x018cff28,
                    0xc332ddef, 0xbe6c5aa5, 0x65582185, 0x68ab9802, 0xeecea50f, 0xdb2f953b, 0x2aef7dad, 0x5b6e2f84,
                    0x1521b628, 0x29076170, 0xecdd4775, 0x619f1510, 0x13cca830, 0xeb61bd96, 0x0334fe1e, 0xaa0363cf,
                    0xb5735c90, 0x4c70a239, 0xd59e9e0b, 0xcbaade14, 0xeecc86bc, 0x60622ca7, 0x9cab5cab, 0xb2f3846e,
                    0x648b1eaf, 0x19bdf0ca, 0xa02369b9, 0x655abb50, 0x40685a32, 0x3c2ab4b3, 0x319ee9d5, 0xc021b8f7,
                    0x9b540b19, 0x875fa099, 0x95f7997e, 0x623d7da8, 0xf837889a, 0x97e32d77, 0x11ed935f, 0x16681281,
                    0x0e358829, 0xc7e61fd6, 0x96dedfa1, 0x7858ba99, 0x57f584a5, 0x1b227263, 0x9b83c3ff, 0x1ac24696,
                    0xcdb30aeb, 0x532e3054, 0x8fd948e4, 0x6dbc3128, 0x58ebf2ef, 0x34c6ffea, 0xfe28ed61, 0xee7c3c73,
                    0x5d4a14d9, 0xe864b7e3, 0x42105d14, 0x203e13e0, 0x45eee2b6, 0xa3aaabea, 0xdb6c4f15, 0xfacb4fd0,
                    0xc742f442, 0xef6abbb5, 0x654f3b1d, 0x41cd2105, 0xd81e799e, 0x86854dc7, 0xe44b476a, 0x3d816250,
                    0xcf62a1f2, 0x5b8d2646, 0xfc8883a0, 0xc1c7b6a3, 0x7f1524c3, 0x69cb7492, 0x47848a0b, 0x5692b285,
                    0x095bbf00, 0xad19489d, 0x1462b174, 0x23820e00, 0x58428d2a, 0x0c55f5ea, 0x1dadf43e, 0x233f7061,
                    0x3372f092, 0x8d937e41, 0xd65fecf1, 0x6c223bdb, 0x7cde3759, 0xcbee7460, 0x4085f2a7, 0xce77326e,
                    0xa6078084, 0x19f8509e, 0xe8efd855, 0x61d99735, 0xa969a7aa, 0xc50c06c2, 0x5a04abfc, 0x800bcadc,
                    0x9e447a2e, 0xc3453484, 0xfdd56705, 0x0e1e9ec9, 0xdb73dbd3, 0x105588cd, 0x675fda79, 0xe3674340,
                    0xc5c43465, 0x713e38d8, 0x3d28f89e, 0xf16dff20, 0x153e21e7, 0x8fb03d4a, 0xe6e39f2b, 0xdb83adf7
                ],
                s2:[
                    0xe93d5a68, 0x948140f7, 0xf64c261c, 0x94692934, 0x411520f7, 0x7602d4f7, 0xbcf46b2e, 0xd4a20068,
                    0xd4082471, 0x3320f46a, 0x43b7d4b7, 0x500061af, 0x1e39f62e, 0x97244546, 0x14214f74, 0xbf8b8840,
                    0x4d95fc1d, 0x96b591af, 0x70f4ddd3, 0x66a02f45, 0xbfbc09ec, 0x03bd9785, 0x7fac6dd0, 0x31cb8504,
                    0x96eb27b3, 0x55fd3941, 0xda2547e6, 0xabca0a9a, 0x28507825, 0x530429f4, 0x0a2c86da, 0xe9b66dfb,
                    0x68dc1462, 0xd7486900, 0x680ec0a4, 0x27a18dee, 0x4f3ffea2, 0xe887ad8c, 0xb58ce006, 0x7af4d6b6,
                    0xaace1e7c, 0xd3375fec, 0xce78a399, 0x406b2a42, 0x20fe9e35, 0xd9f385b9, 0xee39d7ab, 0x3b124e8b,
                    0x1dc9faf7, 0x4b6d1856, 0x26a36631, 0xeae397b2, 0x3a6efa74, 0xdd5b4332, 0x6841e7f7, 0xca7820fb,
                    0xfb0af54e, 0xd8feb397, 0x454056ac, 0xba489527, 0x55533a3a, 0x20838d87, 0xfe6ba9b7, 0xd096954b,
                    0x55a867bc, 0xa1159a58, 0xcca92963, 0x99e1db33, 0xa62a4a56, 0x3f3125f9, 0x5ef47e1c, 0x9029317c,
                    0xfdf8e802, 0x04272f70, 0x80bb155c, 0x05282ce3, 0x95c11548, 0xe4c66d22, 0x48c1133f, 0xc70f86dc,
                    0x07f9c9ee, 0x41041f0f, 0x404779a4, 0x5d886e17, 0x325f51eb, 0xd59bc0d1, 0xf2bcc18f, 0x41113564,
                    0x257b7834, 0x602a9c60, 0xdff8e8a3, 0x1f636c1b, 0x0e12b4c2, 0x02e1329e, 0xaf664fd1, 0xcad18115,
                    0x6b2395e0, 0x333e92e1, 0x3b240b62, 0xeebeb922, 0x85b2a20e, 0xe6ba0d99, 0xde720c8c, 0x2da2f728,
                    0xd0127845, 0x95b794fd, 0x647d0862, 0xe7ccf5f0, 0x5449a36f, 0x877d48fa, 0xc39dfd27, 0xf33e8d1e,
                    0x0a476341, 0x992eff74, 0x3a6f6eab, 0xf4f8fd37, 0xa812dc60, 0xa1ebddf8, 0x991be14c, 0xdb6e6b0d,
                    0xc67b5510, 0x6d672c37, 0x2765d43b, 0xdcd0e804, 0xf1290dc7, 0xcc00ffa3, 0xb5390f92, 0x690fed0b,
                    0x667b9ffb, 0xcedb7d9c, 0xa091cf0b, 0xd9155ea3, 0xbb132f88, 0x515bad24, 0x7b9479bf, 0x763bd6eb,
                    0x37392eb3, 0xcc115979, 0x8026e297, 0xf42e312d, 0x6842ada7, 0xc66a2b3b, 0x12754ccc, 0x782ef11c,
                    0x6a124237, 0xb79251e7, 0x06a1bbe6, 0x4bfb6350, 0x1a6b1018, 0x11caedfa, 0x3d25bdd8, 0xe2e1c3c9,
                    0x44421659, 0x0a121386, 0xd90cec6e, 0xd5abea2a, 0x64af674e, 0xda86a85f, 0xbebfe988, 0x64e4c3fe,
                    0x9dbc8057, 0xf0f7c086, 0x60787bf8, 0x6003604d, 0xd1fd8346, 0xf6381fb0, 0x7745ae04, 0xd736fccc,
                    0x83426b33, 0xf01eab71, 0xb0804187, 0x3c005e5f, 0x77a057be, 0xbde8ae24, 0x55464299, 0xbf582e61,
                    0x4e58f48f, 0xf2ddfda2, 0xf474ef38, 0x8789bdc2, 0x5366f9c3, 0xc8b38e74, 0xb475f255, 0x46fcd9b9,
                    0x7aeb2661, 0x8b1ddf84, 0x846a0e79, 0x915f95e2, 0x466e598e, 0x20b45770, 0x8cd55591, 0xc902de4c,
                    0xb90bace1, 0xbb8205d0, 0x11a86248, 0x7574a99e, 0xb77f19b6, 0xe0a9dc09, 0x662d09a1, 0xc4324633,
                    0xe85a1f02, 0x09f0be8c, 0x4a99a025, 0x1d6efe10, 0x1ab93d1d, 0x0ba5a4df, 0xa186f20f, 0x2868f169,
                    0xdcb7da83, 0x573906fe, 0xa1e2ce9b, 0x4fcd7f52, 0x50115e01, 0xa70683fa, 0xa002b5c4, 0x0de6d027,
                    0x9af88c27, 0x773f8641, 0xc3604c06, 0x61a806b5, 0xf0177a28, 0xc0f586e0, 0x006058aa, 0x30dc7d62,
                    0x11e69ed7, 0x2338ea63, 0x53c2dd94, 0xc2c21634, 0xbbcbee56, 0x90bcb6de, 0xebfc7da1, 0xce591d76,
                    0x6f05e409, 0x4b7c0188, 0x39720a3d, 0x7c927c24, 0x86e3725f, 0x724d9db9, 0x1ac15bb4, 0xd39eb8fc,
                    0xed545578, 0x08fca5b5, 0xd83d7cd3, 0x4dad0fc4, 0x1e50ef5e, 0xb161e6f8, 0xa28514d9, 0x6c51133c,
                    0x6fd5c7e7, 0x56e14ec4, 0x362abfce, 0xddc6c837, 0xd79a3234, 0x92638212, 0x670efa8e, 0x406000e0
                ],
                s3:[
                    0x3a39ce37, 0xd3faf5cf, 0xabc27737, 0x5ac52d1b, 0x5cb0679e, 0x4fa33742, 0xd3822740, 0x99bc9bbe,
                    0xd5118e9d, 0xbf0f7315, 0xd62d1c7e, 0xc700c47b, 0xb78c1b6b, 0x21a19045, 0xb26eb1be, 0x6a366eb4,
                    0x5748ab2f, 0xbc946e79, 0xc6a376d2, 0x6549c2c8, 0x530ff8ee, 0x468dde7d, 0xd5730a1d, 0x4cd04dc6,
                    0x2939bbdb, 0xa9ba4650, 0xac9526e8, 0xbe5ee304, 0xa1fad5f0, 0x6a2d519a, 0x63ef8ce2, 0x9a86ee22,
                    0xc089c2b8, 0x43242ef6, 0xa51e03aa, 0x9cf2d0a4, 0x83c061ba, 0x9be96a4d, 0x8fe51550, 0xba645bd6,
                    0x2826a2f9, 0xa73a3ae1, 0x4ba99586, 0xef5562e9, 0xc72fefd3, 0xf752f7da, 0x3f046f69, 0x77fa0a59,
                    0x80e4a915, 0x87b08601, 0x9b09e6ad, 0x3b3ee593, 0xe990fd5a, 0x9e34d797, 0x2cf0b7d9, 0x022b8b51,
                    0x96d5ac3a, 0x017da67d, 0xd1cf3ed6, 0x7c7d2d28, 0x1f9f25cf, 0xadf2b89b, 0x5ad6b472, 0x5a88f54c,
                    0xe029ac71, 0xe019a5e6, 0x47b0acfd, 0xed93fa9b, 0xe8d3c48d, 0x283b57cc, 0xf8d56629, 0x79132e28,
                    0x785f0191, 0xed756055, 0xf7960e44, 0xe3d35e8c, 0x15056dd4, 0x88f46dba, 0x03a16125, 0x0564f0bd,
                    0xc3eb9e15, 0x3c9057a2, 0x97271aec, 0xa93a072a, 0x1b3f6d9b, 0x1e6321f5, 0xf59c66fb, 0x26dcf319,
                    0x7533d928, 0xb155fdf5, 0x03563482, 0x8aba3cbb, 0x28517711, 0xc20ad9f8, 0xabcc5167, 0xccad925f,
                    0x4de81751, 0x3830dc8e, 0x379d5862, 0x9320f991, 0xea7a90c2, 0xfb3e7bce, 0x5121ce64, 0x774fbe32,
                    0xa8b6e37e, 0xc3293d46, 0x48de5369, 0x6413e680, 0xa2ae0810, 0xdd6db224, 0x69852dfd, 0x09072166,
                    0xb39a460a, 0x6445c0dd, 0x586cdecf, 0x1c20c8ae, 0x5bbef7dd, 0x1b588d40, 0xccd2017f, 0x6bb4e3bb,
                    0xdda26a7e, 0x3a59ff45, 0x3e350a44, 0xbcb4cdd5, 0x72eacea8, 0xfa6484bb, 0x8d6612ae, 0xbf3c6f47,
                    0xd29be463, 0x542f5d9e, 0xaec2771b, 0xf64e6370, 0x740e0d8d, 0xe75b1357, 0xf8721671, 0xaf537d5d,
                    0x4040cb08, 0x4eb4e2cc, 0x34d2466a, 0x0115af84, 0xe1b00428, 0x95983a1d, 0x06b89fb4, 0xce6ea048,
                    0x6f3f3b82, 0x3520ab82, 0x011a1d4b, 0x277227f8, 0x611560b1, 0xe7933fdc, 0xbb3a792b, 0x344525bd,
                    0xa08839e1, 0x51ce794b, 0x2f32c9b7, 0xa01fbac9, 0xe01cc87e, 0xbcc7d1f6, 0xcf0111c3, 0xa1e8aac7,
                    0x1a908749, 0xd44fbd9a, 0xd0dadecb, 0xd50ada38, 0x0339c32a, 0xc6913667, 0x8df9317c, 0xe0b12b4f,
                    0xf79e59b7, 0x43f5bb3a, 0xf2d519ff, 0x27d9459c, 0xbf97222c, 0x15e6fc2a, 0x0f91fc71, 0x9b941525,
                    0xfae59361, 0xceb69ceb, 0xc2a86459, 0x12baa8d1, 0xb6c1075e, 0xe3056a0c, 0x10d25065, 0xcb03a442,
                    0xe0ec6e0e, 0x1698db3b, 0x4c98a0be, 0x3278e964, 0x9f1f9532, 0xe0d392df, 0xd3a0342b, 0x8971f21e,
                    0x1b0a7441, 0x4ba3348c, 0xc5be7120, 0xc37632d8, 0xdf359f8d, 0x9b992f2e, 0xe60b6f47, 0x0fe3f11d,
                    0xe54cda54, 0x1edad891, 0xce6279cf, 0xcd3e7e6f, 0x1618b166, 0xfd2c1d05, 0x848fd2c5, 0xf6fb2299,
                    0xf523f357, 0xa6327623, 0x93a83531, 0x56cccd02, 0xacf08162, 0x5a75ebb5, 0x6e163697, 0x88d273cc,
                    0xde966292, 0x81b949d0, 0x4c50901b, 0x71c65614, 0xe6c6c7bd, 0x327a140a, 0x45e1d006, 0xc3f27b9a,
                    0xc9aa53fd, 0x62a80f00, 0xbb25bfe2, 0x35bdd2f6, 0x71126905, 0xb2040222, 0xb6cbcf7c, 0xcd769c2b,
                    0x53113ec0, 0x1640e3d3, 0x38abbd60, 0x2547adf0, 0xba38209c, 0xf746ce76, 0x77afa1c5, 0x20756060,
                    0x85cbfe4e, 0x8ae88dd8, 0x7aaaf9b0, 0x4cf9aa7e, 0x1948c25c, 0x02fb8a8c, 0x01c36ae4, 0xd6ebe1f9,
                    0x90d4f869, 0xa65cdea0, 0x3f09252d, 0xc208e69f, 0xb74e6132, 0xce77e25b, 0x578fdfe3, 0x3ac372e6
                ]
            }

            function add(x,y){
                return (((x>>0x10)+(y>>0x10)+(((x&0xffff)+(y&0xffff))>>0x10))<<0x10)|(((x&0xffff)+(y&0xffff))&0xffff);
            }

            function xor(x,y){
                return (((x>>0x10)^(y>>0x10))<<0x10)|(((x&0xffff)^(y&0xffff))&0xffff);
            }

            function $(v, box){
                var d=box.s3[v&0xff]; v>>=8;
                var c=box.s2[v&0xff]; v>>=8;
                var b=box.s1[v&0xff]; v>>=8;
                var a=box.s0[v&0xff];

                var r = (((a>>0x10)+(b>>0x10)+(((a&0xffff)+(b&0xffff))>>0x10))<<0x10)|(((a&0xffff)+(b&0xffff))&0xffff);
                r = (((r>>0x10)^(c>>0x10))<<0x10)|(((r&0xffff)^(c&0xffff))&0xffff);
                return (((r>>0x10)+(d>>0x10)+(((r&0xffff)+(d&0xffff))>>0x10))<<0x10)|(((r&0xffff)+(d&0xffff))&0xffff);
            }

            function eb(o, box){
                var l=o.left;
                var r=o.right;
                l=xor(l,box.p[0]);
                r=xor(r,xor($(l,box),box.p[1]));
                l=xor(l,xor($(r,box),box.p[2]));
                r=xor(r,xor($(l,box),box.p[3]));
                l=xor(l,xor($(r,box),box.p[4]));
                r=xor(r,xor($(l,box),box.p[5]));
                l=xor(l,xor($(r,box),box.p[6]));
                r=xor(r,xor($(l,box),box.p[7]));
                l=xor(l,xor($(r,box),box.p[8]));
                r=xor(r,xor($(l,box),box.p[9]));
                l=xor(l,xor($(r,box),box.p[10]));
                r=xor(r,xor($(l,box),box.p[11]));
                l=xor(l,xor($(r,box),box.p[12]));
                r=xor(r,xor($(l,box),box.p[13]));
                l=xor(l,xor($(r,box),box.p[14]));
                r=xor(r,xor($(l,box),box.p[15]));
                l=xor(l,xor($(r,box),box.p[16]));
                o.right=l;
                o.left=xor(r,box.p[17]);
            }

            function db(o, box){
                var l=o.left;
                var r=o.right;
                l=xor(l,box.p[17]);
                r=xor(r,xor($(l,box),box.p[16]));
                l=xor(l,xor($(r,box),box.p[15]));
                r=xor(r,xor($(l,box),box.p[14]));
                l=xor(l,xor($(r,box),box.p[13]));
                r=xor(r,xor($(l,box),box.p[12]));
                l=xor(l,xor($(r,box),box.p[11]));
                r=xor(r,xor($(l,box),box.p[10]));
                l=xor(l,xor($(r,box),box.p[9]));
                r=xor(r,xor($(l,box),box.p[8]));
                l=xor(l,xor($(r,box),box.p[7]));
                r=xor(r,xor($(l,box),box.p[6]));
                l=xor(l,xor($(r,box),box.p[5]));
                r=xor(r,xor($(l,box),box.p[4]));
                l=xor(l,xor($(r,box),box.p[3]));
                r=xor(r,xor($(l,box),box.p[2]));
                l=xor(l,xor($(r,box),box.p[1]));
                o.right=l;
                o.left=xor(r,box.p[0]);
            }

            function init(key){
                var k=key, pos=0, data=0, res={ left:0, right:0 }, i, j, l;
                var box = {
                    p: arrayMapWithHoles(boxes.p.slice(0), function(item){
                        var l=k.length, j;
                        for(j=0; j<4; j++){ data=(data*POW8)|k[pos++ % l]; }
                        return (((item>>0x10)^(data>>0x10))<<0x10)|(((item&0xffff)^(data&0xffff))&0xffff);
                    }),
                    s0:boxes.s0.slice(0),
                    s1:boxes.s1.slice(0),
                    s2:boxes.s2.slice(0),
                    s3:boxes.s3.slice(0)
                };
                for(i=0, l=box.p.length; i<l;){
                    eb(res, box);
                    box.p[i++]=res.left, box.p[i++]=res.right;
                }
                for(i=0; i<4; i++){
                    for(j=0, l=box["s"+i].length; j<l;){
                        eb(res, box);
                        box["s"+i][j++]=res.left, box["s"+i][j++]=res.right;
                    }
                }
                return box;
            }

            this.hexStringToByteArray=function(hexString) {
                if (hexString.length % 2 == 1) {
                    hexString += "0";
                }
                for (var bytes = [], index = 0; index < hexString.length; index += 2) {
                    bytes.push(parseInt(hexString.substr(index, 2), 16));
                }
                return bytes;
            }

            this.getIV=function(){
                return base64.encode(iv);
            };

            this.setIV=function(data){
                var ba=base64.decode(data);
                iv={};
                iv.left=ba[0]*POW24|ba[1]*POW16|ba[2]*POW8|ba[3];
                iv.right=ba[4]*POW24|ba[5]*POW16|ba[6]*POW8|ba[7];
            };

            this.encryptString = function(plaintext, key){
                var bx = init(this.hexStringToByteArray(key)), padding = 8-(plaintext.length&7);
                for (var i=0; i<padding; i++){ plaintext+=String.fromCharCode(padding); }
                var cipher=[], count=plaintext.length >> 3, pos=0, o={};
                for(var i=0; i<count; i++){
                    o.left=plaintext.charCodeAt(pos)*POW24
                        |plaintext.charCodeAt(pos+1)*POW16
                        |plaintext.charCodeAt(pos+2)*POW8
                        |plaintext.charCodeAt(pos+3);
                    o.right=plaintext.charCodeAt(pos+4)*POW24
                        |plaintext.charCodeAt(pos+5)*POW16
                        |plaintext.charCodeAt(pos+6)*POW8
                        |plaintext.charCodeAt(pos+7);
                    eb(o, bx);
                    cipher.push((o.left>>24)&0xff);
                    cipher.push((o.left>>16)&0xff);
                    cipher.push((o.left>>8)&0xff);
                    cipher.push(o.left&0xff);
                    cipher.push((o.right>>24)&0xff);
                    cipher.push((o.right>>16)&0xff);
                    cipher.push((o.right>>8)&0xff);
                    cipher.push(o.right&0xff);
                    pos+=8;
                }
                return stringTranslate(base64.encode(cipher), ["+", "/", "="], ["-", "_", "~"]);
            };

            this.decryptString = function(ciphertext, key){
                var bx = init(this.hexStringToByteArray(key));
                var pt=[];
                var c=base64.decode(stringTranslate(ciphertext, ["-", "_", "~"], ["+", "/", "="]));
                var count=c.length >> 3, pos=0, o={};
                for(var i=0; i<count; i++){
                    o.left=c[pos]*POW24|c[pos+1]*POW16|c[pos+2]*POW8|c[pos+3];
                    o.right=c[pos+4]*POW24|c[pos+5]*POW16|c[pos+6]*POW8|c[pos+7];
                    db(o, bx);
                    pt.push((o.left>>24)&0xff);
                    pt.push((o.left>>16)&0xff);
                    pt.push((o.left>>8)&0xff);
                    pt.push(o.left&0xff);
                    pt.push((o.right>>24)&0xff);
                    pt.push((o.right>>16)&0xff);
                    pt.push((o.right>>8)&0xff);
                    pt.push(o.right&0xff);
                    pos+=8;
                }
                if(pt[pt.length-1]==pt[pt.length-2]||pt[pt.length-1]==0x01){
                    var n=pt[pt.length-1];
                    pt.splice(pt.length-n, n);
                }
                return arrayMapWithHoles(pt, function(item){
                    return String.fromCharCode(item);
                }).join("");
            };
            this.setIV("0000000000000000");
        }();
        return crypto.blowfish;
    })();

    /**
     * Given a user email, generate the Gravatar URL for the image.
     * @param {string} email An email address. This is not validated.
     * @param {integer} size THe size of the avatar image to return, width and height are equal.
     * @returns {string} - A URL.
     */
    commonUtilities.getGravatarURL = function (email, size) {
        var size = size || 80;
        return "https://www.gravatar.com/avatar/" + commonUtilities.md5(email) + ".jpg?s=" + size;
    };

    /* ----------------------------------------------------------------------------------
     * Setup for AMD, node, or standalone reference the commonUtilities object.
     * ----------------------------------------------------------------------------------*/

    if (typeof define === "function" && define.amd) {
        define(function () { return commonUtilities; });
    } else if (typeof exports === "object") {
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
