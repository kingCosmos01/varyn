<?php
/**
 * Define sensitive data in this configuration file.
 * User: jf
 * Date: Feb-13-2016
 */
date_default_timezone_set('America/New_York');
define('LOGFILE_PREFIX', 'varyn');
define('SITE_SESSION_COOKIE', 'varynuser');
define('ENGINESIS_SITE_NAME', 'Varyn');
define('ENGINESIS_SITE_ID', 106);
define('DEBUG_ACTIVE', false);
define('DEBUG_SESSION', false);
define('PUBLISHING_MASTER_PASSWORD', '');
define('REFRESH_TOKEN_KEY', '');
define('ADMIN_ENCRYPTION_KEY', '');
define('COREG_TOKEN_KEY', '');
define('ENGINESIS_DEVELOPER_TOKEN', '');
define('SESSION_REFRESH_HOURS', 4380);     // refresh tokens are good for 6 months
define('SESSION_REFRESH_INTERVAL', 'P6M'); // refresh tokens are good for 6 months
define('SESSION_AUTHTOKEN', 'authtok');
define('SESSION_PARAM_CACHE', 'engsession_params');

// From EnginesisNetworks::enum, but this should not depend on that include so the enums are hardcoded.
$socialServiceKeys = [
    2  => ['service' => 'Facebook', 'app_id' => '489296364486097', 'app_secret' => 'b3e467c573bf5ebc334a8647a88ddfd6', 'admins' =>''],
    7  => ['service' => 'Google', 'app_id' => '1065156255426-al1fbn6kk4enqfq1f9drn8q1111optvt.apps.googleusercontent.com', 'app_secret' => '10xMn5CfHOVSpH8FWyOqyB5a', 'admins' =>''],
    11 => ['service' => 'Twitter', 'app_id' => 'DNJM5ALaCxE1E2TnpnJtEl2ml', 'app_secret' => 'nErbZceOKAcDZpMFQo1N1x1l7Z71kCSv3esKQDfQyDIZRFltJn', 'admins' =>'']
];
$developerKey = 'B3E06F9352AEA898E';
$siteId = ENGINESIS_SITE_ID;
$languageCode = 'en';
