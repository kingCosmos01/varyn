<?php
/**
 * header.php Global header used on every page to handle the following common features:
 * - Handle HTML meta data
 * - Display the global nav bar
 * - information message banner modal-message
 * - Login pop up modal modal-login
 * - subscribe to newsletter pop up modal-subscribe
 * - register pop up modal-register
 * - forgot password pop up modal-forgot-password
 * Using this also requires footer.php to close everything
 */
$pagesDoNotHaveRegistrationForms = ['resetpass', 'forgotpass', 'requestConfirm'];
if ( ! isset($isLoggedIn)) {
    $isLoggedIn = false;
}
if (empty($page)) {
    $page = 'home';
}
if (empty($pageTitle)) {
    $pageTitle = 'Varyn: Fun games you can play anytime, anywhere';
}
if (empty($pageDescription)) {
    $pageDescription = 'Varyn makes fun games using technology that performs on the most popular platforms. Cross platform friendly technologies have created an opportunity to re-invent online games for an audience that moves seamlessly between desktop, tablet, and smart-phone.';
}
if (empty($pageKeywords)) {
    $pageKeywords = 'play,game,free,online';
}
if (empty($pageFavIcon)) {
    $pageFavIcon = '/favicon.ico';
}
if (empty($pageIcon)) {
    $pageIcon = '/favicon-196x196.png';
}
if (empty($pageOGLink)) {
    $pageOGLink = currentPageURL();
}
if (empty($pageSocialImage1)) {
    $pageSocialImage1 = 'https://www.varyn.com/images/1200x600.png';
    $pageSocialImageWidth = 1200;
    $pageSocialImageHeight = 600;
}
if (empty($pageSocialImage2)) {
    $pageSocialImage2 = 'https://www.varyn.com/images/VarynIcon1080.jpg';
}
if ($page == 'play') {
    // the game play page has additional requirements:
    $screenShots = '';
    if (count($gameScreenShots) > 0) {
        foreach($gameScreenShots as $imagePath) {
            $screenShots .= ($screenShots == '' ? '' : ',') . '"' . $imagePath . '"';
        }
    }
    $gameDiscoveryTag = '
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "VideoGame",
      "mainEntityOfPage": {
        "@type": "WebPage",
        "@id": "https://google.com/videogame"
      },
      "name": "' . $pageTitle . '",
      "description": "' . $pageDescription . '",
      "url": "' . $pageOGLink . '",
      "genre": "' . $gameCategory . '",
      "accessibilityControl": "touch",
      "operatingSystem": "web",
      "icon": "' . $pageFavIcon . '",
      "gameBanner": "' . $pageSocialImage1 . '",
      "about": "https://varyn.com/about/",
      "privacyPolicyURL": "https://varyn.com/privacy/",
      "gameExecutionMode": "clientside",
      "image": [' . $screenShots . '],
      "author": {
        "@type": "Organization",
        "name": "Varyn",
        "logo": {
          "@type": "ImageObject",
          "url": "https://varyn.com/favicon-1024x1024.png"
        }
      },
      "publisher": {
        "@type": "Organization",
        "name": "Enginesis",
        "logo": {
          "@type": "ImageObject",
          "url": "https://enginesis.com/favicon-196x196.png"
        }
      }
    }
    </script>
    ';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo($pageTitle);?></title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Access-Control-Allow-Origin" content="*">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="cache-control" content="max-age=0">
    <meta http-equiv="cache-control" content="no-cache">
    <meta http-equiv="expires" content="0">
    <meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT">
    <meta http-equiv="pragma" content="no-cache">
    <meta name="format-detection" content="telephone=no">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="description" content="<?php echo($pageDescription);?>">
    <meta name="keywords" content="<?php echo($pageKeywords);?>">
    <meta name="url" content="<?php echo($pageOGLink);?>">
    <meta name="author" content="Varyn">
    <meta name="google-signin-client_id" content="<?php echo($socialServiceKeys[7]['app_id']);?>">
    <meta itemprop="name" content="<?php echo($pageTitle);?>">
    <meta itemprop="description" content="<?php echo($pageDescription);?>">
    <meta itemprop="image" content="<?php echo($pageSocialImage1);?>">
    <link rel="manifest" href="/varyn.webmanifest">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" href="<?php echo($pageFavIcon);?>">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="icon" type="image/png" href="/favicon-48x48.png" sizes="48x48">
    <link rel="icon" type="image/png" href="/favicon-196x196.png" sizes="196x196">
    <link rel="icon" type="image/png" href="/favicon-160x160.png" sizes="160x160">
    <link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96">
    <link rel="shortcut icon" href="/favicon-48x48.png">
    <link rel="manifest" href="/varyn.webmanifest">
    <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#c7254e">
    <link rel="fluid-icon" href="https://varyn.com/favicon-512x512.png" title="Varyn">
    <meta name="theme-color" content="#c7254e">
    <meta name="msapplication-TileColor" content="#c7254e">
    <meta property="fb:app_id" content="<?php echo($socialServiceKeys[2]['app_id']);?>">
    <meta property="fb:admins" content="<?php echo($socialServiceKeys[2]['admins']);?>">
    <meta property="og:title" content="<?php echo($pageTitle);?>">
    <meta property="og:url" content="<?php echo($pageOGLink);?>">
    <meta property="og:site_name" content="Varyn">
    <meta property="og:description" content="<?php echo($pageDescription);?>">
    <meta property="og:image" content="<?php echo($pageSocialImage1);?>">
    <meta property="og:image:type" content="image/png">
    <meta property="og:image:width" content="<?php echo($pageSocialImageWidth);?>">
    <meta property="og:image:height" content="<?php echo($pageSocialImageHeight);?>">
    <meta property="og:image" content="<?php echo($pageSocialImage2);?>">
    <meta property="og:image" content="https://www.varyn.com/images/1200x600.png">
    <meta property="og:image:type" content="image/png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="600">
    <meta property="og:image" content="https://www.varyn.com/images/VarynIcon640.jpg">
    <meta property="og:image:type" content="image/jpg">
    <meta property="og:image:width" content="640">
    <meta property="og:image:height" content="640">
    <meta property="og:image" content="https://www.varyn.com/images/2048x1536.png">
    <meta property="og:image:type" content="image/png">
    <meta property="og:image:width" content="2048">
    <meta property="og:image:height" content="1536">
    <meta property="og:type" content="website">
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:domain" content="varyn.com">
    <meta property="twitter:site" content="varyndev">
    <meta property="twitter:url" content="<?php echo($pageOGLink);?>">
    <meta property="twitter:creator" content="varyndev">
    <meta property="twitter:title" content="<?php echo($pageTitle);?>">
    <meta property="twitter:description" content="<?php echo($pageDescription);?>">
    <meta property="twitter:image:src" content="<?php echo($pageSocialImage1);?>">
    <meta property="twitter:image:width" content="<?php echo($pageSocialImageWidth);?>">
    <meta property="twitter:image:height" content="<?php echo($pageSocialImageHeight);?>">
    <link href="/common/bootstrap.min.css" rel="stylesheet">
    <link href="/common/varyn.css" rel="stylesheet">
    <?php if (isset($gameDiscoveryTag)) { echo($gameDiscoveryTag); } ?>
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-YL1KFE561M"></script>
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', 'G-YL1KFE561M');
    </script>
    <script src="/common/head.load.min.js"></script>
