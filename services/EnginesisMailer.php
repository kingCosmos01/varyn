<?php
/** EnginesisMailer.php
 *
 * Class to handle php mail functionality, basically simplifies the interface to phpMailer and MailGun,
 * while removing dependency on PHP's mail() function and still maintaining legacy mail interface.
 * Setup the message using the set functions then call send().
 * Also provides a stub test function sendText() to validate the object for unit testing.
 * You can set the message text either plain text, HTML, or an external file containing the email body.
 */
require 'phpmailer/PHPMailerAutoload.php';
require 'lib/vendor/autoload.php';
require_once 'classes/Database.php';
require_once 'EnginesisErrors.php';
use Mailgun\Mailgun;


class EnginesisMailer {
    private $m_siteId;
    private $m_userId;
    private $m_fromEmail;
    private $m_fromName;
    private $m_toList;
    private $m_subject;
    private $m_messageBodyText;
    private $m_messageBodyHtml;
    private $m_emailFile;
    private $m_emailFileIsText;
    private $m_emailId;
    private $m_emailNotificationTypeId;
    private $m_status;
    private $m_debug;
    private $m_logging;
    private $m_extendedErrorInfo;
    private $m_serverStage;
    private $m_mailConfig;
    private $m_languageCode;
    private $m_mailLogCategory;
    private $m_enginesisLogger;

    /**
     * Static helper function to allow sending certain emails quickly and easily.
     * Send the requested email given the email notification type identifier. This function will fail quietly so if you
     * want the error you should monitor the return code. It will log an error to the system log. If the to/from email
     * addresses are fine then a typical failure is related to system SMTP connections and access privileges.
     * 
     * @param string|array $to Who to send to. String if one email address, array if multiple email addresses.
     * @param integer $site_id Site id is required.
     * @param integer $user_id Optional logged in user id.
     * @param integer $game_id Optional game identifier if the email is related to a specific game.
     * @param integer $email_notification_type_id Email notification type identifier, from EmailNotificationTypes, indicates which email category the message belongs to.
     * @param string $language_code
     * @param array $parameters Array of parameters for token replacement in the email content.
     * @return string empty string if email queued to be sent, an error message if something did not go as planned.
     */
    public static function SendUserEmailNotification(
        $to,
        $site_id,
        $user_id,
        $game_id,
        $emailNotificationTypeId,
        $language_code,
        $parameters
    ) {
        $mailer = new EnginesisMailer($site_id, $user_id, '', $to, '', '', '', $language_code);
        if ($mailer !== null) {
            $mailer->setDebug(false);
            $mailer->setEmailNotificationTypeId($emailNotificationTypeId);
            $errorCode = $mailer->sendEmailNotification($game_id, $parameters);
        } else {
            $errorCode = EnginesisErrors::SERVICE_ERROR;
        }
        return $errorCode;
    }

    /**
     * Static helper function to send an email to the site administrator. This allows
     * internal, system-based functions, the ability to easily send a notification email
     * to the site admin. It is NOT intended for user or service capability as it could be
     * abused. This is only intended for internal site-based functions that need a means
     * to alert the site admin.
     * 
     * @param integer $site_id Site id is required.
     * @param integer $user_id Optional logged in user id.
     * @param array $parameters Array of parameters for token replacement in the email message.
     * @param string $language_code Default 'en', helps provide any language based message look-up.
     * @return string empty string if email queued to be sent, an error message if something did not go as planned.
     */
    public static function SendSystemEmailNotification(
        $site_id,
        $user_id,
        $subject,
        $message,
        $parameters,
        $language_code = 'en'
    ) {
        $mailer = new EnginesisMailer($site_id, $user_id, '', '', '', '', '', $language_code);
        if ($mailer !== null) {
            $mailer->setDebug(false);
            $mailer->setEmailNotificationTypeId(99);
            $errorCode = $mailer->sendSystemNotification($subject, $message, $parameters);
        } else {
            $errorCode = EnginesisErrors::SERVICE_ERROR;
        }
        return $errorCode;
    }

    /**
     * Construct the mailer object and set its initial state.
     * 
     * @param integer $site_id Which site is sending the email. Must be a valid site id.
     * @param integer $user_id Which logged in user is sending the email. Optional, use 0 if no logged in user.
     * @param string $fromEmailAddress Optional from email address.
     * @param null|string|array $recipientList optional recipient (string) or recipient list (array)
     * @param string $emailSubject optional message subject
     * @param string $textBody optional text message body
     * @param string $htmlBody optional HTML message body
     * @param string $languageCode Optional language code specifier.
     */
    public function __construct ( $site_id,
                                  $user_id = 0,
                                  $fromEmailAddress = '',
                                  $recipientList = null,
                                  $emailSubject = '',
                                  $textBody = '',
                                  $htmlBody = '',
                                  $languageCode = 'en') {
        global $enginesisLogger;
        $this->m_enginesisLogger = $enginesisLogger;
        $this->clear();
        $this->m_logging = true;
        $this->m_siteId = $site_id;
        $this->m_userId = $user_id;
        $this->setLanguageCode($languageCode);
        $this->setFromEmail($fromEmailAddress);
        if ($recipientList != null) {
            $this->setToEmail($recipientList);
        }
        $this->setSubject($emailSubject);
        if ($textBody != null && $textBody != '') {
            $this->setTextMessage($textBody);
        }
        if ($htmlBody != null && $htmlBody != '') {
            $this->setHTMLMessage($htmlBody);
        }
        $this->setStatus(EnginesisErrors::NO_ERROR, '');
        $this->m_emailId = '';
        if (function_exists('serverStage')) {
            $this->setServerStage(serverStage());
        }
    }

