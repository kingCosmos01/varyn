<hr/>
<footer class="container footer">
    <div class="panel panel-default">
        <div class="panel-heading">
            <span class="pull-left"><img src="/images/logosmall.png" border="0"/></span>
            <p class="pull-right"><a href="#">Back to top</a></p>
            <div class="social">
                <ul>
                    <li><a href="http://www.facebook.com/varyndev" title="Follow Varyn on Facebook"><div class="facebook sprite"></div></a></li>
                    <li><a href="http://twitter.com/@varyndev" title="Follow Varyn on Twitter"><div class="twitter sprite"></div></a></li>
                    <li><a href="https://plus.google.com/b/116018327404323787485" title="Follow Varyn on Google Plus"><div class="gplus sprite"></div></a></li>
                    <li><a href="https://www.linkedin.com/company/varyn-inc-" title="Follow Varyn on LinkedIn"><div class="linkedin sprite"></div></a></li>
                    <li><a href="http://www.youtube.com/channel/UC-TLi2sZGxNptz4p6tdbIqw" title="Follow Varyn on YouTube"><div class="youtube sprite"></div></a></li>
                    <li><a href="http://www.pinterest.com/varyndev/varyndev/" title="Follow Varyn on Pinterest"><div class="pinterest sprite"></div></a></li>
                    <li><a href="http://www.instagram.com/varyndev" title="Follow Varyn on Instagram"><div class="instagram sprite"></div></a></li>
                </ul>
            </div> <!-- end social -->
            <div id="footer-nav" class="text-center"><a href="/Privacy.php"><span class="glyphicon glyphicon-eye-open"></span> Privacy</a> <a href="/tos.php"><span class="glyphicon glyphicon-info-sign"></span> Terms</a> <a href="/About.php"><span class="glyphicon glyphicon-question-sign"></span> About Varyn</a> <a href="/contact.php"><span class="glyphicon glyphicon-comment"></span> Contact</a></div>
            <div><p style="font-size: smaller;"><br/>
                    Varyn makes games using technology that performs on the most popular platforms. Cross platform friendly technologies have created an opportunity to re-invent online games for an audience that moves seamlessly between desktop, tablet, and smart-phone.
                    Varyn creates games that play anytime and anywhere.
                    If you have a game you would like to see featured on our site please contact us using the <a href="/blog/#contact">contact form</a>.
                </p></div>
            <div class="nav navbar-inline">
                <ul>
                    <li><a href="/">Home</a></li>
                    <li><a href="/allgames.php">All Games</a></li>
                    <li><a href="/coupons.php">Coupons &amp; Offers</a></li>
                    <li><a href="/blog">Blog</a></li>
                    <li><a href="/About.php">About</a></li>
                </ul>
            </div>
            <div class="legalcopy"><br />
                <p class="copyright small text-center">Third-party trademarks are used solely for distributing the games herein and no license or affiliation is implied. All copyrights are held by the respective owners.</p>
                <p class="copyright small text-center">Copyright &copy; 2016 <a href="http://www.varyn.com">Varyn, Inc.</a>.  All rights reserved.</p>
            </div>
        </div>
    </div>
</footer>
<div id="fb-root"></div>
<script type="text/javascript">
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
    ga('create', 'UA-41765479-1', 'varyn.com');
    ga('send', 'pageview');
    <?php if (strlen($search) > 0) { ?>
    ga('send', 'event', 'game', 'search', '<?php echo($search);?>', 1);
    <?php } ?>
    (function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s);
        js.id = id;
        js.src = "//connect.facebook.net/en_US/sdk.js";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
</script>
