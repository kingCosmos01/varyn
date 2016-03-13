<?php
    require_once('../services/common.php');
    $page = 'home';
    $search = getPostOrRequestVar('q', null);
    if ($search != null) {
        header('location:/allgames.php?q=' . $search);
        exit;
    }
    $showSubscribe = getPostOrRequestVar('s', '0');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Varyn | Terms of Use</title>
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
    <meta name="description" content="Varyn Terms of Use. Varyn makes games using technology that performs on the most popular platforms. Cross platform friendly technologies have created an opportunity to re-invent online games for an audience that moves seamlessly between desktop, tablet, and smart-phone.">
    <meta name="author" content="Varyn">
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
    <meta property="og:title" content="Varyn Terms of Use">
    <meta property="og:url" content="http://www.varyn.com">
    <meta property="og:site_name" content="Varyn">
    <meta property="og:description" content="Varyn Terms of Use. Varyn makes games using technology that performs on the most popular platforms. Cross platform friendly technologies have created an opportunity to re-invent online games for an audience that moves seamlessly between desktop, tablet, and smart-phone.">
    <meta property="og:image" content="http://www.varyn.com/images/1200x900.png"/>
    <meta property="og:image" content="http://www.varyn.com/images/1024.png"/>
    <meta property="og:image" content="http://www.varyn.com/images/1200x600.png"/>
    <meta property="og:image" content="http://www.varyn.com/images/600x600.png"/>
    <meta property="og:image" content="http://www.varyn.com/images/2048x1536.png"/>
    <meta property="og:type" content="game"/>
    <meta name="twitter:card" content="photo"/>
    <meta name="twitter:site" content="@varyndev"/>
    <meta name="twitter:creator" content="@varyndev"/>
    <meta name="twitter:title" content="Varyn Terms of Use"/>
    <meta name="twitter:image:src" content="http://www.varyn.com/images/600x600.png"/>
    <meta name="twitter:domain" content="varyn.com"/>
    <script src="/common/head.min.js"></script>
    <script type="text/javascript">

        function initApp() {
            var showSubscribe = '<?php echo($showSubscribe);?>';

            if (showSubscribe == '1') {
                showSubscribePopup();
            }
        }

        head.ready(function() {
            initApp();
        });
        head.js("/common/modernizr.custom.74056.js", "/common/jquery.min.js", "/common/bootstrap.min.js", "/common/ie10-viewport-bug-workaround.js", "/common/common.js", "/common/enginesis.js", "/common/ShareHelper.js");

        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
        ga('create', 'UA-41765479-1', 'auto');
        ga('send', 'pageview');
    </script>
</head>
<body>
<?php
    include_once('common/header.php');
?>
<div class="container marketing">
    <div class="panel panel-primary panel-padded">
        <h1>Terms of Use</h1>
        <h5>Acceptance of Terms</h5>
        <p>Welcome to the web site (the "Site") of Varyn, Inc. ("Varyn"). On this web site, Varyn makes available to you a wide range of information, software, products, downloads, documents, communications, files, text, graphics, publications, content, and services. The Terms of Use are subject to change. This document was last updated on 15-Nov-2014.
            PLEASE READ THE TERMS OF USE CAREFULLY BEFORE USING THIS WEBSITE. By accessing and using this web site in any way, including, without limitation, browsing the web site, using any information, using any content, using any services, downloading any materials, and/or placing an order for products or services, you agree to and are bound by the terms of use described in this document ("Terms of Use"). IF YOU DO NOT AGREE TO ALL OF THE TERMS AND CONDITIONS CONTAINED IN THE TERMS OF USE, DO NOT USE THIS WEBSITE IN ANY MANNER. The Terms of Use are entered into by and between Varyn and you. If you are using the web site on behalf of your employer, you represent that you are authorized to accept these Terms of Use on your employer's behalf. Varyn reserves the right, at Varyn's sole discretion, to change, modify, update, add, or remove portions of the Terms of Use at any time without notice to you. Please check these Terms of Use for changes. Your continued use of this web site following the posting of changes to the Terms of Use will mean you accept those changes.</p>
        <h5>Use of Materials Limitations</h5>
        <p>All materials contained in the web site are the copyrighted property of Varyn, its subsidiaries, affiliated companies and/or third-party licensors. All trademarks, service marks, and trade names are proprietary to Varyn, or its subsidiaries or affiliated companies and/or third-part licensors.
            Unless otherwise specified, the materials and services on this web site are for your personal and non-commercial use, and you may not modify, copy, distribute, transmit, display, perform, reproduce, publish, license, create derivative works from, transfer, or sell any information, software, products or services obtained from the web site without the written permission from Varyn.</p>
        <h5>Privacy Policy</h5>
        <p>Varyn's Privacy Policy can be found at <a href="/Privacy.php">www.varyn.com/Privacy.php</a>.</p>
        <h5>No Unlawful or Prohibited Use</h5>
        <p>As a condition of your use of the web site, you will not use the web site for any purpose that is unlawful or prohibited by these terms, conditions, and notices. You may not use the Services in any manner that could damage, disable, overburden, or impair any Varyn server, or the network(s) connected to any Varyn server, or interfere with any other party's use and enjoyment of the web site You may not attempt to gain unauthorized access to services, materials, other accounts, computer systems or networks connected to any Varyn server or to the web site, through hacking, password mining or any other means. You may not obtain or attempt to obtain any materials or information through any means not intentionally made available through the web site.</p>
        <h5>Copyright and Trademark Information</h5>
        <p>COPYRIGHT NOTICE: Copyright &copy; 2015 Varyn, Inc., All Rights Reserved.</p>
        <p>Third-party trademarks are used solely for distributing the games herein and no license or affiliation is implied. All copyrights are held by the respective owners.</p>
    </div>
</div><!-- /.marketing -->
<?php
    include_once('common/footer.php');
?>
</body>
</html>