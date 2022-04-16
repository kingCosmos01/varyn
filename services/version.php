<?php /* version.php - define the version of the code base
 * and lock the system if under maintenance.
 */
if ( ! defined('VARYN_VERSION')) {
    define('VARYN_VERSION', '2.3.8');
}
define('VARYN_ADMIN_LOCK', false);
define('ADMIN_LOCK_MESSAGE', '<h3>Varyn is OFFLINE</h3><p>Varyn is currently offline, most probably due to server maintenance.</p><p>If you have an immediate need for service please contact Varyn support <a href="mailto:support@varyn.com">support@varyn.com</a>.</p>' );
if (VARYN_ADMIN_LOCK) {
    header ("Location: /offline.html");
    exit(0);
}
