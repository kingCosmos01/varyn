<?php
/* This test suite is designed to unit test all of the Enginesis SDK functions found in Enginesis.php functions.
 * Uses PHPUnit to unit test. See https://phpunit.readthedocs.io/en/9.5/assertions.html
 * Run this test by itself with either
 * phpunit --testdox-xml EnginesisSDKTest.xml ./EnginesisSDKTest.php > EnginesisSDKTest.log
 * phpunit ./EnginesisSDKTest.php > EnginesisSDKTest.log
 */
declare(strict_types=1);
require_once('../../services/common.php');
require_once('../../services/Enginesis.php');

use PHPUnit\Framework\TestCase;

final class EnginesisSDKTest extends TestCase {
    public static $setup;
    protected $stage;
    protected $enginesisHost;
    protected $developerAPIKey;
    protected $errorLogger;
    protected $siteId;
    protected $userId;
    protected $gameId;
    protected $languageCode;

    /**
     * Initial test setup
     */
    public static function setUpBeforeClass(): void {
        self::$setup = false;
    }

    protected function setUp (): void {
        $this->siteId = 106;
        $this->userId = 10248;
        $this->languageCode = 'en';
        $this->errorLogger = null;
        if ( ! self::$setup) {
            // Do one-time setup here
            self::$setup = true;
            ini_set('memory_limit', '32M');
            set_time_limit(280);
            $runDate = date('l F jS Y h:i:s A');
            $this->enginesisHost = 'https://www.enginesis' . $this->stage . '.com/index.php';	// define testing server for nonsecure and authenticated procedures
        }
    }

    public function testConstructor() {
        $enginesis = new Enginesis($this->siteId, $this->enginesisHost, $this->developerAPIKey, $this->errorLogger);
        $this->assertNotNull($enginesis, 'Can construct.');
    }

    public function testCMSAuthentication() {
        global $CMSUserLogins;

        $enginesis = new Enginesis($this->siteId, $this->enginesisHost, $this->developerAPIKey, $this->errorLogger);
        $this->assertNotNull($enginesis, 'Can construct Enginesis.');

        $response = $enginesis->callSecureService('ContentIdPack', ['object_id' => 5006, 'content_type_id' => 2]);
        $error = $enginesis->getLastError();
        $this->assertEquals(EnginesisErrors::SERVICE_ERROR, $error['message'], 'Expect an error code');
        $this->assertNotEmpty($error['extended_info'], 'Expect an error message.');

        $this->assertNotEmpty(ENGINESIS_CMS_API_KEY);
        $this->assertIsArray($CMSUserLogins);
        $this->assertNotEmpty($CMSUserLogins);
        $this->assertNotEmpty($CMSUserLogins[0]['user_name']);
        $this->assertNotEmpty($CMSUserLogins[0]['password']);
        $enginesis->setCMSKey(ENGINESIS_CMS_API_KEY, $CMSUserLogins[0]['user_name'], $CMSUserLogins[0]['password']);
        $response = $enginesis->callSecureService('ContentIdPack', ['object_id' => 5006, 'content_type_id' => 2]);

        $this->assertNotEmpty($response, 'Expect a response.');

        // $enginesis->callSecureService($service, $parameters);
    }
}
