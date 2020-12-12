<?php
/**
 * If a user is logged in we show the details about that user
 * and the profile functions such as edit, change password, etc. This page can also show the public details about
 * another user. Finally, if no user is logged in, this page handles all the prompts and details for login, SSO,
 * forgot password.
 *
 * Valid actions are:
 *   login: login a user with name/password from a login form.
 *   logout: logout a user who is currently logged in, clearing all cookies and local data.
 *   signup: show the new user registration form.
 *   popupregister: validate the quick sign-up registration form and create a new account or report errors.
 *   register: validate the full registration form and create a new account or report errors.
 *   update: change profile data for the current logged in user.
 *   forgotpassword: A user submitted the Forgot Password form, initiate the forgot password flow.
 *   resetpassword: The current logged in user requested a Password Reset, initiate the forgot password flow.
 *   resendconfirm: A user needs the registration confirmation form resent (lost it or it expired.)
 *   regconfirm: Handle a redirect from regconfirm.php so we can display the error message.
 *   view: show the public profile of a specified user.
 */
require_once('../../services/common.php');
require_once('../../services/SocialServices.php');
require_once('../../services/strings.php');

processSearchRequest();
$stringTable = new EnginesisStringTable($siteId, $languageCode);
$debug = (int) strtolower(getPostOrRequestVar('debug', 0));
$page = 'profile';
$pageTitle = 'Profile';
$pageDescription = 'View your player profile or review other followers and players at Varyn.com.';
processTrackBack();
$showSubscribe = getPostOrRequestVar('s', '0');

$isValidSession = false;
// $isLoggedIn // reminder this is assigned in common.php
$action = ''; // this value tells the page how to function.
$showRegistrationForm = false;
$errorMessage = '<p>&nbsp;</p>';
$errorFieldId = '';
$inputFocusId = '';
$redirectedStatusMessage = '';
$invalidFields = null;
$socialServices = null;
$otherUserInfo = null;
$userInfo = null;
$authToken = '';
$refreshToken = '';
$isLogout = 'false';
$salutation = $stringTable->lookup(EnginesisUIStrings::PROFILE_PAGE_SALUTATION);

