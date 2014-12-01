<?php
// This file will define the following globals:
//  ROOTPATH is the file path to the root of the web site
//  $stage = -l, -q, -d,or '' for Live
//  $server = which enginesis server to converse with, full protocol/domain/url e.g. https://www.enginesis.com
//  $isLoggedIn = true if the user is logged in
//

if ( ! defined('ROOTPATH') ) {
    define('ROOTPATH', $_SERVER['DOCUMENT_ROOT']);
}
date_default_timezone_set('America/New_York');

function isLoggedInUser ()
{
    // check we have the Enginesis authtoken in engsession cookie
    if (isset($_COOKIE['engsession'])) {
        $authtoken = $_COOKIE['engsession'];
        $isLoggedIn = ($authtoken != NULL && strlen($authtoken) > 0);
    } else {
        $isLoggedIn = false;
    }
    return $isLoggedIn;
}

$page = '';
$isLoggedIn = isLoggedInUser();
$server = '';

if(strpos($_SERVER['HTTP_HOST'], ':') !== false ) {
    $host_name = isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : $_SERVER['HTTP_HOST'];
    $server = substr($host_name, 0, strpos($host_name, ':'));
} else {
    $server = isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : $_SERVER['HTTP_HOST'];
}
$stage = '';
if (strpos($server, '-l.') > 0) {
    $stage = '-l';
} elseif (strpos($server, '-q.') > 0) {
    $stage = '-q';
} elseif (strpos($server, '-d.') > 0) {
    $stage = '-d';
} elseif (strpos($server, '-x.') > 0) {
    $stage = '-x';
}
$server = 'http://www.enginesis' . $stage . '.com';
$webserver = 'http://www.varyn' . $stage . '.com';

?>
