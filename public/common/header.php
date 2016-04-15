<?php
    if ( ! isset($isLoggedIn)) {
        $isLoggedIn = false;
    }
    if ( ! isset($page)) {
        $page = 'home';
    }
?>
<div id="popupCover" class="popupCover">
    <div id="subscribePopup" class="popupFrame">
        <div class="popupCloseButton" onclick="popupCloseClicked();"><img src="/images/close-button.png" width="24" height="24" border="0"/></div>
        <img src="/images/VarynIcon120x120.png" class="logoImg">
        <h3><span class="varyn-shield-icon"></span>Join Our Mailing List?</h3>
        <p>Sign up for our email updates and we will let you know when we have new games, prizes, interesting things to say. We will not abuse this privilege. <a href="/Privacy.php" class="text-muted small" title="Review our privacy policy" alt="Review our privacy policy">Review our privacy policy.</a></p>
        <div class="popupFieldGroup">
            <input type="email" name="email" class="form-control required email" id="emailInput" placeholder="Your email address"/><input type="submit" value="Subscribe" name="subscribe" id="subscribeButton" class="btn btn-default"  onclick="popupSubscribeClicked();"/>
        </div>
        <div class="popupMessageArea">
            <div id="popupMessageResponse" class="popupMessageResponseError">This is the response from the server</div>
        </div>
    </div>