// Related form variables
$userName = '';
$password = '';
$newPassword = '';
$email = '';
$fullname = '';
$location = '';
$tagline = '';
$dateOfBirth = '';
$gender = 'U';
$cellphone = '';
$securityQuestion = '';
$securityAnswer = '';
$aboutMe = '';
$agreement = false; // User agrees to the terms and conditions
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
                    $errorMessage = '<p class="text-error">' . $stringTable->lookup(EnginesisUIStrings::CANNOT_LOG_IN) . ' ' . errorToLocalString($error['message']) . '</p>';
                    $isLoggedIn = false;
                }
            } else {
                $isLoggedIn = true;
                $authToken = $userInfo->authtok;
                $refreshToken = $userInfo->refreshToken;
                $userId = $userInfo->user_id;
            }
        } else {
            debugLog("Connection for network $networkId SSO returned no logged in user");
        }
    } else {
        debugLog("Could not create SSO for network $networkId");
    }
} elseif ($isLoggedIn) {
    // if we have the Enginesis login cookie then we should also verify the user's login with any SSO is still valid.
    $userInfo = $enginesis->getLoggedInUserInfo();
    if ($userInfo != null) {
        $authToken = $userInfo->authtok;
        $userId = $userInfo->user_id;
        $networkId = $enginesis->getNetworkId();
        $socialServices = SocialServices::create($networkId);
        $userInfoSSO = $socialServices->connectSSO();
    } else {
        $isLoggedIn = false;
    }
}
$action = strtolower(getPostOrRequestVar('action', ''));
if ($action == 'login' && ! $isLoggedIn) {
    // User issued a login request we expect user-name and password
    $userName = getPostVar('login_form_username');
    $password = getPostVar('login_form_password');
    $rememberMe = valueToBoolean(getPostVar('login_form_rememberme', false));
    if ($userName == '' && $password == '') {
        $userName = getPostVar('login_username');
        $password = getPostVar('login_password');
        $rememberMe = valueToBoolean(getPostVar('login_rememberme', false));
    }
    $userInfo = $enginesis->userLogin($userName, $password, $rememberMe);
    if ($userInfo == null) {
        $error = $enginesis->getLastError();
        if ($error != null) {
            $linkToResendToken = createResendConfirmEmailLink($error['message'], null, $userName, null, null);
            $errorMessage = '<p class="text-error">' . $stringTable->lookup(EnginesisUIStrings::LOGIN_SYSTEM_FAILURE) . ' ' . errorToLocalString($error['message']) . ' ' . $linkToResendToken . '</p>';
        } else {
            $errorMessage = '<p class="text-error">' . $stringTable->lookup(EnginesisUIStrings::NAME_PASSWORD_MISMATCH) . '</p>';
        }
        $inputFocusId = 'login_form_username';
    } else {
        $isLoggedIn = true;
        $cr = $userInfo->cr;
        // TODO: Verify hash matches, otherwise we should not trust this info.
        $authToken = $userInfo->authtok;
        $refreshToken = $userInfo->refresh_token;
        $tokenExpires = $userInfo->expires;
        $sessionExpires = $userInfo->session_expires;
        $userId = $userInfo->user_id;
    }
} elseif ($action == 'signup' && ! $isLoggedIn) {
    // user requested to sign up, show the registration form
    $showRegistrationForm = true;
    $inputFocusId = 'register_form_email';
} elseif ($action == 'popupregister' && ! $isLoggedIn) {
    // user completed the short registration form
    if (verifyFormHacks(['emailaddress', 'all-clear'])) {
        $userName = getPostVar("register-username", '');
        $password = getPostVar("register-password", '');
        $email = getPostVar("register-email", '');
        $realName = $userName;
        $location = '';
        $tagline = '';
        $date12YearsAgo = strtotime('-12 year');
        $dateOfBirth = date('Y-m-d', $date12YearsAgo);
        $gender = 'U';
        $agreement = checkPostedAgreement("register-agreement");
        $rememberMe = valueToBoolean(getPostVar('register-rememberme', 0));
        $parameters = [
            'user_name' => $userName,
            'password' => $password,
            'email_address' => $email,
            'real_name' => $realName,
            'location' => $location,
            'tagline' => $tagline,
            'dob' => $dateOfBirth,
            'gender' => $gender,
            'agreement' => $agreement
        ];
        $invalidFields = $enginesis->userRegistrationValidation(0, $parameters);
        if ($invalidFields == null) {
            debugLog("Registering a new user: " . implode(', ', $parameters));
            $userInfo = $enginesis->userRegistration($parameters);
            $error = $enginesis->getLastError();
            if ($enginesis->isError($error)) {
                $errorCode = $error['message'];
                debugLog("Registering a new user was error $errorCode");
                switch ($errorCode) {
                    case EnginesisErrors::NAME_IN_USE:
                        $errorInfo = $stringTable->lookup(EnginesisUIStrings::REGISTRATION_NAME_IN_USE);
                        $inputFocusId = 'register_form_username';
                        $errorFieldId = 'register_form_username';
                        break;
                    case EnginesisErrors::EMAIL_IN_USE:
                        $errorInfo = $stringTable->lookup(EnginesisUIStrings::REGISTRATION_EMAIL_IN_USE);
                        $inputFocusId = 'register_form_email';
                        $errorFieldId = 'register_form_email';
                        break;
                    case EnginesisErrors::INVALID_USER_NAME:
                        $errorInfo = $stringTable->lookup(EnginesisUIStrings::REGISTRATION_INVALID);
                        $inputFocusId = 'register_form_username';
                        $errorFieldId = 'register_form_username';
                        break;
                    default:
                        $errorInfo = $stringTable->lookup(EnginesisUIStrings::REGISTRATION_ERROR, ['error' => $errorCode]);
                        $inputFocusId = 'register_form_email';
                        break;
                }
                $errorMessage = '<p class="text-error">' . $stringTable->lookup(EnginesisUIStrings::REGISTRATION_NOT_ACCEPTED) . ' ' . $errorInfo . '</p>';
                $showRegistrationForm = true;
            } else {
                $errorMessage = '<p class="text-error">' . $stringTable->lookup(EnginesisUIStrings::REGISTRATION_ACCEPTED) . '</p>';
                $inputFocusId = 'login_form_username';
            }
        } else {
            debugLog("Registering a new user invalid form: " . implode(', ', $invalidFields));
            debugLog("Registering a new user parameters: " . implode(', ', $parameters));
            // TODO: handle invalid fields by showing UI
            $errorMessage = '<p class="text-error">' . $stringTable->lookup(EnginesisUIStrings::REGISTRATION_ERRORS_FIELDS, ['fields' => implode(', ', $invalidFields)]) . '</p>';
            $inputFocusId = 'register-email';
        }
    } else {
        $errorInfo = 'Invalid form submission. Please correct your error(s) and try again.';
        $errorMessage = '<p class="text-error">' . $stringTable->lookup(EnginesisUIStrings::REGISTRATION_NOT_ACCEPTED) . ' ' . $errorInfo . '</p>';
        $showRegistrationForm = true;
    }
    $action = 'register';
} elseif ($action == 'register' && ! $isLoggedIn) {
    // user completed the full page registration form
    if (verifyFormHacks(['emailaddress', 'all-clear'])) {
        $userName = getPostVar("register_form_username", '');
        $password = getPostVar("register_form_password", '');
        $email = getPostVar("register_form_email", '');
        $fullname = getPostVar("register_form_fullname", '');
        $location = getPostVar("register_form_location", '');
        $tagline = getPostVar("register_form_tagline", '');
        $dateOfBirth = getPostVar("register_form_dob", '');
        $gender = getPostVar("register_form_gender", 'U');
        $agreement = checkPostedAgreement("register_form_agreement");
        $rememberMe = valueToBoolean(getPostVar('register_form_rememberme', false));
        $parameters = [
            'user_name' => $userName,
            'password' => $password,
            'email_address' => $email,
            'real_name' => $fullname,
            'location' => $location,
            'tagline' => $tagline,
            'dob' => $dateOfBirth,
            'gender' => $gender,
            'agreement' => $agreement
        ];
        $invalidFields = $enginesis->userRegistrationValidation(0, $parameters);
        if ($invalidFields == null) {
            debugLog("Registering a new user: " . implode(', ', $parameters));
            $userInfo = $enginesis->userRegistration($parameters);
            $error = $enginesis->getLastError();
            if ($enginesis->isError($error)) {
                $errorCode = $error['message'];
                debugLog("Registering a new user error $errorCode");
                switch ($errorCode) {
                    case EnginesisErrors::NAME_IN_USE:
                        $errorInfo = $stringTable->lookup(EnginesisUIStrings::REGISTRATION_NAME_IN_USE);
                        $inputFocusId = 'register_form_username';
                        $errorFieldId = 'register_form_username';
                        break;
                    case EnginesisErrors::EMAIL_IN_USE:
                        $errorInfo = $stringTable->lookup(EnginesisUIStrings::REGISTRATION_EMAIL_IN_USE);
                        $inputFocusId = 'register_form_email';
                        $errorFieldId = 'register_form_email';
                        break;
                    case EnginesisErrors::INVALID_USER_NAME:
                        $errorInfo = $stringTable->lookup(EnginesisUIStrings::REGISTRATION_INVALID);
                        $inputFocusId = 'register_form_username';
                        $errorFieldId = 'register_form_username';
                        break;
                    default:
                        $errorInfo = $stringTable->lookup(EnginesisUIStrings::REGISTRATION_ERROR, ['error' => $errorCode]);
                        $inputFocusId = 'register_form_email';
                        break;
                }
                $errorMessage = '<p class="text-error">' . $stringTable->lookup(EnginesisUIStrings::REGISTRATION_NOT_ACCEPTED) . ' ' . $errorInfo . '</p>';
                $showRegistrationForm = true;
            } else {
                $errorMessage = '<p class="text-error">' . $stringTable->lookup(EnginesisUIStrings::REGISTRATION_ACCEPTED) . '</p>';
                $inputFocusId = 'login_form_username';
            }
        } else {
            debugLog("Registering a new user bad form " . implode(', ', $invalidFields));
            debugLog("Registering a new user parameters: " . implode(', ', $parameters));
            // TODO: handle invalid fields by showing UI, but try to set the focus on the first field in error.
            $errorMessage = '<p class="text-error">' . $stringTable->lookup(EnginesisUIStrings::REGISTRATION_ERRORS_FIELDS, ['fields' => implode(', ', $invalidFields)]) . '</p>';
            $inputFocusId = 'register_form_email';
            $showRegistrationForm = true;
        }
    } else {
        $errorInfo = 'Invalid form submission. Please correct your error(s) and try again.';
        $errorMessage = '<p class="text-error">' . $stringTable->lookup(EnginesisUIStrings::REGISTRATION_NOT_ACCEPTED) . ' ' . $errorInfo . '</p>';
        $showRegistrationForm = true;
    }
} elseif ($action == 'update') {
    // user requested an update of their profile information
    if ( ! $isLoggedIn || empty($userId)) {
        $errorMessage = '<p class="text-error">' . $stringTable->lookup(EnginesisUIStrings::MUST_BE_LOGGED_IN) . '</p>';
        debugLog("profile.php got into user edit without a valid logged in user.");
    } else {
        $thisFieldMustBeEmpty = getPostVar('emailaddress', null);
        $hackerToken = getPostVar('all-clear', '');
        if ($thisFieldMustBeEmpty === null && $hackerToken === '') {
            // fill in the form with the users current information.
            // Call Enginesis to get a fresh view of the user's data in case it was changed in some other process.
            $userInfo = $enginesis->registeredUserGetEx($userId);
            if ($userInfo == null) {
                // TODO: Need to handle any errors by looking at the error code returned from the server, showing a proper error message, and putting focus in the relevant field
                $errorMessage = '<p class="text-error">' . $stringTable->lookup(EnginesisUIStrings::SYSTEM_ERROR) . ' ' . $enginesis->getLastErrorDescription() . '</p>';
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
                $securityInfo = $enginesis->registeredUserSecurityGet();
                if ($securityInfo != null) {
                    $securityQuestion = $securityInfo->security_question;
                    $securityAnswer = $securityInfo->security_answer;
                } else {
                    // TODO: error handle if the API fails. This is probably a soft error as the user update was ok.
                    $errorMessage = '<p class="text-error">' . $stringTable->lookup(EnginesisUIStrings::SYSTEM_ERROR) . ' ' . $enginesis->getLastErrorDescription() . '</p>';
                }
            }
        } else {
            // User submitted update form. Validate the hacker stuff is ok
            // TODO: determine if no changes made and only submit based on changes
            // TODO: security fields updated?
            if ( ! ($thisFieldMustBeEmpty === '' && validateInputFormHackerToken($hackerToken))) {
                $errorMessage = '<p class="text-error">' . $stringTable->lookup(EnginesisUIStrings::REG_INFO_INCOMPLETE, null) . '</p>';
                debugLog("profile.php error in user update fails hacker test: " . implode(',', [$thisFieldMustBeEmpty, $hackerToken, validateInputFormHackerToken($hackerToken)]));
                $inputFocusId = 'register_form_email';
            } else {
                $userRegistrationDataChanged = true; // TODO: set for when we want to optimize these updates and only save when something changed.
                $userSecurityDataChanged = true;     // TODO: for now just save it.
                $regenerateToken = false;
                if ($userRegistrationDataChanged) {
                    $userName = getPostVar("register_form_username", '');
                    $email = getPostVar("register_form_email", '');
                    $fullname = getPostVar("register_form_fullname", '');
                    $location = getPostVar("register_form_location", '');
                    $tagline = getPostVar("register_form_tagline", '');
                    $dateOfBirth = getPostVar("register_form_dob", '');
                    $gender = getPostVar("register_form_gender", 'U');
                    $cellphone = getPostVar("register_form_phone", '');
                    $aboutMe = getPostVar("register_form_aboutme", '');
                    $parameters = [
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
                    ];
                    $invalidFields = $enginesis->userRegistrationValidation($userId, $parameters);
                    if ($invalidFields == null) {
                        $updateResult = $enginesis->registeredUserUpdate($parameters); // this is {user_id: 9999} if successful and the session should be updated.
                        if ($updateResult) {
                            $error = $enginesis->getLastError();
                            if ($enginesis->isError($error)) {
                                $errorCode = $error['message'];
                                switch ($errorCode) {
                                    case EnginesisErrors::NAME_IN_USE:
                                        $errorInfo = $stringTable->lookup(EnginesisUIStrings::REGISTRATION_NAME_IN_USE);
                                        $inputFocusId = 'register_form_username';
                                        $errorFieldId = 'register_form_username';
                                        break;
                                    case EnginesisErrors::EMAIL_IN_USE:
                                        $errorInfo = $stringTable->lookup(EnginesisUIStrings::REGISTRATION_EMAIL_IN_USE);
                                        $inputFocusId = 'register_form_email';
                                        $errorFieldId = 'register_form_email';
                                        break;
                                    case EnginesisErrors::INVALID_USER_NAME:
                                        $errorInfo = $stringTable->lookup(EnginesisUIStrings::REGISTRATION_INVALID);
                                        $inputFocusId = 'register_form_username';
                                        $errorFieldId = 'register_form_username';
                                        break;
                                    default:
                                        $errorInfo = $stringTable->lookup(EnginesisUIStrings::REGISTRATION_ERROR, ['error' => $errorCode]);
                                        $inputFocusId = 'register_form_email';
                                        break;
                                }
                                $errorMessage = '<p class="text-error">' . $stringTable->lookup(EnginesisUIStrings::REGISTRATION_NOT_ACCEPTED) . ' ' . $errorInfo . '</p>';
                                $showRegistrationForm = true;
                            } else {
                                // TODO: Refresh tokens after user update. Same effort as login, can we just reuse that same code?
                                $refreshToken = $enginesis->sessionGetRefreshToken();
                                if (empty($refreshToken)) {
                                    // If there is no refresh token, we cannot refresh the local auth token. Maybe best to log user out and force a login.
                                    $errorMessage = '<p class="text-error">' . $stringTable->lookup(EnginesisUIStrings::REFRESH_TOKEN_ERROR, null) . '</p>';
                                    debugLog("profile.php sessionGetRefreshToken but no token after user update");
                                } else {
                                    $userInfo = $enginesis->sessionRefresh($refreshToken);
                                    // TODO: sessionRefresh result must be identical to userLogin result
                                    // TODO: REMEMBER to also update Varyn JS local storage as well from JavaScript
                                    if ($userInfo) {
                                        $authToken = $userInfo->authtok;
                                        $userId = $userInfo->user_id;
                                    } else {
                                        // TODO: using the refresh token failed, either it expired or there is a system error.
                                        $errorMessage = '<p class="text-error">' . $stringTable->lookup(EnginesisUIStrings::REG_INFO_INCOMPLETE, null) . '</p>';
                                        debugLog("profile.php sessionRefresh error with user update service: " . debugToString($enginesis->getLastError()));
                                    }
                                }
                            }
                        } else {
                            $lastError = $enginesis->getLastError();
                            $errorMessage = '<p class="text-error">' . $stringTable->lookup(EnginesisUIStrings::REG_INFO_INCOMPLETE, null) . '</p>';
                            debugLog("profile.php registeredUserUpdate error from service: " . debugToString($parameters) . ' ' . debugToString($lastError));
                        }
                    } else {
                        // TODO: handle invalid fields by showing UI, set focus to first field in error
                        $showRegistrationForm = true;
                        $inputFocusId = 'register_form_email';
                        $errorMessage = '<p class="text-error">' . $stringTable->lookup(EnginesisUIStrings::REGISTRATION_ERRORS_FIELDS, ['fields' => implode(', ', $invalidFields)]) . '</p>';
                    }
                }
                if ($inputFocusId == '' && $userSecurityDataChanged) {
                    $securityQuestionId = 1;
                    $securityQuestion = getPostVar("register_form_question", '');
                    $securityAnswer = getPostVar("register_form_answer", '');
                    if (isValidSecurityQuestion($securityQuestion, $securityAnswer)) {
                        $invalidFields = $enginesis->registeredUserSecurityValidation($userId, $cellphone, $securityQuestionId, $securityQuestion, $securityAnswer);
                        if ($invalidFields == null) {
                            $updateResult = $enginesis->registeredUserSecurityUpdate($cellphone, $securityQuestionId, $securityQuestion, $securityAnswer);
                            // TODO: Check and handle Error
                        } else {
                            // handle invalid fields by showing UI
                            $showRegistrationForm = true;
                            $inputFocusId = 'register_form_email';
                            $errorMessage = '<p class="text-error">' . $stringTable->lookup(EnginesisUIStrings::REGISTRATION_ERRORS_FIELDS, ['fields' => implode(', ', $invalidFields)]) . '</p>';
                        }
                    } else {
                        $showRegistrationForm = true;
                        $inputFocusId = 'register_form_email';
                        $errorMessage = '<p class="text-error">' . $stringTable->lookup(EnginesisUIStrings::SECURITY_ERRORS_FIELDS, null) . '</p>';
                    }
                }
                if ($inputFocusId == '') {
                    $errorMessage = '<p class="text-success">' . $stringTable->lookup(EnginesisUIStrings::REG_INFO_UPDATED, null) . '</p>';
                    // If the password changed then start the reset password process.
                    $newPassword = getPostVar("register_form_new_password", '');
                    if ($newPassword != '') {
                        $updateResult = $enginesis->registeredUserRequestPasswordChange();
                    }
                }
            }
        }
    }
} elseif ($action == 'forgotpassword') {
    // user completed the Forgot Password form, initiate a forgot password flow.
    if (verifyFormHacks(['emailaddress', 'all-clear'])) {
        $userName = getPostVar("forgotpassword_username", '');
        $email = getPostVar("forgotpassword_email", '');
        $result = $enginesis->userForgotPassword($userName, $email);
        if ($result != null) {
            if (isset($result->user_id) && $result->user_id > 0) {
                $errorMessage = '<p class="text-info">' . $stringTable->lookup(EnginesisUIStrings::REG_RESET_PASSWORD, null) . '</p>';
                $inputFocusId = 'login_form_username';
            } else {
                $result = null;
            }
        }
        if ($result == null) {
            $error = $enginesis->getLastError();
            $errorCode = $error['message'];
            $info = '';
            if ( ! empty($userName)) {
                $info = $userName;
            }
            if ( ! empty($email)) {
                $info = ($info == '' ? '' : ', ') . $email;
            }
            if ($info != '') {
                $info = '(' . htmlentities($info) . ')';
            }
            $errorMessage = '<p class="text-error">' . errorToLocalString($errorCode) . '<br/>Please check your entry ' . $info . ' or contact support.</p>';
            $inputFocusId = 'profile_forgot_password';
        }
    } else {
        debugLog("profile.php forgotpassword form submission failed the hacker test.");
        $errorCode = EnginesisErrors::SERVICE_ERROR;
        $errorMessage = '<p class="text-error">' . errorToLocalString($errorCode) . '<br/>Please check your entry or contact support.</p>';
        $inputFocusId = 'profile_forgot_password';
    }
} elseif ($action == 'resetpassword') {
    // user requested a Password Reset, initiate a forgot password flow.
    $result = $enginesis->userResetPassword();
    if ($result) {
        $errorMessage = '<p class="text-info">' . $stringTable->lookup(EnginesisUIStrings::REG_COMPLETE_RESET_MESSAGE, null) . '</p>';
        $inputFocusId = 'login_form_username';
    } else {
        $error = $enginesis->getLastError();
        $errorCode = $error['message'];
        if ($errorCode == EnginesisErrors::SYSTEM_ERROR) {
            $errorMessage = '<p class="text-error">' . errorToLocalString($errorCode) . '</p>';
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
            $errorMessage = '<p class="text-error">' . errorToLocalString($errorCode) . '<br/>' . $info . '. Please check your entry.</p>';
        }
        $inputFocusId = 'profile_forgot_password';
    }
} elseif ($action == 'resendconfirm') {
    // User did not confirm registration or it expired, issue a new registration confirmation request.
    // TODO: 1. verify timestamp 2. verify user-name is in waiting for confirm state. 3. call RegisteredUserResetSecondaryPassword
    $_user_id = getPostOrRequestVar('u', 0);
    $_user_name = getPostOrRequestVar('n', '');
    $_email = getPostOrRequestVar('e', '');
    $_token = getPostOrRequestVar('t', '');
    $_timestamp = getPostOrRequestVar('d', 0);
    $result = $enginesis->registeredUserResetSecondaryPassword($_user_id, $_user_name, $_email, $_token);
    $redirectedStatusMessage = $stringTable->lookup(EnginesisUIStrings::REDIRECT_CONFIRM_MESSAGE, null);
} elseif ($action == 'logout') {
    // User requested Logout.
    $result = $enginesis->userLogout();
    $isLogout = 'true'; // to communicate to varyn.js
    $isLoggedIn = false;
    $userInfo = null;
    $userId = 0;
    $userName = '';
    $password = '';
    $authToken = '';
    $refreshToken = '';
    $errorMessage = '<p class="text-info">' . $stringTable->lookup(EnginesisUIStrings::LOGOUT_COMPLETE, null) . '</p>';
} elseif ($action == 'view') {
    // Request to View the profile of a specified user.
    $viewUserId = getPostOrRequestVar(['id','u','user'], '');
    if ($viewUserId != '') {
        $otherUserInfo = $enginesis->userGet($viewUserId);
    }
} elseif ($action == 'test') {
    // Run some tests
    $debug = 1;
    $testInfo = [
        'add items to this array to display test results',
        $stringTable->lookup(EnginesisUIStrings::MISSING_STRING, ['key' => 'user_id']),
        $stringTable->lookup(EnginesisUIStrings::REG_INFO_UPDATED, ['key' => 'user_id'])
    ];
} else {
    if ($action == 'regconfirm') {
        // redirect from regconfirm.php so we can display the error message
        $code = getPostOrRequestVar('code', '');
        if ($code == 'SUCCESS' || $code == '') {
            $redirectedStatusMessage = $stringTable->lookup(EnginesisUIStrings::WELCOME_MESSAGE, null);
            // TODO: Verify the cookie/token matches this user
            // TODO: There should be a safeguard if a hacker comes with action+code but is really not the user we think he is spoofing
            $userInfo = $enginesis->getLoggedInUserInfo();
            $isValidSession = verifySessionIsValid($userId, $authToken);
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
    } else {
        // $action == '' || $action == 'completelogin'
        $errorCode = getPostOrRequestVar('code', null);
        if ($errorCode != null) {
            $errorMessage = '<p class="text-info">' . $stringTable->lookup($errorCode, null) . '</p>';
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
            $userInfo = $enginesis->getLoggedInUserInfo();
            if ($userInfo == null) {
                $userInfo = $enginesis->sessionUserInfoGet();
            }
        }
        $authToken = $userInfo->authtok;
        $userId = $userInfo->user_id;
    }
}

/**
 * Convert the POSTed UI version of the agreement to a boolean value: true if accepted.
 * @param {string} $parameterKey Indicates the POST parameter to read.
 * @return {boolean} true is agreed, false if no agreement.
 */
function checkPostedAgreement($parameterKey) {
    $agreement = (int) getPostVar($parameterKey, 0);
    return $agreement == 2;
}

/**
 * Append a URL parameter if the value is not empty.
 */
function appendParamIfNotEmpty( & $params, $key, $value) {
    if ( ! empty($value)) {
        $params .= '&' . $key . '=' . $value;
    }
    return $params;
}

/**
 * Create a URL that a user can link to in order to resend the confirmation email.
 */
function createResendConfirmEmailLink($errorCode, $userId, $userName, $email, $confirmationToken) {
    $regConfirmErrors = [EnginesisErrors::REGISTRATION_NOT_CONFIRMED, EnginesisErrors::INVALID_SECONDARY_PASSWORD, EnginesisErrors::PASSWORD_EXPIRED];
    if (in_array($errorCode, $regConfirmErrors)) {
        $params = '';
        appendParamIfNotEmpty($params, 'u', $userId);
        appendParamIfNotEmpty($params, 'n', $userName);
        appendParamIfNotEmpty($params, 'e', $email);
        appendParamIfNotEmpty($params, 't', $confirmationToken);
        $params .= '&d=' . time();
        return '<a href="/profile/?action=resendconfirm' . $params . '">Resend confirmation</a>';
    } else {
        return '';
    }
}

function isValidSecurityQuestion($securityQuestion, $securityAnswer) {
    $isValid = strlen(trim($securityQuestion)) > 3 && strlen(trim($securityAnswer)) > 2;
    return $isValid;
}
include_once(VIEWS_ROOT . 'header.php');
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
        if (isset($testInfo)) {
            echo("<h4>Test info:</h4>");
            var_dump($testInfo);
        }
    }
    if ($otherUserInfo == null && $isLoggedIn && ! $showRegistrationForm) {
        if ( ! isset($userInfo)) {
            $userInfo = $enginesis->getLoggedInUserInfo();
            if ($userInfo == null) {
                $userInfo = $enginesis->sessionUserInfoGet();
            }
        }
        if (empty($userInfo->last_login)) {
            $userInfo->last_login = date("Y-m-d H:i:s");
        }
        echo('<h2>' . $salutation . ' ' . $userInfo->user_name . '</h2>');
        if (is_string($errorMessage) && strlen($errorMessage) > 0) {
            echo('<div id="errorContent" class="errorContent">' . $errorMessage . '</div>');
        } elseif (is_array($errorMessage) && count($errorMessage) > 0) {
            echo('<div id="errorContent" class="errorContent">');
            for($i = 0; $i < count($errorMessage); $i ++) {
                echo('<p class="text-error">' . $errorMessage[$i] . '</p>');
            }
            echo('</div>');
        }?>
        <div class="row">
            <div class="col-sm-3 text-center">
                <img class="avatarThumbnail center-block" src="<?php echo($enginesis->avatarURL(1, $userInfo->user_id)); ?>"/>
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
                        <td class="text-large"><mark><?php echo($userInfo->user_rank); ?></mark></td>
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
                        <td class="text-large"><mark><?php echo($otherUserInfo->user_rank); ?></mark></td>
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
        <p>Let's get you registered so you can login to see your profile, earn coins, appear on leader boards, earn rewards, and participate in contests and our community. It's <i>free</i> and it's fun!</p>
<?php
        }
