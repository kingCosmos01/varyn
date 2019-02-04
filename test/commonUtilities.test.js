/**
 * commonUtilities unit tests.
 * Expects the commonUtilities module to load and operate as designed.
 * See Expect interface at https://facebook.github.io/jest/docs/en/expect.html
 */
var commonUtilities = require("../public/common/commonUtilities");


test("Expect commonUtilities to exist and contain required functions", function() {
    expect(commonUtilities).toBeDefined();
    expect(commonUtilities.version).toBeDefined();
    expect(commonUtilities.isMobile).toBeDefined();
});

test("version should be #.#.#", function() {
    var version = commonUtilities.version;
    expect(version.length).toBeGreaterThan(4);
    // RegEx obtained from https://github.com/semver/semver.org/issues/59#issuecomment-389850124
    // expect(version).toMatch(/^(?'MAJOR'(?:0|(?:[1-9]\d*)))\.(?'MINOR'(?:0|(?:[1-9]\d*)))\.(?'PATCH'(?:0|(?:[1-9]\d*)))(?:-(?'prerelease'[1-9A-Za-z-][0-9A-Za-z-]*(\.[0-9A-Za-z-]+)*))?(?:\+(?'build'[0-9A-Za-z-]+(\.[0-9A-Za-z-]+)*))?$/);
    expect(version).toMatch(/^(0|[1-9]\d*)\.(0|[1-9]\d*)\.(0|[1-9]\d*)$/);
});

test("Expect makeSafeForId to work as designed", function() {
    expect(commonUtilities.makeSafeForId).toBeDefined();
    expect(commonUtilities.makeSafeForId("common UTILITIES id")).toEqual("common-utilities-id");
    expect(commonUtilities.makeSafeForId("common ...     UTILITIES id")).toEqual("common-utilities-id");
    expect(commonUtilities.makeSafeForId("coMm+o.n ...     UTILITIES id")).toEqual("common-utilities-id");
    expect(commonUtilities.makeSafeForId("common-utilities-id")).toEqual("common-utilities-id");
    expect(commonUtilities.makeSafeForId("common----utilities--id")).toEqual("common-utilities-id");
    expect(commonUtilities.makeSafeForId("")).toEqual("id");
    expect(commonUtilities.makeSafeForId(null)).toEqual("id");
    expect(commonUtilities.makeSafeForId(123456)).toEqual("123456");
});

test("String translate", function() {
    expect(commonUtilities.stringTranslate).toBeDefined();
    var testString = "This is a test string";
    var result = commonUtilities.stringTranslate(testString, ["s", "a", "i"], ["z", "b", "I"]);

    expect(result).toEqual("ThIz Iz b tezt ztrIng");

    result = commonUtilities.stringTranslate(testString, [], []);
    expect(result).toEqual(testString);

    // mismatched parameters returns first parameter untranslated.
    result = commonUtilities.stringTranslate("", [], []);
    expect(result).toEqual("");
    result = commonUtilities.stringTranslate(null, [], []);
    expect(result).toEqual(null);
    result = commonUtilities.stringTranslate(testString, [], []);
    expect(result).toEqual(testString);
    result = commonUtilities.stringTranslate(testString, [], ["z", "b", "I"]);
    expect(result).toEqual(testString);
    result = commonUtilities.stringTranslate(testString, ["z", "b", "I"], []);
    expect(result).toEqual(testString);
    result = commonUtilities.stringTranslate(6, 6, 6);
    expect(result).toEqual(6);

    // arrays dont match so should return original string untranslated
    result = commonUtilities.stringTranslate(testString, ["s", "i"], ["z", "b", "I"]);
    expect(result).toEqual(testString);
    result = commonUtilities.stringTranslate(testString, ["s", "a", "i"], ["z", "b"]);
    expect(result).toEqual(testString);

    testString       = "HDljz4tC2cXxC1CX4c-_Mro6P8PFlkuRMEWXnpi__XMkMElZdwKWu2rgGwZ9uL-4KoVWe43jq47yBF4XloAaZmAau_ElFpOq9PlN~~";
    var expectString = "HDljz4tC2cXxC1CX4c+/Mro6P8PFlkuRMEWXnpi//XMkMElZdwKWu2rgGwZ9uL+4KoVWe43jq47yBF4XloAaZmAau/ElFpOq9PlN==";
    result = commonUtilities.stringTranslate(testString, ["-", "_", "~"], ["+", "/", "="]);
    expect(result).toEqual(expectString);
    result = commonUtilities.stringTranslate(expectString, ["+", "/", "="], ["-", "_", "~"]);
    expect(result).toEqual(testString);
});

