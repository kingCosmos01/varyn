<?php
// @class EnginesisMailer.php
// @purpose:
// Class to handle php mail functionality, basically simplifies the interface to phpMailer and MailGun,
// while removing dependency on PHP's mail() function and still maintaining legacy mail interface.
// Setup the message using the set functions then call send().
// Also provides a stub test function sendText() to validate the object for unit testing.
// You can set the message text either plain text, HTML, or an external file containing the email body.
// @dependencies:
// You must define $_MAIL_HOSTS.
//
require_once('common.php');
require $_SERVER['DOCUMENT_ROOT'] . '/../services/phpmailer/PHPMailerAutoload.php';


class EnginesisMailer
{
    // All property access is private, use accessor functions.
    private $m_fromEmail;
    private $m_fromName;
    private $m_toList;
    private $m_subject;
    private $m_messageBodyText;
    private $m_messageBodyHtml;
    private $m_emailFile;
    private $m_emailFileIsText;
    private $m_db;
    private $m_dbPrivateConnection;
    private $m_status;
    private $m_debug;
    private $m_extendedErrorInfo;
    private $m_serverStage;

    /**
     * @method contructor
     * @purpose: construct the mailer object and set its initial state
     * @param string $fromEmailAddress options from email address
     * @param null $recipientList optional recipient (string) or recipient list (array)
     * @param string $emailSubject optional message subject
     * @param string $textBody optional text message body
     * @param string $htmlBody optional HTML message body
     */
    public function __construct ( $fromEmailAddress = '',
                                  $recipientList = null,
                                  $emailSubject = '',
                                  $textBody = '',
                                  $htmlBody = '') {
        $this->clear();
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
        $this->m_status = '';
        $this->m_extendedErrorInfo = '';
        $this->m_debug = false;
        $this->m_serverStage = '';
    }

    /**
     * @method destructor
     * @purpose: free any references before destructing the object
     */
    public function __destruct () {
        $this->clear();
    }

    /**
     * @method: setServerStage
     * @purpose: We need someone to tell us which server stage we are running on.
     */
    public function setServerStage ($stage) {
        $this->m_serverStage = $stage;
    }

    /**
     * @method: sendAuthenticated
     * @purpose: send email if the user is authenticated, using the user's name, email as from.
     * TODO: Note yet implemented.
     * @return string send status, '' if OK, otherwise error id.
     */
    public function sendAuthenticated () {
        $this->m_status = 'SEND_ERROR';
        return $this->m_status;
    }