    /**
     * Destructor: free any references before destructing the object.
     */
    public function __destruct () {
        $this->clear();
    }

    /**
     * The mailer must know which stage we are running on.
     * 
     * @param string $serverStage
     */
    public function setServerStage ($serverStage) {
        global $_MAIL_HOSTS;
        if (isset($_MAIL_HOSTS[$this->m_serverStage])) {
            $this->m_serverStage = $serverStage;
        } else {
            // if the stage provided is invalid then assume live.
            $this->m_serverStage = '';
        }
        $this->m_mailConfig = $_MAIL_HOSTS[$this->m_serverStage];
        return $this->m_serverStage;
    }

    /**
     * Send email if the user is authenticated, using the user's name, email as from.
     * TODO: Note yet implemented.
     * 
     * @return string send status, EnginesisErrors::NO_ERROR if OK, otherwise error id.
     */
    public function sendAuthenticated () {
        return $this->setStatus(EnginesisErrors::NOT_IMPLEMENTED, 'This feature has not been implemented');
    }

    /**
     * Send the email. Validate the state of the object and if everything is OK then send the message.
     * 
     * @return string send status, EnginesisErrors::NO_ERROR if OK, otherwise error code.
     */
    public function send () {
        // if HTML body is set we use that, otherwise text body must be set!
        $emailMessage = '';
        if (strlen($this->m_emailFile) > 0) {
            if (file_exists($this->m_emailFile)) {
                $fileHandle = fopen($this->m_emailFile, 'r');
            } else {
                $fileHandle = null;
                $this->setStatus(EnginesisErrors::INVALID_FILE, 'File ' . $this->m_emailFile . ' cannot be opened.');
            }
            if ($fileHandle) {
                while ( ! feof($fileHandle)) {
                    $emailMessage = $emailMessage . fgets($fileHandle, 4096);
                }
                fclose ($fileHandle);
            }
            $useHTML = ! $this->m_emailFileIsText;
        } elseif (strlen($this->m_messageBodyHtml) > 0) {
            $emailMessage = $this->m_messageBodyHtml;
            $useHTML = true;
        } else {
            $emailMessage = $this->m_messageBodyText;
            $useHTML = false;
        }
        if ($this->m_status == EnginesisErrors::NO_ERROR) {
            // if we got this far and still don't have a from address, see if we can derive one from user-id.
            $this->verifyAndSetTo();
            $this->verifyAndSetFrom();
            if (count($this->m_toList) > 0 && strlen($this->m_fromEmail) > 0 && (strlen($this->m_subject) > 0 || strlen($emailMessage) > 0)) {
                if ($this->m_mailConfig['apikey'] == '') {
                    $this->SendEmailViaPhpMail($useHTML, $emailMessage);
                } else {
                    if (strlen($this->m_messageBodyHtml) > 0 && strlen($this->m_messageBodyText) > 0) {
                        $this->SendMultipartEmailViaMailGun($this->m_messageBodyHtml, $this->m_messageBodyText);
                    } else {
                        $this->SendEmailViaMailGun($useHTML, $emailMessage);
                    }
                }
            } else {
                $this->setStatus(
                    EnginesisErrors::INVALID_PARAMETER,
                    'Must provide to (' . count($this->m_toList) . '), from (' . strlen($this->m_fromEmail) . '), subject (' . strlen($this->m_subject) . '), message body (' . strlen($emailMessage) . ')'
                );
            }
        }
        return $this->m_status;
    }

    /**
     * Determine if email can be sent given the state of the object.
     * 
     * @return boolean `true` if there is enough information to make a connection to the mail host.
     */
    public function canSendEmail() {
        $mailConfig = $this->m_mailConfig;
        return ! empty($mailConfig['host']) && ! empty($mailConfig['user']) && ! empty($mailConfig['password']);
    }

    /**
     * Send email using the PHP mail driver. This function will update the internal state of the mail
     * object even if mail cannot be sent at this time.
     * 
     * @return string Empty string if mail was queued for send, otherwise an error code.
     */
    public function SendEmailViaPhpMail ($useHTML, $emailMessage) {
        if (! $this->canSendEmail()) {
            return $this->setStatus(
                EnginesisErrors::SEND_ERROR,
                'This server (' . $this->m_serverStage . ') is not configured to send email.'
            );
        }
        $this->setStatus(EnginesisErrors::NO_ERROR, '');
        $mailConfig = $this->m_mailConfig;
        $mailer = new PHPMailer();
        $mailer->SMTPDebug  = $this->m_debug ? 1 : 0;  // enables SMTP debug information (for testing)
        $mailer->SMTPAuth   = true;                    // enable SMTP authentication
        $mailer->Host       = $mailConfig['host'];
        $mailer->Port       = $mailConfig['port'];     // set the SMTP port for the SMTP server
        $mailer->Username   = $mailConfig['user'];     // SMTP account username
        $mailer->Password   = $mailConfig['password']; // SMTP account password
        if ($mailConfig['ssl']) {
            $mailer->SMTPSecure = 'ssl';
        } elseif ($mailConfig['tls']) {
            $mailer->SMTPSecure = 'tls';
        }
        $mailer->IsSMTP();
        $mailer->IsHTML($useHTML);
        $mailer->From = $this->m_fromEmail;
        $mailer->FromName = $this->getFromName();
        $mailer->Subject = $this->m_subject;
        $mailer->Body = $emailMessage;
        foreach ($this->m_toList as $toAddress) {
            $mailer->AddAddress($toAddress);
        }
        try {
            if ( ! $mailer->Send()) {
                $this->setStatus(EnginesisErrors::SEND_ERROR, $mailer->ErrorInfo);
            } else {
                $this->setStatus(EnginesisErrors::NO_ERROR, '');
                $this->m_emailId = $mailer->MessageID;
                $this->logSuccessfulSend($this->getToEmailAsString());
            }
        } catch (Exception $e) {
            $this->setStatus(EnginesisErrors::SYSTEM_ERROR, $e->getMessage());
        }
        return $this->m_status;
    }

