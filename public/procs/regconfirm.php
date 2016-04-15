<?php
/**
 * Handle registration confirmation from email request.
 * @Date: 1/5/16
 */
    require_once('../../services/common.php');
    $user_id = getPostOrRequestVar('u', 0);
    $site_id = getPostOrRequestVar('s', 0);
    $token = getPostOrRequestVar('t', '');
    $language_code = sessionGetLanguageCode();
    $redirectTo = '/index.php';

    if ($site_id > 0 && isset($site_data[$site_id])) {
        $protocol = getServerHTTPProtocol();
        $serverDomain = serverStageMatch($site_data[$site_id]['site_base_url']);
        $siteLoginURL = $protocol . $serverDomain . $site_data[$site_id]['site_login_url'];
        $siteProfileURL = $protocol . $serverDomain . $site_data[$site_id]['site_profile_url'];
        if ($user_id > 0 && strlen($token) > 0) {
            // TODO: look up token verify user id and set this user as confirmed then redirect to profile page.

            // call RegisteredUserConfirm (IN _site_id int, IN _logged_in_user_id int, IN _secondary_password varchar(128), in _language_code char(2), OUT _success boolean, OUT _status_msg varchar(255))
            $sql = 'call RegisteredUserConfirm(?, ?, ?, ?, @success, @status_msg)';
            $sqlParameters = array($site_id, $user_id, $token, $language_code);
            $sqlResults = dbQuery($sql, $sqlParameters);
            if ($sqlResults && dbRowCount($sqlResults) > 0) {
                $userInfo = dbFetch($sqlResults);
                if ($userInfo != null) {
                    $redirectTo = $siteProfileURL;
                } else {
                    $redirectTo = $siteLoginURL; // Anything we don't like just redirect to login
                }
                dbClearResults($sqlResults);
            } else {
                debugLog('RegisteredUserConfirm database error ' . dbError($sqlResults));
            }
            $sql = 'select @success, @status_msg;';
            $sqlResults = dbQuery($sql, null);
            if ($sqlResults && dbRowCount($sqlResults) > 0) {
                $statusInfo = dbFetch($sqlResults);
                $success = $statusInfo['@success'];
                $status_message = $statusInfo['@status_msg'];
                if ($success == 1) {
                    $redirectTo .= '?regconf=1';
                } else {
                    $redirectTo .= '?regconf=0&msg=' . $status_message;
                }
            } else {
                debugLog('RegisteredUserConfirm database error retrieving status ' . dbError($sqlResults));
            }
        } else {
            $redirectTo = $siteLoginURL; // Anything we don't like just redirect to login
        }
    }
    if ($redirectTo != '') {
        header('Location: ' . $redirectTo); // Anything we don't like just redirect to the home page
        return;
    }
