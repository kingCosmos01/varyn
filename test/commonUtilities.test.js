/**
 * commonUtilities unit tests.
 * Expects the commonUtilities module to load and operate as designed.
 * See Expect interface at https://facebook.github.io/jest/docs/en/expect.html
 */
var commonUtilities = require("../lib/commonUtilities");


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