?>
        <div class="row">
            <div class="col-md-6 profile-login">
                <div id="errorContent" class="errorContent"><?php echo($errorMessage);?></div>
                <form id="register_form" method="POST" action="/profile/" onsubmit="return <?php if ($isLoggedIn) { echo('profilePage.updateFormValidation()'); } else {echo('profilePage.registerFormValidation()');} ?>;">
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane fade in active" id="basicInfo">
<?php
        if ( ! $isLoggedIn) {
?>
                            <h3><span class="varyn-shield-icon"></span> Registration</h3><div class="register-login-option">Already a member? <a href="/profile/" title="Already a member? Log in with your account" alt="Already a member? Log in with your account.">Log in</a>.</div>
<?php
        }
?>
                            <div class="form-group"><label for="register_form_email">Email: <span class="required-field">*</span></label><input type="email" name="register_form_email" class="popup-form-input required email" id="register_form_email" placeholder="Your email address" autocomplete="email" autocorrect="off" autocapitalize="off" required maxlength="80" value="<?php echo($email);?>"/></div>
                            <div class="form-group"><label for="register_form_username">User name: <span class="required-field">*</span></label><input type="text" name="register_form_username" class="popup-form-input required username" id="register_form_username" placeholder="A unique user name" autocomplete="username" autocorrect="off" required maxlength="50" value="<?php echo($userName);?>" data-target="register_user_name_unique"/><span id="register_user_name_unique" class="username-is-not-unique"></span></div>
