<?php /** Varyn home page.
 * 
 */
require_once('../services/common.php');
require_once('../views/sections.php');
processSearchRequest();
$page = 'home';
$pageTitle = 'Varyn: Great games you can play anytime, anywhere';
$pageDescription = 'Varyn makes games using technology that performs on the most popular platforms. Cross platform friendly technologies have created an opportunity to re-invent online games for an audience that moves seamlessly between desktop, tablet, and smart-phone.';
$homePagePromotionId = 3;
$topGamesListId = 4;
$newGamesListId = 5;

processTrackBack();
$showSubscribe = getPostOrRequestVar('s', '0');
include_once(VIEWS_ROOT . 'header.php');
?>
<div class="container top-promo-area">
    <div class="row">
      <?php buildPromoCarousel($homePagePromotionId); ?>
      <div id="ad300" class="col-sm-4 col-md-2">
          <div id="boxAd300" class="ad300">
          <?php
          $adProvider = 'cpmstar';
          include_once(VIEWS_ROOT . 'ad-spot.php');
          ?>
          </div>
          <p id="ad300-subtitle" class="text-right"><small>Advertisement</small></p>
      </div>
  </div>
</div>
<div class="container py-0 my-0">
    <?php buildGamesSection($topGamesListId, 'Hot games'); ?>
    <?php buildGamesSection($newGamesListId, 'New games'); ?>
    <div id="bottomAd" class="row">
    <?php
    $adProvider = 'google';
    include_once(VIEWS_ROOT . 'ad-spot.php');
    ?>
    </div>
</div>
<?php
include_once(VIEWS_ROOT . 'footer.php');
?>
<script src="/common/varynIndexPage.js"></script>
</body>
</html>