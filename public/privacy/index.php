<?php
require_once('../../services/common.php');
$search = getPostOrRequestVar('q', null);
if ($search != null) {
    header('location:/games/?q=' . $search);
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
        <p class="copyright">Updated on 20-May-2018.</p>
            <p>This document sets forth the Varyn Online Privacy Policy (the Privacy Policy) for this web site, www.varyn.com (the Site).
                Varyn is concerned for your privacy and protecting your data and we put forward this policy on data privacy to help you understand what we do and what control you have.
                If you have objections to the Privacy Policy, you should not access or use this Site.
                The Privacy Policy is subject to change.</p>
            <h3>Collection of Personal Information</h3>
            <p class="indent-2">As a visitor to this Site, you can engage in many activities without providing any personal information. In connection with other activities, Varyn may ask you to provide certain information about yourself by filling out and submitting an online form. It is completely optional for you to engage in these activities. If you elect to engage in these activities we may ask that you provide us personal information, such as your name, e-mail address, and other personal information. When ordering products or services on the Site, you may be asked to provide a credit card number. We do not store credit card numbers or associate credit card numbers with your account. Depending upon the activity, some of the information that we ask you to provide is identified as mandatory and some as voluntary. If you do not provide the mandatory data with respect to a particular activity, you will not be able to engage in that activity.</p>
            <p class="indent-2">When you use the Site, Varyn Inc. or third parties authorized by Varyn may also collect certain technical and routing information about your computer to facilitate your use of the Site and its services. For example, we may log environmental variables, such as browser type, operating system, CPU speed, and the Internet Protocol ("IP") address of your computer. We use these environmental variables to facilitate and track your use of the Site and its services in a non-identifying fashion (anonymous). Varyn also uses such environmental variables to measure traffic patterns on the Site. Without expressly informing you in each particular circumstance, we do not match such information with any of your personal information.</p>
            <p class="indent-2">When you submit personal information to Varyn through this Site, you understand and agree that this information may be transferred across national boundaries and may be stored and processed in any of the countries in which Varyn and its affiliates and subsidiaries maintain offices, including without limitation, the United States. You also acknowledge that in certain countries or with respect to certain activities, the collection, transferring, storage and processing of your information may be undertaken by trusted vendors of Varyn. Such vendors are bound by contract to not use your personal information for their own purposes or provide it to any third parties.</p>
            <p class="indent-2">Varyn collects personal data that you voluntarily provide, such as your name and email address, and other optional data to help make our service to you more valuable and engaging for you, such as age, gender, and telephone number. This information is only used to enhance your personal enjoyment of the Service and is not in any way shared with third parties, sold, distributed, monitored, or offered.</p>
            <p class="indent-2">We utilize Cookies with our Services to allow us to track your usage of the Services. We collect various data, including analytics, about how you use and interact with our Services. This allows us to provide you with more relevant Services, a better experience with our Services, and to collect, analyze and improve the performance of our Services.</p>
            <h3>How your Personal Information is Used</h3>
            <p class="indent-2">Varyn may collect information about the use of the Site; such as the types of services used and how many users we receive daily. This information is collected in aggregate form, without identifying any user individually. Varyn may use this aggregate, non-identifying statistical data for statistical analysis, marketing or similar promotional purposes. </p>
            <h3>Your Choices with Respect to Personal Information</h3>
            <p class="indent-2">Varyn recognizes and appreciates the importance of responsible use of information collected on this Site.
                We only use your personal information for the sole purpose of providing you the services of this Site for your personal benefit.
                Without your consent, Varyn will not communicate any information to you regarding products, services, and special offers available from Varyn, although we may find it necessary to communicate with you regarding your use of the services on this Site.
                Except in the particular circumstances described in this Privacy Policy, Varyn will not provide your name or your contact information to any other company or organization without your consent.</p>
            <p class="indent-2">There are other instances in which Varyn may divulge your personal information.
                Varyn may provide your personal information if necessary, in Varyn's good faith judgment, to comply with laws or regulations of a governmental or regulatory body or in response to a valid subpoena, warrant or order or to protect the rights of Varyn or others.</p>
            <p class="indent-2"> If you utilize our Services from a country other than the country where our servers are located, your communications with us may result in transferring your personal data across international borders. Also, when you call us or initiate a chat, we may provide you with support from one of our global locations outside your country of origin. In these cases, your personal data is handled according to this Privacy Policy.</p>
            <p class="indent-2">If at any time you have questions about our practices or any of your rights described here, you may contact our Data Protection Team at support@varyn.com.</p>
        </tr>
    </div>
</div>
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
