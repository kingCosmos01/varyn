<?php /** Varyn home page.
 * 
 */
require_once('../services/common.php');
$search = getPostOrRequestVar('q', null);
if ($search != null) {
    header('location:/games/?q=' . $search);
    exit;
}
$page = 'home';
$pageTitle = 'Varyn: Great games you can play anytime, anywhere';
$pageDescription = 'Varyn makes games using technology that performs on the most popular platforms. Cross platform friendly technologies have created an opportunity to re-invent online games for an audience that moves seamlessly between desktop, tablet, and smart-phone.';

processTrackBack();
$showSubscribe = getPostOrRequestVar('s', '0');
include_once(VIEWS_ROOT . 'header.php');
?>
<div class="container top-promo-area">
    <div class="row">
        <div id="PromoCarousel" class="carousel slide carousel-fade col-sm-8" data-ride="carousel">
          <ol class="carousel-indicators">
              <li data-target="#PromoCarousel" data-slide-to="0" class="active"></li>
          </ol>
          <div id="PromoCarouselInner" class="carousel-inner" role="listbox">
            <div class="item active">
              <div class="sliderContainer" style="background:url(/images/promos/VarynPromoHome.jpg) center center; background-size:cover;">
                <div class="carousel-caption">
                  <h3>Welcome to Varyn!</h3>
                  <p class="sliderCaption">We have games for all ages and the most popular platforms. Follow us for updates.</p>
                  <p><button type="button" class="btn btn-md btn-danger" data-toggle="modal" data-target="#modal-subscribe" onclick="varynApp.showSubscribePopup(true);">Sign up now</button></p>
                </div>
              </div>
            </div>
          </div>
          <a class="left carousel-control" href="#PromoCarousel" role="button" data-slide="prev">
            <span class="glyphicon glyphicon-chevron-left"></span>
            <span class="sr-only">Previous</span>
          </a>
          <a class="right carousel-control" href="#PromoCarousel" role="button" data-slide="next">
            <span class="glyphicon glyphicon-chevron-right"></span>
            <span class="sr-only">Next</span>
          </a>
        </div>
        <div id="ad300" class="col-sm-4 col-md-2">
            <div id="boxAd300" class="ad300">
                <iframe src="<?php echo($webServer);?>/common/ad300.html" frameborder="0" scrolling="no" style="width: 300px; height: 250px; overflow: hidden; z-index: 9999; left: 0px; bottom: 0px; display: inline-block;"></iframe>
            </div>
            <p id="ad300-subtitle" class="text-right"><small>Advertisement</small></p>
        </div>
    </div>
</div>
<div class="container marketing">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">Hot Games</h3>
        </div>
    </div>
    <div id="HomePageTopGames" class="row">
    </div>
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">New Games</h3>
        </div>
    </div>
    <div id="HomePageNewGames" class="row">
    </div>
    <div id="bottomAd" class="row">
        <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
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
<script src="/common/varynIndexPage.js"></script>
</body>
</html>