<?php
/**
 * track.php: record a tracking event and forward to correct page
 */
require_once('../../services/common.php');
$event = getPostOrRequestVar('e', '');
switch ($event) {
    case '1aa': // Return 1px png image
        header('Content-Type: image/png');
        echo base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABAQMAAAAl21bKAAAAA1BMVEUAAACnej3aAAAAAXRSTlMAQObYZgAAAApJREFUCNdjYAAAAAIAAeIhvDMAAAAASUVORK5CYII=');
        break;
    case '1a2': // Subscribe
        header('Location: /?s=1');
        break;
    case '1a3': // Unsubscribe
        header('Location: /');
        break;
    case '1a4': // Edit subscription preferences
        header('Location: /');
        break;
    case '1a0': // record and go to home page
    default:
        header('Location: /');
        break;
}
exit(0);
