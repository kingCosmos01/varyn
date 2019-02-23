<?php
if ( ! isset($showSubscribe)) {
    $showSubscribe = false;
}
?>
<hr/>
<footer class="container footer">
    <div class="panel panel-default">
        <div class="panel-heading">
            <span class="pull-left"><img src="/images/logosmall.png" border="0"/></span>
            <p class="pull-right"><a href="#">Back to top</a></p>
            <div class="social">
                <ul>
                    <li><a href="//www.facebook.com/varyndev" title="Follow Varyn on Facebook"><div class="facebook sprite"></div></a></li>
                    <li><a href="//twitter.com/@varyndev" title="Follow Varyn on Twitter"><div class="twitter sprite"></div></a></li>
                    <li><a href="https://plus.google.com/b/116018327404323787485" title="Follow Varyn on Google Plus"><div class="gplus sprite"></div></a></li>
                    <li><a href="https://www.linkedin.com/company/varyn-inc-" title="Follow Varyn on LinkedIn"><div class="linkedin sprite"></div></a></li>
                    <li><a href="//www.youtube.com/channel/UC-TLi2sZGxNptz4p6tdbIqw" title="Follow Varyn on YouTube"><div class="youtube sprite"></div></a></li>
                    <li><a href="//www.pinterest.com/varyndev/varyndev/" title="Follow Varyn on Pinterest"><div class="pinterest sprite"></div></a></li>
                    <li><a href="//www.instagram.com/varyndev" title="Follow Varyn on Instagram"><div class="instagram sprite"></div></a></li>
                </ul>
            </div>
            <div id="footer-nav" class="text-center"><a href="/privacy/"><span class="glyphicon glyphicon-eye-open"></span> Privacy</a> <a href="/tos/"><span class="glyphicon glyphicon-info-sign"></span> Terms</a> <a href="/about/"><span class="glyphicon glyphicon-question-sign"></span> About Varyn</a> <a href="/contact/"><span class="glyphicon glyphicon-comment"></span> Contact</a></div>
            <div><p style="font-size: smaller;"><br/>
                    Varyn makes games using technology that performs on the most popular platforms. Cross platform friendly technologies have created an opportunity to re-invent online games for an audience that moves seamlessly between desktop, tablet, and smart-phone.
                    Varyn creates games that play anytime and anywhere.
                    If you have a game you would like to see featured on our site please contact us using the <a href="/contact/">contact form</a>.
                </p></div>
            <div class="nav navbar-inline">
                <ul>
                    <li><a href="/">Home</a></li>
                    <li><a href="/games/">Games</a></li>
                    <li><a href="/coupons/">Coupons &amp; Offers</a></li>
                    <li><a href="/blog/">Blog</a></li>
                    <li><a href="/about/">About</a></li>
                </ul>
            </div>
            <div class="legalcopy"><br />
                <p class="copyright small text-center">Third-party trademarks are used solely for distributing the games herein and no license or affiliation is implied. All copyrights are held by the respective owners.</p>
                <p class="copyright small text-center">Copyright &copy; 2019 <a href="//www.varyn.com">Varyn, Inc.</a>.  All rights reserved.</p>
            </div>
        </div>
    </div>
</footer>
<div id="fb-root"></div>
<script>
    var siteConfiguration = {
        siteId: <?php echo($siteId);?>,
        developerKey: "<?php echo($developerKey);?>",
        serverStage: "<?php echo($serverStage);?>",
        authToken: "<?php echo($authToken);?>",
        languageCode: navigator.language || navigator.userLanguage
    };
    var pageParameters = {
        showSubscribe: "<?php echo($showSubscribe);?>"
    };
</script>
<?php if (strlen($search) > 0) { ?>
<script type="text/javascript">
gtag({"event": "search", "q": "'<?php echo($search);?>'"});
</script>
<?php } ?>
