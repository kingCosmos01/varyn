<?php
/**
 * SSO/OAuth services come here when a user is asked to deauthorize our app. In this case we should log the user out
 * and also delete all their data.
 * Author: jf
 * Date: 5/22/2017
 */
require_once('../../services/common.php');
setErrorReporting(true);
debugLog('deauth called with ' . implode(',', $_POST));
