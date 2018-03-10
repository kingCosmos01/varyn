<?php
// Verify the server is operating correctly
require_once('../services/common.php');
require_once('../services/Enginesis.php');

// Verify PHP is properly loaded and we have common.php properly loaded
$pageok = false;
$version = defined('VARYN_VERSION') ? VARYN_VERSION : null;
$pageok = strlen($version) > 0;

// Verify we're on a known server stage
if ($pageok) {
    $serverStage = serverStage();
    $pageok = strlen($serverStage) == 0 || preg_match('/^-[dlqx]$/', $serverStage) === 1;
}

// Verify we can contact Enginesis and run a public service
if ($pageok) {
    $userId = 10243;
    $response = $enginesis->userGet($userId);
    if ($response != null) {
        $pageok = $response->user_id == $userId;
    } else {
        $pageok = false;
    }
}

if ($pageok) {
    echo "PAGEOK";
} else {
    echo "ERROR";
}