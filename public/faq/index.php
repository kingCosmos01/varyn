<?php
require_once('../../services/common.php');
processSearchRequest();
$page = 'home';
$pageTitle = 'Frequently asked questions';
$pageDescription = 'Here are the questions asked most by our users at Varyn.com.';
$showSubscribe = getPostOrRequestVar('s', '0');
include_once(VIEWS_ROOT . 'header.php');
?>
<div class="container marketing">
    <div class="panel panel-primary panel-padded">
        <h2>Frequently Asked Questions</h2>
        <p>Need help? Here are the answers to the most common questions asked by our users.</p>
        <ul class="faq">
            <li class="faq-question">Do I need to Use Facebook to Login?<p class="faq-answer">We support login via Facebook, Twitter, Google, or you can create an account with us using your email address.</p></li>
            <li class="faq-question">I forgot my password, how do I get it?<p class="faq-answer">You must use the forgot password form located on the logged out <a href="/profile/">profile page</a>.</p></li>
            <li class="faq-question">Can I Block Another User From Contacting Me?<p class="faq-answer">Not at this time but that is a feature we are working on.</p></li>
            <li class="faq-question">I Earned Coins, Where Are They?<p class="faq-answer">Your coins are stored in your account. Make sure you are logged in.</p></li>
            <li class="faq-question">My Score Was On The Leader board, Why Is It No Longer There?<p class="faq-answer">Our leader boards refresh every week. You can view the all-time leader board to see how you rank against all players.</p></li>
            <li class="faq-question">There was a problem with a game I played! How do I report it?<p class="faq-answer">Send us email at support@varyn.com and include the game id or URL from where you played it along with any details about what happened.</p></li>
        </ul>
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
</div><!-- /.marketing -->
<?php
include_once(VIEWS_ROOT . 'footer.php');
?>
<script type="text/javascript">

    var enginesisSiteId = <?php echo($siteId);?>,
        serverStage = "<?php echo($serverStage);?>",
        enginesisGameListId = 6,
        enginesisHomePagePromoId = 2;

    function initApp() {
        var serverHostDomain = 'varyn' + serverStage + '.com',
            showSubscribe = '<?php echo($showSubscribe);?>';

        document.domain = serverHostDomain;
        window.EnginesisSession = enginesis(enginesisSiteId, 0, 0, 'enginesis.' + serverHostDomain, '', '', 'en', enginesisCallBack);
        EnginesisSession.gameListListGames(enginesisGameListId, null);
        EnginesisSession.promotionItemList(enginesisHomePagePromoId, EnginesisSession.getDateNow(), null);
        if (showSubscribe == '1') {
            showSubscribePopup();
        }
    }

    function enginesisCallBack (enginesisResponse) {
        var succeeded,
            errorMessage;

        if (enginesisResponse != null && enginesisResponse.fn != null) {
            succeeded = enginesisResponse.results.status.success;
            errorMessage = enginesisResponse.results.status.message;
            switch (enginesisResponse.fn) {
                case "NewsletterAddressAssign":
                    handleNewsletterServerResponse(succeeded);
                    break;
                case "PromotionItemList":
                    if (succeeded == 1) {
                        promotionItemListResponse(enginesisResponse.results.result);
                    }
                    break;
                case "GameListListGames":
                    if (succeeded == 1) {
                        gameListGamesResponse(enginesisResponse.results.result, "HomePageGamesArea", null, "title");
                    }
                    break;
                default:
                    break;
            }
        }
    }
</script>
</body>
</html>