    /**
     * Send email using the Mailgun mail driver. This function will update the internal state of the mail
     * object even if mail cannot be sent at this time.
     * 
     * @param boolean $useHTML true if the message is HTML format, false if it is text format.
     * @param string $message The message body.
     * @return string Empty string if mail was queued for send, otherwise an error code.
     */
    public function SendEmailViaMailGun ($useHTML, $message = null) {
        $this->setStatus(EnginesisErrors::NO_ERROR, '');
        $mailConfig = $this->m_mailConfig;
        $thisServer = '';
        $mailGun = new Mailgun($mailConfig['apikey']);
        if ($mailGun != null) {
            if (! $this->canSendEmail()) {
                return $this->setStatus(EnginesisErrors::SEND_DISABLED, 'This server is not configured to send email.');
            }    
            $mailDomain = $mailConfig['domain'];
            $from = $this->getFromName() . ' <' . $this->m_fromEmail . '>';
            if ( ! empty($message)) {
                if ($useHTML) {
                    $this->setHTMLMessage($htmlBody);
                } else {
                    $this->setTextMessage($textBody);
                }
            }
            $mailParameters = [
                'from'    => $from,
                'subject' => $this->getSubject()
            ];
            if ($useHTML) {
                $mailParameters['html'] = $this->getHTMLMessage();
            } else {
                $mailParameters['text'] = $this->getTextMessage();
            }
            try {
                foreach ($this->m_toList as $toAddress) {
                    $mailParameters['to'] = $toAddress;
                    $result = $mailGun->sendMessage($mailDomain, $mailParameters);
                    if ($result && isset($result->http_response_code)) {
                        if ($result->http_response_code != 200) {
                            $resultStr = json_encode($result);
                            $this->setStatus(EnginesisErrors::SYSTEM_ERROR, $resultStr);
                            $this->log("SendTextEmailViaMailGun error " . $resultStr . " sending to $toAddress through $mailDomain", LogMessageLevel::Error, $this->m_mailLogCategory, __FILE__, __LINE__);
                        } else {
                            if (isset($result->http_response_body) && isset($result->http_response_body)) {
                                $this->m_emailId = $result->http_response_body->id;
                            }
                            $this->setStatus(EnginesisErrors::NO_ERROR, '');
                            $this->logSuccessfulSend($toAddress);
                        }
                    } else {
                        $errorMessage = "SendTextEmailViaMailGun error NO RESPONSE sending to $mailDomain on stage $thisServer";
                        $this->setStatus(EnginesisErrors::SEND_ERROR, $errorMessage);
                        $this->log($errorMessage, LogMessageLevel::Error, $this->m_mailLogCategory, __FILE__, __LINE__);
                    }
                }
            } catch (Exception $e) {
                $errorMessage = "SendTextEmailViaMailGun error $e on $thisServer sending to $mailDomain";
                $this->setStatus(EnginesisErrors::SEND_ERROR, $errorMessage);
                $this->log($errorMessage, LogMessageLevel::Error, $this->m_mailLogCategory, __FILE__, __LINE__);
            }
        } else {
            $errorMessage = "SendTextEmailViaMailGun Cannot open connection to MailGun Service on $thisServer";
            $this->setStatus(EnginesisErrors::SEND_ERROR, $errorMessage);
                $this->log($errorMessage, LogMessageLevel::Error, $this->m_mailLogCategory, __FILE__, __LINE__);
        }
        return $this->m_status;
    }

    /**
     * Send multi-part email using the Mailgun mail driver. A multi-part email contains
     * both HTML and plain text message body. It is expected the email object has been
     * configured with to, from, and subject. This function will update the internal state
     * of the mail object even if mail cannot be sent at this time.
     * 
     * @param string|null $htmlBody The message body as HTML text. Default is to set HTML message body with setHTMLMessage().
     * @param string|null $textBody The message body as plain text. Default is to set text message body with setTextMessage().
     * @return string EnginesisErrors::NO_ERROR if mail was queued for send, otherwise an error code.
     */
    public function SendMultipartEmailViaMailGun ($htmlBody = null, $textBody = null) {
        $this->setStatus(EnginesisErrors::NO_ERROR, '');
        $mailConfig = $this->m_mailConfig;
        $thisServer = '';
        $mailDomain = $mailConfig['domain'];
        $from = $this->getFromName() . ' <' . $this->m_fromEmail . '>';
        $mailGun = new Mailgun($mailConfig['apikey']);
        if ($mailGun != null) {
            if (! $this->canSendEmail()) {
                return $this->setStatus(EnginesisErrors::SYSTEM_ERROR, 'This server is not configured to send email.');
            }    
            try {
                if ( ! empty($htmlBody)) {
                    $this->setHTMLMessage($htmlBody);
                }
                if ( ! empty($textBody)) {
                    $this->setTextMessage($textBody);
                }
                foreach ($this->m_toList as $toAddress) {
                    $parameters = [
                        'from'    => $from,
                        'to'      => $toAddress,
                        'subject' => $this->m_subject,
                        'html'    => $this->getHTMLMessage(),
                        'text'    => $this->getTextMessage()
                    ];
                    $result = $mailGun->sendMessage($mailDomain, $parameters);
                    if ($result && isset($result->http_response_code)) {
                        if ($result->http_response_code != 200) {
                            $resultStr = json_encode($result);
                            $this->setStatus(EnginesisErrors::SEND_ERROR, $resultStr);
                            $this->log("SendMultipartEmailViaMailGun error " . $resultStr . " sending to $toAddress through $mailDomain", LogMessageLevel::Error, $this->m_mailLogCategory, __FILE__, __LINE__);
                        } else {
                            if (isset($result->http_response_body) && isset($result->http_response_body)) {
                                $this->m_emailId = $result->http_response_body->id;
                            }
                            $this->setStatus(EnginesisErrors::NO_ERROR, '');
                            $this->logSuccessfulSend($toAddress);
                        }
                    } else {
                        $errorMessage = "SendMultipartEmailViaMailGun error NO RESPONSE sending to $mailDomain on stage $thisServer";
                        $this->setStatus(EnginesisErrors::SEND_ERROR, $errorMessage);
                        $this->log($errorMessage, LogMessageLevel::Error, $this->m_mailLogCategory, __FILE__, __LINE__);
                    }
                }
            } catch (Exception $e) {
                $errorMessage = "SendMultipartEmailViaMailGun error $e on $thisServer sending to $mailDomain";
                $this->setStatus(EnginesisErrors::SEND_ERROR, $errorMessage);
                $this->log($errorMessage, LogMessageLevel::Error, $this->m_mailLogCategory, __FILE__, __LINE__);
            }
        } else {
            $errorMessage = "SendMultipartEmailViaMailGun Cannot open connection to MailGun Service on $thisServer";
            $this->setStatus(EnginesisErrors::SEND_ERROR, $errorMessage);
            $this->log($errorMessage, LogMessageLevel::Error, $this->m_mailLogCategory, __FILE__, __LINE__);
        }
        return $this->m_status;
    }

