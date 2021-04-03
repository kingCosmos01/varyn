<?php
/**
 * Return oembed object (see oembed.com for spec) when given a valid object id. Valid objects are:
 *   . game
 *   . user
 * Created by: jf
 * Date: 3/20/2016 8:57 AM
 *
 * TODO:
 * 1. look up object id and get the info on it.
 * 2. for users, return the "profile card"
 */
require_once('../services/common.php');

$url = getPostOrRequestVar('url', '');
if ( ! empty($url)) {
    $urlParts = explode('/', $url);
    $numParts = count($urlParts);
    if ($numParts > 0) { // in case a trailing / is on the url
        if (empty($urlParts[$numParts - 1])) {
            unset($urlParts[$numParts - 1]);
        }
    }
    $numParts = count($urlParts);
    if ($numParts > 1) {
        $objectType = $urlParts[$numParts - 2];
        $objectId = $urlParts[$numParts - 1];
    } elseif ($numParts == 1) {
        $objectType = 'game';
        $objectId = $urlParts[0];
    }
    // TODO: look up object in the db.
    // set header to application/json
    header('Content-Type: application/json');
    $object = array(
        "version" => VARYN_VERSION,
        "type" => $objectType,
        "id" => $objectId,
        "width" => 1140,
        "height" => 768,
        "title" => "Match Master 3000",
        "url" => "http://varyn.com/games/1083",
        "author_name" => "Varyn",
        "author_url" => "http://www.varyn.com/games/1083",
        "provider_name" => "Varyn",
        "provider_url" => "http://www.varyn.com/"
    );
    echo(json_encode($object));
}
