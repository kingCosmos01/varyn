<?php
require_once('../../services/common.php');
require_once('../../views/sections.php');
$page = 'games';
$pageTitle = 'All games at Varyn.com';
$pageDescription = 'Check out some of the games that are in development, betas, experiments in new game play ideas, and the research and development going on at Varyn.com.';
$search = fullyCleanString(getPostOrRequestVar('q', ''));
$showSubscribe = getPostOrRequestVar('s', '0');
include_once(VIEWS_ROOT . 'header.php');
?>
<div class="container">
    <?php
    if ($search != '') {
        ?>
        <div class="card card-light m-4 px-4">
            <div class="card-heading">
                <h3 class="card-title"><small class="text-muted">Search for:</small> <?php echo($search);?></h3>
            </div>
        </div>
    <?php
        buildSearchGamesSection($search);
    }
    buildAllGamesSection();
    ?>
    <div id="bottomAd" class="row">
    <?php
    $adProvider = 'google';
    include_once(VIEWS_ROOT . 'ad-spot.php');
    ?>
    </div>
</div>
<?php
include_once(VIEWS_ROOT . 'footer.php');
?>
<script>

    var varynApp;

    head.ready(function() {
        varynApp = varyn(siteConfiguration);
        varynApp.initApp(varynAllGamesPage, pageParameters);
    });

    head.js("/common/modernizr.js", "/common/bootstrap.bundle.min.js", "/common/enginesis.js", "/common/ShareHelper.js", "/common/commonUtilities.js", "/common/varyn.js", "/common/varynAllGamesPage.js");

</script>
</body>
</html>