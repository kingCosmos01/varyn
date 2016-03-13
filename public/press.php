<?php
    require_once('../services/common.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Varyn: Great games you can play anytime, anywhere</title>
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
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="mobile-web-app-capable" content="yes" />
    <meta name="description" content="Varyn makes games using technology that performs on the most popular platforms. Cross platform friendly technologies have created an opportunity to re-invent online games for an audience that moves seamlessly between desktop, tablet, and smart-phone.">
    <meta name="author" content="Varyn">
    <link href="/common/bootstrap.min.css" rel="stylesheet">
    <link href="/common/carousel.css" rel="stylesheet">
    <link href="/common/varyn.css" rel="stylesheet">
    <link rel="icon" href="/favicon.ico">
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
    <link rel="shortcut icon" href="/favicon-196x196.png">
    <meta property="fb:app_id" content="" />
    <meta property="fb:admins" content="726468316" />
    <meta property="og:title" content="Varyn: Great games you can play anytime, anywhere">
    <meta property="og:url" content="http://www.varyn.com">
    <meta property="og:site_name" content="Varyn">
    <meta property="og:description" content="Varyn makes games using technology that performs on the most popular platforms. Cross platform friendly technologies have created an opportunity to re-invent online games for an audience that moves seamlessly between desktop, tablet, and smart-phone.">
    <meta property="og:image" content="http://www.varyn.com/images/1200x900.png"/>
    <meta property="og:image" content="http://www.varyn.com/images/1024.png"/>
    <meta property="og:image" content="http://www.varyn.com/images/1200x600.png"/>
    <meta property="og:image" content="http://www.varyn.com/images/600x600.png"/>
    <meta property="og:image" content="http://www.varyn.com/images/2048x1536.png"/>
    <meta property="og:type" content="game"/>
    <meta name="twitter:card" content="photo"/>
    <meta name="twitter:site" content="@varyndev"/>
    <meta name="twitter:creator" content="@varyndev"/>
    <meta name="twitter:title" content="Varyn: Great games you can play anytime, anywhere"/>
    <meta name="twitter:image:src" content="http://www.varyn.com/images/600x600.png"/>
    <meta name="twitter:domain" content="varyn.com"/>
    <script src="/common/head.min.js"></script>
    <script type="text/javascript">

        head.ready(function() {
            initApp();
        });
        head.js("js/modernizr.custom.74056.js", "/js/jquery.min.js", "/js/bootstrap.min.js", "/js/ie10-viewport-bug-workaround.js", "/js/common.js", "/js/enginesis.js", "/js/ShareHelper.js");

        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
        ga('create', 'UA-41765479-1', 'auto');
        ga('send', 'pageview');
    </script>
</head>
<body>
<div class="navbar-wrapper">
    <div class="container">
        <nav class="navbar navbar-default navbar-static-top" role="navigation">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="/"><img src="/images/logosmall.png" border="0" /></a>
                </div>
                <div id="navbar" class="navbar-collapse collapse">
                    <ul class="nav navbar-nav">
                        <li><a href="/">Home</a></li>
                        <li><a href="/allgames.php">All Games</a></li>
                        <li><a href="/coupons.php">Coupons &amp; Offers</a></li>
                        <li><a href="/blog/?page_id=48">Blog</a></li>
                        <li class="active"><a href="/blog/#our-team">About</a></li>
                    </ul>
                    <form class="navbar-form navbar-right" role="search" method="GET" action="/allgames.php">
                        <div class="form-group">
                            <input type="text" class="form-control" placeholder="Search" name="q">
                        </div>
                        <button type="submit" class="btn btn-default"><span class="glyphicon glyphicon-search"></span></button>
                    </form>
                </div>
            </div>
        </nav>
    </div>
</div><!-- /.navbar-wrapper -->
<div class="container marketing">
    <div class="row">
        <div class="panel panel-default">
            <div class="menubar col-sm-3 col-md-3 sidebar">
                <div class="menu section">
                    <ul class="nav nav-sidebar">
                        <li id="root-item" class="level-0">
                            <ul class="level-1" id="root-list">
                                <li id="company-name-item" class="level-1"><a id="company-name-link" class="level-1" href="/blog/#our-team">Jumpy Dot</a>
                                    <ul class="level-2" id="company-name-list">
                                        <li id="company-name-factsheet-item" class="level-2"><a id="company-name-factsheet-link" href="#company-name-factsheet" class="level-2">Factsheet</a></li>
                                        <li id="company-name-description-item" class="level-2"><a id="company-name-description-link" href="#company-name-description" class="level-2">Description</a></li>
                                        <li id="company-name-history-item" class="level-2"><a id="company-name-history-link" href="#company-name-history" class="level-2">History</a></li>
                                        <li id="company-name-projects-item" class="level-2"><a id="company-name-projects-link" href="#company-name-projects" class="level-2">Projects</a></li>
                                        <li id="company-name-videos-item" class="level-2"><a id="company-name-videos-link" href="#company-name-videos" class="level-2">Videos</a></li>
                                        <li id="company-name-logo-icon-item" class="level-2"><a id="company-name-images-link" href="#company-name-images" class="level-2">Images, Logo &amp; Icon</a></li>
                                        <!-- <li id="company-name-awards-recognition-item" class="level-2"><a id="company-name-awards-recognition-link" href="#company-name-awards-recognition" class="level-2">Awards &amp; Recognition</a></li> -->
                                        <li id="company-name-selected-articles-item" class="level-2"><a id="company-name-selected-articles-link" href="#company-name-selected-articles" class="level-2">Articles &amp; Quotes</a></li>
                                        <li id="company-name-additional-links-item" class="level-2"><a id="company-name-additional-links-link" href="#company-name-additional-links" class="level-2">Additional Links</a></li>
                                        <li id="company-name-team-item" class="level-2"><a id="company-name-team-link" href="#company-name-team" class="level-2">Team &amp; Contacts</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
            <div role='content' class='content'>
                <div role="content" class="content col-sm-9 col-sm-offset-3 col-md-9 col-md-offset-3 main">
                    <h1 id="company-name" class="col-md-12"><img src="/images/JumpyDotLogo500x175.png" width="343" height="120" alt="Jumpy Dot"></h1>
                    <div id="factsheet" class="col-md-4">
                        <h2 id="company-name-factsheet">Factsheet</h2>
                        <p><strong>Developer:</strong><br>Jumpy Dot, LLC</p>
                        <p><strong>Founding date:</strong><br>July 1, 2013</p>
                        <p><strong>Website:</strong><br><a href="http://jumpydot.com" title="Jumpy Dot">JumpyDot.com</a></p>
                        <p><strong>Press / Business contact:</strong><br><a href="mailto:info@jumpydot.com">info@jumpydot.com</a></p>
                        <p><strong>Social:</strong><br><a href="https://twitter.com/jumpydot">twitter.com/jumpydot</a><br>
                            <a href="https://facebook.com/jumpydot">facebook.com/jumpydot</a><br>
                            <a href="callto:jumpydot">JumpyDot</a></p>
                        <p><strong>Releases:</strong><br><a href="projects/exampleproject/">exampleproject</a></p>
                        <p><strong>Address:</strong><br>TBA</p>
                        <p><strong>Phone:</strong><br>TBA</p>
                    </div>
                    <div class="col-md-8">
                        <div id="description">
                            <h2 id="company-name-description">Description</h2>
                            <p>Jumpy Dot was founded in 2013 by games and digital media industry veterans Dan Hart and John Foster.
                                Jumpy Dot is a New York based game developer focused on mobile and online development for select partners and directly to consumers.</p>
                            <p>Jumpy Dot believes in making games with high production value using familiar mechanics with twists that intrigue and delight players. They intend to make games with respect for our players that
                             provide value over the long term.</p>
                            <p>Jumpy Dots founders have been building online games as long as the consumer Internet has been around with the believe that while technologies advance, good play mechanics, well coded game engines, strong GUI, and great art succeed every time.</p>
                        </div>
                        <div id="history">
                            <h2 id="company-name-history">History</h2>
                            <h3 id="company-name-history-beginning">Beginning</h3>
                            <p>We met while Dan was at Yahoo and John was at Skyworks. We collaborated on two of the earliest try-and-buy games: bowling and golf. These games were a huge success on Yahoo.</p>
                            <p>Dan moved to NY and began an new online gaming venture at Viacom, hiring John's team to build Reno 911 Texas Hold'em Poker for Comedy Central. Afterward John joined Dan's team at MTV to help
                            build the MTV Networks online games platform, games microsites, and cutting edge online and multiplayer games.</p>
                            <p>In 2013 they joined forces again to for Jumpy Dot.</p>
                            <h3 id="company-name-history-currentg">Current</h3>
                            <p>At Jumpy Dot the first game was a memory match game build with HTML5. The game was successful branded and deployed at Bravo for the Real Housewives and Top Chef franchises.</p>
                            <p>They continue to innovate with mobile and browser-based games.</p>
                        </div>
                        <div id="projects">
                            <h2 id="company-name-projects">Projects</h2>
                            <ul>
                                <li><a href="projects/exampleproject/">Real Housewives Memory Challenge</a></li>
                                <li><a href="projects/exampleproject/">Top Chef Memory Challenge</a></li>
                                <li><a href="projects/exampleproject/">Match Master 3000</a></li>
                            </ul>
                        </div>
                    </div>
                    <div id="videos" class="col-md-12">
                        <h2 id="company-name-videos">Videos</h2>
                        <p>Sumo Volleyball was a game we acquired at MTV and then turned into several branded games. <a href="https://www.youtube.com/watch?v=919x4FClpVA" title="Sumo Volleyball Trailer on Youtube"> Here is a trailer of someone who is quite good at the game. It brings back such great memories!</a></p>
                        <div class="iframe-container">
                            <iframe src="//www.youtube.com/embed/919x4FClpVA" frameborder="0" allowfullscreen=""></iframe>
                        </div>
                        <p><br></p>
                        <p>Candystand Minigolf was a great game, beloved by many players around the world. <a href="https://www.youtube.com/watch?v=a_9bCHVNbIQ" title="Candystand Minigold recap"> I put a lot of my life into this building game.</a></p>
                        <div class="iframe-container">
                            <iframe src="//www.youtube.com/embed/a_9bCHVNbIQ" frameborder="0" allowfullscreen=""></iframe>
                        </div>
                    </div>
                    <div id="images" class="container-fluid col-md-12">
                        <h2 id="company-name-images">Images</h2>
                        <p>Download all screenshots &amp; photos as <strong> <a href="/images/press-images.zip" title="Press Images archive">.zip (660 KB)</a> </strong></p>
                        <p><a href="/assets/MatchMaster3000/ss1-640x477.jpg"><img class="img-responsive img-thumbnail" src="/assets/MatchMaster3000/ss1-640x477.jpg" alt="Match Master 3000" width="367" height="274"></a>
                            <a href="/assets/MatchMaster3000/ss2-640x477.jpg"><img class="img-responsive img-thumbnail" src="/assets/MatchMaster3000/ss2-640x477.jpg" alt="Match Master 3000" width="367" height="274"></a>
                            <a href="/assets/MatchMaster3000/ss3-640x477.jpg"><img class="img-responsive img-thumbnail" src="/assets/MatchMaster3000/ss3-640x477.jpg" alt="Match Master 3000" width="367" height="274"></a>
                            <a href="/assets/MatchMaster3000/ss5-640x477.jpg"><img class="img-responsive img-thumbnail" src="/assets/MatchMaster3000/ss5-640x477.jpg" alt="Match Master 3000" width="367" height="274"></a>
                        </p>
                    </div>
                    <div id="logo-icon" class="col-md-12">
                        <h2 id="company-name-logo-icon">Logo &amp; Icon</h2><p>Download logo files as <strong> <a href="/images/press-logo.zip" title="Logo &amp; Icon zip archive">.zip (637 KB)</a> </strong></p>
                            <p><a href="/images/JumpyDotLogo250x88.png" title="Logo"><img class="img-responsive img-thumbnail" src="/images/JumpyDotLogo250x88.png" alt="logo"></a>
                            <a href="/images/JumpyDotBallIcon250x250.png" title="Icon"><img class="img-responsive img-thumbnail" src="/images/JumpyDotBallIcon250x250.png" alt="icon"></a>
                        </p>
                    </div>
                    <!-- <div id="awards-recognition" class="col-md-12">
                        <h2 id="company-name-awards-recognition">Awards &amp; Recognition</h2>
                        <blockquote>
                            <ul>
                                <li>“Winner, XX awards.” - <em>game name, December 13, 2013</em></li>
                                <li>“Nominee, YY awards.” - <em>game name, December 13, 2013</em></li>
                            </ul>
                        </blockquote>
                    </div> -->
                    <div id="selected-articles" class="col-md-12">
                        <h2 id="company-name-selected-articles">Selected Articles &amp; Quotes</h2>
                        <blockquote>
                            <ul>
                                <li>“Incremental innovation can reinvent a well-worn play pattern.”<br>— <em>Dan Hart, <a href="http://www.gameballmedia.com/candy-crush-saga-the-king-of-casual-games/">Game Ball Media blog</a></em></li>
                            </ul>
                            <ul>
                                <li>“Whether HTML5 is the future of gaming remains to be determined, but we know Flash has lost its momentum.”<br>— <em>John Foster, <a href="http://varyn.com/blog">Varyn Blog</a></em></li>
                            </ul>
                        </blockquote>
                    </div>
                    <div id="additional-links" class="col-md-12">
                        <h2 id="company-name-additional-links">Additional Links</h2>
                        <p><h5><a href="https://www.jumpydot.com" title="JumpyDot games website">Play Our Games!</a></h5>We host a unique collection of playable games, interactive content, contests, and sweepstakes <a href="https://www.jumpydot.com" title="JumpyDot games website">here</a>.</p>
                        <p><h5><a href="https://www.jumpydot.com/blog/?page_id=48" title="Jumpy Dot blog">Games Blog</a></h5>We are blogging about trends and musings in the games industry <a href="https://www.jumpydot.com/blog/?page_id=48" title="Jumpy Dot blog">here</a>.</p>
                    </div>
                    <div id="team-repeating-collaborator" class="col-md-6">
                        <h2 id="company-name-team">Team</h2>
                        <p><strong>Dan Hart</strong><br><a href="https://link">Founder</a></p>
                        <p><strong>John Foster</strong><br><a href="https://link">Founder</a></p>
                        <p><strong>Elyse Fischer</strong><br><a href="https://link">Education Specialist</a></p>
                    </div>
                    <div id="contact" class="col-md-6">
                        <h2 id="company-name-contact">Contact</h2>
                        <p><strong>Inquiries</strong><br><a href="mailto:info@jumpydot.com">info@jumpydot.com</a></p>
                        <p><strong>Twitter</strong><br><a href="https://twitter.com/jumpydot">twitter.com/jumpydot</a></p>
                        <p><strong>Facebook</strong><br><a href="https://facebook.com/jumpydot">facebook.com/jumpydot</a></p>
                        <p><strong>Web</strong><br><a href="http://jumpydot.com" title="Jumpy Dot">jumpydot.com</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <hr/>
    <footer>
        <div class="panel panel-default">
            <div class="panel-heading">
                <span class="pull-left"><img src="/images/logosmall.png" border="0"/></span>
                <p class="pull-right"><a href="#">Back to top</a></p>
                <div class="social">
                    <ul>
                        <li><a href="http://www.facebook.com/jumpydot" title="Follow JumpyDot on Facebook"><div class="facebook sprite"></div></a></li>
                        <li><a href="http://twitter.com/@jumpydot" title="Follow JumpyDot on Twitter"><div class="twitter sprite"></div></a></li>
                        <li><a href="https://plus.google.com/b/113355649232770457323" title="Follow JumpyDot on Google Plus"><div class="gplus sprite"></div></a></li>
                        <li><a href="https://www.linkedin.com/company/jumpy-dot" title="Follow JumpyDot on LinkedIn"><div class="linkedin sprite"></div></a></li>
                        <li><a href="http://www.youtube.com/channel/UC45mWPk8hgiOuEu_b9LFggA" title="Follow JumpyDot on YouTube"><div class="youtube sprite"></div></a></li>
                        <li><a href="http://www.pinterest.com/jumpydot/jumpydot-games/" title="Follow JumpyDot on Pinterest"><div class="pinterest sprite"></div></a></li>
                    </ul>
                </div> <!-- end social -->
                <div id="footer-nav" class="text-center"><span class="glyphicon glyphicon-eye-open"></span> <a href="/privacy.php">Privacy</a> <span class="glyphicon glyphicon-info-sign"></span> <a href="/tos.php">Terms</a>  <span class="glyphicon glyphicon-question-sign"></span> <a href="/blog/#our-team">About JumpyDot</a> <span class="glyphicon glyphicon-comment"></span> <a href="/blog/#contact">Contact</a></div>
                <div><p style="font-size: smaller;"><br/>
                        At Jumpy Dot, we are building games for the masses – we aim for content that is fun for all ages and technology that performs on all the most popular platforms. Cross platform friendly technologies have created an opportunity to re-invent online games for an audience that moves seamlessly between computers, tablets, and smart-phones. Jumpy Dot aims to take advantage of that flow, creating games that play anywhere.
                        If you have a game you would like to see featured on our site please contact us using the <a href="/blog/#contact">contact form</a>.
                    </p></div>
                <div class="nav navbar-inline">
                    <ul>
                        <li><a href="/">Home</a></li>
                        <li><a href="/allgames.php">All Games</a></li>
                        <li><a href="/coupons.php">Coupons &amp; Offers</a></li>
                        <li><a href="/blog/?page_id=48">Blog</a></li>
                        <li><a href="/blog/#our-team">About</a></li>
                    </ul>
                </div>
                <p class="copyright small text-center">Copyright &copy; 2015 Jumpy Dot, LLC</p>
            </div>
        </div>
    </footer>
</div><!-- /.container -->
</body>
</html>