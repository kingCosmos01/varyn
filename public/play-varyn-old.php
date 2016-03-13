<?php
    require_once('../services/common.php');
    $page = 'play';

    if (isset($_REQUEST['game_id'])) {
        $game_id = $_REQUEST['game_id'];
    } elseif (isset($_REQUEST['id'])) {
        $game_id = $_REQUEST['id'];
    } else {
        $game_id = 1000;
    }
    $today = date('M d, Y');

// Name, Desc, width, height, style/genre, plugin, src, bgcolor
$games_table = array(1000 => array('Zam BeeZee', 'Action/Word game hybrid - Make as many words as you can and fill up the honey barrel before time runs out!', 800, 600, 6, 2, 'zmbz.dcr', '#eeeeee'),
                     1001 => array('Capital Collision', 'Shoot your way through 5 levels of arcade action and discover the truth behind the politics.', 400, 450, 2, 1, 'CCMasterFile.swf', '#000000'),
                     1002 => array('Mah Jongg Classic', 'Match tiles and clear the board in this classic game of Mah Jongg solitaire. Play fast and use strategy to maximize your score.', 848, 604, 4, 2, 'mahjongg_load.dcr', '#000000'),
                     1004 => array('Karoshi Suicide Salaryman', 'Lost all your money? Fine, now you lose your life! Punish this pawn of industry by pushing him down a path of destruction!', 704, 488, 2, 1, 'skywords.swf', '#eeeeee'),
                     1005 => array('Missile Defense', 'My cities are in peril! Fortunately, I have awesome laser towers and trigger happy fingers for the alien smackdown.', 600, 500, 2, 1, 'skywords.swf', '#eeeeee'),
                     1006 => array('Block Drop', 'Find your way home by jumping from block to block but don\'t let the diamond fall. Don\'t. Let. The. Diamond. Fall.', 700, 500, 4, 1, 'skywords.swf', '#eeeeee'),
                     1008 => array('Parking Frenzy', 'Cram cars carefully and crash-free! Precise parking prepares proud & pleasant peoples!', 752, 564, 2, 1, 'skywords.swf', '#eeeeee'),
                     1010 => array('Pencil Racer 3', 'Pencil Racer is now officially off the hook. Drivable vehicles, hazards, powerups, collectibles - make killer tracks and share them. Can your friends drive the tracks you create?', 700, 600, 2, 1, 'skywords.swf', '#ffffff'),
                     1012 => array('Celebrity Snapshot', 'Make a few bucks by catching celebs in the act. Avoid getting your lights punched out by bodyguards!', 752, 564, 2, 1, 'skywords.swf', '#eeeeee'),
                     1013 => array('Sniper Assassin 3', 'Sir Sniper has found his wife\'s killer. But the story only gets more complicated. Fortunately, there\'s plenty of bullets to go around!', 600, 450, 2, 1, 'skywords.swf', '#eeeeee'),
                     1014 => array('Bubble Tanks 2', 'Travel through giant bubbles, destroy enemy tanks, take their bubbles to fuel your growth.', 500, 400, 2, 1, 'skywords.swf', '#eeeeee'),
                     1016 => array('Gem Craft', 'Havoc and corruption swarms through the land, and you are one of those few wizards who can put an end to it. Create and combine magic gems, put them into your towers and banish the monsters back to hell!', 640, 480, 2, 22, 'skywords.swf', '#eeeeee'),
                     1018 => array('Money Seize', 'Sir Reginald MoneySeize II, Esq. must collect coins to fund construction of the tallest tower in the world!', 640, 480, 2, 1, 'skywords.swf', '#eeeeee'),
                     1020 => array('Slider Puzzle', 'Reconstruct the photo in as few moves as you can. The faster you do it, the higher your score.', 320, 480, 10, 2, 'slidepuz.dcr', '#000000'),
                     1023 => array('Street Fighter II: Champion Edition', 'The top-selling arcade fighter classic from Capcom, just like you remember it.', 768, 448, 10, 1, 'skywords.swf', '#eeeeee'),
                     1026 => array('NYC Marathon Quiz', 'Test your knowledge of the New York City Marathon.', 320, 480, 16, 1, 'skywords.swf', '#eeeeee'),
                     1035 => array('Pink Floyd Inquisition', 'Test your knowledge of the origins and legacy of the psychedelic classic rock band Pink Floyd.', 320, 480, 16, 1, 'skywords.swf', '#eeeeee'),
                     1042 => array('Skywords', 'Search for words in a jumble of letters. Find all the words in record time and beat your friends. New challenge every day.', 760, 508, 6, 1, 'skywords.swf', '#eeeeee'),
                     1043 => array('The Winter of Discontent', 'Battle the North Pole\'s true indigenous life with a taste of their own medicine. Can you stop the Ice King before he rallys his forces against you?', 640, 440, 2, 1, 'winterOfDiscontent.swf', '#eeeeee'),
                     1044 => array('Kanye West Glory Hog', 'Can you help Kanye grab all the awards and hog your way to glory?', 660, 528, 2, 1, 'stacker.swf', '#000000'),
                     1046 => array('Microbe Muncher', 'Slither around eating microbes as quick as you can while avoiding ugly viruses looking to infect you.', 700, 525, 2, 1, 'microbe_muncher_preloader.swf', '#eeeeee'),
                     1047 => array('Morningstar', 'Point \'n click adventure: your spaceship has crash landed on an alien planet. Are you resourceful enough to find a way to get back home?', 800, 600, 13, 1, 'morningstar.swf', '#eeeeee'),
                     1049 => array('Air Traffic Chief', 'You control the busy skies and guide planes and helicopters to safe landing. Keep your cool, don\'t let the traffic overwhelm you, and don\'t crash!', 670, 550, 2, 1, 'skywords.swf', '#eeeeee'),
                     1050 => array('Mini Golf', 'Grab your putter and pick your colored ball - it\'s time to play Mini Golf! It\'s as much fun as the amusement park, only online.', 415, 372, 2, 2, 'rcmgload.dcr', '#666666'),
                     1053 => array('Night of My Living Friends', 'Protect your friends from the zombie hoard! Lob all types of explosive devices at the undead as they try to grab your friends and take them where they can do unspeakable things.', 640, 440, 2, 1, 'nightOfMyLivingFriends.swf', '#666666'),
                     1054 => array('Texas Mahjong', 'Texas Mahjong combines the simplicity of Mahjong with the strategy of Poker to create an entirely new solitaire experience.', 780, 520, 2, 1, 'skywords.swf', '#eeeeee'),
                     1055 => array('Kings of Leon Pigeon Bomber', 'Rock out on bass and avoid the pigeon bombs to give the audience a great show!', 550, 440, 2, 1, 'pigeonDrop.swf', '#eeeeee'),
                     1056 => array('Z-Day Armory', 'Manage your weapon cache to fight the impending zombie threat. Match 3 and win! Your typical match-3 game, because every site has to have one of these!', 660, 440, 2, 1, 'zDayArmory.swf', '#000000'),
                     1063 => array('Thank You All!', 'Leo won another award and he just won\'t shut up about it. Help Leo thank all his adoring fans!', 550, 440, 2, 1, 'ThankYouGame.swf', '#999999'),
                     1064 => array('Fire \'em All', 'With the election fast approaching, you\'re campaign budget is leaking fast and your supporters are not returing your calls. Can you reduce staff in time?', 550, 440, 2, 1, 'fireemall.swf', '#0033cc'),
                     1065 => array('Rally Driver', 'Don\'t be late for the interview! Skip traffic or bump and pound your way earning power ups - whatever it takes to rock the road!', 650, 392, 2, 1, 'rsr_car.swf', '#cccccc'),
                     1066 => array('Leap of the Ninja', 'The Ninja is cool and crafty. With the grace of the crane and speed of the hawk, he leaps his way to ever higher goals. Help him leap his way to the top.', 500, 420, 2, 1, 'ninja2.swf', '#000000'),
                     1067 => array('Run Batman Run!', 'It seems like everyone is chasing after the Dark Knight. It is up to you to guide the caped crusader through the city to avoid his adversaries.', 550, 440, 2, 1, 'chaseJumpGame.swf', '#000000'),
                     1069 => array('Memory Match', 'You think you have a good memory? See how many levels you can master in our memory match game that will make your head spin! Can you earn the crown?', 960, 720, 4, 10, 'games/memoryMatch.html', '#0da110'),
                     1070 => array('Closest To The Pin', 'Test your golf skills with the 7 iron. The pressure is on you, and you have only one swing. Can you get closest to the pin?', 600, 450, 2, 1, 'ctp.swf', '#ffffff'),
                     1071 => array('Air Wolf', 'The wolves are out of control and taking over the pristine Alaska wilderness. Can Sarah Palin save the environment?', 550, 440, 2, 1, 'airwolf.swf', '#ffffff'),
                     1072 => array('I Love Toys', 'Hey! The 80\'s called and they want their toys back. Spin and match the toys from a bygone era.', 592, 454, 2, 1, 'ilovetoys.swf', '#ffffff'),
                     1073 => array('Volcano', 'Rotate the pieces of pipe to create a path for the lava to follow from entry to exit. Can you find the path to safety?', 550, 440, 2, 1, 'Volcano.swf', '#ffffff'),
                     1074 => array('Sudoku', 'The popular 9x9 numbers game. Arrange the numbers such that each number appears once in each row, column and 3x3 grid. A real brain teaser!', 592, 454, 2, 1, 'sudoku.swf', '#ffffff'),
                     1075 => array('Border Security', 'Awaken your inner Homeland Security and protect the borders! Prevent unwanted and unlikely peoples from crossing the border.', 550, 440, 2, 1, 'bordersecurity.swf', '#ffffff'),
                     1076 => array('Sound Box', 'Put on your best beats as you create the drum, bass, sound fx, and voice over tracks and share it with your friends.', 550, 440, 18, 2, 'load.dcr', '#ffffff'),
                     1077 => array('Poker Slots', 'A video poker game and slot machine in one game! Spin the reels, make good hands, get big payouts!', 800, 600, 9, 10, 'index.html', '#000000'),
                     1078 => array('Survival Island', 'You are stranded on a remote island and must find a way to signal for help. Use your ingenuity to solve this puzzle.', 800, 600, 9, 6, 'index.html', '#000000'),
                     1079 => array('Revenge of Arcade', 'You were once the boss in the arcades. Now the machines want their revenge! Play through video game history to beat the arcade.', 550, 440, 2, 1, 'retroArcade.swf', '#72A5C5'),
                     1080 => array('Memory Match', 'Use your brain skills and concentrate to find all the matches on the board.', 750, 500, 2, 1, 'mtvMemoryMatch.swf', '#72A5C5'),
                     1081 => array('Hock A Loogie', 'The boys want to cause some trouble at school and have some fun on everyone.', 650, 480, 2, 1, 'BBHL.swf', '#72A5C5'),
                     1082 => array('Holiday Card', 'Chime along with our holiday e-card.', 959, 651, 18, 1, 'carolingBells.swf', '#C9E0F6'),
                     1083 => array('Match Master', 'You think you have a good memory? See how many levels you can master in our memory challenge to take the Match Master Crown!', 960, 720, 2, 1, 'index.html', '#72A5C5'),
                     1084 => array('Real Housewives Memory Challenge', 'You think you have a good memory? See how many levels you can master in our memory challenge to take the Real Housewives Award!', 960, 720, 2, 1, 'index.html', '#FFFFFF'),
                     1085 => array('Top Chef Memory Challenge', 'You think you have a good memory? See how many levels you can master in our memory challenge to take the Top Chef Crown!', 960, 720, 2, 1, 'index.html', '#FFFFFF'),
                     );