<?php
        if ( ! $isLoggedIn) {
?>
                            <div class="form-group"><label for="register_form_password">Password: <span class="required-field">*</span></label><input type="password" name="register_form_password" class="popup-form-input required password" id="register_form_password" placeholder="A secure password" autocomplete="current-password" autocorrect="off" required maxlength="20" value="<?php echo($password);?>"/><div id="optional-small-label" class="checkbox optional-small"><label for="ShowPassword" onclick="profilePage.onClickShowPassword();"><input type="checkbox" name="register_form_showpassword" id="register_form_showpassword" onclick="profilePage.onClickShowPassword();"> <span id="register_form_showpassword_label">Show</span> <span id="register_form_showpassword_icon" class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></label></div></div>
<?php
        }
?>
                            <div class="form-group"><label for="register_form_fullname">Full name:</label><input type="text" name="register_form_fullname" class="popup-form-input fullname" id="register_form_fullname" placeholder="Your full name" autocomplete="name" autocorrect="off" maxlength="50" value="<?php echo($fullname);?>" autocomplete="on" autocorrect="off"/></div>
                            <div class="form-group"><label for="register_form_gender">You are:</label><label><input type="radio" name="register_form_gender" value="M" <?php echo($gender == 'M' ? 'checked' : '');?>/>&nbsp;&nbsp;Male</label><label><input type="radio" name="register_form_gender" value="F" <?php echo($gender == 'F' ? 'checked' : '');?>/>&nbsp;&nbsp;Female</label><label><input type="radio" name="register_form_gender" value="U" <?php echo($gender == 'U' ? 'checked' : '');?>/>&nbsp;&nbsp;Undefined</label></div>
                            <div class="form-group"><label for="register_form_dob">Date of birth:</label><input type="date" name="register_form_dob" class="popup-form-input required dob" id="register_form_dob" placeholder="Birthdate" autocomplete="bday" value="<?php echo($dateOfBirth);?>"/></div>