</head>
<body>
<div class="modal fade" id="modal-message" tabindex="-1" role="dialog" aria-labelledby="modalMessageLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content modal-content-varyn">
            <div class="modal-header">
                <h3 class="modal-title" id="modalMessageLabel"><span class="varyn-shield-icon"></span> <span id="infoMessageTitle">Message Title</span></h3>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body infoMessageArea" id="infoMessageArea">
                <p></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<?php
    if ( ! in_array($page, $pagesDoNotHaveRegistrationForms)) {
?>
<div class="modal fade" id="modal-subscribe" tabindex="-1" role="dialog" aria-labelledby="modalSubscribeLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content modal-content-varyn">
            <div class="modal-header">
                <h3 class="modal-title" id="modalSubscribeLabel"><span class="varyn-shield-icon"></span> Subscribe</h3>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <p>Sign up for our email updates and we will let you know when we have new games, prizes, interesting things to say. We will not abuse this privilege. <a href="/privacy/" class="text-muted small" title="Review our privacy policy" alt="Review our privacy policy">Review our privacy policy.</a></p>
                <form id="subscribe-form">
                    <div class="form-group leader-1 trailer-1 left-2 right-2">
                        <label for="subscribe-email">Email address</label>
                        <input type="email" name="subscribe-email" class="form-control required" id="subscribe-email" placeholder="Your email address" autocapitalize="off" autocorrect="off" autocomplete="email" />
                    </div>
                </form>
                <div class="modalMessageArea">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" value="Subscribe" name="subscribe" id="subscribeButton" onclick="varynApp.popupSubscribeClicked();">Subscribe</button>
            </div>
        </div>
    </div>
</div>
<?php
        $hackerVerification = makeInputFormHackerToken();
        if ( ! $isLoggedIn) {
?>
<div class="modal fade" id="modal-login" tabindex="-1" role="dialog" aria-labelledby="modalLoginLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content modal-content-varyn">
            <div class="modal-header">
                <h3 class="modal-title" id="modalLoginLabel"><span class="varyn-shield-icon"></span> Member Login</h3>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <form id="login-form" method="POST" action="/profile/" onsubmit="return varynApp.popupLoginClicked();">
                    <div class="leader-1 trailer-1 left-2 right-2">
                        <div class="form-group">
                            <label for="login_username">User name:</label>
                            <input type="text" id="login_username" name="login_username" tabindex="17" maxlength="20" class="popup-form-input required" autocorrect="off" autocomplete="name"/>
                        </div>
                        <div class="form-group">
                            <label for="login_password">Password:</label>
                            <input type="password" id="login_password" name="login_password" tabindex="18" maxlength="20" class="popup-form-input required" />
                        </div>
                        <div class="form-group">
                            <input type="text" name="login_email" class="popup-form-address-input" />
                            <input type="hidden" name="all-clear" value="<?php echo($hackerVerification);?>" />
                            <span class="rememberme-container"><input type="checkbox" tabindex="20" checked="checked" name="login_rememberme" id="login_rememberme"><label for="login_rememberme">Remember Me</label></span>
                            <div class="loginPopup_auxLinks">
                                <a id="loginPopup_forgot_password" href="#" onclick="varynApp.popupCloseClicked(); varynApp.showForgotPasswordPopup(1);" tabindex="21">Forgot password?</a><br/>Not a member? <a id="loginPopup_signup" href="#" onclick="varynApp.popupCloseClicked(); varynApp.showRegistrationPopup(1);" tabindex="22">Sign up!</a>
                            </div>
                            <input type="hidden" name="action" value="popuplogin" />
                        </div>
                    </div>
                </form>
                <div class="modalMessageArea">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" value="loginButton" name="loginButton" id="loginButton" onclick="varynApp.popupLoginClicked();">Login</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal-register" tabindex="-1" role="dialog" aria-labelledby="modalRegisterLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content modal-content-varyn">
            <div class="modal-header">
                <h3 class="modal-title" id="modalRegisterLabel"><span class="varyn-shield-icon"></span> Register</h3>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <p>Sign up now to track your progress, earn rewards, and compete with friends:</p>
                <form id="registration-form" method="POST" action="/profile/" onsubmit="return varynApp.popupRegistrationClicked();">
                    <div class="leader-1 trailer-1 left-1 right-1">
                        <div class="form-group">
                            <label for="register-email">Email:</label>
                            <input type="email" name="register-email" class="popup-form-input required email" id="register-email" placeholder="Your email address" autocapitalize="off" autocorrect="off" autocomplete="email" required maxlength="80" tabindex="10"/>
                        </div>
                        <div class="form-group">
                            <label for="register-username">User name:</label>
                            <input type="text" name="register-username" class="popup-form-input required username" id="register-username" placeholder="A unique user name" data-target="popup_user_name_unique" autocorrect="off" autocomplete="name" required maxlength="50" tabindex="11"/><span id="popup_user_name_unique" class="username-is-not-unique"></span>
                        </div>
                        <div class="form-group">
                            <label for="register-password">Password:</label>
                            <input type="password" name="register-password" class="popup-form-input required password" id="register-password" placeholder="A secure password" autocomplete="current-password" autocorrect="off" required maxlength="20" tabindex="12"/><div id="optional-small-label" class="checkbox optional-small"><label for="ShowPassword" onclick="varynApp.onClickShowPassword();"><input type="checkbox" name="ShowPassword" id="register-showpassword"> <span id="register-showpassword-text">Show</span> <span id="register-showpassword-icon" class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></label></div>
                        </div>
                        <div class="form-group">
                            <div class="validation-slider-area">
                                <label for="register-agreement">I agree to the <a href="/tos/" target="_popup">Terms of Use</a></label>
                                <div class="register-agreement-slider"><small class="slider-label">No</small><input type="range" name="register-agreement" class="validation-slider" id="register-agreement" placeholder="Slide this all the way left to agree" tabindex="13" min="0" max="2" /><small class="slider-label">Yes</small></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="text" name="emailaddress" class="popup-form-address-input" />
                            <input type="hidden" name="all-clear" value="<?php echo($hackerVerification);?>" />
                            <span class="rememberme-container"><input type="checkbox" checked="checked" name="register-rememberme" id="register-rememberme" tabindex="16"><label for="register-rememberme">Remember Me</label></span>
                            <input type="hidden" name="action" value="popupregister" />
                        </div>
                    </div>
                </form>
                <div class="modalMessageArea">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" value="Register" name="popupregister" id="registerButton" onclick="varynApp.popupRegistrationClicked();">Register</button>
            </div>
        </div>
    </div>
</div>
<?php
        }
?>
<div class="modal fade" id="modal-forgot-password" tabindex="-1" role="dialog" aria-labelledby="modalForgotPasswordLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content modal-content-varyn">
            <div class="modal-header">
                <h3 class="modal-title" id="modalForgotPasswordLabel"><span class="varyn-shield-icon"></span> Forgot Password</h3>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <p>Please identify your account. We will send email to the address on the account to help you reset your password.</p>
                <form id="forgot-password-form" method="POST" action="/profile/" onsubmit="return varynApp.popupForgotPasswordClicked();">
                    <div class="leader-1 trailer-1 left-2 right-2">
                        <div class="form-group">
                            <label for="forgotpassword_username">User name:</label>
                            <input type="text" id="forgotpassword_username" name="forgotpassword_username" tabindex="23" maxlength="20" class="popup-form-input required"  placeholder="Your user name" autocorrect="off" autocomplete="name"/>
                        </div>
                        <div class="form-group">
                            <label for="forgotpassword_email">Email:</label>
                            <input type="email" id="forgotpassword_email" name="forgotpassword_email" tabindex="24" maxlength="80" class="popup-form-input required email" placeholder="Your email address" autocapitalize="off" autocorrect="off" autocomplete="email"/>
                        </div>
                        <div class="form-group">
                            <input type="hidden" name="action" value="forgotpassword" />
                            <input type="text" name="emailaddress" class="popup-form-address-input" />
                            <input type="hidden" name="all-clear" value="<?php echo($hackerVerification);?>" />
                        </div>
                    </div>
                </form>
                <div class="modalMessageArea">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" value="Reset" name="Reset" id="forgot-password-button" onclick="varynApp.popupForgotPasswordClicked();">Reset</button>
            </div>
        </div>
    </div>
</div>
<?php
    }
    if ($isLoggedIn) {
        $notificationCount = 0;
        $userNotifications = $notificationCount > 0 ? '&nbsp;<span class="badge badge-pill badge-success badge-nav">' . $notificationCount . '</span>' : '';
        $userLoggedInMenuItem = '<span class="glyphicon glyphicon-user badge-nav"></span> Profile' . $userNotifications; // TODO: show Avatar, User-name, Reputation swatch
    } else {
        $userLoggedInMenuItem = '<span class="glyphicon glyphicon-user badge-nav"></span> Login';
    }
    $newGameCount = 0;
    $newGamesPill = $newGameCount > 0 ? '&nbsp;<span class="badge badge-pill badge-success badge-nav">' . $newGameCount . '</span>' : '';
    $newBlogPosts = 0;
    $newBlogPill = $newBlogPosts > 0 ? '&nbsp;<span class="badge badge-pill badge-success badge-nav">' . $newBlogPosts . '</span>' : '';
