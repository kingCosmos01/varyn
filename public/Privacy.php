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
    <title>Varyn | Privacy Policy</title>
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
    <meta name="description" content="Varyn privacy policy. Varyn makes games using technology that performs on the most popular platforms. Cross platform friendly technologies have created an opportunity to re-invent online games for an audience that moves seamlessly between desktop, tablet, and smart-phone.">
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
    <meta property="og:title" content="Varyn Privacy Policy">
    <meta property="og:url" content="http://www.varyn.com">
    <meta property="og:site_name" content="Varyn">
    <meta property="og:description" content="Varyn privacy policy. Varyn makes games using technology that performs on the most popular platforms. Cross platform friendly technologies have created an opportunity to re-invent online games for an audience that moves seamlessly between desktop, tablet, and smart-phone.">
    <meta property="og:image" content="http://www.varyn.com/images/1200x900.png"/>
    <meta property="og:image" content="http://www.varyn.com/images/1024.png"/>
    <meta property="og:image" content="http://www.varyn.com/images/1200x600.png"/>
    <meta property="og:image" content="http://www.varyn.com/images/600x600.png"/>
    <meta property="og:image" content="http://www.varyn.com/images/2048x1536.png"/>
    <meta property="og:type" content="game"/>
    <meta name="twitter:card" content="photo"/>
    <meta name="twitter:site" content="@varyndev"/>
    <meta name="twitter:creator" content="@varyndev"/>
    <meta name="twitter:title" content="Varyn Privacy Policy"/>
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
        <h1>Privacy Policy</h1>
            <p>This document sets forth the Varyn Online Privacy Policy (the Privacy Policy) for this web site, www.varyn.com (the Site).
                If you have objections to the Privacy Policy, you should not access or use this Site.
                The Privacy Policy is subject to change and was last updated on 26-Apr-2013.</p>
            <h5>Collection of Personal Information</h5>
            <p>As a visitor to this Site, you can engage in many activities without providing any personal information. In connection with other activities, Varyn may ask you to provide certain information about yourself by filling out and submitting an online form. It is completely optional for you to engage in these activities. If you elect to engage in these activities, however, we may ask that you provide us personal information, such as your first and last name, e-mail address, and other personal information. When ordering products or services on the Site, you may be asked to provide a credit card number. Depending upon the activity, some of the information that we ask you to provide is identified as mandatory and some as voluntary. If you do not provide the mandatory data with respect to a particular activity, you will not be able to engage in that activity. </p>
            <p>When you use the Site, Varyn Inc. or third parties authorized by Varyn may also collect certain technical and routing information about your computer to facilitate your use of the Site and its services. For example, we may log environmental variables, such as browser type, operating system, CPU speed, and the Internet Protocol ("IP") address of your computer. We use these environmental variables to facilitate and track your use of the Site and its services. Varyn also uses such environmental variables to measure traffic patterns on the Site. Without expressly informing you in each particular circumstance, we do not match such information with any of your personal information. </p>
            <p>When you submit personal information to Varyn through this Site, you understand and agree that this information may be transferred across national boundaries and may be stored and processed in any of the countries in which Varyn and its affiliates and subsidiaries maintain offices, including without limitation, the United States. You also acknowledge that in certain countries or with respect to certain activities, the collection, transferring, storage and processing of your information may be undertaken by trusted vendors of Varyn. Such vendors are bound by contract to not use your personal information for their own purposes or provide it to any third parties.</p>
            <h5>How your Personal Information is Used</h5>
            <p>Varyn  may collect information about the use of the Site; such as the types of services used and how many users we receive daily. This information is collected in aggregate form, without identifying any user individually. Varyn may use this aggregate, non-identifying statistical data for statistical analysis, marketing or similar promotional purposes. </p>
            <h5>Your Choices with Respect to Personal Information</h5>
            <p>Varyn recognizes and appreciates the importance of responsible use of information collected on this Site.
                We only use your personal information for the sole purpose of providing you the services of this Site for your personal benefit.
                Without your consent, Varyn will not communicate any information to you regarding products, services, and special offers available from Varyn, although we may find it necessary to communicate with you regarding your use of the services on this Site.
                Except in the particular circumstances described in this Privacy Policy, Varyn will not provide your name or your contact information to any other company or organization without your consent. </p>
            <p>There are other instances in which Varyn may divulge your personal information.
                Varyn may provide your personal information if necessary, in Varyn's good faith judgment, to comply with laws or regulations of a governmental or regulatory body or in response to a valid subpoena, warrant or order or to protect the rights of Varyn or others. </p>
        </tr>
    </div>
</div><!-- /.marketing -->
<?php
    include_once('common/footer.php');
?>
</body>
</html>