<?php
        if ( ! $isLoggedIn) {
?>
                            <div class="validation-slider-area" style="max-width: 380px;">
                                <label for="register_form_agreement">I agree to the <a href="/tos/" target="_popup">Terms of Use</a><span class="required-field">*</span></label><br/>
                                <span><small>No</small>&nbsp;&nbsp;<input type="range" name="register_form_agreement" class="validation-slider" id="register_form_agreement" placeholder="Slide this to Yes (all the way left) to agree" alt="Slide this to Yes (all the way left) to agree" tabindex="13" min="0" max="2" />&nbsp;&nbsp;<small>Yes</small></span>
                            </div>
                            <div class="form-group"><input type="submit" value="Register" name="popupregister" id="registerButton" class="btn btn-success"/><span class="rememberme-container"><input type="checkbox" tabindex="4" checked="checked" name="register_form_rememberme" id="register_form_rememberme"><label for="register_form_rememberme">Remember Me</label></span></div>
<?php
        } else {
?>
                            <div class="form-group"><input type="submit" value="Update" name="popupregister" id="registerButton" class="btn btn-success"/><button value="Cancel" name="popupcancel" id="registerCancel" class="btn btn-danger" style="margin-left: 2em;" onclick="profilePage.cancelUpdate(event);">Cancel</button></div>
<?php
        }
