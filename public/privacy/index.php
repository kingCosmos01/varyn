<?php
require_once('../../services/common.php');
$search = getPostOrRequestVar('q', null);
if ($search != null) {
    header('location:/allgames/?q=' . $search);
    exit;
}
$page = 'home';
$pageTitle = 'Varyn | Privacy Policy';
$pageDescription = 'Varyn privacy policy. Varyn makes games using technology that performs on the most popular platforms. Cross platform friendly technologies have created an opportunity to re-invent online games for an audience that moves seamlessly between desktop, tablet, and smart-phone.';
$showSubscribe = getPostOrRequestVar('s', '0');
include_once(VIEWS_ROOT . 'header.php');
?>
<div class="container marketing">
    <div class="panel panel-primary panel-padded panel-gutter-2">
        <h1>Privacy Policy</h1>
            <p>This document sets forth the Varyn Online Privacy Policy (the Privacy Policy) for this web site, www.varyn.com (the Site).
                If you have objections to the Privacy Policy, you should not access or use this Site.
                The Privacy Policy is subject to change and was last updated on 2-Apr-2016.</p>
            <h4>Collection of Personal Information</h4>
            <p>As a visitor to this Site, you can engage in many activities without providing any personal information. In connection with other activities, Varyn may ask you to provide certain information about yourself by filling out and submitting an online form. It is completely optional for you to engage in these activities. If you elect to engage in these activities, however, we may ask that you provide us personal information, such as your first and last name, e-mail address, and other personal information. When ordering products or services on the Site, you may be asked to provide a credit card number. Depending upon the activity, some of the information that we ask you to provide is identified as mandatory and some as voluntary. If you do not provide the mandatory data with respect to a particular activity, you will not be able to engage in that activity. </p>
            <p>When you use the Site, Varyn Inc. or third parties authorized by Varyn may also collect certain technical and routing information about your computer to facilitate your use of the Site and its services. For example, we may log environmental variables, such as browser type, operating system, CPU speed, and the Internet Protocol ("IP") address of your computer. We use these environmental variables to facilitate and track your use of the Site and its services. Varyn also uses such environmental variables to measure traffic patterns on the Site. Without expressly informing you in each particular circumstance, we do not match such information with any of your personal information. </p>
            <p>When you submit personal information to Varyn through this Site, you understand and agree that this information may be transferred across national boundaries and may be stored and processed in any of the countries in which Varyn and its affiliates and subsidiaries maintain offices, including without limitation, the United States. You also acknowledge that in certain countries or with respect to certain activities, the collection, transferring, storage and processing of your information may be undertaken by trusted vendors of Varyn. Such vendors are bound by contract to not use your personal information for their own purposes or provide it to any third parties.</p>
            <h4>How your Personal Information is Used</h4>
            <p>Varyn  may collect information about the use of the Site; such as the types of services used and how many users we receive daily. This information is collected in aggregate form, without identifying any user individually. Varyn may use this aggregate, non-identifying statistical data for statistical analysis, marketing or similar promotional purposes. </p>
            <h4>Your Choices with Respect to Personal Information</h4>
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
