<?php
require_once('../common/common.php');

$query = '';
if (isset($_REQUEST['q'])) {
    $query = $_REQUEST['q'];
}

 ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Varyn Games | More Games</title>
    <meta name="title" content="Varyn Games More Games" />
    <meta name="description" content="Varyn Games the Best Place to Play Games Online and On the Go!" />
    <link rel="icon" type="image/png" href="/images/logosmall.png" />
    <link rel="icon" type="image/x-icon" href="/favicon.ico" />
    <link rel="image_src" href="/images/VarynCardLogo.png" />
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <META NAME="Description" CONTENT="Varyn"/>
    <META NAME="Keywords" CONTENT="Varyn"/>
    <META NAME="Author" content="Varyn"/>
    <META NAME="Copyright" content="Copyright © 2013 Varyn. All rights reserved."/>
    <meta name="google-site-verification" content="" />
    <meta property="og:title" content="Varyn Games More Games" />
    <meta property="og:description" content="Varyn Games the Best Place to Play Games Online and On the Go!" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="http://www.varyn.com" />
    <meta property="og:image" content="http://www.varyn.com/images/share_img_0.jpg" />
    <meta property="og:image" content="http://www.varyn.com/images/share_img_1.jpg" />
    <meta property="og:image" content="http://www.varyn.com/images/share_img_2.jpg" />
    <meta property="og:site_name" content="Varyn" />
    <meta property="og:type" content="website" />
    <meta property="fb:admins" content="726468316" />
    <meta property="fb:app_id" content="" />
    <script type="text/javascript" src="/common/head.min.js"></script>
    <link rel="stylesheet" href="http://code.jquery.com/mobile/1.2.1/jquery.mobile-1.2.1.min.css" />
    <link rel="stylesheet" href="/common/main.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="/common/nivo-slider.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="/common/themes/dark/default.css" type="text/css" media="screen" />
</head>
<body>
<div id="page_container">
    <?php
    $page = 'AllGames';
    include_once('../common/header.php');
    ?>
    <div id="top_promo">
        <div class="slider-wrapper theme-default">
            <div id="slider" class="nivoSlider">
                <a href="/play.php?game_id=1053"><img src="/images/promos/promo1.jpg" data-thumb="/images/promos/promo1-thm.jpg" alt="" title="#htmlcaption1" /></a>
                <a href="/play.php?game_id=1002"><img src="/images/promos/promo2.jpg" data-thumb="/images/promos/promo2-thm.jpg" alt="" title="#htmlcaption2" /></a>
                <a href="/play.php?game_id=1044"><img src="/images/promos/promo3.jpg" data-thumb="/images/promos/promo3-thm.jpg" alt="" title="#htmlcaption3" /></a>
                <a href="/play.php?game_id=1055"><img src="/images/promos/promo4.jpg" data-thumb="/images/promos/promo4-thm.jpg" alt="" title="#htmlcaption4" /></a>
                <a href="/play.php?game_id=1043"><img src="/images/promos/promo5.jpg" data-thumb="/images/promos/promo5-thm.jpg" alt="" title="#htmlcaption5" /></a>
                <a href="/play.php?game_id=1061"><img src="/images/promos/promo6.jpg" data-thumb="/images/promos/promo6-thm.jpg" alt="" title="#htmlcaption6" /></a>
            </div>
            <div id="htmlcaption1" class="nivo-html-caption">
                Your <em>friends</em> are getting eaten by zombies! <a href="/play.php?game_id=1053">Save Them!</a>.
            </div>
            <div id="htmlcaption2" class="nivo-html-caption">
                <strong>Mah Jong</strong> is really <em>popular</em> game <a href="/play.php?game_id=1002">Play it now!</a>.
            </div>
            <div id="htmlcaption3" class="nivo-html-caption">
                <strong>Kanye West</strong> took all the awards! <a href="/play.php?game_id=1044">Grab them back now!</a>.
            </div>
            <div id="htmlcaption4" class="nivo-html-caption">
                Pigeons have overrun the stage and are trying to poop on the bass player! <a href="/play.php?game_id=1055">Help him now!</a>.
            </div>
            <div id="htmlcaption5" class="nivo-html-caption">
                The Ice King holds an icy grip on the North Pole <a href="/play.php?game_id=1043">Thaw him now!</a>.
            </div>
            <div id="htmlcaption6" class="nivo-html-caption">
                Test your <em>golf skills</em>! The pressure is on you, and you have only one swing. <a href="/play.php?game_id=1061">Can you get closest to the pin?</a>.
            </div>
        </div>
    </div>
    <div id="topad" align="center" valign="middle">
        <div id="boxAd300">
            <iframe src="<?php echo($webserver);?>/common/ad300.html" frameborder="0" scrolling="no" style="width: 300px; height: 250px; overflow: hidden; z-index: 9999; left: 0px; bottom: 0px; display: inline-block;"></iframe>
        </div>
    </div>
