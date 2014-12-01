<?php
require_once('../common/common.php');

$action = '';
if (isset($_POST["action"])) {
    $action = $_POST["action"];
}
if ($action == 'login') {
    $username = $_POST["username"];
    $password = $_POST["password"];
} elseif ($action == 'register') {
    $action = 'register';
    $username = $_POST["username"];
    $password = $_POST["password"];
    $email = $_POST["email"];
    $fullname = $_POST["fullname"];
    $location = $_POST["location"];
    $tagline = $_POST["tagline"];
    $dateOfBirth = $_POST["dob"];
    $captcha = $_POST["captcha"];
} elseif ($action == 'forgotpassword') {
} else {
    $username = '';
    $password = '';
}

 ?>
<!DOCTYPE html>
<head>
    <title>Varyn Games | My Profile</title>
    <meta name="title" content="Varyn Games My Profile" />
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
    $page = 'MyProfile';
    include_once('../common/header.php');
?>
    <div id="user_profile">
        <h2>My Profile</h2>
<?php
    if ($isLoggedIn) {
?>
        <p>You are logged in! Here is your summary:</p>
        <div id="profile_login">
            <input type="button" id="profile_forgot_password" onclick="logOutUser();" value="Logout" />
            <table class="profile-login-table">
                <tr><td></td><td></td></tr>
            </table>
        </div>
<?php
    } else {
?>
        <p>You are not logged in. You must login to see your profile.</p><br />
        <div id="profile_login">
            <table class="profile-login-table">
                <tr class="profile-login-table-row"><td width="50%">
                    <input type="button" id="facebook-connect-button" value="" onclick="loginFacebook();" title="Login with your Facebook account" /><br /><br /><br />
                    <a id="profile_register_now" title="Sign up with your email account">Sign Up With Email</a>
                </td>
                <td width="50%">
                    <form id="login-form" action="/services/MyProfile.php" method="POST" onSubmit="return validateLoginParameters();">
                        <table class="profile-login-table">
                            <tr class="profile-login-table-row"><td class="profile-login-left"><span class="profile-login-prompt">Login:</span></td><td class="profile-login-center"><input type="text" name="username" maxlength="20" class="profile-login-input" /></td><td class="profile-login-right">&nbsp;</td></tr>
                            <tr class="profile-login-table-row"><td class="profile-login-left"><span class="profile-login-prompt">Password:</span></td><td class="profile-login-center"><input type="password" name="password" maxlength="20" class="profile-login-input" /></td><td class="profile-login-right"><input type="button" id="submit-button-large" onclick="loginSubmit();" title="Login" /></td></tr>
                            <tr class="profile-login-table-row"><td colspan="3"><br /><a id="profile_forgot_password" href="#">Forgot password?</a><input type="hidden" name="action" value="login" /></td></tr>
                        </table>
                    </form>
                </td></tr>
            </table>
        </div>
<?php
    }
?>
    </div>
    <div id="topad" align="center" valign="middle">
        <div id="boxAd300">
            <iframe src="<?php echo($webserver);?>/common/ad300.html" frameborder="0" scrolling="no" style="width: 300px; height: 250px; overflow: hidden; z-index: 9999; left: 0px; bottom: 0px; display: inline-block;"></iframe>
        </div>
    </div>
    <div id="promos_middle_header" class="content_module_header">
<?php
    if ($isLoggedIn) {
?>
        <h3>My Favorite Games</h3>
<?php
    } else {
?>
        <h3>Top Games</h3>
<?php
    }