    /**
     * Set the email to list and attempt to verify the email address(es).
     * Takes a single parameter that may be either a string or an array. If it is an array, each item
     * must be a string representing a single email address. If it is a string, it may contain one or more
     * addresses if they are separated by a semicolon.
     * 
     * @param $to string for single to address, array for list of addresses
     * @return string Error code, empty string if no error detected.
     */
    public function setToEmail ($to) {
        $this->setStatus(EnginesisErrors::NO_ERROR, '');
        if (is_array($to)) {
            // check each item
            for ($i = 0; $i < count($to); $i++ ) {
                $nextEmail = trim($to[$i]);
                if ( ! checkEmailAddress($nextEmail)) {
                    $this->setStatus(EnginesisErrors::INVALID_TO_ADDRESS, $nextEmail);
                } else {
                    $this->m_toList[] = $nextEmail;
                }
            }
        } elseif (is_string($to)) {
            $to = trim($to);
            if (strlen($to) > 0) {
                if (strpos($to, ';') !== false) {
                    // multiple addresses in one string
                    $listOfEmails = explode(';', $to);
                    $errorResult = $this->setToEmail($listOfEmails);
                    if ($errorResult !== EnginesisErrors::NO_ERROR) {
                        $this->setStatus($errorResult, $listOfEmails);
                    }
                } else {
                    // just one address
                    if (checkEmailAddress($to)) {
                        $this->m_toList[] = $to;
                    } else {
                        $this->setStatus(EnginesisErrors::INVALID_TO_ADDRESS, $to);
                    }
                }
            }
        }
        return $this->m_status;
    }

    /**
     * If the email is properly sent (either sent or queued for delivery, it doesn't matter), log
     * the fact that we sent email. We are going to need a robust email log for tracking email SPAM,
     * throttling, quality, and verification.
     * 
     * @param string $toAddress Email address who the email was sent to.
     * @param array $logParameters Key/value parameters that will be saved as email parameters JSON string.
     *   Expected parameters are a K/V array of:
     *      'emailId' => a transaction id provided by the email service
     *      'emailServiceId' => which email service the email was queued with
     *      'sourceIP' => remote client requesting the send
     *      'from'    => $from
     *
     * @return boolean Indicates the success of the operation.
     */
    private function logSuccessfulSend($toAddress) {
        if ( ! $this->m_logging) {
            return $this->m_status;
        }
        $from = $this->getFromEmail();
        $messageParameters = [
            'emailId' => $this->m_emailId,
            'sourceIP' => $_SERVER['REMOTE_ADDR'],
            'emailServiceId' => 1,
            'from' => $from
        ];
        $email_parameters = json_encode($messageParameters);
        $site_id = $this->m_siteId;
        $user_id = $this->m_userId;
        if ($toAddress == null) {
            $toAddress = $this->getToEmailAsString();
        }
        $email_notification_type_id = $this->m_emailNotificationTypeId;
        $this->log("Mail sent site=$site_id, user=$user_id, from=$from, to=$toAddress, type=$email_notification_type_id, parameters=$email_parameters", LogMessageLevel::Info, $this->m_mailLogCategory, __FILE__, __LINE__);
        $databaseConnection = Database::getDatabaseConnection(DATABASE_ENGINESIS);
        if ($databaseConnection->isValid()) {
            $status = 0;
            $message = '';
            $sql = 'call EmailLogQueue(?, ?, ?, ?, ?, ?, @success, @status_msg)';
            $parameters = [$site_id, $user_id, $toAddress, $email_notification_type_id, $email_parameters, $this->m_languageCode];
            $queryResults = $databaseConnection->query($sql, $parameters);
            if ($queryResults != null) {
                $dbError = $databaseConnection->getLastError($queryResults);
                $rowCount = $databaseConnection->rowCount($queryResults);
                $emailQueueData = $databaseConnection->fetch($queryResults);
                $databaseConnection->clearResults($queryResults);
                $databaseConnection->getLastEnginesisStatus($status, $message);
            }
            if ($dbError || $status < 1) {
                $this->log("Mail EmailLogQueue error $dbError: status=$status, status_msg=$message, params=" . implode(',', $parameters), LogMessageLevel::Error, $this->m_mailLogCategory, __FILE__, __LINE__);
            }
        }
        return $this->m_status;
    }

