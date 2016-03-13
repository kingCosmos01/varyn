<?php
    require_once('../services/common.php');
    $page = 'profile';
    $search = getPostOrRequestVar('q', null);
    if ($search != null) {
        header('location:/allgames.php?q=' . $search);
        exit;
    }
    $showSubscribe = getPostOrRequestVar('s', '0');

    $action = '';
    $userName = '';
    $password = '';
    $email = '';
    $fullname = '';
    $location = '';
    $tagline = '';
    $dateOfBirth = '';
    $gender = '';
    $captcha = '';
    $agreement = false;
    $showRegistrationForm = false;
    $errorMessage = '<p>&nbsp;</p>';
    $errorFieldId = '';
    $inputFocusId = '';
    $debug = (int) strtolower(getPostOrRequestVar("debug", 0));
    $action = strtolower(getPostOrRequestVar("action", ''));
    if ($action == 'login') {
        $userName = getPostOrRequestVar("login_form_username");
        $password = getPostOrRequestVar("login_form_password");
        if ($userName == '' && $password == '') {
            $userName = getPostOrRequestVar("login_username");
            $password = getPostOrRequestVar("login_password");
        }
        $userInfo = $enginesis->userLogin($userName, $password);
        if ($userInfo == null) {
            $error = $enginesis->getLastError();
            if ($error != null) {
                $errorMessage = '<p class="error-text">Your account could not be logged in at this time. ' . errorToLocalString($error['message']) . '</p>';
            } else {
                $errorMessage = '<p class="error-text">Your user name and password did not match.</p>';
            }
            $inputFocusId = 'login_form_username';
        } else {
            $isLoggedIn = true;
            // TODO: Is there anything else we should save locally to avoid unnecessary server round-trips?
            // $userInfo Object ( [user_id] => 10239 [site_id] => 106 [user_name] => Varyn [real_name] => Varyn [site_user_id] => [dob] => 2004-02-16 [gender] => F [city] => [state] => [zipcode] => [country_code] => [email_address] => john@varyn.com [mobile_number] => [im_id] => [agreement] => 1 [img_url] => [about_me] => [date_created] => 2016-02-16 20:47:45 [date_updated] => [source_site_id] => 106 [last_login] => 2016-02-20 22:27:38 [login_count] => 34 [tagline] => [additional_info] => [reg_confirmed] => 1 [user_status_id] => 1 [site_currency_value] => 0 [site_experience_points] => 0 [view_count] => 0 [access_level] => 10 [role_name] => [user_rank] => 10001 [session_id] => cecfe3b4b5dac00d464eff98ba5c75c3 [cr] => d2a1bae6ef968501b648ccf253451a1a [authtok] => Dk39dEasNBgO79Mp0gjXnvGYBEPP06d5Pd KmpdvCnVEehliQpl5eezAdVfc9t9xsE7RDp5i9rPDjj73TXxaW1XOrVjWHwZsnQ0q/GsHtWl4tDGgS/lTMA== )
        }
    } elseif ($action == 'signup') {
        $showRegistrationForm = true;
        $inputFocusId = 'register_form_email';
    } elseif ($action == 'popupregister') {
        $userName = getPostOrRequestVar("register-username", '');
        $password = getPostOrRequestVar("register-password", '');
        $email = getPostOrRequestVar("register-email", '');
        $realName = $userName;
        $location = '';
        $tagline = '';
        $date12YearsAgo = strtotime('-12 year');
        $dateOfBirth = date('Y-m-d', $date12YearsAgo);
        $gender = 'F';
        $captcha = getPostOrRequestVar("register-captcha", '');
        $agreement = getPostOrRequestVar("register-agreement", false);
        $parameters = array(
            'user_name' => $userName,
            'password' => $password,
            'email_address' => $email,
            'real_name' => $realName,
            'location' => $location,
            'tagline' => $tagline,
            'dob' => $dateOfBirth,
            'gender' => $gender,
            'captcha' => $captcha,
            'agreement' => $agreement
            );
        $invalidFields = $enginesis->userRegistrationValidation(0, $parameters);
        if ($invalidFields == null) {
            $userInfo = $enginesis->userRegistration($parameters);
            $error = $enginesis->getLastError();
            if ($error != null) {
                $errorMessage = '<p class="error-text">Registration not accepted. ' . errorToLocalString($error['message']) . '</p>';
                $inputFocusId = 'register-email';
                $showRegistrationForm = true;
            } else {
                $errorMessage = '';
            }
        } else {
            // TODO: handle invalid fields by showing UI
            $inputFocusId = 'register-email';
            print_r($invalidFields);
        }
        $action = 'register';
    } elseif ($action == 'register') {
        $action = 'register';
        $userName = getPostOrRequestVar("register_form_username", '');
        $password = getPostOrRequestVar("register_form_password", '');
        $email = getPostOrRequestVar("register_form_email", '');
        $fullname = getPostOrRequestVar("register_form_fullname", '');
        $location = getPostOrRequestVar("register_form_location", '');
        $tagline = getPostOrRequestVar("register_form_tagline", '');
        $dateOfBirth = getPostOrRequestVar("register_form_dob", '');
        $gender = getPostOrRequestVar("register_form_gender", 'F');
        $captcha = getPostOrRequestVar("register_form_captcha", '');
        $agreement = getPostOrRequestVar("register_form_agreement", false);
        $parameters = array(
            'user_name' => $userName,
            'password' => $password,
            'email_address' => $email,
            'real_name' => $fullname,
            'location' => $location,
            'tagline' => $tagline,
            'dob' => $dateOfBirth,
            'gender' => $gender,
            'captcha' => $captcha,
            'agreement' => $agreement
        );
        $invalidFields = $enginesis->userRegistrationValidation(0, $parameters);
        if ($invalidFields == null) {
            $userInfo = $enginesis->userRegistration($parameters);
            $error = $enginesis->getLastError();
            if ($error != null) {
                $errorMessage = '<p class="error-text">Registration not accepted. ' . errorToLocalString($error['message']) . '</p>';
                $inputFocusId = 'register_form_email';
                $showRegistrationForm = true;
            } else {
                $errorMessage = '';
            }
        } else {
            // TODO: handle invalid fields by showing UI
            $inputFocusId = 'register_form_email';
            $showRegistrationForm = true;
            print_r($invalidFields);
        }
    } elseif ($action == 'update') {
        $action = 'update';
        $userName = getPostOrRequestVar("register_form_username", '');
        $password = getPostOrRequestVar("register_form_password", '');
        $email = getPostOrRequestVar("register_form_email", '');
        $fullname = getPostOrRequestVar("register_form_fullname", '');
        $location = getPostOrRequestVar("register_form_location", '');
        $tagline = getPostOrRequestVar("register_form_tagline", '');
        $dateOfBirth = getPostOrRequestVar("register_form_dob", '');
        $gender = getPostOrRequestVar("register_form_gender", 'F');
        $captcha = getPostOrRequestVar("register_form_captcha", '');
        $agreement = getPostOrRequestVar("register_form_agreement", false);
        $parameters = array(
            'user_name' => $userName,
            'password' => $password,
            'email_address' => $email,
            'real_name' => $fullname,
            'location' => $location,
            'tagline' => $tagline,
            'dob' => $dateOfBirth,
            'gender' => $gender,
            'captcha' => $captcha,
            'agreement' => $agreement
        );
        $invalidFields = $enginesis->userRegistrationValidation($userId, $parameters);
        if ($invalidFields == null) {
            $userInfo = $enginesis->userRegistrationUpdate($userId, $parameters);
            print_r($userInfo);
        } else {
            // TODO: handle invalid fields by showing UI
            $inputFocusId = 'register_form_email';
            print_r($invalidFields);
        }
    } elseif ($action == 'forgotpassword') {
        $userName = getPostOrRequestVar("forgotpassword_username", '');
        $email = getPostOrRequestVar("forgotpassword_email", '');
        $result = $enginesis->userForgotPassword($userName, $email);
        if ($result) {
            $errorMessage = '<p class="info-text">Email has been sent to the owner of this account. Please follow the instructions in that message to reset the account password.</p>';
            $inputFocusId = 'login_form_username';
        } else {
            $error = $enginesis->getLastError();
            $errorCode = $error['message'];
            if ($errorCode == 'SYSTEM_ERROR') {
                $errorMessage = '<p class="error-text">' . errorToLocalString($errorCode) . '</p>';
            } else {
                $errorMessage = '<p class="error-text">' . errorToLocalString($errorCode) . '<br/>' . $userName . ', ' . $email . '. Please check your entry.</p>';
            }
            $inputFocusId = 'profile_forgot_password';
        }
    } elseif ($action == 'logout') {
        $result = $enginesis->userLogout();
        $userName = '';
        $password = '';
    } else {
        $action = '';
        $userName = '';
        $password = '';
    }
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
    <meta name="google-signin-client_id" content="AIzaSyD22xO1Z71JywxmKfovgRuqZUHRFhZ8i7A.apps.googleusercontent.com">
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
    <div id="user_profile">