?>
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
            <div class="game_tab"><div class="game_tab_name">Border Security</div></div>
            <div class="game_tab_middle">
                <a href="/play.php?game_id=1075" title="Play Border Security on Varyn now"><img class="game_tab_img" src="<?php echo($server);?>/games/borderSecurity/images/128x75.jpg" border="0" width="128" height="75" alt="Play Border Security on Varyn now" /></a>
                <div class="game_tab_desc">Awaken your inner Homeland Security and protect the borders! Prevent unwanted and unlikely peoples from crossing the border.</div>
            </div>
            <div class="game_tab_bottom"><a class="game_tab_play_btn" href="/play.php?game_id=1075" title="Play Border Security on Varyn now"><span>Play Now</span></a></div>
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
            <div class="game_tab"><div class="game_tab_name">Air Traffic Chief</div></div>
            <div class="game_tab_middle">
                <a href="/play.php?game_id=1049" title="Play Air Traffic Chief on Varyn now"><img class="game_tab_img" src="<?php echo($server);?>/games/airTrafficChief/images/128x75.jpg" border="0" width="128" height="75" alt="Play Air Traffic Chief on Varyn now" /></a>
                <div class="game_tab_desc">You control the busy skies and guide planes and helicopters to safe landing. Keep your cool, don't let the traffic overwhelm you, and don't crash!</div>
            </div>
            <div class="game_tab_bottom"><a class="game_tab_play_btn" href="/play.php?game_id=1049" title="Play Air Traffic Chief on Varyn now"><span>Play Now</span></a></div>
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
                <div class="game_tab_desc">Drive your car through 4 levels, avoiding obstacles, earn pickups, complete your goal before your time runs out.</div>
            </div>
            <div class="game_tab_bottom"><a class="game_tab_play_btn" href="/play.php?game_id=1065" title="Play Rally Driver on Varyn now"><span>Play Now</span></a></div>
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
            <div class="game_tab"><div class="game_tab_name">Sudoku</div></div>
            <div class="game_tab_middle">
                <a href="/play.php?game_id=1074" title="Play Sudoku on Varyn now"><img class="game_tab_img" src="<?php echo($server);?>/games/sudoku/images/128x75.jpg" border="0" width="128" height="75" alt="Play Sudoku on Varyn now" /></a>
                <div class="game_tab_desc">The popular 9x9 numbers game. Arrange the numbers such that each number appears once in each row, column and 3x3 grid. A real brain teaser!</div>
            </div>
            <div class="game_tab_bottom"><a class="game_tab_play_btn" href="/play.php?game_id=1074" title="Play Sudoku on Varyn now"><span>Play Now</span></a></div>
        </div>
        <div class="games_promo">
            <div class="game_tab"><div class="game_tab_name">Block Drop</div></div>
            <div class="game_tab_middle">
                <a href="/play.php?game_id=1006" title="Play Block Drop on Varyn now"><img class="game_tab_img" src="<?php echo($server);?>/games/blockDrop/images/128x75.jpg" border="0" width="128" height="75" alt="Play Block Drop on Varyn now" /></a>
                <div class="game_tab_desc">Find your way home by jumping from block to block but don't let the diamond fall. Don't. Let. The. Diamond. Fall.</div>
            </div>
            <div class="game_tab_bottom"><a class="game_tab_play_btn" href="/play.php?game_id=1006" title="Play Block Drop on Varyn now"><span>Play Now</span></a></div>
        </div>
    </div>
    <div id="promos_middle_header" class="content_module_header">
        <h3>Recently Played Games</h3>
    </div>
    <div id="promos_middle">
        <div class="games_promo">
            <div class="game_tab"><div class="game_tab_name">Volcano!</div></div>
            <div class="game_tab_middle">
                <a href="/play.php?game_id=1073" title="Play Volcano! on Varyn now"><img class="game_tab_img" src="<?php echo($server);?>/games/volcano/images/128x75.jpg" border="0" width="128" height="75" alt="Play Volcano! on Varyn now" /></a>
                <div class="game_tab_desc">Rotate the pieces of pipe to create a path for the lava to follow from entry to exit. Can you find the path to safety?</div>
            </div>
            <div class="game_tab_bottom"><a class="game_tab_play_btn" href="/play.php?game_id=1073" title="Play Volcano! on Varyn now"><span>Play Now</span></a></div>
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
            <div class="game_tab"><div class="game_tab_name">Closest To The Pin</div></div>
            <div class="game_tab_middle">
                <a href="/play.php?game_id=1070" title="Play Closest To The Pin on Varyn now"><img class="game_tab_img" src="<?php echo($server);?>/games/closestToThePin/images/128x75.jpg" border="0" width="128" height="75" alt="Play Closest To The Pin on Varyn now" /></a>
                <div class="game_tab_desc">Test your <em>golf skills</em>! The pressure is on you, and you have only one swing. Can you get closest to the pin?</div>
            </div>
            <div class="game_tab_bottom"><a class="game_tab_play_btn" href="/play.php?game_id=1070" title="Play Closest To The Pin on Varyn now"><span>Play Now</span></a></div>
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
    </div>
<?php
    include_once('../common/footer.php');
 ?>
</div><!-- page_container -->
</body>
</html>