    /**
     * @method: send
     * @purpose: validate the state of the object and if everything is OK send the message
     * @return string send status, '' if OK, otherwise error id.
     */
    public function send () {
        global $_MAIL_HOSTS;

        // if HTML body is set we use that, otherwise text body must be set!
        $emailMessage = '';
        if (strlen($this->m_emailFile) > 0) {
            if (file_exists($this->m_emailFile)) {
                $fileHandle = fopen($this->m_emailFile, 'r');
            } else {
                $fileHandle = null;
                $this->m_status = 'INVALID_FILE';
            }
            if ($fileHandle) {
                while ( ! feof($fileHandle)) {
                    $emailMessage = $emailMessage . fgets($fileHandle, 4096);
                }
                fclose ($fileHandle);
            }
            $useHTML = ! $this->m_emailFileIsText; // TODO: assume the file was HTML maybe we should parse it to find a <body> tag or something
        } elseif (strlen($this->m_messageBodyHtml) > 0) {
            $emailMessage = $this->m_messageBodyHtml;
            $useHTML = true;
        } else {
            $emailMessage = $this->m_messageBodyText;
            $useHTML = false;
        }
        $mailConfig = $_MAIL_HOSTS[$this->m_serverStage];
        if ($this->m_status == '') {
            if (count($this->m_toList) > 0 && strlen($this->m_fromEmail) > 0 && (strlen($this->m_subject) > 0 || strlen($emailMessage) > 0)) {
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
                        $this->m_status = 'SEND_ERROR';
                        $this->m_extendedErrorInfo = $mailer->ErrorInfo;
                    } else {
                        $this->m_status = '';
                    }
                } catch (Exception $e) {
                    $this->m_status = 'SEND_ERROR';
                    $this->m_extendedErrorInfo = $e->getMessage();
                }
            } else {
                $this->m_status = 'INVALID_MESSAGE';
                $this->m_extendedErrorInfo = 'Must provide to (' . count($this->m_toList) . '), from (' . strlen($this->m_fromEmail) . '), subject (' . strlen($this->m_subject) . '), message body (' . strlen($emailMessage) . ')';
            }
        }
        return $this->m_status;
    }

    /**
     * @method: setToEmail
     * @purpose: Takes a single parameter that may be either a string or an array. If it is an array, each item
     * must be a string representing a single email address. If it is a string, it may contain one or more
     * addresses if they are separated by a semicolon.
     * @param $to string for single to address, array for list of addresses
     * @return string
     */
    public function setToEmail ($to) {
        $this->m_status = '';
        if (is_array($to)) { // check each item
            for ($i = 0; $i < count($to); $i++ ) {
                $nextEmail = trim($to[$i]);
                if ( ! checkEmailAddress($nextEmail)) {
                    $this->m_status = 'INVALID_TO_ADDRESS';
                } else {
                    $this->m_toList[] = $nextEmail;
                }
            }
        } elseif (is_string($to)) {
            $to = trim($to);
            if (strlen($to) > 0) {
                if (strpos($to, ';') !== false) { // multiple addresses in one string
                    $listOfEmails = explode(';', $to);
                    $this->m_status = $this->setToEmail($listOfEmails);
                } else { // just one address
                    if (checkEmailAddress($to)) {
                        $this->m_toList[] = $to;
                    } else {
                        $this->m_status = 'INVALID_TO_ADDRESS';
                    }
                }
            }
        }
        return $this->m_status;
    }

    /**
     * @method: getDebug
     * @return bool debug flag
     */
    public function getDebug () {
        return $this->m_debug;
    }

    /**
     * @method: setDebug
     * @param $debugFlag bool set the debug flag
     */
    public function setDebug ($debugFlag) {
        $this->m_debug = $debugFlag;
    }

    /**
     * @method: getExtendedStatusInfo
     * @return string return full status message
     */
    public function getExtendedStatusInfo () {
        return $this->m_extendedErrorInfo;
    }

    /**
     * @method: getToEmail
     * @return mixed returns string if single to address, or array of to addresses
     */
    public function getToEmail () {
        return($this->m_toList);
    }

    /**
     * @method: getToEmailAsString
     * @param string $separator option use this to separate multiple addresses
     * @return string list of all to addresses in a single string
     */
    public function getToEmailAsString ($separator = ', ') {
        return(implode($separator, $this->m_toList));
    }

    /**
     * @method: clearToEmail
     * @purpose: remove all to addresses. Useful if you want to send the same email in some type of loop but send to different addresses separately.
     */
    public function clearToEmail () {
        $this->m_toList = array();
    }

    /**
     * @method: isEmailInToList
     * @purpose: determine is a specified email address is in the to list
     * @param $email
     * @return bool true if the address is included
     */
    public function isEmailInToList ($email) {
        $rc = false;
        if (strlen($email) > 0 ) {
            for ($i = 0; $i < count($this->m_toList); $i++ ) {
                if (strtolower($this->m_toList[$i]) == strtolower($email)) {
                    $rc = true;
                    break;
                }
            }
        }
        return $rc;
    }

    /**
     * @method: setFromEmail
     * @purpose: set the From email addres
     * @param $from
     * @return string the object error status '' if OK otherwise a status code if the from address was determined to be invalid,.
     */
    public function setFromEmail ($from) {
        if (checkEmailAddress($from)) {
            $this->m_fromEmail = $from;
            $this->m_status = '';
        } else {
            $this->m_status = 'INVALID_FROM_ADDRESS';
        }
        return $this->m_status;
    }

    /**
     * @method: getFromEmail
     * @return string the current from email address
     */
    public function getFromEmail () {
        return $this->m_fromEmail;
    }

    /**
     * @method: setFromName
     * @param string set the name the email is from
     */
    public function setFromName ($fromName) {
        $this->m_fromName = $fromName;
    }

    /**
     * @method: getFromName
     * @return string return the name the email is from, or the email address if no name was set
     */
    public function getFromName () {
        if (strlen($this->m_fromName) > 0 ) {
            return $this->m_fromName;
        } else {
            return $this->m_fromEmail;
        }
    }

    /**
     * @method: getMessage
     * @purpose: Return the message body, if HTML is set return that, otherwise return the text version.
     * @return string return the message body, either the HTML body or the text body
     */
    public function getMessage () {
        return $this->m_messageBodyHtml == '' ? $this->m_messageBodyText : $this->m_messageBodyHtml;
    }

    /**
     * @method: setMessage
     * @purpose: Helper function to set the message either text or HTML with a single call.
     * @param bool true if text, false if HTML
     * @param string the message body
     */
    public function setMessage ($isText, $messageBody) {
        if ($isText) {
            $this->setTextMessage($messageBody);
        } else {
            $this->setHTMLMessage($messageBody);
        }
    }

    /**
     * @method: setTextMessage
     * @param string The plain text message body
     */
    public function setTextMessage ($emailMessageText) {
    //
    // Message content is a bit tricky. We want to allow HTML but not just any crap someone can toss in there.
    // Not sure right now how to clean this up, or if it needs to be.
    //
        $this->m_messageBodyText = $emailMessageText;
    }

    /**
     * @method: getTextMessage
     * @returns string The plain text message body
     */
    public function getMessageText () {
        return $this->m_messageBodyText;
    }

    /**
     * @method: setHTMLMessage
     * @param string The HTML message body
     */
    public function setHTMLMessage ($emailMessageHTML) {
        //
        // Message content is a bit tricky. We want to allow HTML but not just any crap someone can toss in there.
        // Not sure right now how to clean this up, or if it needs to be.
        //
        $this->m_messageBodyHtml = $emailMessageHTML;
    }

    /**
     * @method: getHTMLMessage
     * @returns string The HTML message body
     */
    public function getHTMLMessage () {
        return $this->m_messageBodyHtml;
    }

    /**
     * @method: setSubject
     * @param string the message subject
     */
    public function setSubject ($emailSubject) {
        $this->m_subject = htmlspecialchars(trim(str_replace("\n", '', strip_tags($emailSubject))));
    }

    /**
     * @method: getSubject
     * @returns string the message subject
     */
    public function getSubject () {
        return $this->m_subject;
    }

    /**
     * @method: setEmailFile
     * @purpose: set the file containing the email message body. This file is opened read only when send() is called.
     * @param string file name containing the email message body
     * @param bool optional true if the message is a text body, false if it is HTML body.
     */
    public function setEmailFile ($fileName, $isText = false) {
        $this->setEmailTileIsText($isText);
        $this->m_emailFile = $fileName;
    }

    /**
     * @method: getEmailFile
     * @returns string file name previously set
     */
    public function getEmailFile () {
        return $this->m_emailFile;
    }

    /**
     * @method: setEmailFileIsText
     * @param bool true if the email body is considered to be text, false if the email body is HTML.
     */
    public function setEmailFileIsText ($isText) {
        $this->m_emailFileIsText = $isText;
    }

    /**
     * @method: getEmailFileIsText
     * @returns bool true if the email body is considered to be text, false if the email body is HTML.
     */
    public function getEmailFileIsText () {
        return $this->m_emailFileIsText;
    }

    /**
     * @method: clear
     * @purpose: reset the object to initial state and release any references.
     * @return bool false if object not reset, true if OK
     */
    public function clear() {
        $this->m_fromEmail = '';
        $this->m_toList = array();
        $this->m_fromName = '';
        $this->m_subject = '';
        $this->m_messageBodyText = '';
        $this->m_messageBodyHtml = '';
        $this->m_emailFile = '';
        $this->m_emailFileIsText = false;
        $this->m_db = null;
        $this->m_dbPrivateConnection = true;
        $this->m_status = '';
        return true;
    }

    /**
     * @method: toString
     * @return string representation of the current state of the object for debugging/info purposes.
     */
    public function toString () {
    //
    // A debug function to look at all the properties
    //
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
     * @method: sendTest
     * @purpose: send a canned test message. Designed to be used from the Unit test framework so do not call this from any application.
     * @return string send status code.
     */
    public function sendTest ($to) {
        global $admin_notification_list;
        $from = 'info@enginesis.com';
        if (strlen($to) < 1) {
            $to = $admin_notification_list;
        }
        $subject = 'This is a test email from Enginesis';
        $body = 'This is the test email message that was send from the Unit Test EnginesisMailer.sendTest. We are using this to verify sending mail from this server is working.';
        $this->setFromEmail($from);
        $this->setToEmail($to);
        $this->setSubject($subject);
        $this->setTextMessage($body);
        return $this->send();
    }
}