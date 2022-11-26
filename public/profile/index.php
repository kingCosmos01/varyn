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
require_once('../../views/sections.php');

processSearchRequest();
$stringTable = new EnginesisStringTable($siteId, $languageCode);
$debug = (int) getPostOrRequestVar('debug', 0);
$page = 'profile';
$pageTitle = 'Profile';
$pageDescription = 'View your player profile or review other followers and players at Varyn.com.';
processTrackBack();
$showSubscribe = getPostOrRequestVar('s', '0');
$topGamesListId = 5;

$isValidSession = false;
// $isLoggedIn // reminder this is assigned in common.php
$action = ''; // this value tells the page how to function.
$showRegistrationForm = false;
$errorMessage = '';
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
        if ($socialServices != null) {
            $userInfoSSO = $socialServices->connectSSO();
        }
    } else {
        $isLoggedIn = false;
    }
}
$action = strtolower(getPostOrRequestVar('action', ''));
if ($action == 'login' && ! $isLoggedIn) {
    // User issued a login request we expect user-name and password
    $userName = getPostVar('login-form-username');
    $password = getPostVar('login-form-password');
    $rememberMe = valueToBoolean(getPostVar('login-form-rememberme', false));
    // @todo: verify the honeypot and form generation to help avoid bot attacks
    // if (verifyFormHacks(['login-form-email', 'all-clear'])) {
    // $thisFieldMustBeEmpty = getPostVar('login-form-email', null);
    // $hackerToken = getPostVar('all-clear', ''); // this field must contain the token
    if ($userName == '' && $password == '') {
        $userName = getPostVar('login-username');
        $password = getPostVar('login-password');
        $rememberMe = valueToBoolean(getPostVar('login-rememberme', false));
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
        $inputFocusId = 'login-form-username';
    } else {
        $isLoggedIn = true;
        $cr = $userInfo->cr;
        // @todo: Verify hash matches, otherwise we should not trust this info.
        $authToken = $userInfo->authtok;
        $refreshToken = $userInfo->refresh_token;
        $tokenExpires = $userInfo->expires;
        $sessionExpires = $userInfo->session_expires;
        $userId = $userInfo->user_id;
    }
} elseif ($action == 'signup' && ! $isLoggedIn) {
    // user requested to sign up, show the registration form
    $showRegistrationForm = true;
    $inputFocusId = 'register-form-email';
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
                        $inputFocusId = 'register-form-username';
                        $errorFieldId = 'register-form-username';
                        break;
                    case EnginesisErrors::EMAIL_IN_USE:
                        $errorInfo = $stringTable->lookup(EnginesisUIStrings::REGISTRATION_EMAIL_IN_USE);
                        $inputFocusId = 'register-form-email';
                        $errorFieldId = 'register-form-email';
                        break;
                    case EnginesisErrors::REGISTRATION_INVALID:
                        $errorInfo = $stringTable->lookup(EnginesisUIStrings::REGISTRATION_INVALID);
                        $inputFocusId = 'register-form-username';
                        $errorFieldId = 'register-form-username';
                        break;
                    default:
                        $errorInfo = $stringTable->lookup(EnginesisUIStrings::REGISTRATION_ERROR, ['error' => $errorCode]);
                        $inputFocusId = 'register-form-email';
                        break;
                }
                $errorMessage = '<p class="text-error">' . $stringTable->lookup(EnginesisUIStrings::REGISTRATION_NOT_ACCEPTED) . ' ' . $errorInfo . '</p>';
                $showRegistrationForm = true;
            } else {
                $errorMessage = '<p class="text-error">' . $stringTable->lookup(EnginesisUIStrings::REGISTRATION_ACCEPTED) . '</p>';
                $inputFocusId = 'login-form-username';
            }
        } else {
            debugLog("Registering a new user invalid form: " . implode(', ', $invalidFields));
            debugLog("Registering a new user parameters: " . implode(', ', $parameters));
            // @todo: handle invalid fields by showing UI
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
        $userName = getPostVar("register-form-username", '');
        $password = getPostVar("register-form-password", '');
        $email = getPostVar("register-form-email", '');
        $fullname = getPostVar("register-form-fullname", '');
        $location = getPostVar("register-form-location", '');
        $tagline = getPostVar("register-form-tagline", '');
        $dateOfBirth = getPostVar("register-form-dob", '');
        $gender = getPostVar("register-form-gender", 'U');
        $agreement = checkPostedAgreement("register-form-agreement");
        $rememberMe = valueToBoolean(getPostVar('register-form-rememberme', false));
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
                        $inputFocusId = 'register-form-username';
                        $errorFieldId = 'register-form-username';
                        break;
                    case EnginesisErrors::EMAIL_IN_USE:
                        $errorInfo = $stringTable->lookup(EnginesisUIStrings::REGISTRATION_EMAIL_IN_USE);
                        $inputFocusId = 'register-form-email';
                        $errorFieldId = 'register-form-email';
                        break;
                    case EnginesisErrors::REGISTRATION_INVALID:
                        $errorInfo = $stringTable->lookup(EnginesisUIStrings::REGISTRATION_INVALID);
                        $inputFocusId = 'register-form-username';
                        $errorFieldId = 'register-form-username';
                        break;
                    default:
                        $errorInfo = $stringTable->lookup(EnginesisUIStrings::REGISTRATION_ERROR, ['error' => $errorCode]);
                        $inputFocusId = 'register-form-email';
                        break;
                }
                $errorMessage = '<p class="text-error">' . $stringTable->lookup(EnginesisUIStrings::REGISTRATION_NOT_ACCEPTED) . ' ' . $errorInfo . '</p>';
                $showRegistrationForm = true;
            } else {
                $errorMessage = '<p class="text-error">' . $stringTable->lookup(EnginesisUIStrings::REGISTRATION_ACCEPTED) . '</p>';
                $inputFocusId = 'login-form-username';
            }
        } else {
            debugLog("Registering a new user bad form " . implode(', ', $invalidFields));
            debugLog("Registering a new user parameters: " . implode(', ', $parameters));
            // @todo: handle invalid fields by showing UI, but try to set the focus on the first field in error.
            $errorMessage = '<p class="text-error">' . $stringTable->lookup(EnginesisUIStrings::REGISTRATION_ERRORS_FIELDS, ['fields' => implode(', ', $invalidFields)]) . '</p>';
            $inputFocusId = 'register-form-email';
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
                // @todo: Need to handle any errors by looking at the error code returned from the server, showing a proper error message, and putting focus in the relevant field
                $errorMessage = '<p class="text-error">' . $stringTable->lookup(EnginesisErrors::SYSTEM_ERROR) . ' ' . $enginesis->getLastErrorDescription() . '</p>';
            } else {
                // [user_id] => 10241 [site_id] => 106 [user_name] => varyn2 [real_name] => varyn2 [site_user_id] => [dob] => 2004-04-10 [gender] => F [city] => [state] => [zipcode] => [country_code] => [email_address] => john@varyn.com [mobile_number] => [im_id] => [agreement] => 1 [img_url] => [about_me] => [date_created] => 2016-04-10 20:07:55 [date_updated] => [source_site_id] => 106 [last_login] => 2016-04-16 14:09:27 [login_count] => 24 [tagline] => [additional_info] => [reg_confirmed] => 1 [user_status_id] => 2 [site_currency_value] => 0 [site_experience_points] => 0 [view_count] => 0 [friend_count] => 0 [comment_count] => 0 [notification_count] => 0 ) ) [outparams] => Array ( ) [status] => stdClass Object ( [success] => 1 [message] => ) [passthru] => stdClass Object ( [fn] => RegisteredUserGetEx [site_id] => 106 [logged_in_user_id] => 10241 [get_user_id] => NULL [site_user_id] => NULL [language_code] => en [state_seq] => 1 ) ) ) stdClass Object ( [user_id] => 10241 [site_id] => 106 [user_name] => varyn2 [real_name] => varyn2 [site_user_id] => [dob] => 2004-04-10 [gender] => F [city] => [state] => [zipcode] => [country_code] => [email_address] => john@varyn.com [mobile_number] => [im_id] => [agreement] => 1 [img_url] => [about_me] => [date_created] => 2016-04-10 20:07:55 [date_updated] => [source_site_id] => 106 [last_login] => 2016-04-16 14:09:27 [login_count] => 24 [tagline] => [additional_info] => [reg_confirmed] => 1 [user_status_id] => 2 [site_currency_value] => 0 [site_experience_points] => 0 [view_count] => 0 [friend_count] => 0 [comment_count] => 0 [notification_count] => 0
                $showRegistrationForm = true;
                $inputFocusId = 'register-form-email';
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
                    // @todo: error handle if the API fails. This is probably a soft error as the user update was ok.
                    $errorMessage = '<p class="text-error">' . $stringTable->lookup(EnginesisErrors::SYSTEM_ERROR) . ' ' . $enginesis->getLastErrorDescription() . '</p>';
                }
            }
        } else {
            // User submitted update form. Validate the hacker stuff is ok
            // @todo: determine if no changes made and only submit based on changes
            // @todo: security fields updated?
            if ( ! ($thisFieldMustBeEmpty === '' && validateInputFormHackerToken($hackerToken))) {
                $errorMessage = '<p class="text-error">' . $stringTable->lookup(EnginesisUIStrings::REG_INFO_INCOMPLETE, null) . '</p>';
                debugLog("profile.php error in user update fails hacker test: " . implode(',', [$thisFieldMustBeEmpty, $hackerToken, validateInputFormHackerToken($hackerToken)]));
                $inputFocusId = 'register-form-email';
            } else {
                $userRegistrationDataChanged = true; // @todo: set for when we want to optimize these updates and only save when something changed.
                $userSecurityDataChanged = true;     // @todo: for now just save it.
                $regenerateToken = false;
                if ($userRegistrationDataChanged) {
                    $userName = getPostVar("register-form-username", '');
                    $email = getPostVar("register-form-email", '');
                    $fullname = getPostVar("register-form-fullname", '');
                    $location = getPostVar("register-form-location", '');
                    $tagline = getPostVar("register-form-tagline", '');
                    $dateOfBirth = getPostVar("register-form-dob", '');
                    $gender = getPostVar("register-form-gender", 'U');
                    $cellphone = getPostVar("register-form-phone", '');
                    $aboutMe = getPostVar("register-form-aboutme", '');
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
                                        $inputFocusId = 'register-form-username';
                                        $errorFieldId = 'register-form-username';
                                        break;
                                    case EnginesisErrors::EMAIL_IN_USE:
                                        $errorInfo = $stringTable->lookup(EnginesisUIStrings::REGISTRATION_EMAIL_IN_USE);
                                        $inputFocusId = 'register-form-email';
                                        $errorFieldId = 'register-form-email';
                                        break;
                                    case EnginesisErrors::REGISTRATION_INVALID:
                                        $errorInfo = $stringTable->lookup(EnginesisUIStrings::REGISTRATION_INVALID);
                                        $inputFocusId = 'register-form-username';
                                        $errorFieldId = 'register-form-username';
                                        break;
                                    default:
                                        $errorInfo = $stringTable->lookup(EnginesisUIStrings::REGISTRATION_ERROR, ['error' => $errorCode]);
                                        $inputFocusId = 'register-form-email';
                                        break;
                                }
                                $errorMessage = '<p class="text-error">' . $stringTable->lookup(EnginesisUIStrings::REGISTRATION_NOT_ACCEPTED) . ' ' . $errorInfo . '</p>';
                                $showRegistrationForm = true;
                            } else {
                                // @todo: Refresh tokens after user update. Same effort as login, can we just reuse that same code?
                                $refreshToken = $enginesis->sessionGetRefreshToken();
                                if (empty($refreshToken)) {
                                    // If there is no refresh token, we cannot refresh the local auth token. Maybe best to log user out and force a login.
                                    $errorMessage = '<p class="text-error">' . $stringTable->lookup(EnginesisUIStrings::REFRESH_TOKEN_ERROR, null) . '</p>';
                                    debugLog("profile.php sessionGetRefreshToken but no token after user update");
                                } else {
                                    $userInfo = $enginesis->sessionRefresh($refreshToken);
                                    // @todo: sessionRefresh result must be identical to userLogin result
                                    // @todo: REMEMBER to also update Varyn JS local storage as well from JavaScript
                                    if ($userInfo) {
                                        $authToken = $userInfo->authtok;
                                        $userId = $userInfo->user_id;
                                    } else {
                                        // @todo: using the refresh token failed, either it expired or there is a system error.
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
                        // @todo: handle invalid fields by showing UI, set focus to first field in error
                        $showRegistrationForm = true;
                        $inputFocusId = 'register-form-email';
                        $errorMessage = '<p class="text-error">' . $stringTable->lookup(EnginesisUIStrings::REGISTRATION_ERRORS_FIELDS, ['fields' => implode(', ', $invalidFields)]) . '</p>';
                    }
                }
                if ($inputFocusId == '' && $userSecurityDataChanged) {
                    $securityQuestionId = 1;
                    $securityQuestion = getPostVar('register-form-question', '');
                    $securityAnswer = getPostVar('register-form-answer', '');
                    if (isValidSecurityQuestion($securityQuestion, $securityAnswer)) {
                        $invalidFields = $enginesis->registeredUserSecurityValidation($userId, $cellphone, $securityQuestionId, $securityQuestion, $securityAnswer);
                        if ($invalidFields == null) {
                            $updateResult = $enginesis->registeredUserSecurityUpdate($cellphone, $securityQuestionId, $securityQuestion, $securityAnswer);
                            // @todo: Check and handle Error
                        } else {
                            // handle invalid fields by showing UI
                            $showRegistrationForm = true;
                            $inputFocusId = 'register-form-email';
                            $errorMessage = '<p class="text-error">' . $stringTable->lookup(EnginesisUIStrings::REGISTRATION_ERRORS_FIELDS, ['fields' => implode(', ', $invalidFields)]) . '</p>';
                        }
                    } else {
                        $showRegistrationForm = true;
                        $inputFocusId = 'register-form-email';
                        $errorMessage = '<p class="text-error">' . $stringTable->lookup(EnginesisUIStrings::SECURITY_ERRORS_FIELDS, null) . '</p>';
                    }
                }
                if ($inputFocusId == '') {
                    $errorMessage = '<p class="text-success">' . $stringTable->lookup(EnginesisUIStrings::REG_INFO_UPDATED, null) . '</p>';
                    // If the password changed then start the reset password process.
                    $newPassword = getPostVar("register-form-new_password", '');
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
        $userName = getPostVar(["forgot-password-username", "forgot-password-username-form"], '');
        $email = getPostVar(["forgot-password-email", "forgot-password-email-form"], '');
        if (isValidUserName($userName) || checkEmailAddress($email)) {
            $result = $enginesis->userForgotPassword($userName, $email);
            if ($result != null) {
                if (isset($result->user_id) && $result->user_id > 0) {
                    $errorMessage = '<p class="text-info">' . $stringTable->lookup(EnginesisUIStrings::REG_RESET_PASSWORD, null) . '</p>';
                    $inputFocusId = 'login-form-username';
                } else {
                    $errorMessage = '<p class="text-error">Unexpected service response.<br/>Please check your entry or contact support.</p>';
                    $result = null;
                }
            }
            // @todo: Edge case, the query can return results AND and error if server fails to send email.
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
                $inputFocusId = 'profile-forgot-password';
            }
        } else {
            // bad parameters
            $errorMessage = '<p class="text-error">You must provide either your user name or your email address to request a password reset.</p>';
        }
    } else {
        debugLog("profile.php forgotpassword form submission failed the hacker test.");
        $errorCode = EnginesisErrors::SERVICE_ERROR;
        $errorMessage = '<p class="text-error">' . errorToLocalString($errorCode) . '<br/>Please check your entry or contact support.</p>';
        $inputFocusId = 'profile-forgot-password';
    }
} elseif ($action == 'resetpassword') {
    // user requested a Password Reset, initiate a forgot password flow.
    $result = $enginesis->userResetPassword();
    if ($result) {
        $errorMessage = '<p class="text-info">' . $stringTable->lookup(EnginesisUIStrings::REG_COMPLETE_RESET_MESSAGE, null) . '</p>';
        $inputFocusId = 'login-form-username';
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
        $inputFocusId = 'profile-forgot-password';
    }
} elseif ($action == 'resendconfirm') {
    // User did not confirm registration or it expired, issue a new registration confirmation request.
    // @todo: 1. verify timestamp 2. verify user-name is in waiting for confirm state. 3. call RegisteredUserResetSecondaryPassword
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
        if ($code == 'NO_ERROR' || $code == 'SUCCESS' || $code == '') {
            $redirectedStatusMessage = $stringTable->lookup(EnginesisUIStrings::WELCOME_MESSAGE, null);
            // @todo: Verify the cookie/token matches this user
            // @todo: There should be a safeguard if a hacker comes with action+code but is really not the user we think he is spoofing
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

/**
 * Determine if the user's selection of a security question and the answer are valid.
 * @param string $securityQuestion The quesiton chosen by the user.
 * @param string $securityAnswer The answer given by the user.
 * @return boolean True if a valid security question/answer combination.
 */
function isValidSecurityQuestion($securityQuestion, $securityAnswer) {
    $isValid = strlen(trim($securityQuestion)) > 3 && strlen(trim($securityAnswer)) > 2;
    return $isValid;
}

include_once(VIEWS_ROOT . 'header.php');
?>
<div class="container">
    <div id="user-profile" class="card card-primary m-2 p-4">
<?php
    if ($redirectedStatusMessage != '') {
        echo('<div class="card card-danger"><div class="card-heading"><h4>' . $redirectedStatusMessage . '</h4></div></div>');
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
        <p>Let's get you registered so you can login to see your profile, earn coins, appear on leader boards, earn rewards, and participate in contests and our community. It's fun and it's <i>free</i>!</p>
<?php
        }
?>
        <div id="errorContent" class="errorContent"><?php echo($errorMessage);?></div>
        <div class="row">
            <div class="col profile-login">
                <form id="register_form" method="POST" action="/profile/" onsubmit="return <?php if ($isLoggedIn) { echo('profilePage.updateFormValidation()'); } else {echo('profilePage.registerFormValidation()');} ?>;">
<?php
    if ($isLoggedIn) {
?>
                    <ul class="nav nav-tabs" id="registrationTabNav" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="required-tab" data-bs-toggle="tab" data-bs-target="#requiredInfo" type="button" role="tab" aria-controls="requiredInfo" aria-selected="true">Required</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="extended-tab" data-bs-toggle="tab" data-bs-target="#extendedInfo" type="button" role="tab" aria-controls="extendedInfo" aria-selected="false">Extended</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="security-tab" data-bs-toggle="tab" data-bs-target="#securityInfo" type="button" role="tab" aria-controls="securityInfo" aria-selected="false">Security</button>
                        </li>
                    </ul>
<?php
    }
?>
                    <div class="tab-content" id="registrationTabContent">
                        <div role="tabpanel" class="tab-pane fade py-1 px-4 show active" id="requiredInfo" aria-labelledby="required-tab">
<?php
        if ( ! $isLoggedIn) {
?>
                            <h3><span class="varyn-shield-icon"></span> Registration</h3>
<?php
        }
?>
                            <div class="row m-2 g-2">
                                <div class="col-4">
                                    <div class="form-floating">
                                        <input type="email" name="register-form-email" class="form-control required email" id="register-form-email" placeholder="Your@email.address" autocomplete="email" autocorrect="off" autocapitalize="off" required maxlength="80" value="<?php echo($email);?>"/>
                                        <label for="register-form-email">Email: <span class="required-field">*</span></label>
                                    </div>
                                </div>
                                <div class="col-2">
                                </div>
                            </div>
                            <div class="row m-2 g-2">
                                <div class="col-4">
                                    <div class="form-floating">
                                        <input type="text" name="register-form-username" class="form-control required username" id="register-form-username" placeholder="A unique user name" autocomplete="username" autocorrect="off" required maxlength="50" value="<?php echo($userName);?>" data-target="register-form-username-unique"/>
                                        <label for="register-form-username">User name: <span class="required-field">*</span></label>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <span id="register-form-username-unique" class="username-is-not-unique" style="margin-top: 1rem;"></span>
                                </div>
                            </div>
<?php
        if ( ! $isLoggedIn) {
?>
                            <div class="row m-2 g-2">
                                <div class="col-4">
                                    <div class="form-floating">
                                        <input type="password" name="register-form-password" class="form-control required password" id="register-form-password" placeholder="A secure password" autocomplete="current-password" autocorrect="off" required maxlength="20" value="<?php echo($password);?>"/>
                                        <label for="register-form-password">Password: <span class="required-field">*</span></label>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="showPasswordButton" style="margin-top: 1rem;" onclick="profilePage.onClickRegisterShowPassword(this);"><span id="register-form-show-password-text">Show</span> <span id="register-form-show-password-icon" class="iconEye" aria-hidden="true"></span></div>
                                </div>
                            </div>
<?php
        }
?>
                            <div class="row m-2 g-2">
                                <div class="col-4">
                                    <div class="form-floating">
                                        <input type="text" name="register-form-fullname" class="form-control fullname" id="register-form-fullname" placeholder="Your full name" autocomplete="name" autocorrect="off" maxlength="50" value="<?php echo($fullname);?>" autocomplete="on" autocorrect="off"/>
                                        <label for="register-form-fullname">Full name:</label>
                                    </div>
                                </div>
                                <div class="col-2">
                                </div>
                            </div>
                            <div class="row m-2 g-2">
                                <label for="register-form-gender" class="col-1">You are:</label>
                                <div class="col-5 form-group-gender"><label><input type="radio" name="register-form-gender" value="M" <?php echo($gender == 'M' ? 'checked' : '');?>/>&nbsp;&nbsp;Male</label><label><input type="radio" name="register-form-gender" value="F" <?php echo($gender == 'F' ? 'checked' : '');?>/>&nbsp;&nbsp;Female</label><label><input type="radio" name="register-form-gender" value="U" <?php echo($gender == 'U' ? 'checked' : '');?>/>&nbsp;&nbsp;Other</label></div>
                            </div>
                            <div class="row m-2 g-2">
                                <div class="col-4">
                                    <label for="register-form-dob">Date of birth:</label>
                                    <input type="date" name="register-form-dob" class="popup-form-input required dob" id="register-form-dob" placeholder="Birthdate" autocomplete="bday" value="<?php echo($dateOfBirth);?>"/>
                                </div>
                                <div class="col-2">
                                </div>
                            </div>
<?php
        if ( ! $isLoggedIn) {
?>
                            <div class="row m-2 g-2">
                                <div class="col-4 text-center">
                                    <label for="register-form-agreement">I agree to the <a href="/tos/" target="_popup">Terms of Service</a><span class="required-field">*</span></label><br/>
                                    <span><small>No</small>&nbsp;&nbsp;<input type="range" name="register-form-agreement" class="validation-slider" id="register-form-agreement" placeholder="Slide this to Yes (all the way left) to agree" alt="Slide this to Yes (all the way left) to agree" min="0" max="2" />&nbsp;&nbsp;<small>Yes</small></span>
                                </div>
                                <div class="col-2">
                                </div>
                            </div>
                            <div class="row m-2 g-2">
                                <div class="col-4 text-center">
                                    <input type="submit" value="Register" name="popupregister" id="registerButton" class="btn btn-lg btn-success"/>
                                </div>
                                <div class="col-2">
                                </div>
                            </div>
                            <div class="row m-2 g-2">
                                <div class="col-3">
                                    <span class="rememberme-container"><input type="checkbox" checked="checked" name="register-form-rememberme" id="register-form-rememberme"><label for="register-form-rememberme">&nbsp;&nbsp;Remember Me</label></span>
                                </div>
                                <div class="col-3">
                                    <span class="register-login-option">Already a member? <a href="/profile/" title="Already a member? Log in with your account" alt="Already a member? Log in with your account.">Log in</a>.</span>
                                </div>
                            </div>
<?php
        } else {
?>
                            <div><input type="submit" value="Update" name="popupregister" id="registerButton" class="btn btn-success"/><button value="Cancel" name="popupcancel" id="registerCancel" class="btn btn-danger" style="margin-left: 2em;" onclick="profilePage.cancelUpdate(event);">Cancel</button></div>
<?php
        }
?>
                            <input type="hidden" name="action" value="<?php echo($registrationOrUpdate);?>" /><input type="text" name="emailaddress" class="popup-form-address-input" /><input type="hidden" name="all-clear" value="<?php echo($hackerVerification);?>" />
                        </div>
<?php
    if ($isLoggedIn) {
?>
                        <div role="tabpanel" class="tab-pane fade p-4" id="extendedInfo" aria-labelledby="extended-tab">
                            <p>Manage info about you to share with others:</p>
                            <img class="avatarThumbnail" src="<?php echo($enginesis->avatarURL(0, $userInfo->user_id));?>"/>
                            <div class="form-group"><label for="register-form-location">Location:</label><input type="text" name="register-form-location" class="popup-form-input required location" id="register-form-location" placeholder="Where are you?" autocomplete="on" maxlength="80" value="<?php echo($location);?>"/></div>
                            <div class="form-group"><label for="register-form-tagline">Tag line:</label><input type="text" name="register-form-tagline" class="popup-form-input required tagline" id="register-form-tagline" placeholder="Your tag line" autocomplete="on" maxlength="50" value="<?php echo($tagline);?>"/></div>
                            <div class="form-group"><label for="register-form-aboutme">About me:</label><textarea class="form-control" name="register-form-aboutme" id="register-form-aboutme" placeholder="About me" maxlength="500" rows="4"><?php echo($aboutMe);?></textarea></div>
                        </div>
                        <div role="tabpanel" class="tab-pane fade p-4" id="securityInfo" aria-labelledby="security-tab">
                            <p>Manage security settings for your account:</p>
                            <div class="form-group">
                                <input type="button" id="change_password" onclick="profilePage.changePassword();" value="Change Password" class="btn btn-info"/>
                            </div>
                            <div class="form-group"><label for="register-form-question">Your question:</label><input type="text" name="register-form-question" class="form-control" id="register-form-question" placeholder="Security question" autocomplete="on" maxlength="80" value="<?php echo($securityQuestion);?>"/></div>
                            <div class="form-group"><label for="register-form-answer">Your answer:</label><input type="text" name="register-form-answer" class="form-control" id="register-form-answer" placeholder="Security answer" autocomplete="on" maxlength="80" value="<?php echo($securityAnswer);?>"/></div>
                            <div class="form-group"><label for="register-form-phone">Mobile number:</label><input type="tel" name="register-form-phone" class="form-control cellphone" id="register-form-phone" placeholder="Mobile number" autocorrect="off" autocomplete="tel" maxlength="20" value="<?php echo($cellphone);?>"/></div>
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
            $inputFocusId = 'login-form-username';
        }
        $hackerVerification = makeInputFormHackerToken();
?>
        <h2>Profile</h2>
        <p>You are not logged in. Login to see your profile, earn coins, appear on leader boards, and participate in our community.</p>
        <div class="row">
            <div class="card card-light col col-md-6 col-sm-12 p-4 m-2">
                <h4>Already a member? Log in:</h4>
                <div id="errorContent" class="errorContent"><?php echo($errorMessage);?></div>
                <form id="login" method="POST" action="/profile/">
                    <div class="container">
                        <div class="row m-2 g-2">
                            <div class="col">
                                <div class="form-floating">
                                    <input type="text" id="login-form-username" name="login-form-username" class="form-control required" value="<?php echo($userName);?>" autocorrect="off" autocomplete="username" placeholder="Your user name"/>
                                    <label for="login-form-username">User name:</label>
                                </div>
                            </div>
                            <div class="col-1">
                            </div>
                        </div>
                        <div class="row m-2 g-2">
                            <div class="col">
                                <div class="form-floating">
                                    <input type="password" id="login-form-password" name="login-form-password" class="form-control" value="<?php echo($password);?>" placeholder="Your password" autocomplete="current-password" autocorrect="off" required/>
                                    <label for="login-form-password">Password:</label>
                                </div>
                            </div>
                            <div class="col-1">
                                <div class="showPasswordButton" style="margin-top: 1rem; margin-right: -1rem;" onclick="profilePage.onClickLoginShowPassword(this);"><span id="login-form-show-password-text">Show</span> <span id="login-form-show-password-icon" class="iconEye" aria-hidden="true"></span></div>
                            </div>
                        </div>
                        <div class="row m-2 g-2">
                            <input type="button" class="btn btn-success" id="login-button" title="Login" value="Login >" onclick="profilePage.loginValidation();" /><input type="text" name="login-form-email" class="popup-form-address-input" /><input type="hidden" name="all-clear" value="<?php echo($hackerVerification);?>" />
                        </div>
                        <div class="row m-2 g-2">
                            <div class="col align-self-start">
                                <span class="rememberme-container"><input type="checkbox" checked="checked" name="login-form-rememberme" id="login-form-rememberme"><label for="login-form-rememberme">&nbsp;&nbsp;Remember Me</label></span>
                            </div>
                            <div class="col align-self-end">
                                <a id="profile-forgot-password" href="#" data-toggle="modal" data-target="#modal-forgot-password" onclick="profilePage.forgotPassword();">Forgot password?</a><input type="hidden" name="action" value="login" />
                            </div>
                        </div>
                        <div class="row m-2 g-2">
                            <div class="text-center pt-4">
                                <a class="sub-link-group" href="/privacy/">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-incognito" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="m4.736 1.968-.892 3.269-.014.058C2.113 5.568 1 6.006 1 6.5 1 7.328 4.134 8 8 8s7-.672 7-1.5c0-.494-1.113-.932-2.83-1.205a1.032 1.032 0 0 0-.014-.058l-.892-3.27c-.146-.533-.698-.849-1.239-.734C9.411 1.363 8.62 1.5 8 1.5c-.62 0-1.411-.136-2.025-.267-.541-.115-1.093.2-1.239.735Zm.015 3.867a.25.25 0 0 1 .274-.224c.9.092 1.91.143 2.975.143a29.58 29.58 0 0 0 2.975-.143.25.25 0 0 1 .05.498c-.918.093-1.944.145-3.025.145s-2.107-.052-3.025-.145a.25.25 0 0 1-.224-.274ZM3.5 10h2a.5.5 0 0 1 .5.5v1a1.5 1.5 0 0 1-3 0v-1a.5.5 0 0 1 .5-.5Zm-1.5.5c0-.175.03-.344.085-.5H2a.5.5 0 0 1 0-1h3.5a1.5 1.5 0 0 1 1.488 1.312 3.5 3.5 0 0 1 2.024 0A1.5 1.5 0 0 1 10.5 9H14a.5.5 0 0 1 0 1h-.085c.055.156.085.325.085.5v1a2.5 2.5 0 0 1-5 0v-.14l-.21-.07a2.5 2.5 0 0 0-1.58 0l-.21.07v.14a2.5 2.5 0 0 1-5 0v-1Zm8.5-.5h2a.5.5 0 0 1 .5.5v1a1.5 1.5 0 0 1-3 0v-1a.5.5 0 0 1 .5-.5Z"/>
                                </svg> Privacy</a>
                                <a class="sub-link-group" href="/tos/">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle" viewBox="0 0 16 16">
                                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                    <path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
                                </svg> Terms</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card card-light col col-lg-5 col-md-5 col-sm-12 p-4 m-2 align-self-end">
                <h4>Not a member?</h4>
                <input type="button" class="btn btn-primary btn-varyn m-2" id="profile-register-now" value="Sign up with Email" onclick="profilePage.showRegistrationPopup(true);" title="Sign up with your email address" /><br/>
                <h4>Or</h4>
                <input type="button" class="btn btn-primary btn-facebook m-2" id="facebook-connect-button" value="Login with Facebook" title="Login with your Facebook account" aria-label="Login with your Facebook account" role="button">
                <input type="button" class="btn btn-primary btn-gapi-signin m-2" id="gapi-signin-button" value="Sign in with Google" title="Sign in with your Google account" aria-label="Sign in with your Google account" role="button">
                <input type="button" class="btn btn-primary btn-twitter-signin m-2" id="twitter-signin-button" value="Sign in with Twitter" title="Sign in with your Twitter account" aria-label="Sign in with your Twitter account" role="button">
            </div>
        </div>
<?php
    }
?>
    </div>
    <div class="container">
<?php
    if ($isLoggedIn) {
        buildFavoriteGamesSection();
    }
    buildGamesSection($topGamesListId, 'Hot games');
?>
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
if (empty($refreshToken)) {
    $refreshTokenJavaScript = "\n";
} else {
    $refreshTokenJavaScript = "\n        varynApp.saveRefreshToken('$refreshToken');\n";
}
?>
<script>
    var varynApp;
    var profilePage;
    var debug = true;

    head.ready(function() {
        var pageConfiguration = Object.assign(siteConfiguration, {
            gameId: 0,
            gameGroupId: 0,
            gameListIdTop: 4,
            gameListIdNew: 5,
            facebookAppId: '<?php echo($socialServiceKeys[2]['app_id']);?>',
            googleAppId: '<?php echo($socialServiceKeys[7]['app_id']);?>',
            twitterAppId: '<?php echo($socialServiceKeys[11]['app_id']);?>',
            appleAppId: '<?php echo($socialServiceKeys[14]['app_id']);?>'
        });
        var profilePageParameters = {
            errorFieldId: '<?php echo($errorFieldId);?>',
            inputFocusId: '<?php echo($inputFocusId);?>',
            showSubscribe: '<?php echo($showSubscribe);?>',
            userInfo: '<?php echo(addslashes(json_encode($enginesis->getLoggedInUserInfo())));?>',
            isLogout: <?php echo($isLogout);?>
        };
        varynApp = varyn(pageConfiguration);
        profilePage = varynApp.initApp(varynProfilePage, profilePageParameters);<?php echo($refreshTokenJavaScript);?>
    });
    if (debug) {
        head.js('/common/modernizr.js', '/common/bootstrap.bundle.min.js', '/common/enginesis.js', '/common/ShareHelper.js', '/common/commonUtilities.js', '/common/ssoFacebook.js', '/common/ssoGoogle.js', '/common/ssoTwitter.js', '/common/varyn.js', '/common/varynProfilePage.js');
    } else {
        head.js('/common/modernizr.js', '/common/bootstrap.bundle.min.js', '/common/enginesis.min.js', '/common/varynProfilePage.min.js');
    }
</script>
</body>
</html>