?>
                            <input type="hidden" name="action" value="<?php echo($registrationOrUpdate);?>" /><input type="text" name="emailaddress" class="popup-form-address-input" /><input type="hidden" name="all-clear" value="<?php echo($hackerVerification);?>" />
                        </div>
<?php
    if ($isLoggedIn) {
?>
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
<?php
    }
?>
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
        <p>You are not logged in. Login to see your profile, earn coins, appear on leader boards, and participate in our community.</p>
        <div class="row">
            <div class="panel col-md-6 profile-login">
                <form id="login" method="POST" action="/profile/">
                    <h4>Already a member? Log in:</h4>
                    <div id="errorContent" class="errorContent"><?php echo($errorMessage);?></div>
                    <div class="form-group">
                        <label for="login_form_username">User name:</label><input type="text" id="login_form_username" name="login_form_username" tabindex="1" maxlength="20" class="popup-form-input" value="<?php echo($userName);?>" autocorrect="off" autocomplete="username"/><br/>
                        <label for="login_form_password">Password:</label><input type="password" id="login_form_password" name="login_form_password" tabindex="2" maxlength="20" class="popup-form-input" value="<?php echo($password);?>" /><br/>
                        <input type="button" class="btn btn-success" id="login-button" title="Login" value="Login >" tabindex="3" onclick="profilePage.loginValidation();" /><input type="text" name="login_form_email" class="popup-form-address-input" /><input type="hidden" name="all-clear" value="<?php echo($hackerVerification);?>" />
                        <span class="rememberme-container"><input type="checkbox" tabindex="4" checked="checked" name="login_form_rememberme" id="login_form_rememberme"><label for="login_form_rememberme">Remember Me</label></span>
                        <a id="profile_forgot_password" href="#" tabindex="5" onclick="profilePage.forgotPassword();">Forgot password?</a><input type="hidden" name="action" value="login" />
                    </div>
                    <div class="text-center">
                        <a class="sub-link-group" href="/privacy/"><span class="glyphicon glyphicon-eye-open"></span> Privacy</a>
                        <a class="sub-link-group" href="/tos/"><span class="glyphicon glyphicon-info-sign"></span> Terms</a>
                    </div>
                </form>
            </div>
            <div class="col-md-1">
            </div>
            <div class="panel col-md-3 profile-sign-up">
                <h4>Not a member?</h4>
                <input type="button" class="btn btn-primary btn-varyn" id="profile_register_now" value="Sign up with Email" onclick="profilePage.showRegistrationPopup(true);" title="Sign up with your email address" /><br/>
                <h4>Or</h4>
                <input type="button" class="btn btn-primary btn-facebook" id="facebook-connect-button" value="Login with Facebook" title="Login with your Facebook account" aria-label="Login with your Facebook account" role="button">
                <input type="button" class="btn btn-primary btn-gapi-signin" id="gapi-signin-button" value="Sign in with Google" title="Sign in with your Google account" aria-label="Sign in with your Google account" role="button">
                <input type="button" class="btn btn-primary btn-twitter-signin" id="twitter-signin-button" value="Sign in with Twitter" title="Sign in with your Twitter account" aria-label="Sign in with your Twitter account" role="button">
            </div>
        </div>
