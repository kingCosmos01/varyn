<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package WordPress
 * @subpackage Twenty_Twelve
 * @since Twenty Twelve 1.0
 */
if ( ! defined('ROOTPATH') ) {
    define('ROOTPATH', $_SERVER['DOCUMENT_ROOT']);
}

require_once(ROOTPATH . '/../services/common.php');
$page = 'blog';

?><!DOCTYPE html>
<!--[if IE 7]>
<html class="ie ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 7) | !(IE 8)  ]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width" />
<title><?php wp_title( '|', true, 'right' ); ?></title>
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<?php // Loads HTML5 JavaScript file to add support for HTML5 elements in older IE versions. ?>
<!--[if lt IE 9]>
<script src="<?php echo get_template_directory_uri(); ?>/js/html5.js" type="text/javascript"></script>
<![endif]-->
<link href="/common/main.css" rel="stylesheet" type="text/css">
<?php wp_head(); ?>
<script type="text/javascript" src="/common/main.js"></script>
</head>

<body <?php body_class(); ?>>
<div id="page" class="hfeed site">
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
                        <img class="user_profile_img" src="/images/avatar_tmp.jpg" width="30" height="30" border="0" title="Dark Matters" />&nbsp;<span class="header-username">Dark Matters</span>&nbsp;<span class="header-rank-prompt">Site rank:</span>&nbsp;<span class="header-rank">30</span>
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
                            <form id="login-form" action="/services/MyProfile.php" method="POST" onSubmit="return validateLoginParameters(this);">
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
                    <input type="text" maxlength="80" size="30" autocomplete="true" placeholder="Enter search" data-role="none" /><a id="search-button-small" href="javascript: submitSearch();" title="Search Varyn Games"></a>
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
        <div>
            <h1>Register With Varyn</h1>
        </div>
        <form id="registration-form" method="POST" action="/services/MyProfile.php">
            <table>
                <tr><td colspan="2"><p>Some text goes here like we don't steal your info, you are protected, ToS, PP, etc.</p></td></tr>
                <tr><td>User Name:</td><td><input type="text" name="username" maxlength="20" /></td></tr>
                <tr><td>Password: </td><td><input type="password" name="password" maxlength="20" /></td></tr>
                <tr><td>Your Email: </td><td><input type="email" name="email" maxlength="80" /></td></tr>
                <tr><td>Your Name: </td><td><input type="text" name="fullname" maxlength="50" /></td></tr>
                <tr><td>Your Location: </td><td><input type="text" name="location" maxlength="80" /></td></tr>
                <tr><td>Your Date of Birth: </td><td><input type="date" name="dob" /></td></tr>
                <tr><td>Your tag line: </td><td><input type="text" name="tagline" maxlength="255" /></td></tr>
                <tr><td colspan="2" class="table-bottom-nav"><a href="javascript: registrationFormSubmit();" id="submitPopup">Submit</a><input type="hidden" name="action" value="register" /><input type="hidden" name="captcha" value="deadmen6" />&nbsp;&nbsp;<a href="#" id="cancelPopup">Cancel</a></td></tr>
            </table>
        </form>
    </div>
<?php
    }
?>
	<div id="main" class="wrapper">