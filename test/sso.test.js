/**
 * SSO unit tests.
 * Expects the SSO modules to load and operate as designed.
 * See Expect interface at https://facebook.github.io/jest/docs/en/expect.html
 */
var ssoApple = require("../lib/ssoApple");
var ssoFacebook = require("../lib/ssoFacebook");
var ssoGoogle = require("../lib/ssoGoogle");
var ssoTwitter = require("../lib/ssoTwitter");

test("Expect Apple module to exist and contain required functions", function() {
    expect(ssoApple).toBeDefined();
    expect(ssoApple.init).toBeDefined();
    expect(ssoApple.setParameters).toBeDefined();
    expect(ssoApple.load).toBeDefined();
    expect(ssoApple.loadThenLogin).toBeDefined();
    expect(ssoApple.logout).toBeDefined();
    expect(ssoApple.disconnect).toBeDefined();
    expect(ssoApple.isReady()).toBeFalsy();
    expect(ssoApple.networkId()).toBe(14);
    expect(ssoApple.siteUserId()).toBe("");
    expect(ssoApple.userInfo()).toBe(null);
    expect(ssoApple.token()).toBe(null);
    expect(ssoApple.tokenExpirationDate()).toMatchObject(new Date(0));
    expect(ssoApple.isTokenExpired()).toBeTruthy();
});

test("Expect Facebook module to exist and contain required functions", function() {
    expect(ssoFacebook).toBeDefined();
    expect(ssoFacebook.init).toBeDefined();
    expect(ssoFacebook.setParameters).toBeDefined();
    expect(ssoFacebook.load).toBeDefined();
    expect(ssoFacebook.loadThenLogin).toBeDefined();
    expect(ssoFacebook.logout).toBeDefined();
    expect(ssoFacebook.disconnect).toBeDefined();
    expect(ssoFacebook.isReady()).toBeFalsy();
    expect(ssoFacebook.networkId()).toBe(2);
    expect(ssoFacebook.siteUserId()).toBe("");
    expect(ssoFacebook.userInfo()).toBe(null);
    expect(ssoFacebook.token()).toBe(null);
    expect(ssoFacebook.tokenExpirationDate()).toMatchObject(new Date(0));
    expect(ssoFacebook.isTokenExpired()).toBeTruthy();
});

test("Expect Facebook module to exist and contain required functions", function() {
    expect(ssoGoogle).toBeDefined();
    expect(ssoGoogle.init).toBeDefined();
    expect(ssoGoogle.setParameters).toBeDefined();
    expect(ssoGoogle.load).toBeDefined();
    expect(ssoGoogle.loadThenLogin).toBeDefined();
    expect(ssoGoogle.logout).toBeDefined();
    expect(ssoGoogle.disconnect).toBeDefined();
    expect(ssoGoogle.isReady()).toBeFalsy();
    expect(ssoGoogle.networkId()).toBe(7);
    expect(ssoGoogle.siteUserId()).toBe("");
    expect(ssoGoogle.userInfo()).toBe(null);
    expect(ssoGoogle.token()).toBe(null);
    expect(ssoGoogle.tokenExpirationDate()).toMatchObject(new Date(0));
    expect(ssoGoogle.isTokenExpired()).toBeTruthy();
});

test("Expect Facebook module to exist and contain required functions", function() {
    expect(ssoTwitter).toBeDefined();
    expect(ssoTwitter.init).toBeDefined();
    expect(ssoTwitter.setParameters).toBeDefined();
    expect(ssoTwitter.load).toBeDefined();
    expect(ssoTwitter.loadThenLogin).toBeDefined();
    expect(ssoTwitter.logout).toBeDefined();
    expect(ssoTwitter.disconnect).toBeDefined();
    expect(ssoTwitter.isReady()).toBeFalsy();
    expect(ssoTwitter.networkId()).toBe(11);
    expect(ssoTwitter.siteUserId()).toBe("");
    expect(ssoTwitter.userInfo()).toBe(null);
    expect(ssoTwitter.token()).toBe(null);
    expect(ssoTwitter.tokenExpirationDate()).toMatchObject(new Date(0));
    expect(ssoTwitter.isTokenExpired()).toBeTruthy();
});