<?php
    }
?>
    </div>
    <div class="container marketing">
<?php
    if ($isLoggedIn) {
?>
        <div id="FavoriteGamesContainer">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">Favorite Games</h3>
                </div>
            </div>
            <div id="FavoriteGames" class="row">
            </div>
        </div>
<?php
    }
?>
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">Top Games</h3>
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
include_once(VIEWS_ROOT . 'footer.php');
if (empty($refreshToken)) {
    $refreshTokenJavaScript = "\n";
} else {
    $refreshTokenJavaScript = "\n        varynApp.saveRefreshToken('$refreshToken');\n";
}
?>
<script>
    var varynApp,
        profilePage,
        debug = true;

    head.ready(function() {
        var siteConfiguration = {
                siteId: <?php echo($siteId);?>,
                gameId: 0,
                gameGroupId: 0,
                serverStage: '<?php echo($serverStage);?>',
                languageCode: navigator.language || navigator.userLanguage,
                developerKey: '<?php echo($developerKey);?>',
                facebookAppId: '<?php echo($socialServiceKeys[2]['app_id']);?>',
                googleAppId: '<?php echo($socialServiceKeys[7]['app_id']);?>',
                twitterAppId: '<?php echo($socialServiceKeys[11]['app_id']);?>',
                appleAppId: '<?php echo($socialServiceKeys[14]['app_id']);?>',
                authToken: '<?php echo($authToken);?>'
            },
            profilePageParameters = {
                errorFieldId: '<?php echo($errorFieldId);?>',
                inputFocusId: '<?php echo($inputFocusId);?>',
                showSubscribe: '<?php echo($showSubscribe);?>',
                userInfo: '<?php echo(addslashes(json_encode($enginesis->getLoggedInUserInfo())));?>',
                isLogout: <?php echo($isLogout);?>
            };
        varynApp = varyn(siteConfiguration);
        profilePage = varynApp.initApp(varynProfilePage, profilePageParameters);<?php echo($refreshTokenJavaScript);?>
    });
    if (debug) {
        head.js('/common/modernizr.js', '/common/jquery.min.js', '/common/bootstrap.min.js', '/common/enginesis.js', '/common/ShareHelper.js', '/common/commonUtilities.js', '/common/ssoFacebook.js', '/common/ssoGoogle.js', '/common/ssoTwitter.js', '/common/varyn.js', '/common/varynProfilePage.js');
    } else {
        head.js('/common/modernizr.js', '/common/jquery.min.js', '/common/bootstrap.min.js', '/common/enginesis.min.js', '/common/varynProfilePage.min.js');
    }
</script>
</body>
</html>