<?php
    if ( ! $isLoggedIn) {
        $hackerVerification = makeInputFormHackerToken();
?>
        <div id="registrationPopup" class="popupFrame">
            <form id="registration-form" method="POST" action="profile.php" onsubmit="return varynApp.popupRegistrationClicked();">
                <div class="popupCloseButton" onclick="varynApp.popupCloseClicked();"><img src="/images/close-button.png" width="24" height="24" border="0"/></div>
                <h3><span class="varyn-shield-icon"></span>Registration</h3>
                <p>Sign up now to track your progress, earn rewards, and compete with friends!</p>
                <div class="popupFieldGroup">
                    <label for="register-email">Email:</label><input type="email" name="register-email" class="popup-form-input required email" id="register-email" placeholder="Your email address" tabindex="10" autocapitalize="off" autocorrect="off" autocomplete="email"/><br/>
                    <label for="register-username">User name:</label><input type="text" name="register-username" class="popup-form-input required username" id="register-username" placeholder="A unique user name" data-target="popup_user_name_unique" tabindex="11" autocorrect="off" autocomplete="name"/><img id="popup_user_name_unique" class="username-is-not-unique" src="/images/red_x.png" width="32" height="32"/><br/>
                    <label for="register-password">Password:</label><input type="password" name="register-password" class="popup-form-input required password" id="register-password" placeholder="A secure password" tabindex="12"/>
                    <div class="validation-slider-area">
                        <label for="register-agreement">I agree to the <a href="/tos.php" target="_popup">Terms of Use</a></label><br/>
                        <span><small>No</small>&nbsp;&nbsp;<input type="range" name="register-agreement" class="validation-slider" id="register-agreement" placeholder="Slide this all the way left to agree" tabindex="13" min="0" max="2" />&nbsp;&nbsp;<small>Yes</small></span>
                    </div>
                    <input type="text" name="emailaddress" class="popup-form-address-input" /><input type="hidden" name="all-clear" value="<?php echo($hackerVerification);?>" /><br />
                    <input type="submit" value="Register" name="popupregister" id="registerButton" class="btn btn-success" tabindex="15"/>
                    <span id="rememberme-container"><input type="checkbox" checked="checked" name="rememberme" id="rememberme" tabindex="16"><label for="rememberme">Remember Me</label></span>
                    <input type="hidden" name="action" value="popupregister" />
                </div>
                <div class="popupMessageArea">
                    <div class="popupMessageResponseError">This is the response from the server</div>
                </div>
            </form>
        </div>
        <div id="loginPopup" class="popupFrame">
            <form id="login-form" method="POST" action="profile.php" onsubmit="return varynApp.popupLoginClicked();">
                <div class="popupCloseButton" onclick="varynApp.popupCloseClicked();"><img src="/images/close-button.png" width="24" height="24" border="0"/></div>
                <h3><span class="varyn-shield-icon"></span>Member login:</h3>
                <div class="popupFieldGroup">
                    <label for="login_username">User name:</label><input type="text" id="login_username" name="login_username" tabindex="17" maxlength="20" class="popup-form-input required" autocorrect="off" autocomplete="name"/><br/>
                    <label for="login_password">Password:</label><input type="password" id="login_password" name="login_password" tabindex="18" maxlength="20" class="popup-form-input required" /><br/>
                    <input type="submit" value="Login >" name="loginButton" id="loginButton" class="btn btn-success" tabindex="19"/>
                    <span id="rememberme-container"><input type="checkbox" tabindex="20" checked="checked" name="rememberme" id="rememberme"><label for="rememberme">Remember Me</label></span>
                    <div class="loginPopup_auxLinks"><a id="loginPopup_forgot_password" href="#" tabindex="21">Forgot password?</a><br/>Not a member? <a id="loginPopup_signup" href="#" tabindex="22">Sign up!</a></div>
                    <input type="hidden" name="action" value="popuplogin" />
                </div>
                <div class="popupMessageArea">
                    <div class="popupMessageResponseError">This is the response from the server</div>
                </div>
            </form>
        </div>
        <div id="forgotPasswordPopup" class="popupFrame">
            <form id="forgot-password-form" method="POST" action="profile.php" onsubmit="return varynApp.popupForgotPasswordClicked();">
                <div class="popupCloseButton" onclick="varynApp.popupCloseClicked();"><img src="/images/close-button.png" width="24" height="24" border="0"/></div>
                <h3><span class="varyn-shield-icon"></span>Forgot password</h3>
                <p>Please identify your account. We will send email to the address set on the account to allow you to reset your password.</p>
                <div class="popupFieldGroup">
                    <label for="forgotpassword_username">User name:</label><input type="text" id="forgotpassword_username" name="forgotpassword_username" tabindex="23" maxlength="20" class="popup-form-input required"  placeholder="Your user name" autocorrect="off" autocomplete="name"/><br/>
                    <label for="forgotpassword_email">Email:</label><input type="email" id="forgotpassword_email" name="forgotpassword_email" tabindex="24" maxlength="80" class="popup-form-input required email" placeholder="Your email address" autocapitalize="off" autocorrect="off" autocomplete="email"/><br/>
                    <input type="submit" class="btn btn-success" id="forgot-password-button" value="Reset" tabindex="25"/>
                    <input type="hidden" name="action" value="forgotpassword" /><input type="text" name="emailaddress" class="popup-form-address-input" /><input type="hidden" name="all-clear" value="<?php echo($hackerVerification);?>" />
                </div>
                <div class="popupMessageArea">
                    <div class="popupMessageResponseError">This is the response from the server</div>
                </div>
            </form>
        </div>
<?php
        $userLoggedInMenuItem = '<span class="glyphicon glyphicon-user"></span> Login';
    } else {
        $userLoggedInMenuItem = '<span class="glyphicon glyphicon-user"></span> Profile'; // TODO: show Avatar, User-name, Reputation swatch
    }
?>
    <div id="popupErrorMessage" style="display:none;"></div>
</div>
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
                        <li role="presentation"<?php if ($page == 'home') { echo(' class="active"'); } ?>><a href="/"><span class="glyphicon glyphicon-home"></span> Home</a></li>
                        <li role="presentation"<?php if ($page == 'allgames') { echo(' class="active"'); } ?>><a href="/allgames.php"><span class="glyphicon glyphicon-king"></span> All Games</a></li>
                        <li role="presentation"<?php if ($page == 'blog') { echo(' class="active"'); } ?>><a href="/blog"><span class="glyphicon glyphicon-list"></span> Blog</a></li>
                        <li role="presentation"<?php if ($page == 'profile') { echo(' class="active"'); } ?>><a href="/profile.php"><?php echo($userLoggedInMenuItem);?></a></li>
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
</div>