    /**
     * Update the email log to change the status of a previously logged email transaction. This requires
     * knowing the email_log_id that was set when originally logging the email with `logSuccessfulSend`<div class=""></div>
     * TODO: Not implemented.
     * 
     * @param int The email log id to match in the log.
     * @param array Key/value parameters that will be saved as email parameters JSON string.
     * @return boolean Indicates the success of the update.
     */
    private function updateSendStatus($emailId, $parameters) {
        $resultStatus = false;
        return $resultStatus;
    }

    /**
     * Determine the state of the debugging flag.
     * 
     * @return boolean The debug flag current state.
     */
    public function getDebug () {
        return $this->m_debug;
    }

    /**
     * Set the debug flag. The enables or disables debugging the email sending process.
     * Could help discover errors in the email configuration or server issues.
     * 
     * @param boolean $debugFlag Set the debug flag.
     */
    public function setDebug ($debugFlag) {
        $this->m_debug = $debugFlag;
        return $this->m_debug;
    }

    /**
     * Enable or disable mail logging.
     * @param boolean $loggingFlag True turns logging on, false turns it off.
     * @return boolean The new state of logging.
     */
    public function setLogging($loggingFlag) {
        $this->m_logging = $loggingFlag;
        return $this->m_logging;
    }

    /**
     * Set the language code specifier.
     * 
     * @param string $languageCode A language code string.
     */
    public function setLanguageCode ($languageCode) {
        $this->m_languageCode = $languageCode;
        return $this->m_languageCode;
    }

    /**
     * Get the language code.
     * 
     * @return string Language code.
     */
    public function getLanguageCode() {
        return $this->m_languageCode;
    }

    /**
     * Set the site id.
     * 
     * @param integer $site_id The site id.
     */
    public function setSiteId($site_id) {
        $this->m_siteId = $site_id;
        return $this->m_siteId;
    }

    /**
     * Get the site id.
     * 
     * @return integer The site id.
     */
    public function getSiteId() {
        return $this->m_siteId;
    }

    /**
     * Set the user id.
     * 
     * @param integer $user_id The user id.
     */
    public function setUserId($user_id) {
        $this->m_userId = $user_id;
        return $this->m_userId;
    }

    /**
     * Get the user id.
     * 
     * @return integer The user id.
     */
    public function getUserId() {
        return $this->m_userId;
    }

    /**
     * Indicate which email notification type is being sent. This is only required
     * when logging the email for tracking purposes.
     * 
     * @param integer $emailNotificationTypeId The email notification type id from email_notification_types.
     * @return integer The id that was set.
     */
    public function setEmailNotificationTypeId($emailNotificationTypeId)  {
        $this->m_emailNotificationTypeId = $emailNotificationTypeId;
        return $this->m_emailNotificationTypeId;
    }

    /**
     * Get the email notification type id. This is the type of email that is
     * set to send, if using one of the site-wide email templates.
     * 
     * @return integer The email notification type id.
     */
    public function getEmailNotificationTypeId() {
        return $this->m_emailNotificationTypeId;
    }

    /**
     * Set status information.
     * 
     * @param string $status The error code, should be one of EnginesisErrors enums.
     * @param string $extendedInfo Additional information about the error code.
     */
    private function setStatus($status, $extendedInfo) {
        $this->m_status = $status;
        $this->m_extendedErrorInfo = $extendedInfo;
        return $this->m_status;
    }

    /**
     * Get the last error code, if any.
     * 
     * @return string Last set error code.
     */
    public function getErrorCode () {
        return $this->m_status;
    }

    /**
     * Get the extended error information, if any.
     * 
     * @return string return full status message
     */
    public function getExtendedStatusInfo () {
        return $this->m_extendedErrorInfo;
    }

    /**
     * Get the current email to list.
     * 
     * @return string|array|null String if a single to address, or array of strings of to addresses.
     *   Could return null if a to address was never set.
     */
    public function getToEmail () {
        return $this->m_toList;
    }

    /**
     * Get the email to list as a string.
     * 
     * @param string $separator option use this to separate multiple addresses.
     * @return string list of all to addresses in a single string.
     */
    public function getToEmailAsString ($separator = ', ') {
        return implode($separator, $this->m_toList);
    }

    /**
     * Remove all to addresses. Useful if you want to send the same email
     * in some type of loop but send to different addresses separately.
     */
    public function clearToEmail () {
        $this->m_toList = [];
        return true;
    }

    /**
     * Determine if the specified email address is in the to list.
     * 
     * @param string $email Email address to look up.
     * @return boolean true if the address is included
     */
    public function isEmailInToList ($email) {
        $isInToList = false;
        if (strlen($email) > 0) {
            $email = strtolower($email);
            for ($i = 0; $i < count($this->m_toList); $i++) {
                if (strtolower($this->m_toList[$i]) == $email) {
                    $isInToList = true;
                    break;
                }
            }
        }
        return $isInToList;
    }

    /**
     * Set the from email address, the user this email is from (return address.)
     * 
     * @param string $from The return email address.
     * @return string The object error status '' if OK otherwise a status code if the from address was determined to be invalid,.
     */
    public function setFromEmail ($from) {
        if (checkEmailAddress($from)) {
            $this->m_fromEmail = $from;
            $this->setStatus(EnginesisErrors::NO_ERROR, '');
        } else {
            $this->setStatus(EnginesisErrors::INVALID_FROM_ADDRESS, $from);
        }
        return $this->m_status;
    }