<?php
    if ($query != '') {
?>
    <div id="promos_middle_header" class="content_module_header">
        <h3>Search for: <?php echo($query);?></h3>
    </div>
    <div id="promos_middle">
        <div class="games_promo">
            <div class="game_tab"><div class="game_tab_name">Fire 'em All</div></div>
            <div class="game_tab_middle">
                <a href="/play.php?game_id=1077" title="Play Fire 'em All on Varyn now"><img class="game_tab_img" src="<?php echo($server);?>/games/fireEmAll/images/128x75.png" border="0" width="128" height="75" alt="Play Fire 'em All on Varyn now" /></a>
                <div class="game_tab_desc">With the election fast approaching, you're campaign budget is leaking fast and your supporters are not returing your calls. Can you reduce staff in time?</div>
            </div>
            <div class="game_tab_bottom"><a class="game_tab_play_btn" href="/play.php?game_id=1077" title="Play Fire 'em All on Varyn now"><span>Play Now</span></a></div>
        </div>
        <div class="games_promo">
            <div class="game_tab"><div class="game_tab_name">Slider Puzzle</div></div>
            <div class="game_tab_middle">
                <a href="/play.php?game_id=1020" title="Play Slider Puzzle on Varyn now"><img class="game_tab_img" src="<?php echo($server);?>/games/sliderPuzzle/images/128x75.png" border="0" width="128" height="75" alt="Play Slider Puzzle on Varyn now" /></a>
                <div class="game_tab_desc">Reconstruct the photo in as few moves as you can. The faster you do it, the higher your score.</div>
            </div>
            <div class="game_tab_bottom"><a class="game_tab_play_btn" href="/play.php?game_id=1020" title="Play Slider Puzzle on Varyn now"><span>Play Now</span></a></div>
        </div>
        <div class="games_promo">
            <div class="game_tab"><div class="game_tab_name">Sound Box</div></div>
            <div class="game_tab_middle">
                <a href="/play.php?game_id=1076" title="Play Sound Box on Varyn now"><img class="game_tab_img" src="<?php echo($server);?>/games/soundBox/images/128x75.png" border="0" width="128" height="75" alt="Play Sound Box on Varyn now" /></a>
                <div class="game_tab_desc">Put on your best beats as you create the drum, bass, sound fx, and voice over tracks and share it with your friends.</div>
            </div>
            <div class="game_tab_bottom"><a class="game_tab_play_btn" href="/play.php?game_id=1076" title="Play Sound Box on Varyn now"><span>Play Now</span></a></div>
        </div>
    </div>
<?php
    } else {
?>
    <div id="promos_middle_header" class="content_module_header">
        <h3>More Games</h3>
    </div>
    <div id="promos_middle">
        <div class="games_promo">
            <div class="game_tab"><div class="game_tab_name">Closest To The Pin</div></div>
            <div class="game_tab_middle">
                <a href="/play.php?game_id=1070" title="Play Closest To The Pin on Varyn now"><img class="game_tab_img" src="<?php echo($server);?>/games/closestToThePin/images/128x75.jpg" border="0" width="128" height="75" alt="Play Closest To The Pin on Varyn now" /></a>
                <div class="game_tab_desc">Test your <em>golf skills</em>! The pressure is on you, and you have only one swing. Can you get closest to the pin?</div>
            </div>
            <div class="game_tab_bottom"><a class="game_tab_play_btn" href="/play.php?game_id=1070" title="Play Closest To The Pin on Varyn now"><span>Play Now</span></a></div>
        </div>
        <div class="games_promo">
            <div class="game_tab"><div class="game_tab_name">Zam BeeZee</div></div>
            <div class="game_tab_middle">
                <a href="/play.php?game_id=1000" title="Play Zam BeeZee on Varyn now"><img class="game_tab_img" src="<?php echo($server);?>/games/zamBeeZeeOnline/images/128x75.jpg" border="0" width="128" height="75" alt="Play Zam BeeZee on Varyn now" /></a>
                <div class="game_tab_desc">Most addictive Action/Word game hybrid ever! Make as many words as you can and fill up the honey barrel before time runs out!</div>
            </div>
            <div class="game_tab_bottom"><a class="game_tab_play_btn" href="/play.php?game_id=1000" title="Play Zam BeeZee on Varyn now"><span>Play Now</span></a></div>
        </div>
        <div class="games_promo">
            <div class="game_tab"><div class="game_tab_name">Skywords</div></div>
            <div class="game_tab_middle">
                <a href="/play.php?game_id=1042" title="Play Skywords on Varyn now"><img class="game_tab_img" src="<?php echo($server);?>/games/skywords/images/128x75.jpg" border="0" width="128" height="75" alt="Play Skywords on Varyn now" /></a>
                <div class="game_tab_desc">Search for words in a jumble of letters. Find all the words in record time and beat your friends. New challege every day.</div>
            </div>
            <div class="game_tab_bottom"><a class="game_tab_play_btn" href="/play.php?game_id=1042" title="Play Skywords on Varyn now"><span>Play Now</span></a></div>
        </div>
        <div class="games_promo">
            <div class="game_tab"><div class="game_tab_name">Poker Slots!</div></div>
            <div class="game_tab_middle">
                <a href="/play.php?game_id=1077" title="Play Poker Slots on Varyn now"><img class="game_tab_img" src="<?php echo($server);?>/games/pokerslots/images/128x75.png" border="0" width="128" height="75" alt="Play Poker Slots on Varyn now" /></a>
                <div class="game_tab_desc">A video poker game and slot machine in one game! Spin the reels, make good hands, get big payouts!</div>
            </div>
            <div class="game_tab_bottom"><a class="game_tab_play_btn" href="/play.php?game_id=1077" title="Play Poker Slots on Varyn now"><span>Play Now</span></a></div>
        </div>
        <div class="games_promo">
            <div class="game_tab"><div class="game_tab_name">Kanye West Glory Hog</div></div>
            <div class="game_tab_middle">
                <a href="/play.php?game_id=1044" title="Play Kanye West Glory Hog on Varyn now"><img class="game_tab_img" src="<?php echo($server);?>/games/kanyeWestGloryHog/images/128x75.jpg" border="0" width="128" height="75" alt="Play Kanye West Glory Hog on Varyn now" /></a>
                <div class="game_tab_desc">Can you help Kanye grab all the awards and hog your way to glory?</div>
            </div>
            <div class="game_tab_bottom"><a class="game_tab_play_btn" href="/play.php?game_id=1044" title="Play Kanye West Glory Hog on Varyn now"><span>Play Now</span></a></div>
        </div>
        <div class="games_promo">
            <div class="game_tab"><div class="game_tab_name">Border Security</div></div>
            <div class="game_tab_middle">
                <a href="/play.php?game_id=1075" title="Play Border Security on Varyn now"><img class="game_tab_img" src="<?php echo($server);?>/games/borderSecurity/images/128x75.jpg" border="0" width="128" height="75" alt="Play Border Security on Varyn now" /></a>
                <div class="game_tab_desc">Awaken your inner Homeland Security and protect the borders! Prevent unwanted and unlikely peoples from crossing the border.</div>
            </div>
            <div class="game_tab_bottom"><a class="game_tab_play_btn" href="/play.php?game_id=1075" title="Play Border Security on Varyn now"><span>Play Now</span></a></div>
        </div>
        <div class="games_promo">
            <div class="game_tab"><div class="game_tab_name">Microbe Muncher</div></div>
            <div class="game_tab_middle">
                <a href="/play.php?game_id=1046" title="Play Microbe Muncher on Varyn now"><img class="game_tab_img" src="<?php echo($server);?>/games/microbeMuncher/images/128x75.jpg" border="0" width="128" height="75" alt="Play Microbe Muncher on Varyn now" /></a>
                <div class="game_tab_desc">Slither around eating microbes as quick as you can while avoiding ugly viruses looking to infect you.</div>
            </div>
            <div class="game_tab_bottom"><a class="game_tab_play_btn" href="/play.php?game_id=1046" title="Play Microbe Muncher on Varyn now"><span>Play Now</span></a></div>
        </div>
        <div class="games_promo">
            <div class="game_tab"><div class="game_tab_name">Mah Jongg Classic</div></div>
            <div class="game_tab_middle">
                <a href="/play.php?game_id=1002" title="Play Mah Jongg Classic on Varyn now"><img class="game_tab_img" src="<?php echo($server);?>/games/mahJonggClassic/images/128x75.jpg" border="0" width="128" height="75" alt="Play Mah Jongg Classic on Varyn now" /></a>
                <div class="game_tab_desc">Match tiles and clear the board in this classic game of Mah Jongg solitaire. Play fast and use strategy to maximize your score.</div>
            </div>
            <div class="game_tab_bottom"><a class="game_tab_play_btn" href="/play.php?game_id=1002" title="Play Mah Jongg Classic on Varyn now"><span>Play Now</span></a></div>
        </div>
        <div class="games_promo">
            <div class="game_tab"><div class="game_tab_name">Air Wolf</div></div>
            <div class="game_tab_middle">
                <a href="/play.php?game_id=1071" title="Play Air Wolf on Varyn now"><img class="game_tab_img" src="<?php echo($server);?>/games/airwolf/images/128x75.jpg" border="0" width="128" height="75" alt="Play Air Wolf on Varyn now" /></a>
                <div class="game_tab_desc">The wolves are out of control and taking over the pristine Alaska wilderness. Can Sarah Palin save the environment?</div>
            </div>
            <div class="game_tab_bottom"><a class="game_tab_play_btn" href="/play.php?game_id=1071" title="Play Air Wolf on Varyn now"><span>Play Now</span></a></div>
        </div>
        <div class="games_promo">
            <div class="game_tab"><div class="game_tab_name">Capital Collision</div></div>
            <div class="game_tab_middle">
                <a href="/play.php?game_id=1001" title="Play Capital Collision on Varyn now"><img class="game_tab_img" src="<?php echo($server);?>/games/capitalCollision/images/128x75.jpg" border="0" width="128" height="75" alt="Play Capital Collision on Varyn now" /></a>
                <div class="game_tab_desc">Shoot your way through 5 levels of arcade action and discover the truth behind the politics.</div>
            </div>
            <div class="game_tab_bottom"><a class="game_tab_play_btn" href="/play.php?game_id=1001" title="Play Capital Collision on Varyn now"><span>Play Now</span></a></div>
        </div>
        <div class="games_promo">
            <div class="game_tab"><div class="game_tab_name">Night of My Living Friends</div></div>
            <div class="game_tab_middle">
                <a href="/play.php?game_id=1053" title="Play Night of My Living Friends on Varyn now"><img class="game_tab_img" src="<?php echo($server);?>/games/nightOfMyLivingFriends/images/128x75.jpg" border="0" width="128" height="75" alt="Play Night of My Living Friends on Varyn now" /></a>
                <div class="game_tab_desc">Protect your friends from the zombie hoard! Lob all types of explosive devices at the undead as they try to grab your friends and take them where they can do unspeakable things.</div>
            </div>
            <div class="game_tab_bottom"><a class="game_tab_play_btn" href="/play.php?game_id=1053" title="Play Night of My Living Friends on Varyn now"><span>Play Now</span></a></div>
        </div>
        <div class="games_promo">
            <div class="game_tab"><div class="game_tab_name">Kings of Leon Pigeon Bomber</div></div>
            <div class="game_tab_middle">
                <a href="/play.php?game_id=1055" title="Play Kings of Leon Pigeon Bomber on Varyn now"><img class="game_tab_img" src="<?php echo($server);?>/games/pigeonBomber/images/128x75.jpg" border="0" width="128" height="75" alt="Play Kings of Leon Pigeon Bomber on Varyn now" /></a>
                <div class="game_tab_desc">Rock out on bass and avoid the pigeon bombs to give the audience a great show!</div>
            </div>
            <div class="game_tab_bottom"><a class="game_tab_play_btn" href="/play.php?game_id=1055" title="Play Kings of Leon Pigeon Bomber on Varyn now"><span>Play Now</span></a></div>
        </div>
        <div class="games_promo">
            <div class="game_tab"><div class="game_tab_name">Texas Mahjong</div></div>
            <div class="game_tab_middle">
                <a href="/play.php?game_id=1054" title="Play Texas Mahjong on Varyn now"><img class="game_tab_img" src="<?php echo($server);?>/games/texasMahJong/images/128x75.jpg" border="0" width="128" height="75" alt="Play Texas Mahjong on Varyn now" /></a>
                <div class="game_tab_desc">Texas Mahjong combines the simplicity of Mahjong with the strategy of Poker to create an entirely new solitaire experience.</div>
            </div>
            <div class="game_tab_bottom"><a class="game_tab_play_btn" href="/play.php?game_id=1054" title="Play Texas Mahjong on Varyn now"><span>Play Now</span></a></div>
        </div>
        <div class="games_promo">
            <div class="game_tab"><div class="game_tab_name">Run Batman Run!</div></div>
            <div class="game_tab_middle">
                <a href="/play.php?game_id=1067" title="Play Run Batman Run! on Varyn now"><img class="game_tab_img" src="<?php echo($server);?>/games/jumpChase/images/128x75.jpg" border="0" width="128" height="75" alt="Play Run Batman Run! on Varyn now" /></a>
                <div class="game_tab_desc">It seems like everyone is chasing after the Dark Knight. It is up to you to guide the caped crusader through the city to avoid his advisaries.</div>
            </div>
            <div class="game_tab_bottom"><a class="game_tab_play_btn" href="/play.php?game_id=1067" title="Play Run Batman Run! on Varyn now"><span>Play Now</span></a></div>
        </div>
        <div class="games_promo">
            <div class="game_tab"><div class="game_tab_name">Rally Driver</div></div>
            <div class="game_tab_middle">
                <a href="/play.php?game_id=1065" title="Play Rally Driver on Varyn now"><img class="game_tab_img" src="<?php echo($server);?>/games/carChase/images/128x75.jpg" border="0" width="128" height="75" alt="Play Rally Driver on Varyn now" /></a>
                <div class="game_tab_desc">Don't be late for the interview! Skip traffic or bump and pound your way earning power ups - whatever it takes to rock the road!</div>
            </div>
            <div class="game_tab_bottom"><a class="game_tab_play_btn" href="/play.php?game_id=1065" title="Play Rally Driver on Varyn now"><span>Play Now</span></a></div>
        </div>
        <div class="games_promo">
            <div class="game_tab"><div class="game_tab_name">Leap of the Ninja</div></div>
            <div class="game_tab_middle">
                <a href="/play.php?game_id=1066" title="Play Leap of the Ninja on Varyn now"><img class="game_tab_img" src="<?php echo($server);?>/games/leapOfTheNinja/images/128x75.jpg" border="0" width="128" height="75" alt="Play Leap of the Ninja on Varyn now" /></a>
                <div class="game_tab_desc">The Ninja is cool and crafty. With the grace of the crane and speed of the hawk, he leaps his way to ever higher goals. Help him leap his way to the top.</div>
            </div>
            <div class="game_tab_bottom"><a class="game_tab_play_btn" href="/play.php?game_id=1066" title="Play Leap of the Ninja on Varyn now"><span>Play Now</span></a></div>
        </div>
        <div class="games_promo">
            <div class="game_tab"><div class="game_tab_name">Volcano!</div></div>
            <div class="game_tab_middle">
                <a href="/play.php?game_id=1073" title="Play Volcano! on Varyn now"><img class="game_tab_img" src="<?php echo($server);?>/games/volcano/images/128x75.jpg" border="0" width="128" height="75" alt="Play Volcano! on Varyn now" /></a>
                <div class="game_tab_desc">Rotate the pieces of pipe to create a path for the lava to follow from entry to exit. Can you find the path to safety?</div>
            </div>
            <div class="game_tab_bottom"><a class="game_tab_play_btn" href="/play.php?game_id=1073" title="Play Volcano! on Varyn now"><span>Play Now</span></a></div>
        </div>
        <div class="games_promo">
            <div class="game_tab"><div class="game_tab_name">Thank You All!</div></div>
            <div class="game_tab_middle">
                <a href="/play.php?game_id=1063" title="Play Thank You All! on Varyn now"><img class="game_tab_img" src="<?php echo($server);?>/games/quickClick/images/128x75.jpg" border="0" width="128" height="75" alt="Play Thank You All! on Varyn now" /></a>
                <div class="game_tab_desc">Leo won another award and he just won't shut up about it. Help Leo thank all his adoring fans!</div>
            </div>
            <div class="game_tab_bottom"><a class="game_tab_play_btn" href="/play.php?game_id=1063" title="Play Thank You All! on Varyn now"><span>Play Now</span></a></div>
        </div>
        <div class="games_promo">
            <div class="game_tab"><div class="game_tab_name">Pink Floyd Inquisition</div></div>
            <div class="game_tab_middle">
                <a href="/play.php?game_id=1026" title="Play Pink Floyd Inquisition on Varyn now"><img class="game_tab_img" src="<?php echo($server);?>/games/quiz/images/1035/128x75.jpg" border="0" width="128" height="75" alt="Play Pink Floyd Inquisition on Varyn now" /></a>
                <div class="game_tab_desc">Test your knowledge of the origins and legacy of the psychedelic classic rock band Pink Floyd.</div>
            </div>
            <div class="game_tab_bottom"><a class="game_tab_play_btn" href="/play.php?game_id=1026" title="Play Pink Floyd Inquisition on Varyn now"><span>Play Now</span></a></div>
        </div>
        <div class="games_promo">
            <div class="game_tab"><div class="game_tab_name">The Winter of Discontent</div></div>
            <div class="game_tab_middle">
                <a href="/play.php?game_id=1043" title="Play The Winter of Discontent on Varyn now"><img class="game_tab_img" src="<?php echo($server);?>/games/winterOfDiscontent/images/128x75.jpg" border="0" width="128" height="75" alt="Play The Winter of Discontent on Varyn now" /></a>
                <div class="game_tab_desc">Battle the North Pole's true indigenous life with a taste of their own medicine. Can you stop the Ice King before he rallys his forces against you?</div>
            </div>
            <div class="game_tab_bottom"><a class="game_tab_play_btn" href="/play.php?game_id=1043" title="Play The Winter of Discontent on Varyn now"><span>Play Now</span></a></div>
        </div>
        <div class="games_promo">
            <div class="game_tab"><div class="game_tab_name">Z-Day Armory</div></div>
            <div class="game_tab_middle">
                <a href="/play.php?game_id=1056" title="Play Z-Day Armory on Varyn now"><img class="game_tab_img" src="<?php echo($server);?>/games/zDayArmory/images/128x75.jpg" border="0" width="128" height="75" alt="Play Z-Day Armory on Varyn now" /></a>
                <div class="game_tab_desc">Manage your weapon cache to fight the impending zombie threat. Match 3 and win! Your typical match-3 game, because every site has to have one of these!</div>
            </div>
            <div class="game_tab_bottom"><a class="game_tab_play_btn" href="/play.php?game_id=1056" title="Play Z-Day Armory on Varyn now"><span>Play Now</span></a></div>
        </div>
        <div class="games_promo">
            <div class="game_tab"><div class="game_tab_name">Sudoku</div></div>
            <div class="game_tab_middle">
                <a href="/play.php?game_id=1074" title="Play Sudoku on Varyn now"><img class="game_tab_img" src="<?php echo($server);?>/games/sudoku/images/128x75.jpg" border="0" width="128" height="75" alt="Play Sudoku on Varyn now" /></a>
                <div class="game_tab_desc">The popular 9x9 numbers game. Arrange the numbers such that each number appears once in each row, column and 3x3 grid. A real brain teaser!</div>
            </div>
            <div class="game_tab_bottom"><a class="game_tab_play_btn" href="/play.php?game_id=1074" title="Play Sudoku on Varyn now"><span>Play Now</span></a></div>
        </div>
        <div class="games_promo">
            <div class="game_tab"><div class="game_tab_name">I Love Toys</div></div>
            <div class="game_tab_middle">
                <a href="/play.php?game_id=1072" title="Play I Love Toys on Varyn now"><img class="game_tab_img" src="<?php echo($server);?>/games/iLoveToys/images/128x75.jpg" border="0" width="128" height="75" alt="Play I Love Toys on Varyn now" /></a>
                <div class="game_tab_desc">Hey! The 80's called and they want their toys back. Spin and match the toys from a bygone era.</div>
            </div>
            <div class="game_tab_bottom"><a class="game_tab_play_btn" href="/play.php?game_id=1072" title="Play I Love Toys on Varyn now"><span>Play Now</span></a></div>
        </div>
        <div class="games_promo">
            <div class="game_tab"><div class="game_tab_name">Fire 'em All</div></div>
            <div class="game_tab_middle">
                <a href="/play.php?game_id=1064" title="Play Fire 'em All on Varyn now"><img class="game_tab_img" src="<?php echo($server);?>/games/fireEmAll/images/128x75.png" border="0" width="128" height="75" alt="Play Fire 'em All on Varyn now" /></a>
                <div class="game_tab_desc">With the election fast approaching, you're campaign budget is leaking fast and your supporters are not returing your calls. Can you reduce staff in time?</div>
            </div>
            <div class="game_tab_bottom"><a class="game_tab_play_btn" href="/play.php?game_id=1064" title="Play Fire 'em All on Varyn now"><span>Play Now</span></a></div>
        </div>
        <div class="games_promo">
            <div class="game_tab"><div class="game_tab_name">Slider Puzzle</div></div>
            <div class="game_tab_middle">
                <a href="/play.php?game_id=1020" title="Play Slider Puzzle on Varyn now"><img class="game_tab_img" src="<?php echo($server);?>/games/sliderPuzzle/images/128x75.png" border="0" width="128" height="75" alt="Play Slider Puzzle on Varyn now" /></a>
                <div class="game_tab_desc">Reconstruct the photo in as few moves as you can. The faster you do it, the higher your score.</div>
            </div>
            <div class="game_tab_bottom"><a class="game_tab_play_btn" href="/play.php?game_id=1020" title="Play Slider Puzzle on Varyn now"><span>Play Now</span></a></div>
        </div>
        <div class="games_promo">
            <div class="game_tab"><div class="game_tab_name">Sound Box</div></div>
            <div class="game_tab_middle">
                <a href="/play.php?game_id=1076" title="Play Sound Box on Varyn now"><img class="game_tab_img" src="<?php echo($server);?>/games/soundBox/images/128x75.png" border="0" width="128" height="75" alt="Play Sound Box on Varyn now" /></a>
                <div class="game_tab_desc">Put on your best beats as you create the drum, bass, sound fx, and voice over tracks and share it with your friends.</div>
            </div>
            <div class="game_tab_bottom"><a class="game_tab_play_btn" href="/play.php?game_id=1076" title="Play Sound Box on Varyn now"><span>Play Now</span></a></div>
        </div>
        <div class="games_promo">
            <div class="game_tab"><div class="game_tab_name">Survival Island</div></div>
            <div class="game_tab_middle">
                <a href="/play.php?game_id=1078" title="Play Survival Island on Varyn now"><img class="game_tab_img" src="<?php echo($server);?>/games/unityTestGame/images/128x75.png" border="0" width="128" height="75" alt="Play Survival Island on Varyn now" /></a>
                <div class="game_tab_desc">You are stranded on a remote island and must find a way to signal for help. Use your ingenuity to solve this puzzle.</div>
            </div>
            <div class="game_tab_bottom"><a class="game_tab_play_btn" href="/play.php?game_id=1078" title="Play Survival Island on Varyn now"><span>Play Now</span></a></div>
        </div>
        <div class="games_promo">
            <div class="game_tab"><div class="game_tab_name">Revenge of Arcade</div></div>
            <div class="game_tab_middle">
                <a href="/play.php?game_id=1079" title="Play Revenge of Arcade on Varyn now"><img class="game_tab_img" src="<?php echo($server);?>/games/revengeOfArcade/images/128x75.png" border="0" width="128" height="75" alt="Play Revenge of Arcade on Varyn now" /></a>
                <div class="game_tab_desc">You were once the boss in the arcades. Now the machines want their revenge! Play through video game history to beat the arcade.</div>
            </div>
            <div class="game_tab_bottom"><a class="game_tab_play_btn" href="/play.php?game_id=1079" title="Play Revenge of Arcade on Varyn now"><span>Play Now</span></a></div>
        </div>
        <div class="games_promo">
            <div class="game_tab"><div class="game_tab_name">Memory Match</div></div>
            <div class="game_tab_middle">
                <a href="/play.php?game_id=1080" title="Play Memory Match on Varyn now"><img class="game_tab_img" src="<?php echo($server);?>/games/mtvMemoryMatch/images/128x75.png" border="0" width="128" height="75" alt="Play Memory Match on Varyn now" /></a>
                <div class="game_tab_desc">Use your brain skills and concentrate to find all the matches on the board.</div>
            </div>
            <div class="game_tab_bottom"><a class="game_tab_play_btn" href="/play.php?game_id=1080" title="Play Memory Match on Varyn now"><span>Play Now</span></a></div>
        </div>
        <div class="games_promo">
            <div class="game_tab"><div class="game_tab_name">Air Traffic Chief</div></div>
            <div class="game_tab_middle">
                <a href="/play.php?game_id=1049" title="Play Air Traffic Chief on Varyn now"><img class="game_tab_img" src="<?php echo($server);?>/games/airTrafficChief/images/128x75.jpg" border="0" width="128" height="75" alt="Play Air Traffic Chief on Varyn now" /></a>
                <div class="game_tab_desc">You control the busy skies and guide planes and helicopters to safe landing. Keep your cool, don't let the traffic overwhelm you, and don't crash!</div>
            </div>
            <div class="game_tab_bottom"><a class="game_tab_play_btn" href="/play.php?game_id=1049" title="Play Air Traffic Chief on Varyn now"><span>Play Now</span></a></div>
        </div>
        <div class="games_promo">
            <div class="game_tab"><div class="game_tab_name">Morningstar</div></div>
            <div class="game_tab_middle">
                <a href="/play.php?game_id=1047" title="Play Morningstar on Varyn now"><img class="game_tab_img" src="<?php echo($server);?>/games/morningstar/images/128x75.jpg" border="0" width="128" height="75" alt="Play Morningstar on Varyn now" /></a>
                <div class="game_tab_desc">Point 'n click adventure: your spaceship has crash landed on an alien planet. Are you resourceful enough to find a way to get back home?</div>
            </div>
            <div class="game_tab_bottom"><a class="game_tab_play_btn" href="/play.php?game_id=1047" title="Play Morningstar on Varyn now"><span>Play Now</span></a></div>
        </div>
        <div class="games_promo">
            <div class="game_tab"><div class="game_tab_name">Street Fighter II: Champion Edition</div></div>
            <div class="game_tab_middle">
                <a href="/play.php?game_id=1023" title="Play Street Fighter II: Champion Edition on Varyn now"><img class="game_tab_img" src="<?php echo($server);?>/games/streetFighterIIChampionEdition/images/128x75.jpg" border="0" width="128" height="75" alt="Play Street Fighter II: Champion Edition on Varyn now" /></a>
                <div class="game_tab_desc">The top-selling arcade fighter classic from Capcom, just like you remember it.</div>
            </div>
            <div class="game_tab_bottom"><a class="game_tab_play_btn" href="/play.php?game_id=1023" title="Play Street Fighter II: Champion Edition on Varyn now"><span>Play Now</span></a></div>
        </div>
        <div class="games_promo">
            <div class="game_tab"><div class="game_tab_name">Money Seize</div></div>
            <div class="game_tab_middle">
                <a href="/play.php?game_id=1018" title="Play Money Seize on Varyn now"><img class="game_tab_img" src="<?php echo($server);?>/games/moneySeize/images/128x75.jpg" border="0" width="128" height="75" alt="Play Money Seize on Varyn now" /></a>
                <div class="game_tab_desc">Sir Reginald MoneySeize II, Esq. must collect coins to fund construction of the tallest tower in the world!</div>
            </div>
            <div class="game_tab_bottom"><a class="game_tab_play_btn" href="/play.php?game_id=1018" title="Play Money Seize on Varyn now"><span>Play Now</span></a></div>
        </div>
        <div class="games_promo">
            <div class="game_tab"><div class="game_tab_name">Gem Craft</div></div>
            <div class="game_tab_middle">
                <a href="/play.php?game_id=1016" title="Play Gem Craft on Varyn now"><img class="game_tab_img" src="<?php echo($server);?>/games/gemcraft/images/128x75.jpg" border="0" width="128" height="75" alt="Play Gem Craft on Varyn now" /></a>
                <div class="game_tab_desc">Havoc and corruption swarms through the land, and you are one of those few wizards who can put an end to it. Create and combine magic gems, put them into your towers and banish the monsters back to hell!</div>
            </div>
            <div class="game_tab_bottom"><a class="game_tab_play_btn" href="/play.php?game_id=1016" title="Play Gem Craft on Varyn now"><span>Play Now</span></a></div>
        </div>
        <div class="games_promo">
            <div class="game_tab"><div class="game_tab_name">Bubble Tanks 2</div></div>
            <div class="game_tab_middle">
                <a href="/play.php?game_id=1014" title="Play Bubble Tanks 2 on Varyn now"><img class="game_tab_img" src="<?php echo($server);?>/games/bubbletanks2/images/128x75.jpg" border="0" width="128" height="75" alt="Play Bubble Tanks 2 on Varyn now" /></a>
                <div class="game_tab_desc">Travel through giant bubbles, destroy enemy tanks, take their bubbles to fuel your growth.</div>
            </div>
            <div class="game_tab_bottom"><a class="game_tab_play_btn" href="/play.php?game_id=1014" title="Play Bubble Tanks 2 on Varyn now"><span>Play Now</span></a></div>
        </div>
        <div class="games_promo">
            <div class="game_tab"><div class="game_tab_name">Sniper Assassin 3</div></div>
            <div class="game_tab_middle">
                <a href="/play.php?game_id=1013" title="Play Sniper Assassin 3 on Varyn now"><img class="game_tab_img" src="<?php echo($server);?>/games/sniperassassin3/images/128x75.jpg" border="0" width="128" height="75" alt="Play Sniper Assassin 3 on Varyn now" /></a>
                <div class="game_tab_desc">Sir Sniper has found his wife's killer. But the story only gets more complicated. Fortunately, there's plenty of bullets to go around!</div>
            </div>
            <div class="game_tab_bottom"><a class="game_tab_play_btn" href="/play.php?game_id=1013" title="Play Sniper Assassin 3 on Varyn now"><span>Play Now</span></a></div>
        </div>
        <div class="games_promo">
            <div class="game_tab"><div class="game_tab_name">Block Drop</div></div>
            <div class="game_tab_middle">
                <a href="/play.php?game_id=1006" title="Play Block Drop on Varyn now"><img class="game_tab_img" src="<?php echo($server);?>/games/blockDrop/images/128x75.jpg" border="0" width="128" height="75" alt="Play Block Drop on Varyn now" /></a>
                <div class="game_tab_desc">Find your way home by jumping from block to block but don't let the diamond fall. Don't. Let. The. Diamond. Fall.</div>
            </div>
            <div class="game_tab_bottom"><a class="game_tab_play_btn" href="/play.php?game_id=1006" title="Play Block Drop on Varyn now"><span>Play Now</span></a></div>
        </div>
        <div class="games_promo">
            <div class="game_tab"><div class="game_tab_name">Missile Defense</div></div>
            <div class="game_tab_middle">
                <a href="/play.php?game_id=1005" title="Play Missile Defense on Varyn now"><img class="game_tab_img" src="<?php echo($server);?>/games/missileDefense/images/128x75.jpg" border="0" width="128" height="75" alt="Play Missile Defense on Varyn now" /></a>
                <div class="game_tab_desc">My cities are in peril! Fortunately, I have awesome laser towers and trigger happy fingers for the alien smackdown.</div>
            </div>
            <div class="game_tab_bottom"><a class="game_tab_play_btn" href="/play.php?game_id=1005" title="Play Missile Defense on Varyn now"><span>Play Now</span></a></div>
        </div>
        <div class="games_promo">
            <div class="game_tab"><div class="game_tab_name">Karoshi Suicide Salaryman</div></div>
            <div class="game_tab_middle">
                <a href="/play.php?game_id=1004" title="Play Karoshi Suicide Salaryman on Varyn now"><img class="game_tab_img" src="<?php echo($server);?>/games/karoshisuicidesalaryman/images/128x75.jpg" border="0" width="128" height="75" alt="Play Karoshi Suicide Salaryman on Varyn now" /></a>
                <div class="game_tab_desc">Lost all your money? Fine, now you lose your life! Punish this pawn of industry by pushing him down a path of destruction!</div>
            </div>
            <div class="game_tab_bottom"><a class="game_tab_play_btn" href="/play.php?game_id=1004" title="Play Karoshi Suicide Salaryman on Varyn now"><span>Play Now</span></a></div>
        </div>
        <div class="games_promo">
            <div class="game_tab"><div class="game_tab_name">Celebrity Snapshot</div></div>
            <div class="game_tab_middle">
                <a href="/play.php?game_id=1012" title="Play Celebrity Snapshot on Varyn now"><img class="game_tab_img" src="<?php echo($server);?>/games/celebritySnapshot/images/128x75.jpg" border="0" width="128" height="75" alt="Play Celebrity Snapshot on Varyn now" /></a>
                <div class="game_tab_desc">Make a few bucks by catching celebs in the act. Avoid getting your lights punched out by bodyguards!</div>
            </div>
            <div class="game_tab_bottom"><a class="game_tab_play_btn" href="/play.php?game_id=1012" title="Play Celebrity Snapshot on Varyn now"><span>Play Now</span></a></div>
        </div>
        <div class="games_promo">
            <div class="game_tab"><div class="game_tab_name">Pencil Racer 3</div></div>
            <div class="game_tab_middle">
                <a href="/play.php?game_id=1010" title="Play Pencil Racer 3 on Varyn now"><img class="game_tab_img" src="<?php echo($server);?>/games/pencilRacer3/images/128x75.jpg" border="0" width="128" height="75" alt="Play Pencil Racer 3 on Varyn now" /></a>
                <div class="game_tab_desc">Pencil Racer is now officially off the hook. Drivable vehicles, hazards, powerups, collectibles - make killer tracks and share them. Can your friends drive the tracks you create?</div>
            </div>
            <div class="game_tab_bottom"><a class="game_tab_play_btn" href="/play.php?game_id=1010" title="Play Pencil Racer 3 on Varyn now"><span>Play Now</span></a></div>
        </div>
        <div class="games_promo">
            <div class="game_tab"><div class="game_tab_name">Parking Frenzy</div></div>
            <div class="game_tab_middle">
                <a href="/play.php?game_id=1008" title="Play Parking Frenzy on Varyn now"><img class="game_tab_img" src="<?php echo($server);?>/games/parkingFrenzy/images/128x75.jpg" border="0" width="128" height="75" alt="Play Parking Frenzy on Varyn now" /></a>
                <div class="game_tab_desc">Cram cars carefully and crash-free! Precise parking prepares proud & pleasant peoples!</div>
            </div>
            <div class="game_tab_bottom"><a class="game_tab_play_btn" href="/play.php?game_id=1008" title="Play Parking Frenzy on Varyn now"><span>Play Now</span></a></div>
        </div>
    </div>
<?php
    }
    include_once('../common/footer.php');
 ?>
</div><!-- page_container -->
</body>