?>
<div class="container navbar-wrapper" id="varyn-navbar">
    <div class="navbar navbar-default navbar-static-top navbar-expand-sm navbar-light bg-light" role="navigation">
        <a class="navbar-brand" href="/">
            <img src="/images/logosmall.png" alt="Varyn small logo" />
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item navbar-item<?php if ($page == 'home') { echo(' active'); } ?>" role="presentation">
                    <a class="nav-link" href="/">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-house" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M2 13.5V7h1v6.5a.5.5 0 0 0 .5.5h9a.5.5 0 0 0 .5-.5V7h1v6.5a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 13.5zm11-11V6l-2-2V2.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5z"/>
                        <path fill-rule="evenodd" d="M7.293 1.5a1 1 0 0 1 1.414 0l6.647 6.646a.5.5 0 0 1-.708.708L8 2.207 1.354 8.854a.5.5 0 1 1-.708-.708L7.293 1.5z"/>
                    </svg> Home</a>
                </li>
                <li class="nav-item navbar-item<?php if ($page == 'games') { echo(' active'); } ?>" role="presentation">
                    <a class="nav-link" href="/games/">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-joystick" viewBox="0 0 16 16">
                        <path d="M10 2a2 2 0 0 1-1.5 1.937v5.087c.863.083 1.5.377 1.5.726 0 .414-.895.75-2 .75s-2-.336-2-.75c0-.35.637-.643 1.5-.726V3.937A2 2 0 1 1 10 2z"/>
                        <path d="M0 9.665v1.717a1 1 0 0 0 .553.894l6.553 3.277a2 2 0 0 0 1.788 0l6.553-3.277a1 1 0 0 0 .553-.894V9.665c0-.1-.06-.19-.152-.23L9.5 6.715v.993l5.227 2.178a.125.125 0 0 1 .001.23l-5.94 2.546a2 2 0 0 1-1.576 0l-5.94-2.546a.125.125 0 0 1 .001-.23L6.5 7.708l-.013-.988L.152 9.435a.25.25 0 0 0-.152.23z"/>
                    </svg> Games<?php echo($newGamesPill);?></a>
                </li>
                <li class="nav-item navbar-item<?php if ($page == 'blog') { echo(' active'); } ?>" role="presentation">
                    <a class="nav-link" href="/blog/">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-journal-richtext" viewBox="0 0 16 16">
                        <path d="M7.5 3.75a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0zm-.861 1.542 1.33.886 1.854-1.855a.25.25 0 0 1 .289-.047L11 4.75V7a.5.5 0 0 1-.5.5h-5A.5.5 0 0 1 5 7v-.5s1.54-1.274 1.639-1.208zM5 9.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5z"/>
                        <path d="M3 0h10a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2v-1h1v1a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1v1H1V2a2 2 0 0 1 2-2z"/>
                        <path d="M1 5v-.5a.5.5 0 0 1 1 0V5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1H1zm0 3v-.5a.5.5 0 0 1 1 0V8h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1H1zm0 3v-.5a.5.5 0 0 1 1 0v.5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1H1z"/>
                    </svg> Blog<?php echo($newBlogPill);?></a>
                </li>
                <li class="nav-item navbar-item<?php if ($page == 'profile') { echo(' active'); } ?>" role="presentation">
                    <a class="nav-link" href="/profile/"><?php echo($userLoggedInMenuItem);?></a>
                </li>
            </ul>
            <form class="navbar-form navbar-right" role="search" method="GET" action="/games/">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Search" name="q">
                    <button type="submit" class="btn btn-default">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>