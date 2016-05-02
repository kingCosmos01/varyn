<?php
require_once('../services/common.php');
$page = 'template';
$search = getPostOrRequestVar('q', null);
if ($search != null) {
    header('location:/allgames.php?q=' . $search);
    exit;
}
$showSubscribe = getPostOrRequestVar('s', '0');
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
</head>
<body>
<?php
include_once('common/header.php');
?>
<div class="container marketing">
    <div class="row">
        <div class="panel panel-default">
            <div class="menubar col-sm-3 col-md-3 sidebar">
                <div class="menu section">
                    <ul class="nav nav-sidebar">
                        <li id="root-item" class="level-0">
                            <ul class="level-1" id="root-list">
                                <li id="company-name-item" class="level-1"><a id="company-name-link" class="level-1" href="/blog/#our-team">Varyn</a>
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
                    <h1 id="company-name" class="col-md-12"><img src="/images/varyn-shield-96x96.png" width="120" height="120" alt="Varyn, Inc. Logo"><img src="/images/VarynCardLogo_sm.png" width="200" height="200" alt="Varyn, Inc. Logo"></h1>
                    <div id="factsheet" class="col-md-4">
                        <h2 id="company-name-factsheet">Factsheet</h2>
                        <p><strong>Developer:</strong><br>Varyn, Inc.</p>
                        <p><strong>Founding date:</strong><br>June 1, 2013</p>
                        <p><strong>Website:</strong><br><a href="http://varyn.com" title="Varyn website">Varyn.com</a></p>
                        <p><strong>Press / Business contact:</strong><br><a href="mailto:info@varyn.com">info@varyn.com</a></p>
                        <p><strong>Social:</strong><br><a href="https://twitter.com/varyndev">twitter.com/varyndev</a><br>
                            <a href="https://facebook.com/varyndev">facebook.com/varyndev</a><br>
                            <a href="callto:varyn">Varyn</a></p>
                        <p><strong>Releases:</strong><br><a href="projects/exampleproject/">exampleproject</a></p>
                        <p><strong>Address:</strong><br>TBA</p>
                        <p><strong>Phone:</strong><br>TBA</p>
                    </div>
                    <div class="col-md-8">
                        <div id="description">
                            <h2 id="company-name-description">Description</h2>
                            <p>Varyn was founded in 2013 by games and digital media industry veteran John Foster.
                                Varyn is a New York based game developer focused on mobile and online software development for select partners and directly to consumers.</p>
                            <p>Varyn makes games with high production values using familiar mechanics with twists that intrigue and delight players.
                                The intent is to make games with respect for players that provide value, entertainment, and fun.</p>
                            <p>Varyn has been building online games as long as the consumer Internet has been around. We uphold the the belief that while technologies advance, good play mechanics, well coded games, strong UI, and great art always succeed.</p>
                        </div>
                        <div id="history">
                            <h2 id="company-name-history">History</h2>
                            <h3 id="company-name-history-beginning">Beginning</h3>
                            <p>Started programming in 1978 on a Data General mainframe using Basic. On that system I was exposed to a text-based adventure game called Star Trek.
                            Completely mesmerized by this experience I immediately began programming my own game titled "Subby", an underwater submarine adventure game.</p>
                            <p>While at NJIT in the early 1980's I experimented with game programming on the Atari 800 using Forth.</p>
                            <p>Later in my career, working at Sony while the Internet was exploding, I was hired away by Garry Kitchen to help build the back-end to a young but growing exponentially Candystand.com.
                                Got hooked on building games using Macromedia's Director and Shockwave. I built a ton of games over the next 6 years, including:</p>
                            <ul>
                                <li>NabiscoWorld Screen Saver Construction Kit</li>
                                <li>NabiscoWorld MahJongg</li>
                                <li>NabiscoWorld Mini Golf</li>
                                <li>Candystand Mini Golf</li>
                                <li>Candystand Win the Hole Thing</li>
                                <li>NabiscoWorld Mini Mini Golf</li>
                                <li>NabiscoWorld Fireworks Construction Kit</li>
                                <li>NabiscoWorld Shooting Gallery</li>
                                <li>Lincoln Texas Hold'em Poker</li>
                                <li>Reno 911 Texas Hold'em Poker</li>
                                <li>Postopia Mini Golf (multiplayer)</li>
                                <li>Rcade Mini Golf (multiplayer)</li>
                                <li>BMW X5 3D game suite &amp; microsite</li>
                                <li>Coolblasts Music Trivia</li>
                                <li>Fox Sports Genius (multiplayer)</li>
                                <li>iPlay Baseball (BREW, J2ME)</li>
                                <li>iPlay 3-point Basketball (BREW, J2ME)</li>
                                <li>World Winner Parlor Games</li>
                            </ul>
                            <p>Started Blackburst Media with Jeremy Mayes. Built the critically acclaimed by financially disastrous Zam BeeZee.</p>
                            <p>After working on several games for MTV Networks I was offered an opportunity to lead the online games division headed by Dan Hart.
                            At MTV I led the development of the MTV Networks online games platform, games microsites, and cutting edge online and multiplayer games.</p>
                            <p>We built a ton of games at MTV and contracted 3rd party developers to build even more. Some of our titles included:</p>
                            <ul>
                                <li>World Series of Pop Culture Trivia microsite</li>
                                <li>World Series of Pop Culture Trivia Dome (multiplayer)</li>
                                <li>MTV Backchannel - The Hills</li>
                                <li>Sumo Volleyball (multiplayer)</li>
                                <li>The Daily Show Trivia Game</li>
                                <li>Comedy Central Redneck Games</li>
                                <li>Beavis &amp; Butthead Games</li>
                                <li>VH1 Sudoku</li>
                            </ul>
                            <p>In 2010 MTV shut down the online games we built from scratch so I returned my focus to Blackburst.
                            We built several games on our own and won a large contract to build the social multiplayer game MTV's The Flirt Game.
                            After MTV realized they were not able to build an audience large enough to sustain the social game operating costs they cancelled the project, and we ran out of money.</p>
                            <p>In 2013 I decided to go at it on my own and formed Varyn.</p>
                            <h3 id="company-name-history-currentg">Current</h3>
                            <p>At Varyn the first game was a memory match game build with HTML5. The game was successful branded and deployed at Bravo for the Real Housewives and Top Chef franchises.</p>
                            <p>I continue to experiment with HTML5 and Unity to build mobile and browser-based games.</p>
                        </div>
                        <div id="projects">
                            <h2 id="company-name-projects">Projects</h2>
                            <ul>
                                <li><a href="http://varyn.com/play/1083">Match Master 3000</a></li>
                                <li><a href="projects/exampleproject/">Real Housewives Memory Challenge</a></li>
                                <li><a href="projects/exampleproject/">Top Chef Memory Challenge</a></li>
                                <li><a href="projects/exampleproject/">Stranded!</a></li>
                                <li><a href="projects/exampleproject/">Closest to the Pin</a></li>
                            </ul>
                        </div>
                    </div>
                    <div id="videos" class="col-md-12">
                        <h2 id="company-name-videos">Videos</h2>
                        <div>
                            <h2 id="company-name-videos">Sumo Volleyball</h2>
                            <p>Sumo Volleyball was a game we acquired at MTV and then turned into several branded games. <a href="https://www.youtube.com/watch?v=919x4FClpVA" title="Sumo Volleyball Trailer on Youtube"> Here is a trailer of someone who is quite good at the game. It brings back such great memories!</a></p>
                            <div class="iframe-container">
                                <iframe src="//www.youtube.com/embed/919x4FClpVA" frameborder="0" allowfullscreen=""></iframe>
                            </div>
                        </div>
                        <div>
                            <h2 id="company-name-videos">Candystand Minigolf</h2>
                            <p>Candystand Minigolf was a great game, beloved by many players around the world. <a href="https://www.youtube.com/watch?v=a_9bCHVNbIQ" title="Candystand Minigolf recap"> I put a lot of my life into this building game.</a></p>
                            <div class="iframe-container">
                                <iframe src="//www.youtube.com/embed/a_9bCHVNbIQ" frameborder="0" allowfullscreen=""></iframe>
                            </div>
                        </div>
                        <div>
                            <h2 id="company-name-videos">NabiscoWorld Mini-Minigolf</h2>
                            <p>NabiscoWorld Mini-Minigolf was a redux based on the success of the prior minigolf games. <a href="https://www.youtube.com/watch?v=s9XupuRaJLc" title="NabiscoWorld Mini-Minigolf recap"> An interesting take on what it would be like to shrink down and then play minigolf.</a></p>
                            <div class="iframe-container">
                                <iframe src="//www.youtube.com/embed/s9XupuRaJLc" frameborder="0" allowfullscreen=""></iframe>
                            </div>
                        </div>
                    </div>
                    <div id="images" class="container-fluid col-md-12">
                        <h2 id="company-name-images">Images</h2>
                        <p>Download all screenshots &amp; photos as <strong> <a href="/images/press-images.zip" title="Press Images archive">.zip (660 KB)</a> </strong></p>
                        <p><a href="//www.enginesis.com/games/MatchMaster3000/images/ss1-640x477.jpg"><img class="img-responsive img-thumbnail" src="//www.enginesis.com/games/MatchMaster3000/images/ss1-640x477.jpg" alt="Match Master 3000" width="367" height="274"></a>
                            <a href="//www.enginesis.com/games/MatchMaster3000/images/ss2-640x477.jpg"><img class="img-responsive img-thumbnail" src="//www.enginesis.com/games/MatchMaster3000/images/ss2-640x477.jpg" alt="Match Master 3000" width="367" height="274"></a>
                            <a href="//www.enginesis.com/games/MatchMaster3000/images/ss3-640x477.jpg"><img class="img-responsive img-thumbnail" src="//www.enginesis.com/games/MatchMaster3000/images/ss3-640x477.jpg" alt="Match Master 3000" width="367" height="274"></a>
                            <a href="//www.enginesis.com/games/MatchMaster3000/images/ss5-640x477.jpg"><img class="img-responsive img-thumbnail" src="//www.enginesis.com/games/MatchMaster3000/images/ss5-640x477.jpg" alt="Match Master 3000" width="367" height="274"></a>
                        </p>
                    </div>
                    <div id="logo-icon" class="col-md-12">
                        <h2 id="company-name-logo-icon">Logo &amp; Icon</h2><p>Download logo files as <strong> <a href="/images/press-logo.zip" title="Logo &amp; Icon zip archive">.zip (637 KB)</a> </strong></p>
                            <p><a href="/images/v-250.png" title="Logo"><img class="img-responsive img-thumbnail" src="/images/v-250.png" alt="logo"></a>
                            <a href="/images/VarynCardLogo_sm.png" title="Icon"><img class="img-responsive img-thumbnail" src="/images/VarynCardLogo_sm.png" alt="icon"></a>
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
                        <p><h5><a href="https://www.varyn.com" title="JumpyDot games website">Play Our Games!</a></h5>We host a unique collection of playable games, interactive content, contests, and sweepstakes <a href="https://www.varyn.com" title="Varyn games website">here</a>.</p>
                        <p><h5><a href="https://www.varyn.com/blog/?page_id=48" title="Varyn blog">Games Blog</a></h5>We are blogging about trends and musings in the games industry <a href="https://www.varyn.com/blog/?page_id=48" title="Varyn blog">here</a>.</p>
                    </div>
                    <div id="team-repeating-collaborator" class="col-md-6">
                        <h2 id="company-name-team">Team</h2>
                        <p><strong>John Foster</strong><br><a href="https://link">Founder</a></p>
                    </div>
                    <div id="contact" class="col-md-6">
                        <h2 id="company-name-contact">Contact</h2>
                        <p><strong>Inquiries</strong><br><a href="mailto:info@varyn.com">info@varyn.com</a></p>
                        <p><strong>Twitter</strong><br><a href="https://twitter.com/varyndev">twitter.com/varyndev</a></p>
                        <p><strong>Facebook</strong><br><a href="https://facebook.com/varyndev">facebook.com/varyndev</a></p>
                        <p><strong>Web</strong><br><a href="http://varyn.com" title="Varyn website">Varyn.com</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    include_once('common/footer.php');
    ?>
</div><!-- /.container -->
<script type="text/javascript">

    head.ready(function() {
    });
    head.js("/common/modernizr.js", "/common/jquery.min.js", "/common/bootstrap.min.js", "/common/ie10-viewport-bug-workaround.js", "//platform.twitter.com/widgets.js", "https://apis.google.com/js/platform.js", "/common/enginesis.js", "/common/ShareHelper.js", "/common/varyn.js");
</script>
</body>
</html>