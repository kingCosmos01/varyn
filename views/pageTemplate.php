<?php
require_once('../../services/common.php');
processSearchRequest();
$page = '{pagename}';
$pageTitle = '{pagetitle}';
$pageDescription = '{pagedescription}';
$showSubscribe = getPostOrRequestVar('s', '0');
include_once(VIEWS_ROOT . 'header.php');
?>
<div class="container marketing">
    <div class="panel panel-primary panel-padded panel-gutter-2">
        {pagecontent}
    </div>
</div>
<?php
include_once(VIEWS_ROOT . 'footer.php');
?>
<script type="text/javascript">

    function initApp() {
        var showSubscribe = '<?php echo($showSubscribe);?>';

        if (showSubscribe == '1') {
            showSubscribePopup();
        }
    }

    head.ready(function() {
        initApp();
    });
    head.js("/common/modernizr.custom.74056.js", "/common/jquery.min.js", "/common/bootstrap.min.js", "/common/ie10-viewport-bug-workaround.js", "/common/common.js", "/common/enginesis.js", "/common/ShareHelper.js");
</script>
</body>
</html>