<?php
    if ($debug) {
        echo("<p>Page called with action $action; User name $userName; password $password; email $email; Fullname: $fullname; Loc: $location; Tag: $tagline; DOB: $dateOfBirth; captcha: $captcha;</p>");
    }
    if ($isLoggedIn) {
?>
        <h3>Welcome <?php echo($userInfo->user_name);?>!</h3><p>Here is your profile summary:</p>
        <div id="profile_login">
            <input type="button" id="profile_logout" onclick="logOutUser();" value="Logout" />
            <table class="profile-login-table">
                <tr><td><label>Site Rank</label></td><td><?php echo($userInfo->user_rank);?></td></tr>
                <tr><td><label>EXP</label></td><td><?php echo($userInfo->site_experience_points);?></td></tr>
                <tr><td><label>Coins</label></td><td><?php echo($userInfo->site_currency_value);?></td></tr>
                <tr><td><label>Profile views</label></td><td><?php echo($userInfo->view_count);?></td></tr>
                <tr><td><label>Last login</label></td><td><?php echo($userInfo->last_login);?></td></tr>
            </table>
        </div>
<?php
    } elseif ($showRegistrationForm) {
 ?>
        <h2>Register</h2>
        <p>Let's get you registered so you can login to see your profile, earn coins, appear on leader boards, and participate in contests and our community.</p>
        <div class="row">
            <div class="panel col-md-10 profile-login">
                <div id="errorContent" class="errorContent"><p>&nbsp;</p></div>
                <form id="register_form" method="POST" action="profile.php" onsubmit="return registerFormValidation();">
                    <h3><span class="varyn-shield-icon"></span> Registration</h3><div class="register-login-option">Already a member? <a href="profile.php" title="Already a member? Log in with your account" alt="Already a member? Log in with your account.">Log in</a>.</div>
                    <div id="errorContent" class="errorContent"><?php echo($errorMessage);?></div>
                    <div class="form-group"><label for="register_form_email">Email: <span class="required-field">*</span></label><input type="email" name="register_form_email" class="popup-form-input required email" id="register_form_email" placeholder="Your email address" autocomplete="email" required value="<?php echo($email);?>"/></div>
                    <div class="form-group"><label for="register_form_username">User name: <span class="required-field">*</span></label><input type="text" name="register_form_username" class="popup-form-input required username" id="register_form_username" placeholder="A unique user name" autocomplete="username" required value="<?php echo($userName);?>" data-target="register_user_name_unique"/><img id="register_user_name_unique" class="username-is-not-unique" src="/images/red_x.png" width="32" height="32"/></div>
                    <div class="form-group"><label for="register_form_password">Password: <span class="required-field">*</span></label><input type="password" name="register_form_password" class="popup-form-input required password" id="register_form_password" placeholder="A secure password" autocomplete="current-password" required value="<?php echo($password);?>"/></div>
                    <div class="form-group"><label for="register_form_fullname">Full name:</label><input type="text" name="register-fullname" class="popup-form-input fullname" id="register_form_fullname" placeholder="Your full name" autocomplete="name" value="<?php echo($fullname);?>"/></div>
                    <div class="form-group"><label for="register_form_gender">You are:</label><label><input type="radio" name="register_form_gender" value="M"/>&nbsp;&nbsp;Male</label>&nbsp;<label><input type="radio" name="register_form_gender" value="F"/>&nbsp;&nbsp;Female</label></input></div>
                    <div class="form-group"><label for="register_form_dob">Date of Birth:</label><input type="date" name="register_form_dob" class="popup-form-input required dob" id="register_form_dob" placeholder="Birthdate" autocomplete="bday" value="<?php echo($dateOfBirth);?>"/></div>
                    <div class="form-group"><label for="register_form_location">Location:</label><input type="text" name="register_form_location" class="popup-form-input required location" id="register_form_location" placeholder="Where are you?" value="<?php echo($location);?>"/></div>
                    <div class="form-group"><label for="register_form_tagline">Tag line:</label><input type="text" name="register_form_tagline" class="popup-form-input required tagline" id="register_form_tagline" placeholder="Your tag line" value="<?php echo($tagline);?>"/></div>
                    <div class="form-group"><label for="register_form_captcha">R U 4 Real? <span class="required-field">*</span></label><input type="text" name="register_form_captcha" class="popup-form-input required" id="register_form_captcha" placeholder="favorite color?"/></div>
                    <div class="form-group"><label for="register_form_agreement">&nbsp;</label><label><input type="checkbox" name="register_form_agreement" id="register_form_agreement"/> I agree to the <a href="/tos.php" target="_popup">Terms of Use</a></label>&nbsp;<span class="required-field">*</span></div>
                    <div class="form-group"><input type="submit" value="Register" name="popupregister" id="registerButton" class="btn btn-success"/><span id="rememberme-container"><input type="checkbox" tabindex="4" checked="checked" name="rememberme" id="rememberme"><label for="rememberme">Remember Me</label></span></div>
                    <input type="hidden" name="action" value="register" />
                </form>
            </div>
        </div>
<?php
    } else {
        $inputFocusId = 'login_form_username';
        ?>
        <h2>Profile</h2>
        <p>You are not logged in. You must login to see your profile, earn coins, appear on leader boards, and participate in our community.</p>
        <div class="row">
            <div class="panel col-md-6 profile-login">
                <form id="login" method="POST" action="profile.php">
                    <h4>Already a member? Log in:</h4>
                    <div id="errorContent" class="errorContent"><?php echo($errorMessage);?></div>
                    <div class="form-group">
                        <label for="login_form_username">User name:</label><input type="text" id="login_form_username" name="login_form_username" tabindex="1" maxlength="20" class="popup-form-input" value="<?php echo($userName);?>"/><br/>
                        <label for="login_form_password">Password:</label><input type="password" id="login_form_password" name="login_form_password" tabindex="2" maxlength="20" class="popup-form-input" value="<?php echo($password);?>" /><br/>
                        <input type="button" class="btn btn-success" id="login-button" title="Login" value="Login >" tabindex="3" onclick="loginValidation();" />
                        <span id="rememberme-container"><input type="checkbox" tabindex="4" checked="checked" name="rememberme" id="rememberme"><label for="rememberme">Remember Me</label></span>
                        <a id="profile_forgot_password" href="#" tabindex="5" onclick="forgotPassword();">Forgot password?</a><input type="hidden" name="action" value="login" />
                    </div>
                </form>
            </div>
            <div class="col-md-1">
            </div>
            <div class="panel col-md-3 profile-sign-up">
                <h4>Not a member?</h4>
                <input type="button" class="btn btn-primary btn-varyn" id="profile_register_now" value="Sign up with Email" onclick="showRegistrationPopup(true);" title="Sign up with your email address" /><br/>
                <h4>Or</h4>
                <input type="button" class="btn btn-primary btn-facebook" id="facebook-connect-button" value="Login with facebook" title="Login with your Facebook account" />
                <input type="button" class="btn btn-primary btn-gapi-signin" id="gapi-signin-button" value="Sign in with Google" title="Sign in with your Google+ account" />
            </div>
        </div>
<?php
    }
