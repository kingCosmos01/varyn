<?php
/* This test suite is designed to unit test all of the common.php functions.
 * Uses PHPUnit to unit test. See https://phpunit.readthedocs.io/en/9.5/assertions.html
 * Run this test by itself with either
 * phpunit --testdox-xml CommonTest.xml ./CommonTest.php > CommonTest.log
 * phpunit ./CommonTest.php > CommonTest.log
 */
declare(strict_types=1);
require_once('../../services/common.php');
require_once('../../services/EnginesisErrors.php');

use PHPUnit\Framework\TestCase;

final class CommonTest extends TestCase {
    public static $setup;
    protected $stage;
    protected $enginesisHost;
    protected $site_id;
    protected $user_id;
    protected $language_code;
    protected $site_user_id;
    protected $network_id;
    protected $user_name;
    protected $access_level;

    /**
     * Initial test setup
     */
    public static function setUpBeforeClass(): void {
        self::$setup = false;
    }

    protected function setUp (): void {
        $this->site_id = 106;
        $this->user_id = 10248;
        $this->language_code = 'en';
        $this->stage = serverStage();
        if ( ! self::$setup) {
            // Do one-time setup here
            self::$setup = true;
            if ($this->stage == '') {
                die("You cannot run this on the Live server. Please only run the services test on a -l/-d/-q instance.");
            }
            ini_set('memory_limit', '32M');
            set_time_limit(280);
            $runDate = date('l F jS Y h:i:s A');
            $_SERVER['DOCUMENT_ROOT'] = '../../public';
            $this->enginesisHost = 'http://www.enginesis' . $this->stage . '.com/index.php';	// define testing server for nonsecure and authenticated procedures
        }
    }

    public function testConfiguration() {
        global $_MEMCACHE_HOSTS;
        global $admin_notification_list;
        global $CMSUserLogins;
        global $_MAIL_HOSTS;
        global $socialServiceKeys;
        global $developerKey;
        global $siteId;
        global $languageCode;

        // verify all globals and defines are defined and have a value
        $this->assertNotEmpty(SERVER_ROOT);
        $this->assertNotEmpty(SERVER_DATA_PATH);
        $this->assertNotEmpty(SERVER_PRIVATE_PATH);
        $this->assertNotEmpty(SERVICE_ROOT);
        $this->assertNotEmpty(VIEWS_ROOT);

        $this->assertNotEmpty(LOGFILE_PREFIX);
        $this->assertNotEmpty(LOGFILE_PREFIX);
        $this->assertNotEmpty(ENGINESIS_SITE_NAME);
        $this->assertNotEmpty(ENGINESIS_SITE_ID);
        $this->assertIsBool(DEBUG_ACTIVE);
        $this->assertIsBool(DEBUG_SESSION);
        $this->assertNotEmpty(PUBLISHING_MASTER_PASSWORD);
        $this->assertNotEmpty(REFRESH_TOKEN_KEY);
        $this->assertNotEmpty(ADMIN_ENCRYPTION_KEY);
        $this->assertIsString(COREG_TOKEN_KEY);
        $this->assertNotEmpty(ENGINESIS_DEVELOPER_API_KEY);
        $this->assertNotEmpty(ENGINESIS_CMS_API_KEY);
        $this->assertNotEmpty(SESSION_REFRESH_HOURS);
        $this->assertNotEmpty(SESSION_REFRESH_INTERVAL);
        $this->assertNotEmpty(SESSION_AUTHTOKEN);
        $this->assertNotEmpty(SESSION_PARAM_CACHE);

        $this->assertIsArray($_MEMCACHE_HOSTS);
        $this->assertNotEmpty($_MEMCACHE_HOSTS);

        $this->assertIsArray($admin_notification_list);
        $this->assertNotEmpty($admin_notification_list);
        $this->assertTrue(checkEmailAddress($admin_notification_list[0]), 'At least one email address must be provided.');

        $this->assertIsArray($CMSUserLogins);
        $this->assertNotEmpty($CMSUserLogins);
        $this->assertNotEmpty($CMSUserLogins[0]['user_name']);
        $this->assertNotEmpty($CMSUserLogins[0]['password']);

        $this->assertIsArray($socialServiceKeys);
        $this->assertNotEmpty($socialServiceKeys);

        $this->assertIsArray($_MAIL_HOSTS);
        $this->assertNotEmpty($_MAIL_HOSTS);
        $this->assertArrayHasKey('-l', $_MAIL_HOSTS);
        $this->assertArrayHasKey('-d', $_MAIL_HOSTS);
        $this->assertArrayHasKey('-q', $_MAIL_HOSTS);
        $this->assertArrayHasKey('', $_MAIL_HOSTS);

        $this->assertTrue(! isset($developerKey));
        $this->assertNotEmpty($siteId);
        $this->assertNotEmpty($languageCode);
    }

