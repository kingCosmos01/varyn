<?php

require_once('common.php');

// Verify the server is operating correctly
$info = isset($_GET['info']) && $_GET['info'] == 1;
if ($info) {
	phpinfo();
	var_dump(gd_info());
    echo("<br/>\n");
}

$pageok = true;
if ($pageok) {
	echo "PAGEOK";
}
?>