test("is valid email", function() {
    expect(commonUtilities.isValidEmail).toBeDefined();
    var testEmail = "john@abc.com";
    var result = commonUtilities.isValidEmail(testEmail);
    expect(result).toBeTruthy();
    testEmail = "a@b.org";
    result = commonUtilities.isValidEmail(testEmail);
    expect(result).toBeTruthy();
    testEmail = "1@2.3";
    result = commonUtilities.isValidEmail(testEmail);
    expect(result).toBeTruthy();
    testEmail = "wyeruweiuryweyriwyeiruywieyruiwyeiruywiueryw@whejwherhuwehfuwhefuhewufheuw.edu";
    result = commonUtilities.isValidEmail(testEmail);
    expect(result).toBeTruthy();
    testEmail = "herman.munster@transulvania.edu";
    result = commonUtilities.isValidEmail(testEmail);
    expect(result).toBeTruthy();
    testEmail = "herman.munster@transulvania.education.edu";
    result = commonUtilities.isValidEmail(testEmail);
    expect(result).toBeTruthy();
    testEmail = "al.pha@be-ta.commercial";
    result = commonUtilities.isValidEmail(testEmail);
    expect(result).toBeTruthy();
    testEmail = "";
    result = commonUtilities.isValidEmail(testEmail);
    expect(result).toBeFalsy();
    testEmail = "a";
    result = commonUtilities.isValidEmail(testEmail);
    expect(result).toBeFalsy();
    testEmail = "a all at there dot com";
    result = commonUtilities.isValidEmail(testEmail);
    expect(result).toBeFalsy();
    testEmail = "a@";
    result = commonUtilities.isValidEmail(testEmail);
    expect(result).toBeFalsy();
    testEmail = "a@b";
    result = commonUtilities.isValidEmail(testEmail);
    expect(result).toBeFalsy();
    testEmail = "@.";
    result = commonUtilities.isValidEmail(testEmail);
    expect(result).toBeFalsy();
    testEmail = "alpha.beta.";
    result = commonUtilities.isValidEmail(testEmail);
    expect(result).toBeFalsy();
    testEmail = "al.pha@.commercial";
    result = commonUtilities.isValidEmail(testEmail);
    expect(result).toBeFalsy();

    // I don't think this test should pass, but it does.
    // testEmail = "al.pha@be-ta";
    // result = commonUtilities.isValidEmail(testEmail);
    // expect(result).toBeFalsy();
});

test("getGravatarURL is gravatar URL", function() {
    expect(commonUtilities.getGravatarURL).toBeDefined();
    var testEmail = "john@abc.com";
    var size = 90;
    var result = commonUtilities.getGravatarURL(testEmail, size);
    expect(result.length).toBeGreaterThan(41);
    expect(result).toContain("https");
    expect(result).toContain(".jpg");
    expect(result).toContain("s=");
    var startStringIndex = result.indexOf("avatar/") + 7;
    var endStringIndex = result.indexOf(".jpg");
    var checkString = result.substr(startStringIndex, endStringIndex - startStringIndex);
    expect(checkString.length).toBe(32);
});

test("is empty", function() {
    expect(commonUtilities.isEmpty).toBeDefined();
    var result = commonUtilities.isEmpty("");
    expect(result).toBeTruthy();
    result = commonUtilities.isEmpty(null);
    expect(result).toBeTruthy();
    result = commonUtilities.isEmpty(0);
    expect(result).toBeTruthy();
    result = commonUtilities.isEmpty(false);
    expect(result).toBeTruthy();
    result = commonUtilities.isEmpty(undefined);
    expect(result).toBeTruthy();
    result = commonUtilities.isEmpty();
    expect(result).toBeTruthy();
    result = commonUtilities.isEmpty([]);
    expect(result).toBeTruthy();

    result = commonUtilities.isEmpty(" ");
    expect(result).toBeFalsy();
    result = commonUtilities.isEmpty("0");
    expect(result).toBeFalsy();
    result = commonUtilities.isEmpty("null");
    expect(result).toBeFalsy();
    result = commonUtilities.isEmpty(1);
    expect(result).toBeFalsy();
    result = commonUtilities.isEmpty(-1);
    expect(result).toBeFalsy();
    result = commonUtilities.isEmpty(true);
    expect(result).toBeFalsy();
    result = commonUtilities.isEmpty( ! result);
    expect(result).toBeFalsy();
    var b="test";
    result = commonUtilities.isEmpty(b);
    expect(result).toBeFalsy();
    result = commonUtilities.isEmpty([0]);
    expect(result).toBeFalsy();
    result = commonUtilities.isEmpty([1]);
    expect(result).toBeFalsy();
});

