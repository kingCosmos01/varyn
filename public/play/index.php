<?php
require_once('../../services/common.php');
$search = getPostOrRequestVar('q', null);
if ($search != null) {
    header('location:/games/?q=' . $search);
    exit;
}
$page = 'play';
$showSubscribe = getPostOrRequestVar('s', '0');
$gameId = getPostOrRequestVar(['id', 'gameid', 'game_id', 'gameId', 'gameName', 'g'], '');
if ($gameId == '') {
    header("Location: /games/");
}
$gameWidth = 1024;
$gameHeight = 768;
$pageDescription = '';
$gameInfo = null;
$receivedGameInfo = false;
$gameContainerHTML = '';
$isPlayBuzzSpecialCase = false;

// get game info: we need the game info immediately in order to build the page
// GameGet only works for numeric game_id, if game name we need to call GameGetByName
if (is_numeric($gameId)) {
    $gameInfo = $enginesis->gameGet($gameId);
} elseif ( ! empty($gameId)) {
    $gameInfo = $enginesis->gameGetByName($gameId);
} else {
    header("Location: /games/");
    exit(0);
}
if ($gameInfo != null) {
    $receivedGameInfo = true;
    $gameId = $gameInfo->game_id;
    $gameName = $gameInfo->game_name;
    $title = $gameInfo->title;
    $pageSocialImage1 = '//enginesis.varyn.com/games/' . $gameName . '/images/600x450.png';
    $pageSocialImage2 = '//enginesis.varyn.com/games/' . $gameName . '/images/586x308.png';
    $pageFavIcon = '//enginesis.varyn.com/games/' . $gameName . '/images/50x50.png';
    $pageIcon = '//enginesis.varyn.com/games/' . $gameName . '/images/50x50.png';
    $gameLink = '//www.varyn.com/game/' . $gameName;
    $pageOGLink = '//www.varyn.com/game/' . $gameName;
    $pageDescription = $gameInfo->short_desc;
    $pageKeywords = $gameInfo->search_terms;
    $gameContainerHTML = setGameContainer($gameInfo, $enginesis->getServiceRoot(), $siteId, $gameId);
} else {
    // TODO: It may be better to go to /games/ with a search string ?q=$gameId but with an error message "Game not found"
    // header("Location: /games/?q=$gameId");
    header("Location: /missing.php?m=" . urlencode("No information found for $gameId."));
    exit(0);
}

function setGameContainer ($gameInfo, $enginesisServer, $siteId, $gameId) {
    // generate the necessary HTML to setup the game container div

    $width = $gameInfo->width;
    $height = $gameInfo->height;
    $bgcolor = '#' . $gameInfo->bgcolor;
    $pluginId = $gameInfo->game_plugin_id;
    $allowScroll = $gameInfo->popup == 0 ? 'no' : 'yes';
    $gameContainerHTML = '<!-- debug: plugin=' . $pluginId . ' w/h=' . $width . 'x' . $height . '-->';
    if ($pluginId == 9) { // embed
        $gameContainerHTML .= '<div id="gameContainer-iframe" style="position: relative; margin: 0 auto; width: 100%; height: 100%;">' . $gameInfo->game_link . '</div>';
    } else {
        if ($pluginId == 10) { // canvas
            if (strpos($gameInfo->game_link, '://') > 0) {
                $gameLink = $gameInfo->game_link;
            } else {
                $gameLink = $enginesisServer . 'games/' . $gameInfo->game_name . '/' . $gameInfo->game_link;
            }
        } else {
            $gameLink = $enginesisServer . 'games/play.php?site_id=' . $siteId . '&game_id=' . $gameId;
        }
        // $gameContainerHTML .= '<iframe id="gameContainer-iframe" src="' . $gameLink . '" allowfullscreen scrolling="' . $allowScroll . '" width="' . $width . '" height="' . $height . '" border="0"></iframe>';
        $gameContainerHTML .= '<iframe id="gameContainer-iframe" src="' . $gameLink . '" allowfullscreen scrolling="' . $allowScroll . '"></iframe>';
    }
    return $gameContainerHTML;
}

$pageTitle = $title . ' on Varyn.com';
include_once(VIEWS_ROOT . 'header.php');
?>
<div id="topContainer" class="container top-promo-area">
    <div id="gameContainer" class="row"><?php echo($gameContainerHTML);?></div>
    <div id="playgame-InfoPanel" class="row">
        <div class="panel panel-default">
            <div class="panel-body">
                <div id="gameInfo">
                <?php
                if ($receivedGameInfo) {
                    $shareFacebook = '<li><a href="https://www.facebook.com/sharer/sharer.php?u=' . $pageOGLink . '" target="_blank" title="Share ' . $title . ' with your Facebook network"><div class="facebook-small"></div></a></li>';
                    $shareGoogle = '<li><a href="https://plus.google.com/share?url=' . $gameLink . '" target="_blank" title="Share ' . $title . ' with your Google Plus circles"><div class="gplus-small"></div></a></li>';
                    $shareTwitter = '<li><a href="https://twitter.com/share?text=Play ' . $title . ' on varyn.com:&url=' . $gameLink . '&via=varyn" target="_blank" title="Share ' . $title . ' with your Twitter followers"><div class="twitter-small"></div></a></li>';
                    $shareEmail = '<li><a href="mailto:?subject=Check out ' . $title . ' on varyn.com&body=I played ' . $title . ' on varyn.com and thought you would like to check it out: ' . $gameLink . '" title="Share ' . $title . ' by email"><div class="email-small"></div></a></li>';
                    echo('<div class="social-game-info"><ul>' . $shareFacebook . $shareGoogle . $shareTwitter . $shareEmail . '</ul></div><h2>' . $title . '</h2><p>' . $gameInfo->long_desc . '</p>');
                } else {
                    echo('<p>No information regarding your request. Please check your entry.</p>');
                }
                ?>
                </div>
                <div id="gameDeveloper">
                </div>
            </div>
        </div>
    </div>
</div>
<div id="playgame-BottomPanel" class="container marketing">
    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Other games you may like:</h3>
            </div>
        </div>
    </div>
    <div id="PlayPageGamesArea" class="row">
    </div>
    <div id="bottomAd" class="row">
        <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
        <!-- varyn Responsive -->
        <ins class="adsbygoogle"
             style="display:block"
             data-ad-client="ca-pub-9118730651662049"
             data-ad-slot="5571172619"
             data-ad-format="auto"></ins>
        <script>
            (adsbygoogle = window.adsbygoogle || []).push({});
        </script>
    </div>
</div>
<?php
include_once(VIEWS_ROOT . 'footer.php');
?>
<script type="text/javascript">

    var varynApp;

    head.ready(function() {
        siteConfiguration.gameId = '<?php echo($gameId);?>';
        pageParameters.width = '<?php echo($gameInfo->width);?>';
        pageParameters.height = '<?php echo($gameInfo->height);?>';
        pageParameters.pluginId = '<?php echo($gameInfo->game_plugin_id);?>';
        pageParameters.developerId = '<?php echo($gameInfo->developer_id);?>';
        varynApp = varyn(siteConfiguration);
        varynApp.initApp(varynPlayPage, pageParameters);
    });

    head.js("/common/modernizr.js", "/common/jquery.min.js", "/common/bootstrap.min.js", "/common/ie10-viewport-bug-workaround.js", "//platform.twitter.com/widgets.js", "https://apis.google.com/js/platform.js", "/common/enginesis.js", "/common/ShareHelper.js", "/common/commonUtilities.js", "/common/varyn.js", "/common/varynPlayPage.js");

</script>
</body>
</html>