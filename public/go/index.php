<?php
require_once('../../services/common.php');
$defaultResource = '/';
$f = fopen('cloaked.txt', 'r');
if ($f) {
    $id   = isset($_GET['id']) ? rtrim(trim($_GET['id']), '/') : 'default';
    while ($data = fgetcsv($f)) {
        if (isset($data[0]) && isset($data[1])) {
            $key = trim($data[0]);
            $resource = trim($data[1]);
            if ($id == $key) {
                fclose($f);
                header( "X-Robots-Tag: noindex, nofollow", true );
                header( "Location: " .  $resource, 302 );
                exit(0);
            }
        }
    }
    fclose($f);
    header( "X-Robots-Tag: noindex, nofollow", true );
    header( "Location: " .  $defaultResource, 302 );
    exit(0);
} else {
    debugLog('cloak called with no access to cloaked site list.');
}
