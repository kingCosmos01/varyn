/**
 * Enginesis unit tests.
 * Expects the UserData module to load and operate as designed.
 * See Expect interface at https://facebook.github.io/jest/docs/en/expect.html
 */
var enginesis = require("../lib/enginesis");

test('Expect enginesis to exist and contain required functions', function() {
    expect(enginesis).toBeDefined();
    expect(enginesis.init).toBeDefined();
    expect(enginesis.isUserLoggedIn).toBeDefined();
    expect(enginesis.versionGet).toBeDefined();
    expect(enginesis.conferenceTopicList).toBeDefined();
});

test('Expect enginesis version to be #.#.#', function() {
    var version = enginesis.versionGet();
    var versionCheck = version.match(/^(0|[1-9]\d*)\.(0|[1-9]\d*)\.(0|[1-9]\d*)(-(0|[1-9]\d*|\d*[a-zA-Z-][0-9a-zA-Z-]*)(\.(0|[1-9]\d*|\d*[a-zA-Z-][0-9a-zA-Z-]*))*)?(\+[0-9a-zA-Z-]+(\.[0-9a-zA-Z-]+)*)?$/);
    expect(versionCheck).toBeTruthy();
});

test('Expect enginesis blowfish to work', function() {
    // test without padding
    var encryptedBase64Safe  = "Qolmvwn1kaOOdjWtUcg8Df8zHz_7E60iNDvIRiqSBvIs_ncp__u9gSingDcjxbQk";
    var encryptedBase64Truth = "Qolmvwn1kaOOdjWtUcg8Df8zHz/7E60iNDvIRiqSBvIs/ncp//u9gSingDcjxbQk";
    var blowfishClear = "score=9989&time=23450&achievements=9,22,77,2";
    var blowfishKey = "6e0837f99c242c13665de09c271d5653";

    expect(enginesis.blowfish).toBeDefined();
    expect(enginesis.blowfish.encryptString).toBeDefined();
    expect(enginesis.blowfish.decryptString).toBeDefined();

    // encrypt a string to a know (precomputed) cypher
    var encryptedTest = enginesis.blowfish.encryptString(blowfishClear, blowfishKey);
    expect(encryptedTest).toBe(encryptedBase64Safe);

    // verify the same encryption produces the same clear text
    var decryptedTest = enginesis.blowfish.decryptString(encryptedTest, blowfishKey);
    expect(decryptedTest).toBe(blowfishClear);

    // verify the translated chars get handled properly
    var decryptedTest = enginesis.blowfish.decryptString(encryptedBase64Safe, blowfishKey);
    expect(decryptedTest).toBe(blowfishClear);

    // verify the normal base 64 is acceptable (the translated chars don't matter)
    var decryptedTest = enginesis.blowfish.decryptString(encryptedBase64Truth, blowfishKey);
    expect(decryptedTest).toBe(blowfishClear);

    // test with padding
    encryptedBase64Safe  = "bs3hRQkz7rwGbvveSEJQhD5iNtBgHuCdf06X6E8tiKyqGTX2JmvjSZk4pmxqs7MpBPnNvw2QsHWHOHc1IbVcEkxogshACNbeOLys9sik3ZE~";
    encryptedBase64Truth = "bs3hRQkz7rwGbvveSEJQhD5iNtBgHuCdf06X6E8tiKyqGTX2JmvjSZk4pmxqs7MpBPnNvw2QsHWHOHc1IbVcEkxogshACNbeOLys9sik3ZE=";
    blowfishClear = "site_id=100&user_id=99239&score=80127&time_played=44561&achievements=1,2,3,4,5";
    blowfishKey = "4e1bbbbee5773ac8c3c7fc6a3e69f4bd";

    // encrypt a string to a know (precomputed) cypher
    var encryptedTest = enginesis.blowfish.encryptString(blowfishClear, blowfishKey);
    expect(encryptedTest).toBe(encryptedBase64Safe);

    // verify the same encryption produces the same clear text
    var decryptedTest = enginesis.blowfish.decryptString(encryptedTest, blowfishKey);
    expect(decryptedTest).toBe(blowfishClear);

    // verify the translated chars get handled properly
    var decryptedTest = enginesis.blowfish.decryptString(encryptedBase64Safe, blowfishKey);
    expect(decryptedTest).toBe(blowfishClear);

    // verify the normal base 64 is acceptable (the translated chars don't matter)
    var decryptedTest = enginesis.blowfish.decryptString(encryptedBase64Truth, blowfishKey);
    expect(decryptedTest).toBe(blowfishClear);
});