    /**
     * Set the message from email address.
     * 
     * @return string the current from email address
     */
    public function getFromEmail () {
        return $this->m_fromEmail;
    }

    /**
     * Set the name the message is from.
     * 
     * @param string set the name the email is from
     */
    public function setFromName ($fromName) {
        $this->m_fromName = $fromName;
        return $this->m_fromName;
    }

    /**
     * Return the name set as the from message field.
     * 
     * @return string return the name the email is from, or the email address if no name was set.
     */
    public function getFromName () {
        if (strlen($this->m_fromName) > 0 ) {
            return $this->m_fromName;
        } else {
            return $this->m_fromEmail;
        }
    }

    /**
     * Query the database for the current logged in user's name and email
     * so that we can set the To or From email fields to this user. Returns
     * key/value array ['user_name', 'email_address'] for a successful query.
     * If fails, returns null, and sets status.
     * 
     * @return Array|null Returns null if failed to look up user info, otherwise returns
     *   key/value array ['user_name', 'email_address']
     */
    private function queryUserNameAndEmail() {
        $userData = null;
        $databaseConnection = Database::getDatabaseConnection(DATABASE_ENGINESIS);
        if ($databaseConnection->isValid()) {    
            $sql = 'select email_address, user_name from users where site_id=? and user_id=?';
            $parameters = [$this->m_siteId, $this->m_userId];
            $sqlResult = $databaseConnection->query($sql, $parameters);
            if ($sqlResult !== null && $databaseConnection->rowCount($sqlResult) > 0) {
                $userData = $databaseConnection->fetch($sqlResult);
                if ($userData == null) {
                    $dbError = $databaseConnection->getLastError($sqlResult);
                    $rowCount = $databaseConnection->rowCount($sqlResult);
                    $this->setStatus(EnginesisErrors::SYSTEM_ERROR, 'Failed to look up user info for ' . implode(',', [$this->m_siteId, $this->m_userId, $dbError]));
                    $this->log("queryUserNameAndEmail got $rowCount rows. " . $this->m_extendedErrorInfo, LogMessageLevel::Error, $this->m_mailLogCategory, __FILE__, __LINE__);
                }
            } else {
                $dbError = $databaseConnection->getLastError($sqlResult);
                $this->setStatus(EnginesisErrors::SYSTEM_ERROR, 'Failed to look up user info for ' . implode(',', [$this->m_siteId, $this->m_userId, $dbError]));
                $this->log("queryUserNameAndEmail query error. " . $this->m_extendedErrorInfo, LogMessageLevel::Error, $this->m_mailLogCategory, __FILE__, __LINE__);
            }
        }
        return $userData;
    }

    /**
     * Check atleast one to address is set. If not and we have a logged in user
     * then we can set to email as the logged in user.
     * 
     * @return boolean true if was set, false if not.
     */
    private function verifyAndSetTo() {
        $wasSet = false;
        if (empty($this->m_toList) && isValidId($this->m_userId)) {
            $userData = $this->queryUserNameAndEmail();
            if ($userData !== null) {
                $this->setToEmail($userData['email_address']);
                $wasSet = true;
            } else {
                $this->log("verifyAndSetTo failed to query user inf0", LogMessageLevel::Error, $this->m_mailLogCategory, __FILE__, __LINE__);
            }
        }
        return $wasSet;
    }

    /**
     * Check the from address is set. If not and we have a logged in user
     * then we can set from email and from name as the logged in user.
     * 
     * @return boolean true if was set, false if not.
     */
    private function verifyAndSetFrom() {
        $wasSet = false;
        if ((empty($this->m_fromEmail) || (! checkEmailAddress($this->m_fromEmail))) && isValidId($this->m_userId)) {
            $userData = $this->queryUserNameAndEmail();
            if ($userData !== null) {
                $this->setFromEmail($userData['email_address']);
                $this->setFromName($userData['user_name']);
                $wasSet = true;
            } else {
                $this->log("verifyAndSetFrom failed to query user info", LogMessageLevel::Error, $this->m_mailLogCategory, __FILE__, __LINE__);
            }
        }
        return $wasSet;
    }

    /**
     * Return the message body, if HTML is set return that, otherwise return the text version.
     * usually it is better to set both HTML and text body and use the corresponding
     * getter function (getHTMLMessage() or getTextMessage().)
     * 
     * @return string return the message body, either the HTML body or the text body.
     */
    public function getMessageBody () {
        if ( ! empty($this->m_messageBodyHtml)) {
            return $this->m_messageBodyHtml;
        } else {
            return $this->m_messageBodyText;
        }
    }

    /**
     * Helper function to set the message either text or HTML with a single call.
     * 
     * @param boolean true if HTML, false if plan text.
     * @param string the message body.
     * @return string The message body that was set.
     */
    public function setMessageBody ($isHTML, $messageBody) {
        if ($isHTML) {
            return $this->setHTMLMessage($messageBody);
        } else {
            return $this->setTextMessage($messageBody);
        }
    }

    /**
     * Set the message body as text.
     * 
     * @param string The plain text message body.
     */
    public function setTextMessage ($emailMessageText) {
        $this->m_messageBodyText = $emailMessageText;
        return $this->m_messageBodyText;
    }

    /**
     * Get the message plain text body.
     * 
     * @return string The plain text message body.
     */
    public function getTextMessage () {
        return $this->m_messageBodyText;
    }

    /**
     * Set the message HTML body.
     * 
     * @param string The HTML message body.
     */
    public function setHTMLMessage ($emailMessageHTML) {
        $this->m_messageBodyHtml = $emailMessageHTML;
        return $this->m_messageBodyHtml;
    }

