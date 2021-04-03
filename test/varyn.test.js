/**
 * Tests for varyn.js
 */
var varyn = require("../public/common/varyn");

test('Expect varyn to exist and contain required functions', function() {
    expect(varyn).toBeDefined();
});

/**
runUnitTests: function() {
    console.log('==== Starting Varyn.js Unit Tests ====');
    console.log('enginesisSession.versionGet: ' + enginesisSession.versionGet());
    console.log('enginesisSession.getRefreshToken: ' + enginesisSession.getRefreshToken());
    console.log('enginesisSession.getGameImageURL: ' + enginesisSession.getGameImageURL({gameName: 'MatchMaster3000', width: 0, height: 0, format: null}));
    console.log('enginesisSession.getDateNow: ' + enginesisSession.getDateNow());
    console.log('varyn.networkIdToString: ' + varynApp.networkIdToString(11));
    console.log('==== Completed Varyn.js Unit Tests ====');
}
*/