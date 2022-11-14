<?php
require_once('../../services/common.php');
processSearchRequest();
$page = 'featured';
$pageTitle = 'Hot games, contests, and tournaments';
$pageDescription = 'Discover the most recent content added to the site. We offer games, contests for prizes, and tournaments.';
$webserver = '';
$server = 'https://www.enginesis.com';
include_once(VIEWS_ROOT . 'header.php');
?>
<div class="container top-promo-area">
    <div class="row">
        <div id="top_promo">
            <h2>What's Hot</h2>
            <p>Here we will place featured content we curated from throughout the web, whether it is ours or not,
                our top promoted games, contests, blog entries, media call-outs.</p>
        </div>
        <div id="ad300" class="col-sm-4 col-md-2">
            <div id="boxAd300" class="ad300">
                <?php
                $adProvider = 'cpmstar';
                include_once(VIEWS_ROOT . 'ad-spot.php');
                ?>
            </div>
            <p id="ad300-subtitle" class="text-right"><small>Advertisement</small></p>
        </div>
    </div>
</div>
<div class="container">
    <div id="promos_middle_header" class="content_module_header">
        <h3>Hot Games</h3>
    </div>
    <div id="promos_middle">
        <div class="games_promo">
            <div class="game_tab">
                <div class="game_tab_name">Closest To The Pin</div>
            </div>
            <div class="game_tab_middle">
                <a href="/play/?game_id=1070" title="Play Closest To The Pin on Varyn now"><img class="game_tab_img" src="<?php echo ($server); ?>/games/closestToThePin/images/128x75.jpg" alt="Closest To The Pin game promotion image" width="128" height="75" alt="Play Closest To The Pin on Varyn now" /></a>
                <div class="game_tab_desc">Test your <em>golf skills</em>! The pressure is on you, and you have only one swing. Can you get closest to the pin?</div>
            </div>
            <div class="game_tab_bottom"><a class="game_tab_play_btn" href="/play/?game_id=1070" title="Play Closest To The Pin on Varyn now"><span>Play Now</span></a></div>
        </div>
        <div class="games_promo">
            <div class="game_tab">
                <div class="game_tab_name">Border Security</div>
            </div>
            <div class="game_tab_middle">
                <a href="/play/?game_id=1075" title="Play Border Security on Varyn now"><img class="game_tab_img" src="<?php echo ($server); ?>/games/borderSecurity/images/128x75.jpg" alt="Border Security game promotion image" width="128" height="75" alt="Play Border Security on Varyn now" /></a>
                <div class="game_tab_desc">Awaken your inner Homeland Security and protect the borders! Prevent unwanted and unlikely peoples from crossing the border.</div>
            </div>
            <div class="game_tab_bottom"><a class="game_tab_play_btn" href="/play/?game_id=1075" title="Play Border Security on Varyn now"><span>Play Now</span></a></div>
        </div>
        <div class="games_promo">
            <div class="game_tab">
                <div class="game_tab_name">Zam BeeZee</div>
            </div>
            <div class="game_tab_middle">
                <a href="/play/?game_id=1000" title="Play Zam BeeZee on Varyn now"><img class="game_tab_img" src="<?php echo ($server); ?>/games/zamBeeZeeOnline/images/128x75.jpg" alt="Zam BeeZee game promotion image" width="128" height="75" alt="Play Zam BeeZee on Varyn now" /></a>
                <div class="game_tab_desc">Most addictive Action/Word game hybrid ever! Make as many words as you can and fill up the honey barrel before time runs out!</div>
            </div>
            <div class="game_tab_bottom"><a class="game_tab_play_btn" href="/play/?game_id=1000" title="Play Zam BeeZee on Varyn now"><span>Play Now</span></a></div>
        </div>
        <div class="games_promo">
            <div class="game_tab">
                <div class="game_tab_name">Skywords</div>
            </div>
            <div class="game_tab_middle">
                <a href="/play/?game_id=1042" title="Play Skywords on Varyn now"><img class="game_tab_img" src="<?php echo ($server); ?>/games/skywords/images/128x75.jpg" alt="Skywords game promotion image" width="128" height="75" alt="Play Skywords on Varyn now" /></a>
                <div class="game_tab_desc">Search for words in a jumble of letters. Find all the words in record time and beat your friends. New challege every day.</div>
            </div>
            <div class="game_tab_bottom"><a class="game_tab_play_btn" href="/play/?game_id=1042" title="Play Skywords on Varyn now"><span>Play Now</span></a></div>
        </div>
        <div class="games_promo">
            <div class="game_tab">
                <div class="game_tab_name">Microbe Muncher</div>
            </div>
            <div class="game_tab_middle">
                <a href="/play/?game_id=1046" title="Play Microbe Muncher on Varyn now"><img class="game_tab_img" src="<?php echo ($server); ?>/games/microbeMuncher/images/128x75.jpg" alt="Microbe Muncher game promotion image" width="128" height="75" alt="Play Microbe Muncher on Varyn now" /></a>
                <div class="game_tab_desc">Slither around eating microbes as quick as you can while avoiding ugly viruses looking to infect you.</div>
            </div>
            <div class="game_tab_bottom"><a class="game_tab_play_btn" href="/play/?game_id=1046" title="Play Microbe Muncher on Varyn now"><span>Play Now</span></a></div>
        </div>
        <div class="games_promo">
            <div class="game_tab">
                <div class="game_tab_name">Mah Jongg Classic</div>
            </div>
            <div class="game_tab_middle">
                <a href="/play/?game_id=1002" title="Play Mah Jongg Classic on Varyn now"><img class="game_tab_img" src="<?php echo ($server); ?>/games/mahJonggClassic/images/128x75.jpg" alt="Mah Jongg Classic game promotion image" width="128" height="75" alt="Play Mah Jongg Classic on Varyn now" /></a>
                <div class="game_tab_desc">Match tiles and clear the board in this classic game of Mah Jongg solitaire. Play fast and use strategy to maximize your score.</div>
            </div>
            <div class="game_tab_bottom"><a class="game_tab_play_btn" href="/play/?game_id=1002" title="Play Mah Jongg Classic on Varyn now"><span>Play Now</span></a></div>
        </div>
        <div class="games_promo">
            <div class="game_tab">
                <div class="game_tab_name">Air Wolf</div>
            </div>
            <div class="game_tab_middle">
                <a href="/play/?game_id=1071" title="Play Air Wolf on Varyn now"><img class="game_tab_img" src="<?php echo ($server); ?>/games/airwolf/images/128x75.jpg" alt="Air Wolf game promotion image" width="128" height="75" alt="Play Air Wolf on Varyn now" /></a>
                <div class="game_tab_desc">The wolves are out of control and taking over the pristine Alaska wilderness. Can Sarah Palin save the environment?</div>
            </div>
            <div class="game_tab_bottom"><a class="game_tab_play_btn" href="/play/?game_id=1071" title="Play Air Wolf on Varyn now"><span>Play Now</span></a></div>
        </div>
        <div class="games_promo">
            <div class="game_tab">
                <div class="game_tab_name">Air Traffic Chief</div>
            </div>
            <div class="game_tab_middle">
                <a href="/play/?game_id=1049" title="Play Air Tower on Varyn now"><img class="game_tab_img" src="<?php echo ($server); ?>/games/airtower/images/128x75.jpg" alt="Air Tower game promotion image" width="128" height="75" alt="Play Air Tower on Varyn now" /></a>
                <div class="game_tab_desc">You control the busy skies and guide planes and helicopters to safe landing. Keep your cool, don't let the traffic overwhelm you, and don't crash!</div>
            </div>
            <div class="game_tab_bottom"><a class="game_tab_play_btn" href="/play/?game_id=1049" title="Play Air Traffic Chief on Varyn now"><span>Play Now</span></a></div>
        </div>
        <div class="games_promo">
            <div class="game_tab">
                <div class="game_tab_name">Run Batman Run!</div>
            </div>
            <div class="game_tab_middle">
                <a href="/play/?game_id=1067" title="Play Run Batman Run! on Varyn now"><img class="game_tab_img" src="<?php echo ($server); ?>/games/jumpChase/images/128x75.jpg" alt="Run Batman Run game promotion image" width="128" height="75" alt="Play Run Batman Run! on Varyn now" /></a>
                <div class="game_tab_desc">It seems like everyone is chasing after the Dark Knight. It is up to you to guide the caped crusader through the city to avoid his advisaries.</div>
            </div>
            <div class="game_tab_bottom"><a class="game_tab_play_btn" href="/play/?game_id=1067" title="Play Run Batman Run! on Varyn now"><span>Play Now</span></a></div>
        </div>
        <div class="games_promo">
            <div class="game_tab">
                <div class="game_tab_name">Rally Driver</div>
            </div>
            <div class="game_tab_middle">
                <a href="/play/?game_id=1065" title="Play Rally Driver on Varyn now"><img class="game_tab_img" src="<?php echo ($server); ?>/games/carChase/images/128x75.jpg" alt="Rally Driver game promotion image" width="128" height="75" alt="Play Rally Driver on Varyn now" /></a>
                <div class="game_tab_desc">Don't be late for the interview! Skip traffic or bump and pound your way earning power ups - whatever it takes to rock the road!</div>
            </div>
            <div class="game_tab_bottom"><a class="game_tab_play_btn" href="/play/?game_id=1065" title="Play Rally Driver on Varyn now"><span>Play Now</span></a></div>
        </div>
        <div class="games_promo">
            <div class="game_tab">
                <div class="game_tab_name">Leap of the Ninja</div>
            </div>
            <div class="game_tab_middle">
                <a href="/play/?game_id=1066" title="Play Leap of the Ninja on Varyn now"><img class="game_tab_img" src="<?php echo ($server); ?>/games/leapOfTheNinja/images/128x75.jpg" alt="Leap of the Ninja game promotion image" width="128" height="75" alt="Play Leap of the Ninja on Varyn now" /></a>
                <div class="game_tab_desc">The Ninja is cool and crafty. With the grace of the crane and speed of the hawk, he leaps his way to ever higher goals. Help him leap his way to the top.</div>
            </div>
            <div class="game_tab_bottom"><a class="game_tab_play_btn" href="/play/?game_id=1066" title="Play Leap of the Ninja on Varyn now"><span>Play Now</span></a></div>
        </div>
        <div class="games_promo">
            <div class="game_tab">
                <div class="game_tab_name">Volcano!</div>
            </div>
            <div class="game_tab_middle">
                <a href="/play/?game_id=1073" title="Play Volcano! on Varyn now"><img class="game_tab_img" src="<?php echo ($server); ?>/games/volcano/images/128x75.jpg" alt="Volcano game promotion image" width="128" height="75" alt="Play Volcano! on Varyn now" /></a>
                <div class="game_tab_desc">Rotate the pieces of pipe to create a path for the lava to follow from entry to exit. Can you find the path to safety?</div>
            </div>
            <div class="game_tab_bottom"><a class="game_tab_play_btn" href="/play/?game_id=1073" title="Play Volcano! on Varyn now"><span>Play Now</span></a></div>
        </div>
        <div class="games_promo">
            <div class="game_tab">
                <div class="game_tab_name">Sudoku</div>
            </div>
            <div class="game_tab_middle">
                <a href="/play/?game_id=1074" title="Play Sudoku on Varyn now"><img class="game_tab_img" src="<?php echo ($server); ?>/games/sudoku/images/128x75.jpg" alt="Sudoku game promotion image" width="128" height="75" alt="Play Sudoku on Varyn now" /></a>
                <div class="game_tab_desc">The popular 9x9 numbers game. Arrange the numbers such that each number appears once in each row, column and 3x3 grid. A real brain teaser!</div>
            </div>
            <div class="game_tab_bottom"><a class="game_tab_play_btn" href="/play/?game_id=1074" title="Play Sudoku on Varyn now"><span>Play Now</span></a></div>
        </div>
        <div class="games_promo">
            <div class="game_tab">
                <div class="game_tab_name">Block Drop</div>
            </div>
            <div class="game_tab_middle">
                <a href="/play/?game_id=1006" title="Play Block Drop on Varyn now"><img class="game_tab_img" src="<?php echo ($server); ?>/games/blockDrop/images/128x75.jpg" alt="Block Drop game promotion image" width="128" height="75" alt="Play Block Drop on Varyn now" /></a>
                <div class="game_tab_desc">Find your way home by jumping from block to block but don't let the diamond fall. Don't. Let. The. Diamond. Fall.</div>
            </div>
            <div class="game_tab_bottom"><a class="game_tab_play_btn" href="/play/?game_id=1006" title="Play Block Drop on Varyn now"><span>Play Now</span></a></div>
        </div>
        <div class="games_promo">
            <div class="game_tab">
                <div class="game_tab_name">Missile Defense</div>
            </div>
            <div class="game_tab_middle">
                <a href="/play/?game_id=1005" title="Play Missile Defense on Varyn now"><img class="game_tab_img" src="<?php echo ($server); ?>/games/missileDefense/images/128x75.jpg" alt="Missile Defense game promotion image" width="128" height="75" alt="Play Missile Defense on Varyn now" /></a>
                <div class="game_tab_desc">My cities are in peril! Fortunately, I have awesome laser towers and trigger happy fingers for the alien smackdown.</div>
            </div>
            <div class="game_tab_bottom"><a class="game_tab_play_btn" href="/play/?game_id=1005" title="Play Missile Defense on Varyn now"><span>Play Now</span></a></div>
        </div>
        <div class="games_promo">
            <div class="game_tab">
                <div class="game_tab_name">Gem Craft</div>
            </div>
            <div class="game_tab_middle">
                <a href="/play/?game_id=1016" title="Play Gem Craft on Varyn now"><img class="game_tab_img" src="<?php echo ($server); ?>/games/gemcraft/images/128x75.jpg" alt="Gem Craft game promotion image" width="128" height="75" alt="Play Gem Craft on Varyn now" /></a>
                <div class="game_tab_desc">Havoc and corruption swarms through the land, and you are one of those few wizards who can put an end to it. Create and combine magic gems, put them into your towers and banish the monsters back to hell!</div>
            </div>
            <div class="game_tab_bottom"><a class="game_tab_play_btn" href="/play/?game_id=1016" title="Play Gem Craft on Varyn now"><span>Play Now</span></a></div>
        </div>
        <div class="games_promo">
            <div class="game_tab">
                <div class="game_tab_name">Street Fighter II: Champion Edition</div>
            </div>
            <div class="game_tab_middle">
                <a href="/play/?game_id=1023" title="Play Street Fighter II: Champion Edition on Varyn now"><img class="game_tab_img" src="<?php echo ($server); ?>/games/streetFighterIIChampionEdition/images/128x75.jpg" alt="Street Fighter II: Champion Edition game promotion image" width="128" height="75" alt="Play Street Fighter II: Champion Edition on Varyn now" /></a>
                <div class="game_tab_desc">The top-selling arcade fighter classic from Capcom, just like you remember it.</div>
            </div>
            <div class="game_tab_bottom"><a class="game_tab_play_btn" href="/play/?game_id=1023" title="Play Street Fighter II: Champion Edition on Varyn now"><span>Play Now</span></a></div>
        </div>
        <div class="games_promo">
            <div class="game_tab">
                <div class="game_tab_name">Morningstar</div>
            </div>
            <div class="game_tab_middle">
                <a href="/play/?game_id=1047" title="Play Morningstar on Varyn now"><img class="game_tab_img" src="<?php echo ($server); ?>/games/morningstar/images/128x75.jpg" alt="Morningstar game promotion image" width="128" height="75" alt="Play Morningstar on Varyn now" /></a>
                <div class="game_tab_desc">Point 'n click adventure: your spaceship has crash landed on an alien planet. Are you resourceful enough to find a way to get back home?</div>
            </div>
            <div class="game_tab_bottom"><a class="game_tab_play_btn" href="/play/?game_id=1047" title="Play Morningstar on Varyn now"><span>Play Now</span></a></div>
        </div>
    </div>
    <div id="bottomAd" class="row">
        <?php
        $adProvider = 'google';
        include_once(VIEWS_ROOT . 'ad-spot.php');
        ?>
    </div>
</div>
<?php
include_once(VIEWS_ROOT . 'footer.php');
?>
</div>
</body>

</html>