    /**
     * Get the HTML message body.
     * 
     * @return string The HTML message body.
     */
    public function getHTMLMessage () {
        return $this->m_messageBodyHtml;
    }

    /**
     * Set the email subject. The text is scrubbed for invalid subject characters.
     * 
     * @param string the message subject
     */
    public function setSubject ($emailSubject) {
        $this->m_subject = htmlspecialchars(trim(str_replace("\n", '', strip_tags($emailSubject))));
        return $this->m_subject;
    }

    /**
     * Return the email subject.
     * 
     * @return string The message subject
     */
    public function getSubject () {
        return $this->m_subject;
    }

    /**
     * Set the file containing the email message body. This file is opened read
     * only when send() is called.
     * 
     * @param string file name containing the email message body
     * @param boolean optional true if the message is a text body, false if it is HTML body.
     */
    public function setEmailFile ($fileName, $isText = false) {
        $this->setEmailTileIsText($isText);
        $this->m_emailFile = $fileName;
        return $this->m_emailFile;
    }

    /**
     * Get the email file name.
     * 
     * @return string file name previously set
     */
    public function getEmailFile () {
        return $this->m_emailFile;
    }

    /**
     * Set a flag to indicate the email body is text and not HTML.
     * 
     * @param bool true if the email body is considered to be text, false if the email body is HTML.
     */
    public function setEmailFileIsText ($isText) {
        $this->m_emailFileIsText = $isText;
        return $this->m_emailFileIsText;
    }

    /**
     * @return boolean true if the email body is considered to be text, false if the email body is HTML.
     */
    public function getEmailFileIsText () {
        return $this->m_emailFileIsText;
    }

    /**
     * Reset the object to initial state and release any references.
     * 
     * @return bool false if object not reset, true if OK
     */
    public function clear() {
        $this->m_debug = false;
        $this->m_mailLogCategory = 'MAIL';
        $this->m_siteId = 0;
        $this->m_userId = 0;
        $this->m_fromEmail = '';
        $this->m_toList = [];
        $this->m_fromName = '';
        $this->m_subject = '';
        $this->m_messageBodyText = '';
        $this->m_messageBodyHtml = '';
        $this->m_emailFile = '';
        $this->m_emailFileIsText = false;
        $this->m_languageCode = 'en';
        $this->setStatus(EnginesisErrors::NO_ERROR, '');
        $this->setEmailNotificationTypeId(99);
        return true;
    }

    /**
     * A debug function to look at the current object state.
     * 
     * @return string representation of the current state of the object for debugging/info purposes.
     */
    public function toString () {
        if (count($this->m_toList) > 0) {
            $toStr = $this->getToEmailAsString(';');
        } else {
            $toStr = '[empty]';
        }
        $resultStr = "From: $this->m_fromEmail<br/>To: $toStr<br/>Subject: $this->m_subject<br/>Status: " . $this->m_status . "<br/>Message: $this->m_messageBodyText<br/>Debug: " . ($this->m_debug ? 'ON' : 'OFF') . "<br/>";
        $resultStr .= "\n";
        return $resultStr;
    }

    /**
     * Send the requested email given the email notification type identifier. This function will fail quietly so if you
     * want the error you should monitor the return code. It will log an error to the system log. If the to/from email
     * addresses are fine then a typical failure is related to system SMTP connections and access privileges.
     * 
     * @param integer $game_id The game id, if required by the email notification type.
     * @param array $parameters Array of parameters for token replacement in the email content.
     * @return string EnginesisErrors error code if email queued to be sent, an error message if something did not go as planned.
     */
    public function sendEmailNotification($game_id, $parameters) {
        global $site_data;

        $errorCode = EnginesisErrors::NO_ERROR;
        $errorMessage = '';
        $this->setStatus($errorCode, $errorMessage);
        $databaseConnection = Database::getDatabaseConnection(DATABASE_ENGINESIS);
        if ($databaseConnection->isValid()) {
            $site_id = $this->m_siteId;
            $user_id = $this->m_userId;
            $email_notification_type_id = $this->m_emailNotificationTypeId;
            $language_code = $this->m_languageCode;
            $sql = 'call EmailNotificationGet(?, ?, ?, ?, ?, @success, @status_msg)';
            $sqlParameters = [$site_id, $user_id, $game_id, $email_notification_type_id, $language_code];
            $sqlResults = $databaseConnection->query($sql, $sqlParameters);
            if ($sqlResults && $databaseConnection->rowCount($sqlResults) > 0) {
                $notificationInfo = $databaseConnection->fetch($sqlResults);
                if ($notificationInfo !== null) {
                    $from = $site_data[$site_id]['site_support_email'];
                    $fromName = $site_data[$site_id]['site_name'];
                    $emailSubject = tokenReplace($notificationInfo['subject_text'], $parameters);
                    $this->setSubject($emailSubject);
                    $this->setHTMLMessage(tokenReplace($notificationInfo['html_msg'], $parameters));
                    $this->setTextMessage(tokenReplace($notificationInfo['text_msg'], $parameters));
                    $databaseConnection->clearResults($sqlResults);
                    $errorCode = $this->send();
                    if ($errorCode != EnginesisErrors::NO_ERROR) {
                        $emailAddress = $this->getToEmailAsString();
                        $errorMessage = "sendEmailNotification failed with code: $errorCode (" . $this->getExtendedStatusInfo() . "); siteId: $site_id; email id: $email_notification_type_id; params: " . implode(',', $parameters);
                        $databaseConnection->errorReport($site_id, $user_id, $errorCode, $errorMessage, $game_id, $language_code);
                        if ($this->m_debug && ($this->m_serverStage == '-l' || $this->m_serverStage == '-d')) {
                            $errorMessage = "<h3>Cannot send email on this server</h3><p>To: $emailAddress</p><p>From: $from</p><p>Subject: $emailSubject</p><p>$errorMessage</p>";
                        }
                    }
                } else {
                    $databaseConnection->clearResults($sqlResults);
                    $errorCode = EnginesisErrors::SYSTEM_ERROR;
                    $errorMessage = "sendEmailNotification: Database error getting notification for siteId: $site_id; email id: $email_notification_type_id.";
                }
            } else {
                $errorCode = EnginesisErrors::SYSTEM_ERROR;
                $errorMessage = "sendEmailNotification: Email notification not defined for siteId: $site_id; email id: $email_notification_type_id.";
            }
            $sqlResults = null;
        } else {
            $errorCode = EnginesisErrors::SYSTEM_ERROR;
            $errorMessage = 'sendEmailNotification: Not able to open a connection to the database.';
        }
        if ($errorMessage != '') {
            $this->log($errorMessage, LogMessageLevel::Error, $this->m_mailLogCategory, __FILE__, __LINE__);
        }
        // If for some reason an error occurred but was not set, set it here so that
        // the caller can discover it.
        if ($this->m_extendedErrorInfo == '') {
            $this->setStatus($errorCode, $errorMessage);
        }
        return $errorCode;
    }

