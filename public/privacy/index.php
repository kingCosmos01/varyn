<?php
require_once('../../services/common.php');
processSearchRequest();
$page = 'home';
$pageTitle = 'Privacy Policy';
$pageDescription = 'Varyn privacy policy. Varyn is concerned for your privacy and protecting your data. We put forward this policy on data privacy to help you understand what we do and what control you have.';
$showSubscribe = getPostOrRequestVar('s', '0');
include_once(VIEWS_ROOT . 'header.php');
?>
<div class="container">
    <div class="card m-2 p-4">
        <h1>Privacy Policy</h1>
        <p class="copyright">Updated on 25-June-2021.</p>
        <p>This document sets forth the Varyn Online Privacy Policy (the Privacy Policy) for this web site, www.varyn.com (the Site).
            Varyn is concerned for your privacy and protecting your data. We put forward this policy on data privacy to help you understand what we do and what control you have.
            If you have objections to the Privacy Policy, you should not access or use this Site.
            The Privacy Policy is subject to change.</p>
        <h3>Collection of Personal Information</h3>
        <p class="indent-2">As a visitor to this Site, you can engage in many activities without providing any personal information. In connection with other activities, Varyn may ask you to provide certain information about yourself by filling out and submitting an online form. This information is retained to increase your personal enjoyment of this Site and Services and so that we may communicate with you regarding important information about your activity such as security updates and Service changes. It is completely optional for you to engage in these activities. If you elect to engage in these activities we may ask that you provide us personal information, such as your name, e-mail address, and other personal information. Depending upon the activity, some of the information that we ask you to provide is identified as mandatory and some as voluntary. If you do not provide the mandatory data with respect to a particular activity, you will not be able to engage in that activity.</p>
        <p class="indent-2">When ordering products or services on the Site, you may be asked to provide a credit card number. We do not retqin credit card information or store credit card numbers or associate credit card numbers on this Site or with your account. </p>
        <p class="indent-2">When you use the Site, Varyn Inc. or third parties authorized by Varyn may also collect certain technical and routing information about your computer to facilitate your use of the Site and its services. For example, we may log environmental variables, such as browser type, operating system, CPU speed, and the Internet Protocol ("IP") address of your computer. We use these environmental variables to facilitate and track your use of the Site and its services in a non-identifying fashion (anonymous). Varyn also uses such environmental variables to measure traffic patterns on the Site. Without expressly informing you in each particular circumstance, we do not match such information with any of your personal information.</p>
        <p class="indent-2">When you submit personal information to Varyn through this Site, you understand and agree that this information may be transferred across national boundaries and may be stored and processed in any of the countries in which Varyn and its affiliates and subsidiaries maintain offices, including without limitation, the United States. You also acknowledge that in certain countries or with respect to certain activities, the collection, transferring, storage and processing of your information may be undertaken by trusted vendors of Varyn. Such vendors are bound by contract to not use your personal information for their own purposes or provide it to any third parties.</p>
        <p class="indent-2">Varyn collects personal data that you voluntarily provide, such as your name and email address, and other optional data to help make our service to you more valuable and engaging for you, such as age, gender, and telephone number. This information is only used to enhance your personal enjoyment of the Service and is not in any way shared with third parties, sold, distributed, monitored, or offered.</p>
        <p class="indent-2">We utilize cookies with our Services to allow us to give you an optimal experience using the Services of this website and to track your usage of the Services. We collect various data, including analytics, about how you use and interact with our Services. This allows us to provide you with more relevant Services, a better experience with our Services, and to collect, analyze and improve the performance of our Services. This tracking information is anonymous, it is not identified to any identifiable user, and it is private to be used only by Varyn and its authorized trusted vendors to improve the Services for your benefit. The data is not shared or sold.</p>
        <h3>How your Personal Information is Used</h3>
        <p class="indent-2">Varyn may collect information about the use of the Site; such as the types of services used and how many users we receive daily. This information is collected in aggregate form, without identifying any user individually. Varyn may use this aggregate, non-identifying statistical data for statistical analysis, marketing or similar promotional purposes. </p>
        <h3>Your Choices with Respect to Personal Information</h3>
        <p class="indent-2">Varyn recognizes and appreciates the importance of responsible use of information collected on this Site.
            We only use your personal information for the sole purpose of providing you the services of this Site for your personal benefit.
            Without your consent, Varyn will not communicate any information to you regarding products, services, and special offers available from Varyn, although we may find it necessary to communicate with you regarding your use of the services on this Site.
            Except in the particular circumstances described in this Privacy Policy, Varyn will not provide your name or your contact information to any other company or organization without your consent.</p>
        <p class="indent-2">There are other instances in which Varyn may divulge your personal information.
            Varyn may provide your personal information if necessary, in Varyn's good faith judgment, to comply with laws or regulations of a governmental or regulatory body or in response to a valid subpoena, warrant or order or to protect the rights of Varyn or others.</p>
        <p class="indent-2">If you utilize our Services from a country other than the country where our servers are located, your communications with us may result in transferring your personal data across international borders. Also, when you call us or initiate a chat, we may provide you with support from one of our global locations outside your country of origin. In these cases, your personal data is handled according to this Privacy Policy.</p>
        <p class="indent-2">If at any time you have questions about our practices or any of your rights described here, you may contact our Data Protection Team at support@varyn.com.</p>
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
    head.js("/common/modernizr.js", "/common/bootstrap.bundle.min.js", "/common/common.js", "/common/enginesis.js", "/common/ShareHelper.js");
</script>
</body>
</html>
