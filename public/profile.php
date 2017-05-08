<?php
/**
 * Varyn's profile.php handles the user's profile page. If a user is logged in we show the details about that user
 * and the profile functions such as edit, change password, etc. This page can also show the public details about
 * another user. Finally, if no user is logged in, this page handles all the prompts and details for login, SSO,
 * forgot password.
 *
 * Valid actions are:
 *   login:
 *   logout:
 *   signup:
 *   popupregister:
 *   register:
 *   update:
 *   securityupdate:
 *   view:
 */
    require_once('../services/common.php');
    require_once('../services/SocialServices.php');

    $debug = (int) strtolower(getPostOrRequestVar('debug', 0));
    $page = 'profile';
    $search = getPostOrRequestVar('q', null);
    if ($search != null) {
        header('location:/allgames.php?q=' . $search);
        exit;
    }
    processTrackBack();
    $showSubscribe = getPostOrRequestVar('s', '0');

    $action = ''; // this value tells the page how to function.
    $showRegistrationForm = false;
    $errorMessage = '<p>&nbsp;</p>';
    $errorFieldId = '';
    $inputFocusId = '';
    $redirectedStatusMessage = '';
    $invalidFields = null;
    $socialServices = null;
    $otherUserInfo = null;
    $userInfoJSON = ''; // a JSON representation of the $userInfo object
    $authToken = '';
    $refreshToken = '';

    // Related form variables
    $userName = '';
    $password = '';
    $newPassword = '';
    $email = '';
    $fullname = '';
    $location = '';
    $tagline = '';
    $dateOfBirth = '';
    $gender = '';
    $cellphone = '';
    $securityQuestion = '';
    $securityAnswer = '';
    $aboutMe = '';
    $agreement = false;
    $networkId = getPostOrRequestVar('network_id', 0);
    if ($networkId > 1) {
        // if we are passed a networkId then another page performed SSO and redirected here to finish. we should
        // verify we have a valid logged in user on that network and if so login with Enginesis.
        $socialServices = SocialServices::create($networkId);
        if ($socialServices) {
            $userInfoSSO = $socialServices->connectSSO();
            if ($userInfoSSO != null) {
                // We are logged in with $networkId, set the cookie and continue
                $rememberMe = true;
                $userInfo = $enginesis->userLoginCoreg($userInfoSSO, $rememberMe);
                if ($userInfo == null) {
                    $error = $enginesis->getLastError();
                    if ($error != null) {
                        $errorMessage = '<p class="error-text">Your account could not be logged in at this time. ' . errorToLocalString($error['message']) . '</p>';
                    }
                } else {
                    $isLoggedIn = true;
                    $authToken = $userInfo->authtok;
                    $refreshToken = $userInfo->refreshToken;
                    $userId = $userInfo->user_id;
                    setVarynUserCookie($userInfo, $enginesis->getServerName());
                    $userInfoJSON = getVarynUserCookie();
                }
            // } else {
                // echo("<p>Facebook $networkId SSO returned no user</p>");
            }
        }
    } elseif ($isLoggedIn) {
        // if we have the Enginesis login cookie then we should also verify the user's login with any SSO is still valid.
        if ($enginesis->getTokenStatus() == EnginesisRefreshStatus::refreshed) {
            $userInfo = $enginesis->getRefreshedUserInfo();
//            debugVar($userInfo, 'isloggedin and Refreshed and set new cookies');
            setVarynUserCookie($userInfo, $enginesis->getServerName());
        } else {
            $userInfo = getVarynUserCookieObject();
            if ($userInfo == null) {
                $userInfo = $enginesis->sessionUserInfoGet();
            }
        }
//        if ($userInfo == null) {
//            echo("<h3>isloggedin cookies NOT set</h3>");
//        }
        $userInfoJSON = getVarynUserCookie();
        $authToken = $userInfo->authtok;
        $userId = $userInfo->user_id;
        $networkId = $enginesis->getNetworkId();
        $socialServices = SocialServices::create($networkId);
        $userInfoSSO = $socialServices->connectSSO();
//    } else {
//        echo("<h3>No User is logged in</h3>");
    }
    $action = strtolower(getPostOrRequestVar('action', ''));
    if ($action == 'login') {
        $userName = getPostVar('login_form_username');
        $password = getPostVar('login_form_password');
        $rememberMe = getPostVar('rememberme');
        $thisFieldMustBeEmpty = getPostVar('login_form_email', null);
        $hackerToken = getPostVar('all-clear', ''); // this field must contain the token
        if ($userName == '' && $password == '') {
            $userName = getPostVar('login_username');
            $password = getPostVar('login_password');
        }
        $userInfo = $enginesis->userLogin($userName, $password, $rememberMe);
        if ($userInfo == null) {
            $error = $enginesis->getLastError();
            if ($error != null) {
                $linkToResendToken = createResendConfirmEmailLink($error['message'], null, $userName, null, null);
                $errorMessage = '<p class="error-text">Your account could not be logged in at this time. ' . errorToLocalString($error['message']) . ' ' . $linkToResendToken . '</p>';
            } else {
                $errorMessage = '<p class="error-text">Your user name and password did not match.</p>';
            }
            $inputFocusId = 'login_form_username';
        } else {
            $isLoggedIn = true;
            $authToken = $userInfo->authtok;
            $refreshToken = $userInfo->refreshToken;
            $userId = $userInfo->user_id;
            setVarynUserCookie($userInfo, $enginesis->getServerName());
            $userInfoJSON = getVarynUserCookie();
        }
    } elseif ($action == 'signup') {
        $showRegistrationForm = true;
        $inputFocusId = 'register_form_email';
    } elseif ($action == 'popupregister') {
        $userName = getPostVar("register-username", '');
        $password = getPostVar("register-password", '');
        $email = getPostVar("register-email", '');
        $thisFieldMustBeEmpty = getPostVar("emailaddress", null);
        $hackerToken = getPostVar("all-clear", '');
        $realName = $userName;
        $location = '';
        $tagline = '';
        $date12YearsAgo = strtotime('-12 year');
        $dateOfBirth = date('Y-m-d', $date12YearsAgo);
        $gender = 'N';
        $agreement = getPostVar("register-agreement", 0);
        $parameters = array(
            'user_name' => $userName,
            'password' => $password,
            'email_address' => $email,
            'real_name' => $realName,
            'location' => $location,
            'tagline' => $tagline,
            'dob' => $dateOfBirth,
            'gender' => $gender,
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
                $errorMessage = '<p class="error-text">Your registration has been accepted, but to complete your registration you must accept the email confirmation. Please check your email and use the link provided to complete your registration.</p>';
                $inputFocusId = 'login_form_username';
            }
        } else {
            // TODO: handle invalid fields by showing UI
            $inputFocusId = 'register-email';
        }
        $action = 'register';
    } elseif ($action == 'register') {
        $userName = getPostVar("register_form_username", '');
        $password = getPostVar("register_form_password", '');
        $email = getPostVar("register_form_email", '');
        $fullname = getPostVar("register_form_fullname", '');
        $location = getPostVar("register_form_location", '');
        $tagline = getPostVar("register_form_tagline", '');
        $dateOfBirth = getPostVar("register_form_dob", '');
        $gender = getPostVar("register_form_gender", 'N');
        $agreement = getPostVar("register_form_agreement", 0);
        $thisFieldMustBeEmpty = getPostVar("emailaddress", null);
        $hackerToken = getPostVar("all-clear", '');
        $parameters = array(
            'user_name' => $userName,
            'password' => $password,
            'email_address' => $email,
            'real_name' => $fullname,
            'location' => $location,
            'tagline' => $tagline,
            'dob' => $dateOfBirth,
            'gender' => $gender,
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
                $errorMessage = '<p class="error-text">Your registration has been accepted, but to complete your registration you must accept the email confirmation. Please check your email and use the link provided to complete your registration.</p>';
                $inputFocusId = 'login_form_username';
            }
        } else {
            // TODO: handle invalid fields by showing UI
            $inputFocusId = 'register_form_email';
            $showRegistrationForm = true;
        }
    } elseif ($action == 'update') {
        if ( ! $isLoggedIn) {
            $errorMessage = "<p class=\"error-text\">You must be logged in to use this feature.</p>";
        } else {
            $thisFieldMustBeEmpty = getPostVar("emailaddress", null);
            $hackerToken = getPostVar("all-clear", '');
            if ($thisFieldMustBeEmpty == null && $hackerToken == '') {
                // Call Enginesis to get a fresh view of the user's data in case it was changed in some other process.
                $userInfo = $enginesis->registeredUserGetEx($userId);
                if ($userInfo == null) {
                    // TODO: Need to handle any errors
                    $errorMessage = "<p class=\"error-text\">There was a system error retrieving your information. " . $enginesis->getLastErrorDescription() . "</p>";
                } else {
                    // [user_id] => 10241 [site_id] => 106 [user_name] => varyn2 [real_name] => varyn2 [site_user_id] => [dob] => 2004-04-10 [gender] => F [city] => [state] => [zipcode] => [country_code] => [email_address] => john@varyn.com [mobile_number] => [im_id] => [agreement] => 1 [img_url] => [about_me] => [date_created] => 2016-04-10 20:07:55 [date_updated] => [source_site_id] => 106 [last_login] => 2016-04-16 14:09:27 [login_count] => 24 [tagline] => [additional_info] => [reg_confirmed] => 1 [user_status_id] => 2 [site_currency_value] => 0 [site_experience_points] => 0 [view_count] => 0 [friend_count] => 0 [comment_count] => 0 [notification_count] => 0 ) ) [outparams] => Array ( ) [status] => stdClass Object ( [success] => 1 [message] => ) [passthru] => stdClass Object ( [fn] => RegisteredUserGetEx [site_id] => 106 [logged_in_user_id] => 10241 [get_user_id] => NULL [site_user_id] => NULL [language_code] => en [state_seq] => 1 ) ) ) stdClass Object ( [user_id] => 10241 [site_id] => 106 [user_name] => varyn2 [real_name] => varyn2 [site_user_id] => [dob] => 2004-04-10 [gender] => F [city] => [state] => [zipcode] => [country_code] => [email_address] => john@varyn.com [mobile_number] => [im_id] => [agreement] => 1 [img_url] => [about_me] => [date_created] => 2016-04-10 20:07:55 [date_updated] => [source_site_id] => 106 [last_login] => 2016-04-16 14:09:27 [login_count] => 24 [tagline] => [additional_info] => [reg_confirmed] => 1 [user_status_id] => 2 [site_currency_value] => 0 [site_experience_points] => 0 [view_count] => 0 [friend_count] => 0 [comment_count] => 0 [notification_count] => 0
                    $showRegistrationForm = true;
                    $inputFocusId = 'register_form_email';
                    $userName = $userInfo->user_name;
                    $originalUserName = $userName; // if changed we need to check for name clash
                    $email = $userInfo->email_address;
                    $fullname = $userInfo->real_name;
                    $location = $userInfo->city;
                    $tagline = $userInfo->tagline;
                    $aboutMe = $userInfo->about_me;
                    $dateOfBirth = $userInfo->dob;
                    $cellphone = $userInfo->mobile_number;
                    $gender = $userInfo->gender;
                }
            } else {
                // TODO: determine if no changes made and only submit based on changes
                // TODO: security fields updated?
                // TODO: new password?

                // Validate the hacker stuff is ok
                if ( ! ($thisFieldMustBeEmpty == '' && validateInputFormHackerToken($hackerToken))) {
                    $errorMessage = "<p class=\"error-text\">Registration info is incomplete, please check your entry.</p>";
                    $inputFocusId = 'register_form_email';
                } else {
                    $userRegistrationDataChanged = true; // TODO: set for when we want to optimize these updates and only save when something changed.
                    $userSecurityDataChanged = true;     // TODO: for now just save it.
                    $regenerateToken = false;
                    if ($userRegistrationDataChanged) {
                        $userName = getPostVar("register_form_username", '');
                        $email = getPostVar("register_form_email", '');
                        $fullname = getPostVar("register-fullname", '');
                        $location = getPostVar("register_form_location", '');
                        $tagline = getPostVar("register_form_tagline", '');
                        $dateOfBirth = getPostVar("register_form_dob", '');
                        $gender = getPostVar("register_form_gender", 'N');
                        $cellphone = getPostVar("register_form_phone", '');
                        $aboutMe = getPostVar("register_form_aboutme", '');
                        $parameters = array(
                            'user_name' => $userName,
                            'email_address' => $email,
                            'real_name' => $fullname,
                            'city' => $location,
                            'state' => '',
                            'zipcode' => '',
                            'country_code' => 'US',
                            'tagline' => $tagline,
                            'dob' => $dateOfBirth,
                            'gender' => $gender,
                            'mobile_number' => $cellphone,
                            'im_id' => '',
                            'about_me' => $aboutMe,
                            'additional_info' => ''
                        );
                        $invalidFields = $enginesis->userRegistrationValidation($userId, $parameters);
                        if ($invalidFields == null) {
                            $updateResult = $enginesis->registeredUserUpdate($parameters);
                            // TODO: Handle Error
                            // TODO: update $userInfo with the changed fields and save it
                            // TODO: if username changed, refresh token
                        } else {
                            // TODO: handle invalid fields by showing UI
                            $showRegistrationForm = true;
                            $inputFocusId = 'register_form_email';
                            echo("<h4>registeredUserUpdate bad data:</h4>");
                            print_r($invalidFields);
                        }
                    }
                    if ($inputFocusId == '' && $userSecurityDataChanged) {
                        $securityQuestionId = 1;
                        $securityQuestion = getPostVar("register_form_question", '');
                        $securityAnswer = getPostVar("register_form_answer", '');
                        $cellphone = getPostVar("register_form_phone", '');
                        if (strlen($securityQuestion) > 3 and strlen($securityAnswer) > 2) {
                            $invalidFields = $enginesis->registeredUserSecurityValidation($userId, $cellphone, $securityQuestionId, $securityQuestion, $securityAnswer);
                            if ($invalidFields == null) {
                                $updateResult = $enginesis->registeredUserSecurityUpdate($cellphone, $securityQuestionId, $securityQuestion, $securityAnswer);
                                // TODO: Handle Error
                                // TODO: update $userInfo with the changed fields and save it
                            } else {
                                // TODO: handle invalid fields by showing UI
                                $showRegistrationForm = true;
                                $inputFocusId = 'register_form_email';
                                echo("<h4>registeredUserSecurityUpdate failed:</h4>");
                                print_r($invalidFields);
                            }
                        }
                    }
                    if ($inputFocusId == '') {
                        // TODO: if the password changed then start the reset password process.
                        $newPassword = getPostVar("register_form_new_password", '');
                        if ($newPassword != '') {
                            $updateResult = $enginesis->registeredUserRequestPasswordChange();
                            echo("<h4>registeredUserRequestPasswordChange response:</h4>");
                            print_r($updateResult);
                        }
                    }
                }
            }
        }
    } elseif ($action == 'securityupdate') {
        // TODO: security update form: password, security question. User must be logged in.
        if ( ! $isLoggedIn) {
            $errorMessage = "<p class=\"error-text\">You must be logged in to use this feature.</p>";
        } else {
            // TODO: Process security question
        }
    } elseif ($action == 'forgotpassword') {
        $userName = getPostVar("forgotpassword_username", '');
        $email = getPostVar("forgotpassword_email", '');
        $thisFieldMustBeEmpty = getPostVar("emailaddress", null);
        $hackerToken = getPostVar("all-clear", '');
        $result = $enginesis->userForgotPassword($userName, $email);
        if ($result != null) {
            if (isset($result->user_id) && $result->user_id > 0) {
                $errorMessage = '<p class="info-text">Email has been sent to the owner of this account. Please follow the instructions in that message to reset the account password.</p>';
                $inputFocusId = 'login_form_username';
            } else {
                $result = null;
            }
        }
        if ($result == null) {
            $error = $enginesis->getLastError();
            $errorCode = $error['message'];
            if ($errorCode == 'SYSTEM_ERROR') {
                $errorMessage = '<p class="error-text">' . errorToLocalString($errorCode) . '</p>';
            } else {
                $info = '';
                if ( ! empty($userName) && ! empty($email)) {
                    $info = $userName . ', ' . $email;
                } else {
                    if ( ! empty($userName)) {
                        $info = $userName;
                    }
                    if ( ! empty($email)) {
                        $info = ($info == '' ? '' : ', ') . $email;
                    }
                }
                $info = htmlentities($info);
                $info .= $info == '' ? '' : '. ';
                $errorMessage = '<p class="error-text">' . errorToLocalString($errorCode) . '<br/>' . $info . 'Please check your entry.</p>';
            }
            $inputFocusId = 'profile_forgot_password';
        }
    } elseif ($action == 'resetpassword') {
        $result = $enginesis->userResetPassword();
        if ($result) {
            $errorMessage = '<p class="info-text">Email has been sent with instructions to complete your account password reset.</p>';
            $inputFocusId = 'login_form_username';
        } else {
            $error = $enginesis->getLastError();
            $errorCode = $error['message'];
            if ($errorCode == 'SYSTEM_ERROR') {
                $errorMessage = '<p class="error-text">' . errorToLocalString($errorCode) . '</p>';
            } else {
                $info = '';
                if ( ! empty($userName) && ! empty($email)) {
                    $info = $userName . ', ' . $email;
                } else {
                    if ( ! empty($userName)) {
                        $info = $userName;
                    }
                    if ( ! empty($email)) {
                        $info = ($info == '' ? '' : ', ') . $email;
                    }
                }
                $info .= $info == '' ? '' : '. ';
                $errorMessage = '<p class="error-text">' . errorToLocalString($errorCode) . '<br/>' . $info . '. Please check your entry.</p>';
            }
            $inputFocusId = 'profile_forgot_password';
        }
    } elseif ($action == 'resendconfirm') {
        $userName = getPostOrRequestVar('u');
        // TODO: 1. verify user-name is in waiting for confirm state. 2. call RegisteredUserResetSecondaryPassword
        $_user_id = getPostOrRequestVar('u', 0);
        $_user_name = getPostOrRequestVar('n', '');
        $_email = getPostOrRequestVar('e', '');
        $_token = getPostOrRequestVar('t', '');
        $result = $enginesis->RegisteredUserResetSecondaryPassword($userName, $email);
        $redirectedStatusMessage = 'Your registration confirmation email has been resent. Please check your email.';
    } elseif ($action == 'logout') {
        $result = $enginesis->userLogout();
        clearVarynUserCookie($enginesis->getServerName());
        $isLoggedIn = false;
        $userInfo = null;
        $userId = 0;
        $userName = '';
        $password = '';
        $authToken = '';
        $refreshToken = '';
        $userInfoJSON = '';
    } elseif ($action == 'view') {
        $viewUserId = getPostOrRequestVar('id', '');
        if ($viewUserId == '') {
            $viewUserId = getPostOrRequestVar('user', '');
        }
        if ($viewUserId != '') {
            $otherUserInfo = $enginesis->userGet($viewUserId);
        }
    } else {
        if ($action == 'regconfirm') {
            // redirect from regconfirm.php so we can display the error message
            $code = getPostOrRequestVar('code', '');
            if ($code == 'SUCCESS' || $code == '') {
                $redirectedStatusMessage = 'Your registration has been confirmed! Welcome to Varyn. Now let\'s play some games!';
                // TODO: Verify the cookie/token matches this user
                // TODO: There should be a safeguard if a hacker comes with action+code but is really not the user we think he is spoofing
                $userInfo = getVarynUserCookieObject();
                $userInfoJSON = getVarynUserCookie();
                $isLoggedIn = true;
                $authToken = $userInfo->authtok;
                $refreshToken = $userInfo->refreshToken;
                $userId = $userInfo->user_id;
                $enginesis->userLoginRefresh();
            } else {
                $user_user_id = getPostOrRequestVar('u', '');
                $confirmation_token = getPostOrRequestVar('t', '');
                $linkToResendToken = createResendConfirmEmailLink($code, $user_user_id, $userName, '', $confirmation_token);
                $redirectedStatusMessage = errorToLocalString($code);
            }
        }
        $viewUserId = getPostOrRequestVar('id', '');
        if ($viewUserId == '') {
            $viewUserId = getPostOrRequestVar('user', '');
        }
        if ($viewUserId != '') {
            $otherUserInfo = $enginesis->userGet($viewUserId);
            $action = 'view';
        } else {
            $action = '';
            $userName = '';
            $password = '';
        }
        if ($isLoggedIn) {
            if ($userInfo == null) {
                $userInfo = getVarynUserCookieObject();
                if ($userInfo == null) {
                    $userInfo = $enginesis->sessionUserInfoGet();
                }
            }
            $authToken = $userInfo->authtok;
            $userId = $userInfo->user_id;
            $userInfoJSON = getVarynUserCookie();
        }
    }

    function appendParamIfNotEmpty($params, $key, $value) {
        if ( ! empty($value)) {
            $params .= '&' . $key . '=' . $value;
        }
        return $params;
    }

    function createResendConfirmEmailLink($errorCode, $user_id, $user_name, $email, $confirmation_token) {
        $regConfirmErrors = array(EnginesisErrors::REGISTRATION_NOT_CONFIRMED, EnginesisErrors::INVALID_SECONDARY_PASSWORD, EnginesisErrors::PASSWORD_EXPIRED);
        if (in_array($errorCode, $regConfirmErrors)) {
            $params = '';
            appendParamIfNotEmpty($params, 'u', $user_id);
            appendParamIfNotEmpty($params, 'n', $user_name);
            appendParamIfNotEmpty($params, 'e', $email);
            appendParamIfNotEmpty($params, 't', $confirmation_token);
            return '<a href=/profile.php?action=resendconfirm' . $params . '>Resend confirmation</a>';
        } else {
            return '';
        }
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
    <meta property="og:url" content="//www.varyn.com">
    <meta property="og:site_name" content="Varyn">
    <meta property="og:description" content="Varyn makes games using technology that performs on the most popular platforms. Cross platform friendly technologies have created an opportunity to re-invent online games for an audience that moves seamlessly between desktop, tablet, and smart-phone.">
    <meta property="og:image" content="//www.varyn.com/images/1200x900.png"/>
    <meta property="og:image" content="//www.varyn.com/images/1024.png"/>
    <meta property="og:image" content="//www.varyn.com/images/1200x600.png"/>
    <meta property="og:image" content="//www.varyn.com/images/600x600.png"/>
    <meta property="og:image" content="//www.varyn.com/images/2048x1536.png"/>
    <meta property="og:type" content="game"/>
    <meta name="twitter:card" content="photo"/>
    <meta name="twitter:site" content="@varyndev"/>
    <meta name="twitter:creator" content="@varyndev"/>
    <meta name="twitter:title" content="Varyn: Great games you can play anytime, anywhere"/>
    <meta name="twitter:image:src" content="//www.varyn.com/images/600x600.png"/>
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
    if ($redirectedStatusMessage != '') {
        echo('<div class="panel panel-danger"><div class="panel-heading"><h4>' . $redirectedStatusMessage . '</h4></div></div>');
    }
    if ($debug == 1) {
        echo("<h3>Debug info:</h3><p>Page called with action $action; User name $userName; password $password; email $email; Fullname: $fullname; Loc: $location; Tag: $tagline; DOB: $dateOfBirth;</p>");
        if ($invalidFields != null) {
            echo("<h4>Invalid fields detected:</h4>");
            print_r($invalidFields);
        }
        if ($otherUserInfo != null) {
            echo("<h4>View user:</h4>");
            print_r($otherUserInfo);
        }
    }
    if ($otherUserInfo == null && $isLoggedIn && ! $showRegistrationForm) {
        if ( ! isset($userInfo)) {
            $userInfo = getVarynUserCookieObject();
            if ($userInfo == null) {
                $userInfo = $enginesis->sessionUserInfoGet();
            }
        }
        if (empty($userInfo->last_login)) {
            $userInfo->last_login = date("Y-m-d H:i:s");
        }
        ?>
        <h2>Welcome <?php echo($userInfo->user_name); ?>!</h2>
        <?php if (strlen($errorMessage) > 0) {
            echo('<div id="errorContent" class="errorContent">' . $errorMessage . '</div>');
        } ?>
        <div class="row">
            <div class="col-sm-3 text-center">
                <img class="avatarThumbnail center-block" src="<?php echo($enginesis->avatarURL(0, $userInfo->user_id)); ?>"/>
                <p><?php echo($userInfo->real_name); ?></p>
                <p>
                    <input type="button" id="profile_edit" onclick="profilePage.startUpdate();" value="Edit" class="btn btn-info"/> <input type="button" id="profile_logout" onclick="profilePage.logout();" value="Logout" class="btn btn-default"/>
                </p>
            </div>
            <div id="profile_login" class="col-sm-4">
                <h4>Your profile summary:</h4>
                <table class="profile-login-table">
                    <tr>
                        <td><label>Site Rank</label></td>
                        <td><?php echo($userInfo->user_rank); ?></td>
                    </tr>
                    <tr>
                        <td><label>EXP</label></td>
                        <td><?php echo($userInfo->site_experience_points); ?></td>
                    </tr>
                    <tr>
                        <td><label>Coins</label></td>
                        <td><?php echo($userInfo->site_currency_value); ?></td>
                    </tr>
                    <tr>
                        <td><label>Profile views</label></td>
                        <td><?php echo($userInfo->view_count); ?></td>
                    </tr>
                    <tr>
                        <td><label>Last login</label></td>
                        <td><?php echo(mySqlDateToHumanDate($userInfo->last_login)); ?></td>
                    </tr>
                </table>
            </div>
            <div class="col-sm-3">
                <h4>Awards &amp; Badges</h4>
                <p>You have not earned any yet - so get out and play!</p>
            </div>
        </div>
<?php
    } elseif ($otherUserInfo != null) {
?>
        <h2>Member <?php echo($otherUserInfo->user_name); ?>:</h2>
        <?php if (strlen($errorMessage) > 0) {
            echo('<div id="errorContent" class="errorContent">' . $errorMessage . '</div>');
        } ?>
        <div class="row">
            <div class="col-sm-3 text-center">
                <img class="avatarThumbnail center-block" src="<?php echo($enginesis->avatarURL(0, $otherUserInfo->user_id)); ?>"/>
                <p><?php echo($otherUserInfo->about_me); ?></p>
            </div>
            <div id="profile_login" class="col-sm-4">
                <h4>Profile summary:</h4>
                <table class="profile-login-table">
                    <tr>
                        <td><label>Site Rank</label></td>
                        <td><?php echo($otherUserInfo->user_rank); ?></td>
                    </tr>
                    <tr>
                        <td><label>EXP</label></td>
                        <td><?php echo($otherUserInfo->site_experience_points); ?></td>
                    </tr>
                    <tr>
                        <td><label>Profile views</label></td>
                        <td><?php echo($otherUserInfo->view_count); ?></td>
                    </tr>
                    <tr>
                        <td><label>Member since</label></td>
                        <td><?php echo($otherUserInfo->date_created); ?></td>
                    </tr>
                    <tr>
                        <td><label>Last seen</label></td>
                        <td><?php echo($otherUserInfo->last_login); ?></td>
                    </tr>
                </table>
            </div>
            <div class="col-sm-3">
                <h4>Awards &amp; Badges</h4>
                <p>None - yet!</p>
            </div>
        </div>
<?php
    } elseif ($showRegistrationForm) {
        $hackerVerification = makeInputFormHackerToken();
        if ($isLoggedIn) {
            $registrationOrUpdate = 'update';
?>
        <h2>Profile Update</h2>
        <p>Update the attributes of your user registration.</p>
            <ul class="nav nav-tabs col-md-6" role="tablist">
                <li role="presentation" class="active"><a href="#basicInfo" id="basic-info" role="tab" aria-controls="basicInfo" data-toggle="tab">Basic Info</a></li>
                <li role="presentation"><a href="#extendedInfo" id="extended-info" role="tab" aria-controls="extendedInfo" data-toggle="tab">Extended Info</a></li>
                <li role="presentation"><a href="#secureInfo" id="secure-info" role="tab" aria-controls="secureInfo" data-toggle="tab">Security</a></li>
            </ul>
<?php
        } else {
            $registrationOrUpdate = 'register';
?>
        <h2>Register</h2>
        <p>Let's get you registered so you can login to see your profile, earn coins, appear on leader boards, and participate in contests and the community.</p>
<?php
        }
?>
        <div class="row">
            <div class="col-md-6 profile-login">
                <div id="errorContent" class="errorContent"><?php echo($errorMessage);?></div>
                <form id="register_form" method="POST" action="profile.php" onsubmit="return <?php if ($isLoggedIn) { echo('profilePage.updateFormValidation()'); } else {echo('profilePage.registerFormValidation()');} ?>;">
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane fade in active" id="basicInfo">
<?php
        if ( ! $isLoggedIn) {
?>
                            <h3><span class="varyn-shield-icon"></span> Registration</h3><div class="register-login-option">Already a member? <a href="profile.php" title="Already a member? Log in with your account" alt="Already a member? Log in with your account.">Log in</a>.</div>
<?php
        }
?>
                            <div class="form-group"><label for="register_form_email">Email: <span class="required-field">*</span></label><input type="email" name="register_form_email" class="popup-form-input required email" id="register_form_email" placeholder="Your email address" autocomplete="email" autocorrect="off" autocapitalize="off" required maxlength="80" value="<?php echo($email);?>"/></div>
                            <div class="form-group"><label for="register_form_username">User name: <span class="required-field">*</span></label><input type="text" name="register_form_username" class="popup-form-input required username" id="register_form_username" placeholder="A unique user name" autocomplete="username" autocorrect="off" required maxlength="50" value="<?php echo($userName);?>" data-target="register_user_name_unique"/><img id="register_user_name_unique" class="username-is-not-unique" src="/images/red_x.png" width="32" height="32"/></div>
<?php
        if ( ! $isLoggedIn) {
?>
                            <div class="form-group"><label for="register_form_password">Password: <span class="required-field">*</span></label><input type="password" name="register_form_password" class="popup-form-input required password" id="register_form_password" placeholder="A secure password" autocomplete="current-password" autocorrect="off" required maxlength="20" value="<?php echo($password);?>"/><div id="optional-small-label" class="checkbox optional-small"><label for="ShowPassword" onclick="profilePage.onClickShowPassword();"><input type="checkbox" name="register_form_showpassword" id="register_form_showpassword" onclick="profilePage.onClickShowPassword();"> <span id="register_form_showpassword_label">Show</span> <span id="register_form_showpassword_icon" class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></label></div></div>
<?php
        }
?>
                            <div class="form-group"><label for="register_form_fullname">Full name:</label><input type="text" name="register-fullname" class="popup-form-input fullname" id="register_form_fullname" placeholder="Your full name" autocomplete="name" autocorrect="off" maxlength="50" value="<?php echo($fullname);?>" autocomplete="on" autocorrect="off"/></div>
                            <div class="form-group"><label for="register_form_gender">You are:</label><label><input type="radio" name="register_form_gender" value="M" <?php echo($gender == 'M' ? 'checked' : '');?>/>&nbsp;&nbsp;Male</label><label><input type="radio" name="register_form_gender" value="F" <?php echo($gender == 'F' ? 'checked' : '');?>/>&nbsp;&nbsp;Female</label><label><input type="radio" name="register_form_gender" value="N" <?php echo($gender == 'N' ? 'checked' : '');?>/>&nbsp;&nbsp;Neutral</label></div>
                            <div class="form-group"><label for="register_form_dob">Date of birth:</label><input type="date" name="register_form_dob" class="popup-form-input required dob" id="register_form_dob" placeholder="Birthdate" autocomplete="bday" value="<?php echo($dateOfBirth);?>"/></div>
<?php
        if ( ! $isLoggedIn) {
?>
                            <div class="validation-slider-area" style="max-width: 380px;">
                                <label for="register_form_agreement">I agree to the <a href="/tos.php" target="_popup">Terms of Use</a><span class="required-field">*</span></label><br/>
                                <span><small>No</small>&nbsp;&nbsp;<input type="range" name="register_form_agreement" class="validation-slider" id="register_form_agreement" placeholder="Slide this all the way left to agree" tabindex="13" min="0" max="2" />&nbsp;&nbsp;<small>Yes</small></span>
                            </div>
                            <div class="form-group"><input type="submit" value="Register" name="popupregister" id="registerButton" class="btn btn-success"/><span id="rememberme-container"><input type="checkbox" tabindex="4" checked="checked" name="rememberme" id="rememberme"><label for="rememberme">Remember Me</label></span></div>
<?php
        } else {
?>
                            <div class="form-group"><input type="submit" value="Update" name="popupregister" id="registerButton" class="btn btn-success"/><button value="Cancel" name="popupcancel" id="registerCancel" class="btn btn-danger" style="margin-left: 2em;" onclick="profilePage.cancelUpdate(event);">Cancel</button></div>
<?php
        }
?>
                            <input type="hidden" name="action" value="<?php echo($registrationOrUpdate);?>" /><input type="text" name="emailaddress" class="popup-form-address-input" /><input type="hidden" name="all-clear" value="<?php echo($hackerVerification);?>" />
                        </div>
                        <div role="tabpanel" class="tab-pane fade" id="extendedInfo">
                            <p>Manage info about you to share with others:</p>
                            <img class="avatarThumbnail" src="<?php echo($enginesis->avatarURL(0, $userInfo->user_id));?>"/>
                            <div class="form-group"><label for="register_form_location">Location:</label><input type="text" name="register_form_location" class="popup-form-input required location" id="register_form_location" placeholder="Where are you?" autocomplete="on" maxlength="80" value="<?php echo($location);?>"/></div>
                            <div class="form-group"><label for="register_form_tagline">Tag line:</label><input type="text" name="register_form_tagline" class="popup-form-input required tagline" id="register_form_tagline" placeholder="Your tag line" autocomplete="on" maxlength="50" value="<?php echo($tagline);?>"/></div>
                            <div class="form-group"><label for="register_form_aboutme">About me:</label><textarea class="form-control" name="register_form_aboutme" id="register_form_aboutme" placeholder="About me" maxlength="500" rows="4"><?php echo($aboutMe);?></textarea></div>
                        </div>
                        <div role="tabpanel" class="tab-pane fade" id="secureInfo">
                            <p>Manage security settings for your account:</p>
                            <div class="form-group">
                                <input type="button" id="change_password" onclick="profilePage.changePassword();" value="Change Password" class="btn btn-info"/>
                            </div>
                            <div class="form-group"><label for="register_form_question">Your question:</label><input type="text" name="register_form_question" class="form-control" id="register_form_question" placeholder="Security question" autocomplete="on" maxlength="80" value="<?php echo($securityQuestion);?>"/></div>
                            <div class="form-group"><label for="register_form_answer">Your answer:</label><input type="text" name="register_form_answer" class="form-control" id="register_form_answer" placeholder="Security answer" autocomplete="on" maxlength="80" value="<?php echo($securityAnswer);?>"/></div>
                            <div class="form-group"><label for="register_form_phone">Mobile number:</label><input type="tel" name="register_form_phone" class="form-control cellphone" id="register_form_phone" placeholder="Mobile number" autocorrect="off" autocomplete="tel" maxlength="20" value="<?php echo($cellphone);?>"/></div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
<?php
    } else {
        if ($inputFocusId == '') {
            $inputFocusId = 'login_form_username';
        }
        $hackerVerification = makeInputFormHackerToken();
?>
        <h2>Profile</h2>
        <p>You are not logged in. You must login to see your profile, earn coins, appear on leader boards, and participate in our community.</p>
        <div class="row">
            <div class="panel col-md-6 profile-login">
                <form id="login" method="POST" action="profile.php">
                    <h4>Already a member? Log in:</h4>
                    <div id="errorContent" class="errorContent"><?php echo($errorMessage);?></div>
                    <div class="form-group">
                        <label for="login_form_username">User name:</label><input type="text" id="login_form_username" name="login_form_username" tabindex="1" maxlength="20" class="popup-form-input" value="<?php echo($userName);?>" autocorrect="off" autocomplete="username"/><br/>
                        <label for="login_form_password">Password:</label><input type="password" id="login_form_password" name="login_form_password" tabindex="2" maxlength="20" class="popup-form-input" value="<?php echo($password);?>" /><br/>
                        <input type="button" class="btn btn-success" id="login-button" title="Login" value="Login >" tabindex="3" onclick="profilePage.loginValidation();" /><input type="text" name="login_form_email" class="popup-form-address-input" /><input type="hidden" name="all-clear" value="<?php echo($hackerVerification);?>" />
                        <span id="rememberme-container"><input type="checkbox" tabindex="4" checked="checked" name="rememberme" id="rememberme"><label for="rememberme">Remember Me</label></span>
                        <a id="profile_forgot_password" href="#" tabindex="5" onclick="profilePage.forgotPassword();">Forgot password?</a><input type="hidden" name="action" value="login" />
                    </div>
                </form>
            </div>
            <div class="col-md-1">
            </div>
            <div class="panel col-md-3 profile-sign-up">
                <h4>Not a member?</h4>
                <input type="button" class="btn btn-primary btn-varyn" id="profile_register_now" value="Sign up with Email" onclick="profilePage.showRegistrationPopup(true);" title="Sign up with your email address" /><br/>
                <h4>Or</h4>
                <input type="button" class="btn btn-primary btn-facebook" id="facebook-connect-button" value="Login with facebook" title="Login with your Facebook account" />
                <input type="button" class="btn btn-primary btn-gapi-signin" id="gapi-signin-button" value="Sign in with Google" title="Sign in with your Google+ account" />
                <input type="button" class="btn btn-primary btn-twitter-signin" id="twitter-signin-button" value="Sign in with Twitter" title="Sign in with your Twitter account" />
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
        <div id="HomePageTopGames" class="row">
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
    if (empty($refreshToken)) {
        $refreshTokenJavaScript = '';
    } else {
        $refreshTokenJavaScript = "\n        varynApp.saveRefreshToken('$refreshToken');\n";
    }
 ?>
<script type="text/javascript">

    var varynApp,
        profilePage;

    head.ready(function() {
        var siteConfiguration = {
                siteId: <?php echo($siteId);?>,
                gameId: 0,
                gameGroupId: 0,
                serverStage: '<?php echo($stage);?>',
                languageCode: navigator.language || navigator.userLanguage,
                developerKey: '<?php echo($developerKey);?>',
                facebookAppId: '<?php echo($socialServiceKeys[2]['app_id']);?>',
                authToken: '<?php echo($authToken);?>'
            },
            profilePageParameters = {
                errorFieldId: '<?php echo($errorFieldId);?>',
                inputFocusId: '<?php echo($inputFocusId);?>',
                showSubscribe: '<?php echo($showSubscribe);?>',
                userInfo: '<?php echo(addslashes($userInfoJSON));?>'
            };
        varynApp = varyn(siteConfiguration);
        profilePage = varynApp.initApp(varynProfilePage, profilePageParameters);<?php echo($refreshTokenJavaScript);?>
        varynApp.runUnitTests();
    });

    head.js("/common/modernizr.js", "/common/jquery.min.js", "/common/bootstrap.min.js", "/common/ie10-viewport-bug-workaround.js", "//platform.twitter.com/widgets.js", "https://apis.google.com/js/platform.js", "/common/enginesis.js", "/common/ShareHelper.js", "/common/commonUtilities.js", "/common/varyn.js", "/common/varynProfilePage.js");

</script>
</body>
</html>