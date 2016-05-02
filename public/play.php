<?php
    require_once('../services/common.php');
    $page = 'play';
    $search = getPostOrRequestVar('q', null);
    if ($search != null) {
        header('location:/allgames.php?q=' . $search);
        exit;
    }
    $showSubscribe = getPostOrRequestVar('s', '0');
    $gameId = getPostOrRequestVar('id', '');
    if ($gameId == '') {
        $gameId = getPostOrRequestVar('gameid', '');
    }
    if ($gameId == '') {
        $gameId = getPostOrRequestVar('game_id', '');
    }
    if ($gameId == '') {
        $gameId = getPostOrRequestVar('gameId', '');
    }
    if ($gameId == '') {
        $gameId = getPostOrRequestVar('gameName', '');
    }
    if ($gameId == '') {
        $gameId = getPostOrRequestVar('gamename', '');
    }
    if ($gameId == '') {
        $gameId = getPostOrRequestVar('g', '');
    }
    if ($gameId == '') {
        header("Location: /allgames.php");
    }
    $gameWidth = 1024;
    $gameHeight = 768;
    $gameDescription = '';
    $gameInfo = null;
    $receivedGameInfo = false;
    $gameContainerHTML = '';
    $isPlayBuzzSpecialCase = false;

    // get game info: we need the game info immediately in order to build the page
    // TODO: GameGet only works for numeric game_id, if game name we need to call GameGetByName

    if (is_numeric($gameId)) {
        $gameInfo = $enginesis->gameGet($gameId);
    } elseif ( ! empty($gameId)) {
        $gameInfo = $enginesis->gameGetByName($gameId);
    } else {
        header("Location: /allgames.php");
        exit(0);
    }
    if ($gameInfo != null) {
        $receivedGameInfo = true;
        $gameId = $gameInfo->game_id;
        $gameName = $gameInfo->game_name;
        $title = $gameInfo->title;
        $gameImg = 'http://enginesis.varyn.com/games/' . $gameName . '/images/600x450.png';
        $gameImg2 = 'http://enginesis.varyn.com/games/' . $gameName . '/images/586x308.png';
        $gameThumb = 'http://enginesis.varyn.com/games/' . $gameName . '/images/50x50.png';
        $gameLink = 'http://www.varyn.com/play.php?gameid=' . $gameId;
        $gameOGLink = 'http://www.varyn.com/play/' . $gameId;
        $gameDesc = $gameInfo->short_desc;
        $gameContainerHTML = setGameContainer($gameInfo, $enginesis->getServiceRoot(), $siteId, $gameId);
    } else {
        header("Location: /missing.php");
        exit(0);
    }

    function setGameContainer ($gameInfo, $enginesisServer, $siteId, $gameId) {
        // generate the necessary HTML to setup the game container div

        $width = $gameInfo->width;
        $height = $gameInfo->height;
        $bgcolor = '#' . $gameInfo->bgcolor;
        $pluginId = $gameInfo->game_plugin_id;
        $allowScroll = $gameInfo->popup == 0 ? 'no' : 'yes';
        if ($pluginId == 9) { // embed
            $gameContainerHTML = '<div id="gameContainer-iframe" style="position: relative; margin: 0 auto; width: 100%; height: 100%;">' . $gameInfo->game_link . '</div>';
        } else {
            if ($pluginId == 10) { // canvas
                if (strpos($gameInfo->game_link, '://') > 0) {
                    $gameLink = $gameInfo->game_link;
                } else {
                    $gameLink = $enginesisServer . '/games/' . $gameInfo->game_name . '/' . $gameInfo->game_link;
                }
            } else {
                $gameLink = $enginesisServer . '/games/play.php?site_id=' . $siteId . '&game_id=' . $gameId;
            }
            $gameContainerHTML = '<iframe id="gameContainer-iframe" src="' . $gameLink . '" allowfullscreen scrolling="' . $allowScroll . '" width="' . $width . '" height="' . $height . '" border="0"></iframe>';
        }
        return $gameContainerHTML;
    }

    // TODO: Setup Facebook app and add     <meta property="fb:app_id" content="###" />
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php echo($title);?> on Varyn.com</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Access-Control-Allow-Origin" content="*">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta http-equiv="cache-control" content="max-age=0" />
    <meta http-equiv="cache-control" content="no-cache" />
    <meta http-equiv="expires" content="0" />
    <meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT" />
    <meta http-equiv="pragma" content="no-cache" />
    <meta name="format-detection" content="telephone=no" />
    <meta name="viewport" content="width=device-width, initial-scale=1, minimal-ui">
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="mobile-web-app-capable" content="yes" />
    <meta name="description" content="<?php echo($gameDesc);?>">
    <meta name="author" content="Varyn">
    <link href="/common/bootstrap.min.css" rel="stylesheet">
    <link href="/common/carousel.css" rel="stylesheet">
    <link href="/common/varyn.css" rel="stylesheet">
    <link rel="icon" href="<?php echo($gameThumb);?>">
    <link rel="icon" type="image/png" href="/favicon-48x48.png" sizes="48x48"/>
    <link rel="icon" type="image/png" href="/favicon-196x196.png" sizes="196x196">
    <link rel="icon" type="image/png" href="/favicon-160x160.png" sizes="160x160">
    <link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96">
    <link rel="icon" type="image/png" href="/favicon-16x16.png" sizes="16x16">
    <link rel="icon" type="image/png" href="/favicon-32x32.png" sizes="32x32">
    <link rel="apple-touch-icon" href="/apple-touch-icon-60x60.png" sizes="60x60"/>
    <link rel="apple-touch-icon" href="/apple-touch-icon-72x72.png" sizes="72x72"/>
    <link rel="apple-touch-icon" href="/apple-touch-icon-76x76.png"/>
    <link rel="apple-touch-icon" href="/apple-touch-icon-76x76.png" sizes="76x76"/>
    <link rel="apple-touch-icon" href="/apple-touch-icon-114x114.png" sizes="114x114"/>
    <link rel="apple-touch-icon" href="/apple-touch-icon-120x120.png" sizes="120x120"/>
    <link rel="apple-touch-icon" href="/apple-touch-icon-152x152.png" sizes="152x152"/>
    <link rel="shortcut icon" href="<?php echo($gameThumb);?>">
    <meta property="og:title" content="<?php echo($title);?> on varyn.com">
    <meta property="og:url" content="<?php echo($gameOGLink);?>">
    <meta property="og:site_name" content="varyn">
    <meta property="og:description" content="<?php echo($gameDesc);?>">
    <meta property="og:image" content="<?php echo($gameImg);?>"/>
    <meta property="og:image" content="<?php echo($gameImg2);?>"/>
    <meta property="og:image" content="<?php echo($gameThumb);?>"/>
    <meta property="og:type" content="game"/>
    <meta name="twitter:card" content="photo"/>
    <meta name="twitter:site" content="@varyndev"/>
    <meta name="twitter:creator" content="@varyndev"/>
    <meta name="twitter:title" content="<?php echo($title);?> on Varyn.com"/>
    <meta name="twitter:image:src" content="<?php echo($gameImg);?>"/>
    <meta name="twitter:domain" content="varyn.com"/>
    <script src="/common/head.min.js"></script>