if ( ! isset($games_table[$game_id])) {
    $game_id = 1000;
}
$game_info = $games_table[$game_id];
$game_name = $game_info[0];
$game_desc = $game_info[1];
$game_width = $game_info[2];
$game_height = $game_info[3];
$game_style = $game_info[4];
$game_plugin_type = $game_info[5];
$game_src = '/games/' . $game_info[6];
$game_bg_color = $game_info[7];
$game_img = '/images/games/' . $game_id . '-128x75.jpg';
$game_dom_id = 'game';
if ($game_id == 1035 || $game_id == 1026) { // hack for the quizzes until i can figure out why they don't work on a different site id.
    $site_id = 100;
}
?>
<!DOCTYPE html>
<head>
	<title>Varyn Games | Play <?php echo($game_name);?></title>
	<meta name="title" content="Varyn Games Play <?php echo($game_name);?>" />
	<meta name="description" content="<?php echo($game_name);?>: <?php echo($game_desc);?>" />
	<link rel="image_src" href="<?php echo($game_img);?>" />
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<META NAME="Description" CONTENT="Varyn"/>
	<META NAME="Keywords" CONTENT="Varyn"/>
	<META NAME="Author" content="Varyn"/>
	<META NAME="Copyright" content="Copyright © 2014 Varyn. All rights reserved."/>
	<meta name="google-site-verification" content="" />
	<meta property="og:title" content="Varyn Games Play <?php echo($game_name);?>" />
	<meta property="og:description" content="<?php echo($game_name);?>: <?php echo($game_desc);?>" />
	<meta property="og:type" content="website" />
	<meta property="og:url" content="http://www.varyn.com" />
	<meta property="og:image" content="http://www.varyn.com/images/share_img_0.jpg" />
	<meta property="og:image" content="http://www.varyn.com/images/share_img_1.jpg" />
	<meta property="og:image" content="http://www.varyn.com/images/share_img_2.jpg" />
	<meta property="og:site_name" content="Varyn" />
	<meta property="og:type" content="website" />
	<meta property="fb:admins" content="726468316" />
	<meta property="fb:app_id" content="" />
    <link rel="apple-touch-icon" sizes="57x57" href="/apple-touch-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/apple-touch-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="72x72" href="/apple-touch-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="144x144" href="/apple-touch-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="60x60" href="/apple-touch-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="120x120" href="/apple-touch-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="76x76" href="/apple-touch-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/apple-touch-icon-152x152.png">
    <link rel="icon" type="image/png" href="/favicon-196x196.png" sizes="196x196">
    <link rel="icon" type="image/png" href="/favicon-160x160.png" sizes="160x160">
    <link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96">
    <link rel="icon" type="image/png" href="/favicon-16x16.png" sizes="16x16">
    <link rel="icon" type="image/png" href="/favicon-32x32.png" sizes="32x32">
    <meta name="msapplication-TileColor" content="#b91d47">
    <meta name="msapplication-TileImage" content="/mstile-144x144.png">
    <link rel="stylesheet" href="http://code.jquery.com/mobile/1.2.1/jquery.mobile-1.2.1.min.css" />
    <link href="common/main.css" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="/common/head.min.js"></script>
