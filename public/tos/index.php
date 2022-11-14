<?php
require_once('../../services/common.php');
processSearchRequest();
$page = 'home';
$pageTitle = 'Terms of Use';
$pageDescription = 'Varyn terms of service regarding the use of this website. Please review these terms of use before using this website.';
$showSubscribe = getPostOrRequestVar('s', '0');
include_once(VIEWS_ROOT . 'header.php');
?>
<div class="container">
    <div class="card card-primary m-2 p-4">
        <h1>Terms of Use</h1>
        <p class="copyright">Updated on 17-August-2021.</p>
        <h3 id="acceptance">Acceptance of Terms</h3>
        <p class="indent-2">Welcome to the web site (the "Site") of Varyn, Inc. ("Varyn"). On this web site, Varyn makes available to you a wide range of information and services. The Terms of Use apply to this website and services provided. The Terms of Use are subject to change.</p>
        <p class="indent-2">PLEASE READ THE TERMS OF USE CAREFULLY BEFORE USING THIS WEBSITE. By accessing and using this web site and services, you agree to and are bound by the terms of use described in this document ("Terms of Use"). IF YOU DO NOT AGREE TO ALL OF THE TERMS AND CONDITIONS CONTAINED IN THE TERMS OF USE, DO NOT USE THIS WEBSITE IN ANY MANNER.</p>
        <p class="indent-2">The Terms of Use are entered into by and between Varyn and you. If you are using the web site on behalf of your employer, you represent that you are authorized to accept these Terms of Use on your employer's behalf. Varyn reserves the right, at Varyn's sole discretion, to change, modify, update, add, or remove portions of the Terms of Use at any time without notice to you. Please check these Terms of Use for changes. Your continued use of this web site following the posting of changes to the Terms of Use will mean you accept those changes.</p>
        <h3>Use of Materials Limitations</h3>
        <p class="indent-2">All materials contained in the web site are the copyrighted property of Varyn, its subsidiaries, affiliated companies and/or third-party licensors. All trademarks, service marks, and trade names are proprietary to Varyn, or its subsidiaries or affiliated companies and/or third-part licensors.
            Unless otherwise specified, the materials and services on this web site are for your personal and non-commercial use, and you may not modify, copy, distribute, transmit, display, perform, reproduce, publish, license, create derivative works from, transfer, or sell any information, software, products or services obtained from the web site without the written permission from the copyright owner.</p>
        <h3 id=privacy-policy>Privacy Policy</h3>
        <p class="indent-2">Review Varyn's <a href="/privacy/">privacy policy</a> to understand how we safeguard your personal data and use of this site. By using this website and its services, you agree to our privacy policy.</p>
        <h3 id="code-of-conduct">Code of Conduct</h3>
        <p class="indent-2">Varyn is committed to making participation in this website a harassment-free experience for everyone, regardless of level of experience, gender, gender identity and expression, sexual orientation, disability, personal appearance, body size, race, ethnicity, age, religion, or nationality. We expect all participants to do the same.</p>
        <p class="indent-2">We do not tolerate the following behavior:</p>
        <div class="indent-2">
            <ul>
                <li>Trolling, insulting, or derogatory comments toward any individual or group.</li>
                <li>The use of sexualized or violent language or imagery.</li>
                <li>Personal attacks.</li>
                <li>Public or private harassment.</li>
                <li>Publishing other's private information, such as physical or electronic addresses, without explicit permission.</li>
                <li>Impersonating another person or individual in an effort to deceive or coerce other members.</li>
                <li>You may not use bots or scripts to automate any usage of the services provided.</li>
                <li>Unethical or unprofessional conduct.</li>
            </ul>
        </div>
        <p class="indent-2">Any member caught violating the code of conduct will be expelled from the site and all personal data will be removed.</p>
        <h3 id="use">No Unlawful or Prohibited Use</h3>
        <p class="indent-2">As a condition of your use of the web site, you will not use the web site for any purpose that is unlawful or prohibited by these terms, conditions, and notices. You may not use the Services in any manner that could damage, disable, overburden, or impair any Varyn server, or the network(s) connected to any Varyn server, or interfere with any other party's use and enjoyment of the web site You may not attempt to gain unauthorized access to services, materials, other accounts, computer systems or networks connected to any Varyn server or to the web site, through hacking, password mining or any other means. You may not obtain or attempt to obtain any materials or information through any means not intentionally made available through the web site.</p>
        <p class="indent-2">Accounts registered by "bots", scripts, or other automated methods are not permitted. Any account identified as a bot or script creation will be immediately removed without notification.</p>
        <p class="indent-2">Any type of game play, score submission, contest submission, or user interaction generated by "bots", scripts, or other automated methods are not permitted. Any behavior identified as a bot or script generated will be immediately removed without notification.</p>
        <h3 id="rights">Data Rights</h3>
        <p class="indent-2">By using this site and its services you agree to allow Varyn to record and manage data on your behalf. Varyn will use this data as required to perform its services and commitments to you and as it sees fit to offer, perform, produce, manage, and maintain the services available on this website and within the terms of this agreement. You agree to allow Varyn to use your data in anyway it sees fit in the operation of these services. Varyn will not make any of this data available to any third party. Varyn will not sell your data. Your data is yours and you can copy and use your own personal data freely as you wish within the terms of this agreement and the <a href="/privacy/">privacy policy</a>. At any time if you disagree with these terms you may delete your account and all of your personal data will be removed from the site.</p>
        <p class="indent-2">Varyn will not sell, trade, or offer any of your data to any third party at any time.</p>
        <h3 id="copyright">Copyright and Trademark Information</h3>
        <p class="indent-2">COPYRIGHT NOTICE: Copyright &copy; 2021 Varyn, Inc., All Rights Reserved.</p>
        <p class="indent-2">Third-party trademarks are used solely for distributing the games herein and no license or affiliation is implied. All copyrights are held by the respective owners.</p>
    </div>
</div>
<?php
include_once(VIEWS_ROOT . 'footer.php');
?>
<script type="text/javascript">
    function initApp() {
        var showSubscribe = '<?php echo ($showSubscribe); ?>';

        if (showSubscribe == '1') {
            showSubscribePopup();
        }
    }

    head.ready(function() {
        initApp();
    });
    head.js("/common/modernizr.js", "/common/bootstrap.bundle.min.js", "/common/common.js", "/common/enginesis.js", "/common/ShareHelper.js");
</script>
</body>

</html>