</head>
<body>
<?php
    include_once('common/header.php');
?>
<div id="topContainer" class="container top-promo-area">
    <div id="gameContainer" class="row"><?php echo($gameContainerHTML);?></div>
    <div id="playgame-InfoPanel" class="row">
        <div class="panel panel-default">
            <div class="panel-body">
                <div id="gameInfo">
                <?php
                if ($receivedGameInfo) {
                    $shareFacebook = '<li><a href="https://www.facebook.com/sharer/sharer.php?u=' . $gameOGLink . '" target="_blank" title="Share ' . $title . ' with your Facebook network"><div class="facebook-small"></div></a></li>';
                    $shareGoogle = '<li><a href="https://plus.google.com/share?url=' . $gameLink . '" target="_blank" title="Share ' . $title . ' with your Google Plus circles"><div class="gplus-small"></div></a></li>';
                    $shareTwitter = '<li><a href="http://twitter.com/share?text=Play ' . $title . ' on varyn.com:&url=' . $gameLink . '&via=varyn" target="_blank" title="Share ' . $title . ' with your Twitter followers"><div class="twitter-small"></div></a></li>';
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
</div><!-- /top-promo-area -->
<div id="playgame-BottomPanel" class="container marketing">
    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Other games you may like:</h3>
            </div>
        </div>
    </div><!-- row -->
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
</div><!-- /.container -->
<?php
    include_once('common/footer.php');
?>
<script type="text/javascript">

    var varynApp;

    head.ready(function() {
        var siteConfiguration = {
                siteId: '<?php echo($siteId);?>',
                gameId: '<?php echo($gameId);?>',
                serverStage: '<?php echo($stage);?>',
                languageCode: navigator.language || navigator.userLanguage
            },
            pageParameters = {
                showSubscribe: '<?php echo($showSubscribe);?>',
                width: '<?php echo($gameInfo->width);?>',
                height: '<?php echo($gameInfo->height);?>',
                pluginId: '<?php echo($gameInfo->game_plugin_id);?>',
                developerId: '<?php echo($gameInfo->developer_id);?>'
            };

        varynApp = varyn(siteConfiguration);
        varynApp.initApp(varynPlayPage, pageParameters);
    });

    head.js("/common/modernizr.js", "/common/jquery.min.js", "/common/bootstrap.min.js", "/common/ie10-viewport-bug-workaround.js", "//platform.twitter.com/widgets.js", "https://apis.google.com/js/platform.js", "/common/enginesis.js", "/common/ShareHelper.js", "/common/varyn.js", "/common/varynPlayPage.js");

</script>
</body>
</html>