</head>
<body>
<div id="page_container">
<?php
include_once('common/header.php');
?>
    <div id="page_content_area" data-role="content">
        <div id="game_container" style="width: <?php echo($game_width);?>px; height: <?php echo($game_height + 30);?>px;">
            <iframe src="<?php echo($server);?>/games/play.php?site_id=<?php echo($site_id);?>&game_id=<?php echo($game_id);?>" width="<?php echo($game_width);?>" height="<?php echo($game_height);?>" frameborder="0" scrolling="no" marginwidth="0" marginheight="0" allowfullscreen title="<?php echo($game_desc);?>"></iframe>
	    </div>
        <div id="game_info">
            <div class="followgame">
                <ul id="followlist">
                    <li id="icon-favorite"><a href="https://enginesis.com/index.php?<?php echo($game_id);?>" title="Add <?php echo($game_name);?> to your favorites">Favorite</a></li>
                    <li id="icon-twitter"><a href="http://twitter.com/<?php echo($game_name);?>" title="Share <?php echo($game_name);?> on Twitter" target="_new">Share on Twitter</a></li>
                    <li id="icon-facebook"><a href="http://www.facebook.com/<?php echo($game_name);?>" title="Share <?php echo($game_name);?> on Facebook" target="_new">Share on Facebook</a></li>
                    <li><div id="sharethis"><script type="text/javascript" src="http://w.sharethis.com/button/sharethis.js#publisher=9e9438c3-2278-4f6e-b0f9-f7533b8689cb&amp;type=website&amp;embeds=true&amp;style=rotate"></script></div></li>
                </ul>
            </div>
            <h3><?php echo($game_name);?></h3>
            <p><?php echo($game_desc);?></p><br />
            <p>Your best score: 99,999; last played: Jan 10, 2013</p>
        </div>
        <table id="content_module_two_column">
            <tr>
            <td width="50%">
                <div id="game_leaderboard">
                    <div class="content_module_half_header">
                        <h3>Leaders</h3>
                    </div>
                    <div class="leaderboard_entry"><div class="leaderboard_rank">1.</div>&nbsp;<div class="leaderboard_img"><a href="/services/MyProfile.php?id=12345"><img class="user_profile_img" src="/images/avatar_tmp.jpg" width="25" height="25" border="0" title="View public profile of member Dark Matters" /></a></div><div class="leaderboard_username"><a href="/services/MyProfile.php?id=12345" title="View public profile of member Dark Matters">Dark Matters</a></div><div class="leaderboard_score">99,999</div></div>
                    <div class="leaderboard_entry"><div class="leaderboard_rank">2.</div>&nbsp;<div class="leaderboard_img"><a href="/services/MyProfile.php?id=12345"><img class="user_profile_img" src="/images/avatar_tmp.jpg" width="25" height="25" border="0" title="View public profile of member Dark Matters" /></a></div><div class="leaderboard_username"><a href="/services/MyProfile.php?id=12345" title="View public profile of member Dark Matters">Dark Matters</a></div><div class="leaderboard_score">99,999</div></div>
                    <div class="leaderboard_entry"><div class="leaderboard_rank">3.</div>&nbsp;<div class="leaderboard_img"><a href="/services/MyProfile.php?id=12345"><img class="user_profile_img" src="/images/avatar_tmp.jpg" width="25" height="25" border="0" title="View public profile of member Dark Matters" /></a></div><div class="leaderboard_username"><a href="/services/MyProfile.php?id=12345" title="View public profile of member Dark Matters">Dark Matters</a></div><div class="leaderboard_score">99,999</div></div>
                    <div class="leaderboard_entry"><div class="leaderboard_rank">4.</div>&nbsp;<div class="leaderboard_img"><a href="/services/MyProfile.php?id=12345"><img class="user_profile_img" src="/images/avatar_tmp.jpg" width="25" height="25" border="0" title="View public profile of member Dark Matters" /></a></div><div class="leaderboard_username"><a href="/services/MyProfile.php?id=12345" title="View public profile of member Dark Matters">Dark Matters</a></div><div class="leaderboard_score">99,999</div></div>
                    <div class="leaderboard_entry"><div class="leaderboard_rank">5.</div>&nbsp;<div class="leaderboard_img"><a href="/services/MyProfile.php?id=12345"><img class="user_profile_img" src="/images/avatar_tmp.jpg" width="25" height="25" border="0" title="View public profile of member Dark Matters" /></a></div><div class="leaderboard_username"><a href="/services/MyProfile.php?id=12345" title="View public profile of member Dark Matters">Dark Matters</a></div><div class="leaderboard_score">99,999</div></div>
                    <div class="leaderboard_entry"><div class="leaderboard_rank">6.</div>&nbsp;<div class="leaderboard_img"><a href="/services/MyProfile.php?id=12345"><img class="user_profile_img" src="/images/avatar_tmp.jpg" width="25" height="25" border="0" title="View public profile of member Dark Matters" /></a></div><div class="leaderboard_username"><a href="/services/MyProfile.php?id=12345" title="View public profile of member Dark Matters">Dark Matters</a></div><div class="leaderboard_score">99,999</div></div>
                    <div class="leaderboard_entry"><div class="leaderboard_rank">7.</div>&nbsp;<div class="leaderboard_img"><a href="/services/MyProfile.php?id=12345"><img class="user_profile_img" src="/images/avatar_tmp.jpg" width="25" height="25" border="0" title="View public profile of member Dark Matters" /></a></div><div class="leaderboard_username"><a href="/services/MyProfile.php?id=12345" title="View public profile of member Dark Matters">Dark Matters</a></div><div class="leaderboard_score">99,999</div></div>
                    <div class="leaderboard_entry"><div class="leaderboard_rank">8.</div>&nbsp;<div class="leaderboard_img"><a href="/services/MyProfile.php?id=12345"><img class="user_profile_img" src="/images/avatar_tmp.jpg" width="25" height="25" border="0" title="View public profile of member Dark Matters" /></a></div><div class="leaderboard_username"><a href="/services/MyProfile.php?id=12345" title="View public profile of member Dark Matters">Dark Matters</a></div><div class="leaderboard_score">99,999</div></div>
                    <div class="leaderboard_entry"><div class="leaderboard_rank">9.</div>&nbsp;<div class="leaderboard_img"><a href="/services/MyProfile.php?id=12345"><img class="user_profile_img" src="/images/avatar_tmp.jpg" width="25" height="25" border="0" title="View public profile of member Dark Matters" /></a></div><div class="leaderboard_username"><a href="/services/MyProfile.php?id=12345" title="View public profile of member Dark Matters">Dark Matters</a></div><div class="leaderboard_score">99,999</div></div>
                    <div class="leaderboard_entry"><div class="leaderboard_rank">10.</div>&nbsp;<div class="leaderboard_img"><a href="/services/MyProfile.php?id=12345"><img class="user_profile_img" src="/images/avatar_tmp.jpg" width="25" height="25" border="0" title="View public profile of member Dark Matters" /></a></div><div class="leaderboard_username"><a href="/services/MyProfile.php?id=12345" title="View public profile of member Dark Matters">Dark Matters</a></div><div class="leaderboard_score">99,999</div></div>
                    <div class="leaderboard_entry"><div class="leaderboard_rank">11.</div>&nbsp;<div class="leaderboard_img"><a href="/services/MyProfile.php?id=12345"><img class="user_profile_img" src="/images/avatar_tmp.jpg" width="25" height="25" border="0" title="View public profile of member Dark Matters" /></a></div><div class="leaderboard_username"><a href="/services/MyProfile.php?id=12345" title="View public profile of member Dark Matters">Dark Matters</a></div><div class="leaderboard_score">99,999</div></div>
                    <div class="leaderboard_entry"><div class="leaderboard_rank">12.</div>&nbsp;<div class="leaderboard_img"><a href="/services/MyProfile.php?id=12345"><img class="user_profile_img" src="/images/avatar_tmp.jpg" width="25" height="25" border="0" title="View public profile of member Dark Matters" /></a></div><div class="leaderboard_username"><a href="/services/MyProfile.php?id=12345" title="View public profile of member Dark Matters">Dark Matters</a></div><div class="leaderboard_score">99,999</div></div>
                    <div class="leaderboard_entry"><div class="leaderboard_rank">13.</div>&nbsp;<div class="leaderboard_img"><a href="/services/MyProfile.php?id=12345"><img class="user_profile_img" src="/images/avatar_tmp.jpg" width="25" height="25" border="0" title="View public profile of member Dark Matters" /></a></div><div class="leaderboard_username"><a href="/services/MyProfile.php?id=12345" title="View public profile of member Dark Matters">Dark Matters</a></div><div class="leaderboard_score">99,999</div></div>
                    <div class="leaderboard_entry"><div class="leaderboard_rank">14.</div>&nbsp;<div class="leaderboard_img"><a href="/services/MyProfile.php?id=12345"><img class="user_profile_img" src="/images/avatar_tmp.jpg" width="25" height="25" border="0" title="View public profile of member Dark Matters" /></a></div><div class="leaderboard_username"><a href="/services/MyProfile.php?id=12345" title="View public profile of member Dark Matters">Dark Matters</a></div><div class="leaderboard_score">99,999</div></div>
                    <div class="leaderboard_entry"><div class="leaderboard_rank">15.</div>&nbsp;<div class="leaderboard_img"><a href="/services/MyProfile.php?id=12345"><img class="user_profile_img" src="/images/avatar_tmp.jpg" width="25" height="25" border="0" title="View public profile of member Dark Matters" /></a></div><div class="leaderboard_username"><a href="/services/MyProfile.php?id=12345" title="View public profile of member Dark Matters">Dark Matters</a></div><div class="leaderboard_score">99,999</div></div>
                    <div class="leaderboard_entry"><div class="leaderboard_rank">16.</div>&nbsp;<div class="leaderboard_img"><a href="/services/MyProfile.php?id=12345"><img class="user_profile_img" src="/images/avatar_tmp.jpg" width="25" height="25" border="0" title="View public profile of member Dark Matters" /></a></div><div class="leaderboard_username"><a href="/services/MyProfile.php?id=12345" title="View public profile of member Dark Matters">Dark Matters</a></div><div class="leaderboard_score">99,999</div></div>
                    <div class="leaderboard_entry"><div class="leaderboard_rank">17.</div>&nbsp;<div class="leaderboard_img"><a href="/services/MyProfile.php?id=12345"><img class="user_profile_img" src="/images/avatar_tmp.jpg" width="25" height="25" border="0" title="View public profile of member Dark Matters" /></a></div><div class="leaderboard_username"><a href="/services/MyProfile.php?id=12345" title="View public profile of member Dark Matters">Dark Matters</a></div><div class="leaderboard_score">99,999</div></div>
                    <div class="leaderboard_entry"><div class="leaderboard_rank">18.</div>&nbsp;<div class="leaderboard_img"><a href="/services/MyProfile.php?id=12345"><img class="user_profile_img" src="/images/avatar_tmp.jpg" width="25" height="25" border="0" title="View public profile of member Dark Matters" /></a></div><div class="leaderboard_username"><a href="/services/MyProfile.php?id=12345" title="View public profile of member Dark Matters">Dark Matters</a></div><div class="leaderboard_score">99,999</div></div>
                    <div class="leaderboard_entry"><div class="leaderboard_rank">19.</div>&nbsp;<div class="leaderboard_img"><a href="/services/MyProfile.php?id=12345"><img class="user_profile_img" src="/images/avatar_tmp.jpg" width="25" height="25" border="0" title="View public profile of member Dark Matters" /></a></div><div class="leaderboard_username"><a href="/services/MyProfile.php?id=12345" title="View public profile of member Dark Matters">Dark Matters</a></div><div class="leaderboard_score">99,999</div></div>
                    <div class="leaderboard_entry"><div class="leaderboard_rank">20.</div>&nbsp;<div class="leaderboard_img"><a href="/services/MyProfile.php?id=12345"><img class="user_profile_img" src="/images/avatar_tmp.jpg" width="25" height="25" border="0" title="View public profile of member Dark Matters" /></a></div><div class="leaderboard_username"><a href="/services/MyProfile.php?id=12345" title="View public profile of member Dark Matters">Dark Matters</a></div><div class="leaderboard_score">99,999</div></div>
                    <div class="leaderboard_entry"><div class="leaderboard_rank">21.</div>&nbsp;<div class="leaderboard_img"><a href="/services/MyProfile.php?id=12345"><img class="user_profile_img" src="/images/avatar_tmp.jpg" width="25" height="25" border="0" title="View public profile of member Dark Matters" /></a></div><div class="leaderboard_username"><a href="/services/MyProfile.php?id=12345" title="View public profile of member Dark Matters">Dark Matters</a></div><div class="leaderboard_score">99,999</div></div>
                    <div class="leaderboard_entry"><div class="leaderboard_rank">22.</div>&nbsp;<div class="leaderboard_img"><a href="/services/MyProfile.php?id=12345"><img class="user_profile_img" src="/images/avatar_tmp.jpg" width="25" height="25" border="0" title="View public profile of member Dark Matters" /></a></div><div class="leaderboard_username"><a href="/services/MyProfile.php?id=12345" title="View public profile of member Dark Matters">Dark Matters</a></div><div class="leaderboard_score">99,999</div></div>
                    <div class="leaderboard_entry"><div class="leaderboard_rank">23.</div>&nbsp;<div class="leaderboard_img"><a href="/services/MyProfile.php?id=12345"><img class="user_profile_img" src="/images/avatar_tmp.jpg" width="25" height="25" border="0" title="View public profile of member Dark Matters" /></a></div><div class="leaderboard_username"><a href="/services/MyProfile.php?id=12345" title="View public profile of member Dark Matters">Dark Matters</a></div><div class="leaderboard_score">99,999</div></div>
                    <div class="leaderboard_entry"><div class="leaderboard_rank">24.</div>&nbsp;<div class="leaderboard_img"><a href="/services/MyProfile.php?id=12345"><img class="user_profile_img" src="/images/avatar_tmp.jpg" width="25" height="25" border="0" title="View public profile of member Dark Matters" /></a></div><div class="leaderboard_username"><a href="/services/MyProfile.php?id=12345" title="View public profile of member Dark Matters">Dark Matters</a></div><div class="leaderboard_score">99,999</div></div>
                    <div class="leaderboard_entry"><div class="leaderboard_rank">25.</div>&nbsp;<div class="leaderboard_img"><a href="/services/MyProfile.php?id=12345"><img class="user_profile_img" src="/images/avatar_tmp.jpg" width="25" height="25" border="0" title="View public profile of member Dark Matters" /></a></div><div class="leaderboard_username"><a href="/services/MyProfile.php?id=12345" title="View public profile of member Dark Matters">Dark Matters</a></div><div class="leaderboard_score">99,999</div></div>
                </div>
            </td>
            <td width="50%">
                <div class="content_modules_half">
                    <div class="content_module_half_header">
                        <h3>If you like this, try one of these:</h3>
                    </div>
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
                </div>
            </td>
            </tr>
        </table>
        <div id="content_modules">
            <div id="content_modules_header" class="content_module_header">
                <h3>User Comments</h3>
            </div>
            <div class="comment_entry">
                <div class="comment_user">
                    <a href="/services/MyProfile.php?id=12345"><img class="user_profile_img" src="/images/avatar_tmp.jpg" width="35" height="35" border="0" title="View public profile of member Dark Matters" align="left" /></a>
                    <div class="comment_username"><a href="/services/MyProfile.php?id=12345" title="View public profile of member Dark Matters">Dark Matters</a></div>
                    <div class="comment_date">Jan 30, 2013 4:55 pm</div>
                </div>
                <div class="comment_text">This is the text of what the user had to say. Lorem ipsom alpha lanbda vidi verdad. Lorem ipsom alpha lanbda vidi verdad. Lorem ipsom alpha lanbda vidi verdad.</div>
            </div>
            <div class="comment_input">
                <form method="POST" action="">
                <div class="comment_user">
                    <img class="user_profile_img" src="/images/avatar_tmp.jpg" width="35" height="35" border="0" title="View profile" align="left" />
                    <div class="comment_username">Dark Matters</div>
                    <div class="comment_date"><?php echo($today);?></div>
                </div>
                <textarea class="comment_text" cols="100" height="4"></textarea><br />
                    <input type="button" value="Comment" style="margin-left: 46px; margin-bottom: 6px;" />
                </form>
            </div>
        </div>
    </div><!-- page_content_area -->
<?php
    include_once('common/footer.php');
 ?>
</div><!-- page_container -->
</body>
</html>
