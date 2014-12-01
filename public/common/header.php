    <header data-role="header">
    <div id="page_header">
        <div id="page_header_left">
            <h1>Varyn</h1>
        </div>
        <div id="page_header_right">
<?php
        if ($isLoggedIn) {
?>
            <div id="header_profile">
                <img id="header_user_profile_img" class="user_profile_img" src="/images/avatar_tmp.jpg" width="30" height="30" border="0" title="Dark Matters" />&nbsp;<span id="header_username" class="header-username">Dark Matters</span>&nbsp;<span class="header-rank-prompt">Site rank:</span>&nbsp;<span id="header_siterank" class="header-rank">30</span>
            </div>
<?php
        } else {
?>
            <div id="header_login">
                <div id="header_login_subitems">
                    <a id="facebook-connect-button" title="Login with Facebook" href="javascript: loginFacebook();"></a>
                    <a id="register_now">Sign Up With Email</a>
                    <a id="forgot_password" href="#">Forgot password?</a>
                </div>
                <div>
                    <form id="login-form" action="/services/MyProfile.php" method="POST" onSubmit="return validateLoginParameters();">
                    <span class="header-login-prompt" data-role="none">Login:</span><input type="text" name="username" maxlength="20" class="header-login-input" data-role="none" />&nbsp;<span class="header-login-prompt"data-role="none">Password:</span><input type="password" name="password" maxlength="20" class="header-login-input" data-role="none" /><a id="submit-button-small" href="javascript: loginSubmit();" title="Login" data-role="none" data-inline="true"></a><input type="hidden" name="action" value="login" />
                    </form>
                </div>
            </div>
<?php
        }
?>
        </div>
    </div>
    <div id="page_top_navigation">
        <div id="header_menu">
            <div id="top_header"></div>
            <div id="main_menu">
                <ul id="mainmenuitems">
                    <li><a data-role="none" href="/services/AllGames.php"<?php echo(isset($page) && $page == 'AllGames' ? ' id="menuitem_active"' : ''); ?>>All Games</a></li>
                    <li><a data-role="none" href="/services/Featured.php"<?php echo(isset($page) && $page == 'Featured' ? ' id="menuitem_active"' : ''); ?>>Featured</a></li>
                    <li><a data-role="none" href="/blog/index.php"<?php echo(isset($page) && $page == 'blog' ? ' id="menuitem_active"' : ''); ?>>Blog</a></li>
                    <li><a data-role="none" href="/services/Community.php"<?php echo(isset($page) && $page == 'Community' ? ' id="menuitem_active"' : ''); ?>>Community</a></li>
                    <li><a data-role="none" href="/services/MyProfile.php"<?php echo(isset($page) && $page == 'MyProfile' ? ' id="menuitem_active"' : ''); ?>>My Profile</a></li>
                </ul>
            </div>
            <div id="main_menu_search">
                <input type="text" id="search-query" maxlength="80" size="30" autocomplete="true" placeholder="Enter search" data-role="none" /><a id="search-button-small" href="javascript: submitSearch();" title="Search Varyn Games"></a>
            </div>
        </div>
    </div>
    </header>
<?php
    if ( ! $isLoggedIn) {
?>
    <div id="popupCover"></div>
    <div id="popupErrorMessage" style="display:none;"></div>
    <div id="popupRegistration" style="display:none;">
        <a href="#" id="closePopup" title="Close"></a>
        <div class="dialogHeader">
            <h1 class="dialogTitle">Register With Varyn</h1>
        </div>
        <form id="registration-form" method="POST" action="/services/MyProfile.php">
            <table>
                <tr><td colspan="2"><p>Some text goes here like we don't steal your info, you are protected, <a href="/tou.php" target="_popup">ToS</a>, <a href="/Privacy.php" target="_popup">PP</a>, etc.</p></td></tr>
                <tr><td>User Name:</td><td><input type="text" name="username" maxlength="20" /></td></tr>
                <tr><td>Password: </td><td><input type="password" name="password" maxlength="20" /></td></tr>
                <tr><td>Your Email: </td><td><input type="email" name="email" maxlength="80" /></td></tr>
                <tr><td>Your Name: </td><td><input type="text" name="fullname" maxlength="50" /></td></tr>
                <tr><td>Your Location: </td><td><input type="text" name="location" maxlength="80" /></td></tr>
                <tr><td>Your are: </td><td><input type="radio" name="gender" value="M">Male</input><img src="/images/clear.png" border="0" width="48" height="12" /><input type="radio" name="gender" value="F">Female</input></td></tr>
                <tr><td>Your Date of Birth: </td><td><input type="date" name="dob" /></td></tr>
                <tr><td>Your tag line: </td><td><input type="text" name="tagline" maxlength="255" /></td></tr>
                <tr><td colspan="2" class="table-bottom-nav">
                        <input type="checkbox" name="agreement">I agree to the <a href="/tou.php" target="_popup">Terms of Service</a></input><br />
                        <a href="javascript: registrationFormSubmit();" id="submitPopup">Submit</a><input type="hidden" name="action" value="register" /><input type="hidden" name="captcha" value="deadmen6" /><img src="/images/clear.png" border="0" width="32" height="22" /><a href="#" id="cancelPopup">Cancel</a>
                    </td></tr>
            </table>
        </form>
    </div>
<?php
    }