?>
    </div>
    <div class="container marketing">
        <div class="panel panel-primary">
            <div class="panel-heading">
<?php
    if ($isLoggedIn) {
?>
                <h3 class="panel-title">Favorite Games</h3>
<?php
    } else {
?>
                <h3 class="panel-title">Top Games</h3>
<?php
    }
?>
            </div>
        </div>
        <div id="ProfilePageTopGames" class="row">
        </div>
    </div>
    <div id="bottomAd" class="row">
        <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
        <!-- Varyn Responsive -->
        <ins class="adsbygoogle"
             style="display:block"
             data-ad-client="ca-pub-9118730651662049"
             data-ad-slot="5571172619"
             data-ad-format="auto"></ins>
        <script>
            (adsbygoogle = window.adsbygoogle || []).push({});
        </script>
    </div>
</div>
<?php
    include_once('common/footer.php');
 ?>
</div><!-- page_container -->
<script type="text/javascript">

    var enginesisSiteId = <?php echo($siteId);?>,
        serverStage = "<?php echo($stage);?>",
        enginesisGameListId = 7,
        enginesisHomePagePromoId = 3;

    function initApp() {
        var serverHostDomain = 'varyn' + serverStage + '.com',
            showSubscribe = '<?php echo($showSubscribe);?>';

        $('#register_form_username').on('change', onChangeRegisterUserName);
        $('#register_form_username').on('input', onChangeRegisterUserName);
        $('#register_form_username').on('propertychange', onChangeRegisterUserName);
        setupRegisterUserNameOnChangeHandler();
        document.domain = serverHostDomain;
        window.EnginesisSession = enginesis(enginesisSiteId, 0, 0, 'enginesis.' + serverHostDomain, '', '', 'en', enginesisCallBack);
        EnginesisSession.gameListListGames(enginesisGameListId, null);
        EnginesisSession.promotionItemList(enginesisHomePagePromoId, EnginesisSession.getDateNow(), null);
        if (showSubscribe == '1') {
            showSubscribePopup();
        }
        gapi.signin2.render('g-signin2', {
            'scope': 'https://www.googleapis.com/auth/plus.login',
            'width': 200,
            'height': 50,
            'longtitle': true,
            'theme': 'dark',
            'onsuccess': onGapiSuccess,
            'onfailure': onGapiFailure
        });
        $('#profile_forgot_password').click(forgotPassword);
        $('#facebook-connect-button').click(loginFacebook);
        onPageLoadSetFocus();
        onChangeRegisterUserName($('#register_form_username').get(0), 'register_user_name_unique'); // in case field is pre-populated
//        showSubscribePopup();
//        showLoginPopup(true);
//        showRegistrationPopup(true);
    }

    function onPageLoadSetFocus () {
        var errorFieldId = "<?php echo($errorFieldId);?>",
            inputFocusId = "<?php echo($inputFocusId);?>";

        if (inputFocusId != "") {
            document.getElementById(inputFocusId).focus();
        }
        if (errorFieldId != "") {
            $('#' + errorFieldId).removeClass("popup-form-input").addClass("popup-form-input-error");
        }
    }

    function enginesisCallBack (enginesisResponse) {
        var succeeded,
            errorMessage;

        if (enginesisResponse != null && enginesisResponse.fn != null) {
            succeeded = enginesisResponse.results.status.success;
            errorMessage = enginesisResponse.results.status.message;
            switch (enginesisResponse.fn) {
                case "NewsletterAddressAssign":
                    handleNewsletterServerResponse(succeeded);
                    break;
                case "PromotionItemList":
                    if (succeeded == 1) {
                        promotionItemListResponse(enginesisResponse.results.result);
                    }
                    break;
                case "GameListListGames":
                    if (succeeded == 1) {
                        gameListGamesResponse(enginesisResponse.results.result, "ProfilePageTopGames", null, false);
                    }
                    break;
                default:
                    break;
            }
        }
    }

    function loginValidation () {
        var userName = $("input[name=login_form_username]").val(),
            password = $("input[name=login_form_password]").val(),
            errorMessage = "",
            errorField = "";

        $("#login_form_username").removeClass("popup-form-input-error").addClass("popup-form-input");
        $("#login_form_password").removeClass("popup-form-input-error").addClass("popup-form-input");
        showErrorMessage("", "");
        if (errorMessage == "" && ! isValidUserName(userName)) {
            errorMessage = "User name is not acceptable. Please enter your user name.";
            errorField = "login_form_username";
        }
        if (errorMessage == "" && ! isValidPassword(password)) {
            errorMessage = "Password is not acceptable, at least 4 characters. Please retry your password.";
            errorField = "login_form_password";
        }
        if (errorMessage == "") {
            // good enough to send to the server for more validation
            document.getElementById('login').submit();
        } else {
            showErrorMessage(errorMessage, errorField);
        }
    }

    function registerFormValidation () {
        var userName = $("input[name=register_form_username]").val(),
            password = $("input[name=register_form_password]").val(),
            email = $("input[name=register_form_email]").val(),
            dob = $("input[name=register_form_dob]").val(),
            agreement = $("input[name=register_form_agreement]").prop('checked'),
            captcha = $("input[name=register_form_captcha]").val(),
            errorMessage = "",
            errorField = "";

        $("#register_form_username").removeClass("popup-form-input-error").addClass("popup-form-input");
        $("#register_form_password").removeClass("popup-form-input-error").addClass("popup-form-input");
        $("#register_form_email").removeClass("popup-form-input-error").addClass("popup-form-input");
        showErrorMessage("", "");
        if (errorMessage == "" && ! isValidUserName(userName)) {
            errorMessage = "User name is not acceptable. Please enter your user name.";
            errorField = "register_form_username";
        }
        if (errorMessage == "" && ! isValidPassword(password)) {
            errorMessage = "Password is not acceptable, at least 4 characters. Please retry your password.";
            errorField = "register_form_password";
        }
        if (errorMessage == "" && ! isValidEmail(email)) {
            errorMessage = "Email " + email + " doesn't look right. Please enter a proper email address.";
            errorField = "register_form_email";
        }
        if (errorField == "" && ! isValidDateOfBirth(dob)) {
            errorMessage = "You must be at least 13 years of age to register an account on this site.";
            errorField = "register_form_dob";
        }
        if (errorField == "" && ! agreement) {
            errorMessage = "You must agree with the terms of service or you cannot register.";
            errorField = "register_form_agreement";
        }
        if (errorField == "" && captcha.trim().length < 3) {
            errorMessage = "Please answer the human test. Can you try again?";
            errorField = "register_form_captcha";
        }
        if (errorMessage != "") {
            showErrorMessage(errorMessage, errorField);
        }
        return errorMessage == "";
    }

    function onGapiSuccess (googleUser) {

    }

    function onGapiFailure (error) {

    }

    function logout () {
        alert("You are logged OUT");
    }

    function forgotPassword () {
        showForgotPasswordPopup(true);
    }

    function showRegistrationForm () {
        showRegistrationPopup(true);
    }

    function popupRegistrationClicked () {
        showRegistrationPopup(false);
    }

    function loginFacebook () {
        fbLogin('/facebook/endpoints/connect_fb.php');
        return false;
    }

    function register () {

    }

    function showErrorMessage (errorMessage, fieldWithError) {
        var errorContent = document.getElementById('errorContent'),
            errorFieldElement = document.getElementById(fieldWithError);

        if (errorMessage == "") {
            errorContent.innerHTML = '<p>&nbsp;</p>';
        } else if (errorContent != null) {
            errorContent.innerHTML = '<p class="error-text">' + errorMessage + '</p>';
        }
        if (errorFieldElement != null) {
            $(errorFieldElement).removeClass("popup-form-input").addClass("popup-form-input-error");
            errorFieldElement.focus();
        }
    }

</script>
</body>
</html>