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
$whichDatabase = 'enginesis';
$sqlDB = &$sqlDBs[$whichDatabase];
$host = $sqlDB['host'];

$DBConn = dbConnect($whichDatabase);
if ($DBConn) {
	$req = dbQuery('SELECT count(*) from sites', array());
	if ($req) {
		$row = dbFetch($req);
		if ($row) {
			$pageok = true;
		} else {
			echo("Error in $whichDatabase data " . dbError($DBConn));
		}
	} else {
		echo("Error in $whichDatabase schema " . dbError($DBConn));
	}
	dbClose($DBConn);
    $DBConn = null;
} else {
	echo("Error connecting to database");
    if ($info) {
        echo("<p>DB=$whichDatabase; H=$host U=" . $sqlDB['user'] . "</p>\n");
    }
}
if ($pageok) {
	echo "PAGEOK";
}
?>