<?php

$f = fopen('cloaked.txt', 'r');
if ( ! $f) {
    echo 'Cloaked site list not found.';
} else {
    $id   = isset($_GET['id']) ? rtrim(trim($_GET['id']), '/') : 'default';
    $urls = array();

    while ($data = fgetcsv($f)) {
        if ( ! isset($data[0]) || ! isset($data[1])) {
            continue;
        }
        $key = trim($data[0]);
        $val = trim($data[1]);
        $urls[$key] = $val;
    }
    // Check if the given ID is set, if it is, set the URL to that, if not, default
    $url = ( isset( $urls[ $id ] ) ) ? $urls[ $id ] : ( isset( $urls[ 'default' ] ) ? $urls[ 'default' ] : false );
    if ($url != NULL) {
        header( "X-Robots-Tag: noindex, nofollow", true );
        header( "Location: " .  $url, 302 );
        die;
    } else {
        echo '<p>Make sure your cloaked.txt file contains a default value.</p>';
    }
}
 ?>
