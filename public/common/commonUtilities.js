/**  CommonUtilities.js
 * 
 *   A static object of static utility functions for handling common problems
 *   found in JavaScript and web development. I find on every JS project I work
 *   on I need most of these functions, so I pulled them all together in one place.
 * 
 *   This module includes many function utilities for data transformations such as
 *   base64, url and query string processing, data validation, and cookie handling.
 *
 */
(function CommonUtilities () {

    'use strict';
    var commonUtilities = {
        version: '1.1.3'
    },
    _base64KeyStr = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",
    _testNumber = 0;

  
    /** 
     * Return the provided object as a string in key: value; format.
     *
     * @param {object} The object to convert to a string representation.
     * @return {string} The object converted to a string representation.
     */
    commonUtilities.objectToString = function (obj) {
        var result ;
        if (obj) {
            result = '';
            for (var prop in obj) {
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
     * Return the provided object as a string in key: value; format. This version handles
     * functions.
     *
     * @param {object} The object to convert to a string representation.
     * @return {string} The object converted to a string representation.
     */
    commonUtilities.objectStringify = function (object) {
        var subObjects = [], // An array of sub-objects that will later be joined into a string.
            property;
            
        if (object == undefined) {
            return String(object);
            
        } else if (typeof(object) == "function") {
            subObjects.push(object.toString());

        } else if (typeof(object) == "object") {
            // is object (or array):
            //    Both arrays and objects seem to return "object" when typeof(obj)
            //    is applied to them. So instead we check if they have the property
            //    join, a function of the array prototype. Unless the object actually
            //    defines its own join property!
            if (object.join == undefined) {
                for (property in object) {
                    if (object.hasOwnProperty(property)) {
                        subObjects.push(property + ": " + this.objectStringify(object[property]));
                    }
                };
                return "{" + subObjects.join(", ") + "}";
            } else {
                for (property in object) {
                    subObjects.push(this.objectStringify(object[property]));
                }
                return "[" + subObjects.join(", ") + "]";
            s}
        } else {
            // all other value types can be represented with JSON.stringify
            subObjects.push(JSON.stringify(object))
        }
        return subObjects.join(", ");
    }
    
    /** 
     * Return the current document query string as an object.
     *
     * @param {string} An optional query string to parse as the query string. If not
     * provided window.location.search will be used.
     * @return {object} The query string converted to an object of key/value pairs.
     */
    commonUtilities.queryStringToObject = function (urlParamterString) {
        var match,
            search = /([^&=]+)=?([^&]*)/g,
            decode = function (s) { return decodeURIComponent(s.replace(/\+/g, " ")); },
            result = {};
        if ( ! urlParamterString) {
            urlParamterString = window.location.search.substring(1);
        }
        while (match = search.exec(urlParamterString)) {
            result[decode(match[1])] = decode(match[2]);
        }
        return result;
    }
    
    /** 
     * Given a path make sure it represents a full path with a leading and trailing /.
     *
     * @param {string} URI path to check.
     * @return {string} Full URI path.
     */
    commonUtilities.makeFullPath(path) {
        if (path) {
            if (path[path.length - 1] !== '/') {
                path += '/'
            }
            if (path[0] !== '/') {
                path = '/' + path
            }
        } else {
            path = '/';
        }
        return path
    }
    
    /** 
     * Append a folder or file name to the end of an existing path string.
     *
     * @param {string} URI path to append to.
     * @param {string} folder or file to append.
     * @return {string} Full URI path.
     */
    commonUtilities.appendFileToPath(path, file) {
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
        return path
    }
    
    /**
     * Determine if the current invokation environment is a mobile device.
     * TODO: Really would rather use modernizr.js as you really do not want isMobile(), you want isTouchDevice()
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

    /* 
     * A very basic function performance tester. Will track the time it takes to run the
     *        function for the specified number of iterations.
     * @param {function} a function to test. This function take no parameters. If you 
     *        require parameters wrap into a function that takes no parameters.
     * @param {string} any id you want to assign to the test. Not used, but returned.
     * @param {int} number of times to call this function.
     * @return {object} test results object including test number, test function id, duration,
     *         duration units, and total iterations.
     */
    commonUtilities.performanceTest = function (testFunction, testId, totalIterations) {
        var start,
            duration,
            i,
            results;
        
        _testNumber ++;
        start = performance.now();
        for (i = 0; i < totalIterations; i ++) {
            testFunction();
        }
        duration = performance.now() - start;
        results = {
            testNumber: _testNumber,
            testFunction: testId,
            duration: duration,
            durationUnits: 'ms',
            totalIterations: i
        };
        return results;
    };

    /* ----------------------------------------------------------------------------------
     * Various conversion utilities - UTF-8, Base 64
     *
     */

    /*
     * Encode a Unicode string in UTF-8 character encoding.
     *
     * @param {string} string in Unicode to convert to UTF-8.
     * @return {string} UTF-8 encoded input string.
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

    /*
     * Decode a UTF-8 encoded string into a Unicode character coding format.
     *
     * @param {string} string in UTF-8 to convert to Unicode.
     * @return {string} Unicode representation of input string.
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
    
    /*
     * Convert an image located at the URL specified into its Base 64 representation.
     * Because the image is loaded asynchronously over the network a callback function
     * will be called once the image is loaded and encoded.
     *
     * @param {string} URL to an image.
     * @param {function} Called when image is loaded. This function takes one parameter,
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

    /*
     * Encode a string into its base 64 representation.
     *
     * @param {string} string to encode in base 64.
     * @return {string} encoded string.
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

    /*
     * Convert a base 64 encoded string to its UTF-8 character coding.
     * 
     * @param {string} string in base 64 to convert to UTF-8.
     * @return {string} UTF-8 string.
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

    /* ----------------------------------------------------------------------------------
     * Cookie handling functions
     *
     */
     
    /*
     * Return the contents fo the cookie indexed by the specified key.
     *
     * @param {string} Indicate which cookie to get.
     * @return {string} Contents of cookie stored with key.
     */
    commonUtilities.cookieGet = function (key) {
        if (key) {
            return decodeURIComponent(document.cookie.replace(new RegExp("(?:(?:^|.*;)\\s*" + encodeURIComponent(key).replace(/[\-\.\+\*]/g, "\\$&") + "\\s*\\=\\s*([^;]*).*$)|^.*$"), "$1")) || null;
        } else {
            return null;
        } 
    };
  
    /*
     * Set a cookie indexed by the specified key.
     *
     * @param key {string} Indicate which cookie to set.
     * @param value {string} Value to store under key.
     * @param expiration {var} When the cookie should expire.
     * @param path {string} Cookie URL path.
     * @param domain {string} Cookie domain.
     * @param isSecure {bool} Set cookie secure flag.
     * @return {bool} true if set, false if error.
     */
    commonUtilities.cookieSet = function (key, value, expiration, path, domain, isSecure) {
        if ( ! key || /^(?:expires|max\-age|path|domain|secure)$/i.test(key)) {
            return false;
        } else {
            var expires = "";
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
  
    /*
     * Remove a cookie indexed by the specified key.
     *
     * @param key {string} Indicate which cookie to remove.
     * @param path {string} Cookie URL path.
     * @param domain {string} Cookie domain.
     * @return {bool} true if removed, false if doesn't exist.
     */
    commonUtilities.cookieRemove = function (key, path, domain) {
        if (commonUtilities.cookieExists(key)) {
            document.cookie = encodeURIComponent(key) + "=; expires=Thu, 01 Jan 1970 00:00:00 GMT" + (domain ? "; domain=" + domain : "") + (path ? "; path=" + path : "");
            return true;
        } else {
            return false;
        }
    };
  
    /*
     * Determine if the cookie exists.
     *
     * @param key {string} Key to test if exists.
     * @return {bool} true if exists, false if doesn't exist.
     */
    commonUtilities.cookieExists = function (key) {
        if (key) {
            return (new RegExp("(?:^|;\\s*)" + encodeURIComponent(key).replace(/[\-\.\+\*]/g, "\\$&") + "\\s*\\=")).test(document.cookie);
        } else {
            return false;
        }
    };
  
    /*
     * Return an array of all cookie keys.
     *
     * @return {array} Array of all stored cookie keys.
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
     * Very basic social network sharing utilities
     *
     */
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
    
    /* ----------------------------------------------------------------------------------
     * Setup for AMD or standalone reference the commonUtilities object.
     */

    if (typeof define === 'function' && define.amd) {
        define(function () { return commonUtilities; });
    } else if (typeof exports === 'object') {
        module.exports = commonUtilities;
    } else {
        var existingUtilityFunctions = window.commonUtilities;
        commonUtilities.existingUtilityFunctions = function () {
            window.commonUtilities = existingUtilityFunctions;
            return this;
        };
        window.commonUtilities = commonUtilities;
    }
})();
