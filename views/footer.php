<?php
if ( ! isset($showSubscribe)) {
    $showSubscribe = false;
}
?>
<div class="container footer">
    <div class="card card-light p-2">
        <div class="row">
            <div class="col align-self-start px-3 py-2">
                <img src="/images/logosmall.png" alt="Varyn small logo"/>
            </div>
            <div id="footer-nav" class="col-8 align-self-center text-center py-3">
                <ul class="nav justify-content-center">
                    <li class="nav-item">
                        <a href="/privacy/" class="nav-link">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-incognito" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="m4.736 1.968-.892 3.269-.014.058C2.113 5.568 1 6.006 1 6.5 1 7.328 4.134 8 8 8s7-.672 7-1.5c0-.494-1.113-.932-2.83-1.205a1.032 1.032 0 0 0-.014-.058l-.892-3.27c-.146-.533-.698-.849-1.239-.734C9.411 1.363 8.62 1.5 8 1.5c-.62 0-1.411-.136-2.025-.267-.541-.115-1.093.2-1.239.735Zm.015 3.867a.25.25 0 0 1 .274-.224c.9.092 1.91.143 2.975.143a29.58 29.58 0 0 0 2.975-.143.25.25 0 0 1 .05.498c-.918.093-1.944.145-3.025.145s-2.107-.052-3.025-.145a.25.25 0 0 1-.224-.274ZM3.5 10h2a.5.5 0 0 1 .5.5v1a1.5 1.5 0 0 1-3 0v-1a.5.5 0 0 1 .5-.5Zm-1.5.5c0-.175.03-.344.085-.5H2a.5.5 0 0 1 0-1h3.5a1.5 1.5 0 0 1 1.488 1.312 3.5 3.5 0 0 1 2.024 0A1.5 1.5 0 0 1 10.5 9H14a.5.5 0 0 1 0 1h-.085c.055.156.085.325.085.5v1a2.5 2.5 0 0 1-5 0v-.14l-.21-.07a2.5 2.5 0 0 0-1.58 0l-.21.07v.14a2.5 2.5 0 0 1-5 0v-1Zm8.5-.5h2a.5.5 0 0 1 .5.5v1a1.5 1.5 0 0 1-3 0v-1a.5.5 0 0 1 .5-.5Z"/>
                        </svg> Privacy</a>
                    </li>
                    <li class="nav-item">
                        <a href="/tos/" class="nav-link">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle" viewBox="0 0 16 16">
                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                            <path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
                        </svg> Terms</a>
                    </li>
                    <li class="nav-item">
                        <a href="/about/" class="nav-link">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-question-circle" viewBox="0 0 16 16">
                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                            <path d="M5.255 5.786a.237.237 0 0 0 .241.247h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286zm1.557 5.763c0 .533.425.927 1.01.927.609 0 1.028-.394 1.028-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94z"/>
                        </svg> About Varyn</a>
                    </li>
                    <li class="nav-item">
                        <a href="/contact/" class="nav-link">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-rolodex" viewBox="0 0 16 16">
                            <path d="M8 9.05a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z"/>
                            <path d="M1 1a1 1 0 0 0-1 1v11a1 1 0 0 0 1 1h.5a.5.5 0 0 0 .5-.5.5.5 0 0 1 1 0 .5.5 0 0 0 .5.5h9a.5.5 0 0 0 .5-.5.5.5 0 0 1 1 0 .5.5 0 0 0 .5.5h.5a1 1 0 0 0 1-1V3a1 1 0 0 0-1-1H6.707L6 1.293A1 1 0 0 0 5.293 1H1Zm0 1h4.293L6 2.707A1 1 0 0 0 6.707 3H15v10h-.085a1.5 1.5 0 0 0-2.4-.63C11.885 11.223 10.554 10 8 10c-2.555 0-3.886 1.224-4.514 2.37a1.5 1.5 0 0 0-2.4.63H1V2Z"/>
                        </svg> Contact</a>
                    </li>
                </ul>
            </div>
            <div class="col py-3 align-self-end">
                <a href="#">Back to top</a>
            </div>
        </div>
        <div class="row social justify-content-center">
            <p class="text-center text-muted">Follow us on:</p>
            <div class="col-5 align-self-center text-center">
                <ul class="nav justify-content-center">
                    <li class="nav-item"><a href="https://www.facebook.com/varyndev" title="Follow Varyn on Facebook"><div class="facebook sprite"></div></a></li>
                    <li class="nav-item"><a href="https://twitter.com/@varyndev" title="Follow Varyn on Twitter"><div class="twitter sprite"></div></a></li>
                    <li class="nav-item"><a href="https://www.linkedin.com/company/varyn-inc-" title="Follow Varyn on LinkedIn"><div class="linkedin sprite"></div></a></li>
                    <li class="nav-item"><a href="https://www.youtube.com/channel/UC6v8MOc99Z6nGluQiiuZ9ow" title="Follow Varyn on YouTube"><div class="youtube sprite"></div></a></li>
                    <li class="nav-item"><a href="https://www.pinterest.com/varyndev/varyndev/" title="Follow Varyn on Pinterest"><div class="pinterest sprite"></div></a></li>
                    <li class="nav-item"><a href="https://www.instagram.com/varyndev" title="Follow Varyn on Instagram"><div class="instagram sprite"></div></a></li>
                </ul>
            </div>
        </div>
        <div class="row px-5 py-0">
            <p class="text-small">
                Varyn makes games using technology that performs on the most popular platforms. Cross platform friendly technologies have created an opportunity to re-invent online games for an audience that moves seamlessly between desktop, tablet, and smart-phone.
                Varyn creates games that play anytime and anywhere.
                If you have a game you would like to see featured on our site please contact us using the <a href="/contact/">contact form</a>.
            </p>
        </div>
        <div class="row justify-content-center py-0">
            <ul class="nav justify-content-center">
                <li class="nav-item"><a class="nav-link" href="/">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="/games/">Games</a></li>
                <li class="nav-item"><a class="nav-link" href="/blog/">Blog</a></li>
                <li class="nav-item"><a class="nav-link" href="/about/">About</a></li>
            </ul>
        </div>
        <div class="row px-5 py-0 legalcopy">
            <p class="copyright text-small text-center">Third-party trademarks are used solely for distributing the games herein and no license or affiliation is implied. All copyrights are held by the respective owners.</p>
            <p class="copyright text-small text-center">Copyright &copy; 2022 <a href="https://www.varyn.com">Varyn, Inc.</a>.  All rights reserved.</p>
        </div>
    </div>
</div>
<div id="fb-root"></div>
<script>
    var siteConfiguration = {
        siteId: <?php echo($siteId);?>,
        developerKey: "<?php echo(ENGINESIS_DEVELOPER_API_KEY);?>",
        serverStage: "<?php echo($serverStage);?>",
        authToken: "<?php echo($authToken);?>",
        languageCode: navigator.language || navigator.userLanguage
    };
    var pageParameters = {
        showSubscribe: "<?php echo($showSubscribe);?>"
    };
</script>
<?php if ( ! empty($search)) { ?>
<script type="text/javascript">
gtag({"event": "search", "q": "'<?php echo($search);?>'"});
</script>
<?php } ?>
