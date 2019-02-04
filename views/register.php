<?php
require_once('../services/common.php');
?>
<!DOCTYPE html>
<html lang="en-us">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Varyn Games | Register</title>
    <link rel="icon" type="image/png" href="/images/logosmall.png" />
    <link rel="icon" type="image/x-icon" href="favicon.ico" />
    <link rel="image_src" href="/images/VarynCardLogo.png" />
    <META NAME="Description" CONTENT="Varyn"/>
    <META NAME="Keywords" CONTENT="Varyn"/>
    <META NAME="Author" content="Varyn"/>
    <META NAME="Copyright" content="Copyright � 2013 Varyn. All rights reserved."/>
    <meta name="google-site-verification" content="" />
    <meta property="og:title" content="Varyn" />
    <meta property="og:description" content="Varyn" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="http://www.varyn.com" />
    <meta property="og:image" content="http://www.varyn.com/images/share_img_0.jpg" />
    <meta property="og:image" content="http://www.varyn.com/images/share_img_1.jpg" />
    <meta property="og:image" content="http://www.varyn.com/images/share_img_2.jpg" />
    <meta property="og:site_name" content="Varyn" />
    <meta property="og:type" content="website" />
    <meta property="fb:admins" content="726468316" />
    <meta property="fb:app_id" content="" />
    <link rel="stylesheet" href="/common/jquery.mobile-1.2.1.min.css" />
    <link rel="stylesheet" href="/common/nivo-slider.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="/common/themes/dark/default.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="/common/main.css" type="text/css" media="screen" />
    <script type="text/javascript" src="/common/head.min.js"></script>
</head>
<body>
<div id="page_container" data-role="dialog">
    <div data-role="header">
        <h1>Register</h1>
    </div>
    <div data-role="content" data-theme="c">
        <p>User Name: <input type="text" name="username" /></p>
        <p>Password: <input type="password" name="password" /></p>
        <p>Your Email: <input type="email" name="email" /></p>
        <p>Your Name: <input type="text" name="fullname" /></p>
        <p>Your Location: <input type="text" name="location" /></p>
        <p>Your tag line: <input type="text" name="tagline" /></p>
        <a href="/profile/" data-role="button">Submit</a><a href="#" data-rel="back" data-role="button">Cancel</a>
    </div>
</div><!-- page_container -->
</body>
</html>
