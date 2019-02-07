<?php
    /**
     * Handle a request approval.
     *  - Friend request
     *  - Team request
     *  - Quest request
     * @Date: 1/11/16
     */
    require_once('../../services/common.php');
    $debug = (int) strtolower(getPostOrRequestVar('debug', 0));
    $page = 'requestConfirmation';
    $search = getPostOrRequestVar('q', null);
    if ($search != null) {
        header('location:/games/?q=' . $search);
        exit;
    }
    processTrackBack();
    $user_id = getPostOrRequestVar('u', 0);
    $site_id = getPostOrRequestVar('s', 0);
    $token = getPostOrRequestVar('t', '');
    $requestId = getPostOrRequestVar('r', '');
    $approval = getPostOrRequestVar('a', '');
    $action = getPostOrRequestVar('action', '');
    $errorMessage = '';

    if ($isLoggedIn) {
        $userInfo = getSiteUserCookieObject();
        $authToken = $userInfo->authtok;
        $user_id = $userInfo->user_id; // only use the user_id that is logged in
        $site_id = $userInfo->site_id;
    }
    $pageTitle = 'Process Request | Varyn';
    include_once(VIEWS_ROOT . 'header.php');
?>
<div class="container marketing">
    <div class="panel panel-info panel-padded">
        <h1>Request</h1>
        <p>Your request is being processed.</p>
        <p><a href="/profile/">Profile</a></p>
        <p><a href="mailto:support@varyn.com">Contact Support</a></p>
    </div>
    <div id="bottomAd" class="row">
        <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
        <!-- Varyn Responsive -->
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
<script>

    var varynApp;

    head.ready(function() {
        var siteConfiguration = {
                siteId: <?php echo($siteId);?>,
                gameId: 0,
                gameGroupId: 0,
                serverStage: "<?php echo($serverStage);?>",
                languageCode: navigator.language || navigator.userLanguage,
                developerKey: '<?php echo($developerKey);?>',
                facebookAppId: '<?php echo($socialServiceKeys[2]['app_id']);?>',
                authToken: '<?php echo($authToken);?>'
            },
            resetPasswordPageParameters = {
                errorFieldId: "<?php echo($errorFieldId);?>",
                inputFocusId: "<?php echo($inputFocusId);?>",
                showSubscribe: "<?php echo($showSubscribe);?>"
            };
        varynApp = varyn(siteConfiguration);
    });

    head.js("/common/modernizr.js", "/common/jquery.min.js", "/common/bootstrap.min.js", "/common/ie10-viewport-bug-workaround.js", "//platform.twitter.com/widgets.js", "https://apis.google.com/js/platform.js", "/common/enginesis.js", "/common/ShareHelper.js", "/common/commonUtilities.js", "/common/varyn.js", "/common/varynResetPasswordPage.js");

</script>
</body>
</html>