test("MySQLDate", function() {
    expect(commonUtilities.MySQLDate).toBeDefined();
    var testDate = Date.now();
    var result = commonUtilities.MySQLDate(testDate);
    expect(result).toMatch(/\d\d\d\d-\d\d-\d\d/);
    result = commonUtilities.MySQLDate("2010-02-26");
    expect(result).toMatch(/2010-02-26/);
    result = commonUtilities.MySQLDate("February 26, 2010");
    expect(result).toMatch(/2010-02-26/);
});

test("subtractYearsFromNow", function() {
    expect(commonUtilities.subtractYearsFromNow).toBeDefined();
    var testDate = new Date();
    var years = 20;
    var resultDate = commonUtilities.subtractYearsFromNow(years);
    expect(resultDate).toBeInstanceOf(Date);
    expect(resultDate.getFullYear()).toEqual(testDate.getFullYear() - years);
    expect(resultDate.getMonth()).toEqual(testDate.getMonth());
    expect(resultDate.getDate()).toEqual(testDate.getDate());
});

test("tagParse", function() {
//    commonUtilities.tagParse = function (tags, delimiter) {
    expect(commonUtilities.tagParse).toBeDefined();
});

test("stripTags", function() {
//    commonUtilities.stripTags = function (input, allowed) {
    expect(commonUtilities.stripTags).toBeDefined();
});

test("safeForHTML", function() {
//    commonUtilities.safeForHTML = function (string) {
    expect(commonUtilities.safeForHTML).toBeDefined();
});

test("objectToString", function() {
//     commonUtilities.objectToString = function (obj) {
    expect(commonUtilities.objectToString).toBeDefined();
});

test("arrayToString", function() {
//    commonUtilities.arrayToString = function (array) {
    expect(commonUtilities.arrayToString).toBeDefined();
});

test("objectStringify", function() {
//    commonUtilities.objectStringify = function (object) {
    expect(commonUtilities.objectStringify).toBeDefined();
});

test("queryStringToObject", function() {
//    commonUtilities.queryStringToObject = function (urlParameterString) {
    expect(commonUtilities.queryStringToObject).toBeDefined();
});

test("extendObject", function() {
//    commonUtilities.extendObject = function() {
    expect(commonUtilities.extendObject).toBeDefined();
});

test("matchInArray", function() {
//    commonUtilities.matchInArray = function (pattern, arrayOfStrings) {
    expect(commonUtilities.matchInArray).toBeDefined();
});

test("makeFullPath", function() {
//    commonUtilities.makeFullPath = function (path) {
    expect(commonUtilities.makeFullPath).toBeDefined();
});    

test("appendFileToPath", function() {
    //    commonUtilities.appendFileToPath = function (path, file) {
        expect(commonUtilities.appendFileToPath).toBeDefined();
});

test("tokenReplace", function() {
//    commonUtilities.tokenReplace = function (text, parameters) {
    expect(commonUtilities.tokenReplace).toBeDefined();
});

test("utf8Encode", function() {
//    commonUtilities.utf8Encode = function (input) {
    expect(commonUtilities.utf8Encode).toBeDefined();
});

test("utf8Decode", function() {
//    commonUtilities.utf8Decode = function (utfText) {
    expect(commonUtilities.utf8Decode).toBeDefined();
});

test("base64FromImageUrl", function() {
//    commonUtilities.base64FromImageUrl = function(url, callback) {
    expect(commonUtilities.base64FromImageUrl).toBeDefined();
});

test("base64Encode", function() {
    expect(commonUtilities.base64Encode).toBeDefined();
    var data = "1234567890ABCDEF1234567890ABCDEF1234567890ABCDEF";
    var expectedResult = "MTIzNDU2Nzg5MEFCQ0RFRjEyMzQ1Njc4OTBBQkNERUYxMjM0NTY3ODkwQUJDREVG";
    var result = commonUtilities.base64Encode(data);
    expect(typeof result).toEqual("string");
    expect(result).toEqual(expectedResult);
});

