<?php

// Verify the server is operating correctly
$info = isset($_GET['info']) && $_GET['info'] == 1;
if ($info) {
    phpinfo();
    var_dump(gd_info());
    echo("<br/>\n");
}

require_once('../services/common.php');

$pageok = false;
$version = VARYN_VERSION;

if (strlen($version) > 0) {
    echo "PAGEOK";
} else {
    echo "TEST_FAILED";
}