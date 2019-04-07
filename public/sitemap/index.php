<?php
require_once('../../services/common.php');
$search = getPostOrRequestVar('q', null);
if ($search != null) {
    header('location:/games/?q=' . $search);
    exit;
}
$page = 'home';
$pageTitle = 'Site Map';
$pageDescription = 'Site map index for Varyn.com';
$showSubscribe = getPostOrRequestVar('s', '0');
include_once(VIEWS_ROOT . 'header.php');
?>
<div class="container marketing">
    <div class="panel panel-primary panel-padded panel-gutter-2">
        <div id="sitemap">
<h2>Varyn.com Site Map</h2>
<div id="section-Varyn">
  <h3>Varyn</h3>
  <ul>
<li><a href="/about/">About Varyn</a> Learn more about who is Varyn, our mission, and what we stand for.</li>

<li><a href="/contact/">Contact Varyn</a> Contact us if you have something to say, you need help, or if you are interested in more information about what we do.</li>

<li><a href="/press/">Varyn Press Kit</a> Find assets and collatoral here. If you are interested in working with Varyn or writing about what we do you will find what you need in this section of the website.</li>

<li><a href="/privacy/">Privacy Policy</a> Varyn privacy policy. Varyn is concerned for your privacy and protecting your data. We put forward this policy on data privacy to help you understand what we do and what control you have.</li>

<li><a href="/tos/">Terms of Use</a> Varyn terms of service regarding the use of this website. Please review these terms of use before using this website.</li>
  </ul>
</div>
<div id="section-Games">
  <h3>Games</h3>
  <ul>
<li><a href="/allgames/">All Games at Varyn.com</a> Discover the games we offer or search for the game you are looking for.</li>

<li><a href="/faq/">Frequently asked questions</a> Here are the questions asked most by our users at Varyn.com.</li>

<li><a href="/featured/">Hot games, contests, and tournaments</a> Discover the most recent content added to the site. We offer games, contests for prizes, and tournaments.</li>

<li><a href="/games/">Experimental Games at Varyn.com</a> Check out some of the games that are in development, betas, experiments in new game play ideas, and the research and development going on at Varyn.com.</li>

<li><a href="/profile/">Profile</a> View your player profile or review other followers and players at Varyn.com.</li>
  </ul>
</div>
<div id="section-Blog">
  <h3>Blog</h3>
  <ul>
<li><a href="/blog/">The Making of Match Master</a> The Match Master project started out with some bold ideas. We wanted to push the bar as high as we could with HTML5. This postmortem analyzes our development experience.</li>
  </ul>
</div>
</div>
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