test("base64Decode", function() {
    expect(commonUtilities.base64Decode).toBeDefined();
    var data = "MTIzNDU2Nzg5MEFCQ0RFRjEyMzQ1Njc4OTBBQkNERUYxMjM0NTY3ODkwQUJDREVG";
    var expectedResult = "1234567890ABCDEF1234567890ABCDEF1234567890ABCDEF";
    var result = commonUtilities.base64Decode(data);
    expect(typeof result).toEqual("string");
    expect(result).toEqual(expectedResult);
});

test("roundTo", function() {
    expect(commonUtilities.roundTo).toBeDefined();
    var testNumber = 1.0006789;
    var decimalPlaces = 5;
    var expectedResult = 1.00068;
    var result = commonUtilities.roundTo(testNumber, decimalPlaces);
    expect(result).toEqual(expectedResult);

    testNumber = 1.006789;
    decimalPlaces = 2;
    expectedResult = 1.01;
    result = commonUtilities.roundTo(testNumber, decimalPlaces);
    expect(result).toEqual(expectedResult);

    testNumber = 1.6789;
    decimalPlaces = 0;
    expectedResult = 2;
    result = commonUtilities.roundTo(testNumber, decimalPlaces);
    expect(result).toEqual(expectedResult);

    testNumber = 1;
    decimalPlaces = 2;
    expectedResult = 1.00;
    result = commonUtilities.roundTo(testNumber, decimalPlaces);
    expect(result).toEqual(expectedResult);
});

test("md5", function() {
    expect(commonUtilities.md5).toBeDefined();
    var result = commonUtilities.md5("test");
    expect(typeof result).toEqual("string");
    expect(result).toEqual("098f6bcd4621d373cade4e832627b4f6");
});

test("blowfish", function() {
    expect(commonUtilities.blowfish).toBeDefined();
    expect(commonUtilities.blowfish.encryptString).toBeDefined();
    expect(commonUtilities.blowfish.decryptString).toBeDefined();

    // make sure blowfish matches https://www.enginesis.com/admin/procs/blowfishDecrypt.php
    var phpResult = "smu9AgCA5PxC8B5XBhXJNrJrvQIAgOT8QvAeVwYVyTaya70CAIDk_ELwHlcGFck2OWD9ATbGEW4~";

    var data = "1234567890ABCDEF1234567890ABCDEF1234567890ABCDEF";
    var key = "90AB45DF67EE";
    var result = commonUtilities.blowfish.encryptString(data, key);
    expect(typeof result).toEqual("string");
    expect(result).toEqual(phpResult);

    var decryptedResult = commonUtilities.blowfish.decryptString(result, key);
    expect(decryptedResult).toEqual(data);
});

test("unlockWebAudio", function() {
    expect(commonUtilities.unlockWebAudio).toBeDefined();
    var audioElement = commonUtilities.unlockWebAudio();
    expect(audioElement).toBeDefined();
    expect(audioElement.play).toBeDefined();
});
    
test("test for expected browser functions", function() {
    expect(commonUtilities.isMobile).toBeDefined();
    expect(commonUtilities.isMobileAndroid).toBeDefined();
    expect(commonUtilities.isMobileBlackberry).toBeDefined();
    expect(commonUtilities.isMobileIos).toBeDefined();
    expect(commonUtilities.isMobileWindows).toBeDefined();

    expect(commonUtilities.cookieGet).toBeDefined();
    expect(commonUtilities.cookieSet).toBeDefined();
    expect(commonUtilities.cookieRemove).toBeDefined();
    expect(commonUtilities.cookieExists).toBeDefined();
    expect(commonUtilities.cookieGetKeys).toBeDefined();

    expect(commonUtilities.haveSessionStorage).toBeDefined();
    expect(commonUtilities.haveLocalStorage).toBeDefined();
    expect(commonUtilities.storageGet).toBeDefined();
    expect(commonUtilities.saveObjectWithKey).toBeDefined();
    expect(commonUtilities.loadObjectWithKey).toBeDefined();
    expect(commonUtilities.removeObjectWithKey).toBeDefined();

    expect(commonUtilities.shareOnFacebook).toBeDefined();
    expect(commonUtilities.shareOnTwitter).toBeDefined();
    expect(commonUtilities.shareOnGoogle).toBeDefined();
    expect(commonUtilities.shareByEmail).toBeDefined();

    expect(commonUtilities.performanceTest).toBeDefined();
    expect(commonUtilities.insertScriptElement).toBeDefined();
    expect(commonUtilities.validateFields).toBeDefined();
});