    public function testServerStage() {
        $stage = serverStage();
        $this->assertNotEquals('', $stage, 'Cannot run unit tests on a live server.');
        $this->assertEquals('-', $stage[0], 'Must be in the format -[x|d|q|l].');
    }

    // Verify operation for randomString()
    public function testRandomString() {
        $length = 16;
        $maxCodePoint = 32;
        $reseed = false;
        $value = randomString($length, $maxCodePoint, $reseed);
        $this->assertEquals($length, strlen($value));

        $length = 32;
        $value = randomString($length, $maxCodePoint, $reseed);
        $this->assertEquals($length, strlen($value));

        $length = 5;
        $value = randomString($length, $maxCodePoint, $reseed);
        $this->assertEquals($length, strlen($value));
        $valuex = randomString($length, $maxCodePoint, $reseed);
        $this->assertEquals($length, strlen($value));
        $this->assertNotEquals($value, $valuex);
    }

    public function testTokenReplace() {
        $text = 'This %food% is a %meat% %food%.';
        $parameters = ["food" => "sandwich", "meat" => "turkey"];
        $expected = 'This sandwich is a turkey sandwich.';
        $result = tokenReplace($text, $parameters);
        $this->assertEquals($expected, $result);
    }

    // verify makeErrorResponse() returns expected Enginesis response object
    public function testMakeErrorResponse() {
        $errorCode = EnginesisErrors::CANNOT_COMMENT_YOURSELF;
        $errorMessage = 'You cannot assign a comment to yourself.';
        $parameters = ['fn' => 'CommentAssign', 'two' => 2, 'three' => 3];
        $response = makeErrorResponse($errorCode, $errorMessage, $parameters);
        $this->assertNotEmpty($response);
        $result = json_decode($response);
        $this->assertEquals('0', $result->results->status->success);
        $this->assertEquals(EnginesisErrors::CANNOT_COMMENT_YOURSELF, $result->results->status->message);
        $this->assertNotEmpty($result->results->status->extended_info);
        $this->assertEquals('CommentAssign', $result->results->passthru->fn);
    }

    public function testGetURLContents() {
        $url = 'https://www.killerquiz.com/';
        $response = getURLContents($url, null, null);
        $this->assertNotEmpty($response);
        $this->assertStringContainsString('trivia skills', $response);

        $url = 'https://www.enginesis.com/index.php';
        $get_params = ['fn' => 'CommentAssign', 'site_id' => 100, 'id' => 3, 'response' => 'json'];
        $response = getURLContents($url, $get_params, null);
        $this->assertNotEmpty($response);
        $result = json_decode($response);
        $this->assertEquals('0', $result->results->status->success);
        $this->assertEquals(EnginesisErrors::INVALID_SERVICE_REQUEST, $result->results->status->message);
        $this->assertNotEmpty($result->results->status->extended_info);
        $this->assertEquals('CommentAssign', $result->results->passthru->fn);

        $post_params = ['fn' => 'CommentAssign', 'site_id' => 100, 'id' => 3, 'response' => 'json'];
        $response = getURLContents($url, null, $post_params);
        $this->assertNotEmpty($response);
        $result = json_decode($response);
        $this->assertEquals('0', $result->results->status->success);
        $this->assertEquals(EnginesisErrors::INVALID_SERVICE_REQUEST, $result->results->status->message);
        $this->assertNotEmpty($result->results->status->extended_info);
        $this->assertEquals('CommentAssign', $result->results->passthru->fn);
    }
}