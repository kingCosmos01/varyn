<?php
    /**
     * Define sensitive data in this configuration file.
     * User: jf
     * Date: Feb-13-2016
     */

    $_DB_CONNECTIONS = array(
        '-l' => array(
            'host' => '127.0.0.1',
            'port' => '3306',
            'user' => 'varynwp',
            'password' => 'm3@tEr45',
            'db' => 'wordpressvaryn'
        ),
        '-d' => array(
            'host' => 'localhost',
            'port' => '3306',
            'user' => 'varynwp',
            'password' => 'm3@tEr45',
            'db' => 'wordpressvaryn'
        ),
        '-q' => array(
            'host' => 'localhost',
            'port' => '3306',
            'user' => 'varynwp',
            'password' => 'm3@tEr45',
            'db' => 'wordpressvaryn'
        ),
        '-x' => array(
            'host' => 'localhost',
            'port' => '3306',
            'user' => 'varynwp',
            'password' => 'm3@tEr45',
            'db' => 'wordpressvaryn'
        ),
        ''   => array(
            'host' => 'localhost',
            'port' => '3306',
            'user' => 'varynwp',
            'password' => 'm3@tEr45',
            'db' => 'wordpressvaryn'
        )
    );

    // Mail/sendmail/Postfix/Mailgun config
    $_MAIL_HOSTS = array(
        '-l' => array('host' => 'smtp.verizon.net', 'port' => 465, 'ssl' => true, 'tls' => false, 'user' => 'jlf990@verizon.net', 'password' => 'proPhet5++'),
        '-d' => array('host' => 'smtp.mailgun.org', 'port' => 587, 'ssl' => false, 'tls' => true, 'user' => 'postmaster@mailer.enginesis-q.com', 'password' => '1h4disai51w5'),
        '-q' => array('host' => 'smtp.mailgun.org', 'port' => 587, 'ssl' => false, 'tls' => true, 'user' => 'postmaster@mailer.enginesis-q.com', 'password' => '1h4disai51w5'),
        '-x' => array('host' => 'smtpout.secureserver.net', 'port' => 25, 'ssl' => false, 'tls' => false, 'user' => '', 'password' => ''),
        ''   => array('host' => 'smtp.mailgun.org', 'port' => 587, 'ssl' => false, 'tls' => true, 'user' => 'postmaster@mailer.enginesis.com', 'password' => '6w88jmvawr63')
    );
