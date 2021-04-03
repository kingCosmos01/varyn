<?php
/**
 * Created by PhpStorm.
 * User: jf
 * Date: 4/10/2015
 * Time: 9:25 AM
 */
require_once('common.php');
require_once('EnginesisMailer.php');

$mailer = new EnginesisMailer('info@jumpydot.com', 'john@varyn.com', 'Test Email from server', 'This is a test 1 2 3.', NULL);
$mailer->sendTest('john@varyn.com');
$message = $mailer->toString();
$errorInfo = $mailer->getExtendedStatusInfo();

echo("<h4>Enginesis Mailer Test</h4><p>Info: $message</p><p>Error Info: $errorInfo</p>");
