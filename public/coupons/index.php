<?php
require_once('../../services/common.php');
$page = 'offers';
$pageTitle = 'Coupons and Offers';
$pageDescription = 'Get Coupons and Offers at Varyn! We partner with some great companies and retailers to offer you discounts on the products you want.';
$search = getPostOrRequestVar('q', '');
include_once(VIEWS_ROOT . 'header.php');
?>
<div class="container marketing">
    <div id="AllGamesArea" class="row">
        <div class="col-xs-12" style="min-height: 1190px;">
            <noscript>
                <p>Coupons powered by <a href="http://www.coupons.com?pid=13903&nid=10&zid=xh20&bid=1379910001">Coupons.com</a></p>
            </noscript>
            <script id="scriptId_718x940_117571" type="text/javascript" src="//bcg.coupons.com/?scriptId=117571&bid=1379910001&format=718x940&bannerType=3&channel=Coupon%20Page">
            </script>
        </div>
    </div>
    <div id="bottomAd" class="row">
        <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
        <!-- JumpyDot Responsive -->
        <ins class="adsbygoogle"
             style="display:block"
             data-ad-client="ca-pub-9118730651662049"
             data-ad-slot="5571172619"
             data-ad-format="auto"></ins>
        <script>
            (adsbygoogle = window.adsbygoogle || []).push({});
        </script>
    </div>
</div>
<?php
include_once(VIEWS_ROOT . 'footer.php');
?>
</body>
</html>