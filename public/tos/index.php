<?php
require_once('../../services/common.php');
$search = getPostOrRequestVar('q', null);
if ($search != null) {
    header('location:/games/?q=' . $search);
    exit;
}
$page = 'home';
$pageTitle = 'Varyn | Terms of Use';
$pageDescription = 'Varyn Terms of Service. Please review our terms of use before using our website.';
$showSubscribe = getPostOrRequestVar('s', '0');
include_once(VIEWS_ROOT . 'header.php');
?>
<div class="container marketing">
    <div class="panel panel-primary panel-padded panel-gutter-2">
        <h1>Terms of Use</h1>
        <h4>Acceptance of Terms</h4>
        <p>Welcome to the web site (the "Site") of Varyn, Inc. ("Varyn"). On this web site, Varyn makes available to you a wide range of information, software, products, downloads, documents, communications, files, text, graphics, publications, content, and services. The Terms of Use are subject to change. This document was last updated on 15-Nov-2014.
            PLEASE READ THE TERMS OF USE CAREFULLY BEFORE USING THIS WEBSITE. By accessing and using this web site in any way, including, without limitation, browsing the web site, using any information, using any content, using any services, downloading any materials, and/or placing an order for products or services, you agree to and are bound by the terms of use described in this document ("Terms of Use"). IF YOU DO NOT AGREE TO ALL OF THE TERMS AND CONDITIONS CONTAINED IN THE TERMS OF USE, DO NOT USE THIS WEBSITE IN ANY MANNER. The Terms of Use are entered into by and between Varyn and you. If you are using the web site on behalf of your employer, you represent that you are authorized to accept these Terms of Use on your employer's behalf. Varyn reserves the right, at Varyn's sole discretion, to change, modify, update, add, or remove portions of the Terms of Use at any time without notice to you. Please check these Terms of Use for changes. Your continued use of this web site following the posting of changes to the Terms of Use will mean you accept those changes.</p>
        <h4>Use of Materials Limitations</h4>
        <p>All materials contained in the web site are the copyrighted property of Varyn, its subsidiaries, affiliated companies and/or third-party licensors. All trademarks, service marks, and trade names are proprietary to Varyn, or its subsidiaries or affiliated companies and/or third-part licensors.
            Unless otherwise specified, the materials and services on this web site are for your personal and non-commercial use, and you may not modify, copy, distribute, transmit, display, perform, reproduce, publish, license, create derivative works from, transfer, or sell any information, software, products or services obtained from the web site without the written permission from Varyn.</p>
        <h4>Privacy Policy</h4>
        <p>Varyn's Privacy Policy can be found at <a href="/privacy.php">www.varyn.com/privacy.php</a>.</p>
        <h4>No Unlawful or Prohibited Use</h4>
        <p>As a condition of your use of the web site, you will not use the web site for any purpose that is unlawful or prohibited by these terms, conditions, and notices. You may not use the Services in any manner that could damage, disable, overburden, or impair any Varyn server, or the network(s) connected to any Varyn server, or interfere with any other party's use and enjoyment of the web site You may not attempt to gain unauthorized access to services, materials, other accounts, computer systems or networks connected to any Varyn server or to the web site, through hacking, password mining or any other means. You may not obtain or attempt to obtain any materials or information through any means not intentionally made available through the web site.</p>
        <h4>Copyright and Trademark Information</h4>
        <p>COPYRIGHT NOTICE: Copyright &copy; 2017 Varyn, Inc., All Rights Reserved.</p>
        <p>Third-party trademarks are used solely for distributing the games herein and no license or affiliation is implied. All copyrights are held by the respective owners.</p>
    </div>
</div><!-- /.marketing -->
<?php
include_once(VIEWS_ROOT . 'footer.php');
?>
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
</script>
</body>
</html>