    /**
     * Send an email to the site admin. Usually this is an internal system notification event.
     * 
     * @param string $subject The message subject.
     * @param string $message The email body.
     * @param array $parameters Key/Value array of parameters for token replacement in the email content.
     * @return string Error code. Empty string if email queued to be sent, an error code if something did not go as planned.
     */
    public function sendSystemNotification($subject, $message, $parameters) {
        global $site_data;

        // return empty if email sent, or an error message if failed.
        $errorCode = EnginesisErrors::NO_ERROR;
        $site_id = $this->m_siteId;
        if (isValidSiteId($site_id) && isset($site_data[$site_id]['site_support_email'])) {
            $to = $site_data[$site_id]['site_support_email'];
        } else {
            $to = 'support@enginesis.com';
        }
        if (checkEmailAddress($to)) {
            $from = 'info@enginesis.com';
            $fromName = 'Enginesis';
            if (isEmpty($subject)) {
                $subject = 'System alert from Enginesis.com';
            }
            $emailContent = tokenReplace($message, $parameters);
            if ($this->m_debug) {
                echo("<h3>sendSystemNotification</h3>");
                echo("<p>From: $fromName&lt;$from&gt;</p>");
                echo("<p>To: $to</p>");
                echo("<p>Subject: $subject</p>");
                echo("<hr>");
                echo("<p>$emailContent</p>");
                $errorCode = EnginesisErrors::SYSTEM_ERROR;
                return $errorCode;
            }
            $this->setToEmail($to);
            $this->setFromEmail($from);
            $this->setFromName($fromName);
            $this->setSubject($subject);
            $this->setHTMLMessage($emailContent);
            $this->setEmailNotificationTypeId(99);
            $errorCode = $this->send();
            if ($errorCode != EnginesisErrors::NO_ERROR) {
                $errorInfo = "sendSystemNotification failed with code: $errorCode (" . $this->getExtendedStatusInfo() . "); siteId: $site_id; params: " . implode(',', $parameters);
                if ($this->m_serverStage == '-l' || $this->m_serverStage == '-d') {
                    $errorInfo = "<h3>Cannot send email on this server</h3><p>To: $to</p><p>From: $from</p><p>Subject: $subject</p>";
                }
            }
        } else {
            // No recipients.
            $errorCode = EnginesisErrors::INVALID_PARAMETER;
        }
        return $errorCode;
    }

    /**
     * Send a message to the logging function, if one was set. This is for internal logging.
     * All parameters are required.
     * @param string $message A message to send to the logging function.
     * @param int $loggingLevel Level setting from LogMessageLevel.
     * @param string $eventCategory A category identifier to group log messages.
     * @param string $file The file that generated log message.
     * @param int $line The line number in $file that generated the log message.
     * @return boolean True if logged, false if not logged.
     */
    private function log($message, $loggingLevel, $eventCategory, $file, $line) {
        if ($this->m_enginesisLogger != null) {
            $this->m_enginesisLogger->log($message, $loggingLevel, $eventCategory, $file, $line);
            return true;
        }
        return false;
    }

    public function debugDump() {
        $debugMessage = "<div><h3>Enginesis Mailer:</h3><ul>" .
            "<li>Status: " . $this->m_status . ": " . $this->m_extendedErrorInfo . "</li>" .
            "<li>Debug: " . castBoolToString($this->m_debug) . "</li>" .
            "<li>Site: " . $this->m_siteId . "</li>" .
            "<li>User: " . $this->m_userId . "</li>" .
            "<li>Stage: " . $this->m_serverStage . "</li>" .
            "<li>Config: " . $this->m_mailConfig['host'] . "</li>" .
            "<li>Type: " . $this->m_emailNotificationTypeId . "</li>" .
            "<li>From: " . $this->m_fromEmail . " / " . $this->m_fromName . "</li>" .
            "<li>To: " . implode(', ', $this->m_toList) . "</li>" .
            "<li>Subject: " . $this->m_subject . "</li>" .
            "<li>File: " . $this->m_emailFile . " (is text? " . castBoolToString($this->m_emailFileIsText) . ")</li>" .
            "<li>TID: " . $this->m_emailId . "</li>" .
            "</ul><p>" . $this->m_messageBodyText . "</p><p>" . $this->m_messageBodyHtml . "</p>" .
            "</div>";
        return $debugMessage;
    }
}
