<?php /** sections.php
 * Code used on pages to build out sections from dynamic data queries.
 */

function formatPromotionItem($isActive, $promotion) {
  $title = $promotion->promotion_item_title;
  $caption = $promotion->promotion_item_description;
  $image = $promotion->promotion_item_img;
  $link = $promotion->promotion_item_link;
  $linkTitle = $promotion->promotion_item_link_title;
  $carouselItem = '<div class="carousel-item' . ($isActive ? ' active' : '') . '">';
  $carouselItem .= '<div class="sliderContainer" style="background:url(' . $image . ') center center; background-size:cover;">';
  $carouselItem .= '<div class="carousel-caption d-none d-md-block">';
  $carouselItem .= '<h3>' . $title . '</h3>';
  $carouselItem .= '<p class="sliderCaption">' . $caption . '</p>';
  if (looksLikeURLPattern($link)) {
    $carouselItem .= '<p><a class="btn btn-md btn-success" href="' . $link . '" role="button">' . $linkTitle . '</a></p>';
  } else {
    // @todo: if it is not a URL then we expect it to be the complete HTML
    $carouselItem .= '<p><button type="button" class="btn btn-md btn-success" data-bs-toggle="modal" data-bs-target="#modal-subscribe" onclick="' . $link . '">' . $linkTitle . '</button></p>';
  }
  $carouselItem .= '</div></div></div>';
  return $carouselItem;
}

/**
 * Render the Promotion carousel HTML.
 */
function buildPromoCarousel($promotionId) {
  global $enginesis;
  $errorMessage = '';
  $items = 0;
  ?>
  <div id="PromoCarousel" class="carousel slide carousel-fade col-sm-8" data-bs-ride="carousel">
  <?php
  if ($enginesis) {
    $serverResponse = $enginesis->promotionItemList($promotionId, time());
    if ($serverResponse === null) {
      $error = $enginesis->getLastError();
      $errorMessage = $error['message'] . ': ' . $error['extended_info'];
    } else {
      $items = count($serverResponse);
      if ($items > 0) {
        $carouselIndicators = '<div class="carousel-indicators">';
        $carouselInner = '<div id="PromoCarouselInner" class="carousel-inner">';
        for ($item = 0; $item < $items; $item += 1) {
          $isActive = $item == 0;
          $carouselIndicators .= '<button type="button" data-bs-target="#PromoCarousel" data-bs-slide-to="' . $item . '" aria-label="Slide ' . ($item + 1) . '"' . ($isActive ? ' class="active" aria-current="true"' : '') . '></button>';
          $promotion = $serverResponse[$item];
          $carouselItem = formatPromotionItem($isActive, $promotion);
          $carouselInner .= $carouselItem;
        }
        $carouselIndicators .= '</div>';
        $carouselInner .= "</div>\n";
        echo($carouselIndicators);
        echo($carouselInner);
      } else {
        $errorMessage = "There are no current promotions. Check out the games below.";
      }
    }
  } else {
      $errorMessage = "The service appears to be offline at the moment. Please try again later or contact support for assistance.";
  }
  if ($errorMessage != '') {
    $errorMessage = "<p class=\"p-5 text-error\">$errorMessage</p>\n";
    echo($errorMessage);
  }
  if ($items > 0) {
  ?>
    <button class="carousel-control-prev" type="button" data-bs-target="#PromoCarousel" data-bs-slide="prev">
      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#PromoCarousel" data-bs-slide="next">
      <span class="carousel-control-next-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Next</span>
    </button>
  <?php
  }
  ?>
  </div>
  <?php
}

/**
 * Render the HTML for a single game card.
 * @param object $gameInfo A single game entry.
 */
function formatGameCard($gameInfo) {
  global $enginesis;
  $gameId = $gameInfo->game_id;
  $gameName = $gameInfo->game_name;
  $title = $gameInfo->title;
  $gameDescription = $gameInfo->short_desc;
  $baseURL = $enginesis->getServiceRoot() . '/games/';
  $playURL = '/play/' . $gameName;
  $thumbnailURL = $baseURL . $gameName . '/images/300x225.png';
  $isFavorite = false;
  ?>
  <div class="col-sm-12 col-md-6 col-lg-4">
    <div class="card" data-gameid="<?php echo($gameId);?>" data-gamename="<?php echo($gameName);?>">
      <a href="<?php echo($playURL);?>" title="Play <?php echo($title);?> Now!"><img class="card-img-top" src="<?php echo($thumbnailURL);?>" alt="<?php echo($title);?>"></a>
      <div class="card-body">
          <h3 class="card-title"><?php echo($title);?></h3>
          <p class="card-text" style="min-height: 6rem;"><?php echo($gameDescription);?></p>
          <a href="<?php echo($playURL);?>" class="btn btn-md btn-success" role="button" title="Play <?php echo($title);?> Now!" alt="Play <?php echo($title);?> Now!">Play now</a>
          <img class="favorite-button" src="/images/favorite-button-off-196.png" data-gameid="<?php echo($gameId);?>" data-favorite="false" alt="Add <?php echo($title);?> to your favorite games">
      </div>
    </div>
  </div>
  <?php
}

/**
 * Render the HTML for a list of games derived from a list query.
 * @param integer $gamesListId The list id to query.
 * @param string $sectionTitle A title to show above the section.
 */
function buildGamesSection($gamesListId, $sectionTitle) {
  global $enginesis;
  $errorMessage = '';
  $items = 0;
  ?>
    <div id="section-cards" class="px-0 py-2">
        <div class="row w-auto mb-2">
            <div class="card card-dark">
                <h4 class="my-1 py-1"><?php echo($sectionTitle);?></h4>
            </div>
        </div>
        <div class="row gy-2">
  <?php
  if ($enginesis) {
    $serverResponse = $enginesis->gameListListGames($gamesListId);
    if ($serverResponse === null) {
      $error = $enginesis->getLastError();
      $errorMessage = $error['message'] . ': ' . $error['extended_info'];
    } else {
      $items = count($serverResponse);
      if ($items > 0) {
        for ($item = 0; $item < $items; $item += 1) {
          $gameInfo = $serverResponse[$item];
          echo(formatGameCard($gameInfo));
          echo("\n");
        }
      } else {
        $errorMessage = "There are no games matching this query. Try again later.";
      }
    }
  } else {
      $errorMessage = "The service appears to be offline at the moment. Please try again later or contact us for assistance.";
  }
  if ($errorMessage != '') {
    $errorMessage = "<p class=\"p-5 text-error\">$errorMessage</p>\n";
    echo($errorMessage);
  }
  ?>
        </div>
    </div>
    <?php
}

/**
 * Render the HTML for the All Games section. This renders all games available.
 */
function buildAllGamesSection() {
  global $enginesis;
  $sectionTitle = 'All games';
  $errorMessage = '';
  $items = 0;
  $startItem = 1;
  $numberOfItems = 50;
  $gameStatusId = 2;
  ?>
    <div id="section-cards" class="px-0 py-2">
        <div class="row w-auto mb-2">
            <div class="card card-dark">
                <h4 class="my-1 py-1"><?php echo($sectionTitle);?></h4>
            </div>
        </div>
        <div class="row gy-2">
  <?php
  if ($enginesis) {
    $serverResponse = $enginesis->siteListGames($startItem, $numberOfItems, $gameStatusId);
    if ($serverResponse === null) {
      $error = $enginesis->getLastError();
      $errorMessage = $error['message'] . ': ' . $error['extended_info'];
    } else {
      $items = count($serverResponse);
      if ($items > 0) {
        for ($item = 0; $item < $items; $item += 1) {
          $gameInfo = $serverResponse[$item];
          echo(formatGameCard($gameInfo));
          echo("\n");
        }
      } else {
        $errorMessage = "There are no games available at this time. Try again later.";
      }
    }
  } else {
      $errorMessage = "The service appears to be offline at the moment. Please try again later or contact us for assistance.";
  }
  if ($errorMessage != '') {
    $errorMessage = "<p class=\"p-5 text-error\">$errorMessage</p>\n";
    echo($errorMessage);
  }
  ?>
        </div>
    </div>
    <div class="row gy-2">
      <nav aria-label="Game pagination">
        <ul class="pagination">
          <li class="page-item"><a class="page-link" href="#">Previous</a></li>
          <li class="page-item"><a class="page-link" href="#">Next</a></li>
        </ul>
      </nav>
    </div>
    <?php
}

/**
 * Render the HTML for a list of games derived from search query.
 * @param integer $search The saerch string query.
 */
function buildSearchGamesSection($search) {
  global $enginesis;
  $errorMessage = '';
  $items = 0;
  ?>
    <div id="section-cards" class="px-0 py-2">
        <div class="row gy-2">
  <?php
  if ($enginesis) {
    $serverResponse = $enginesis->gameFind($search);
    if ($serverResponse === null) {
      $error = $enginesis->getLastError();
      $errorMessage = $error['message'] . ': ' . $error['extended_info'];
    } else {
      $items = count($serverResponse);
      if ($items > 0) {
        for ($item = 0; $item < $items; $item += 1) {
          $gameInfo = $serverResponse[$item];
          echo(formatGameCard($gameInfo));
          echo("\n");
        }
      } else {
        $errorMessage = "There are no games matching <em>$search</em>. Try again later or try a different search.";
      }
    }
  } else {
      $errorMessage = "The service appears to be offline at the moment. Please try again later or contact us for assistance.";
  }
  if ($errorMessage != '') {
    $errorMessage = "<p class=\"p-5 text-error\">$errorMessage</p>\n";
    echo($errorMessage);
  }
  ?>
        </div>
    </div>
    <?php
}

function buildFavoriteGamesSection() {
    ?>
    <div id="section-cards" class="px-0 py-2">
        <div class="row w-auto">
            <div class="card card-dark">
                <h4 class="my-1 py-1">Your favorite games</h4>
            </div>
        </div>
        <div class="row gy-2">
          <p class="p-4">You do not have any favorite games. Try some of our games and if you like them click the heart 
          <img src="/images/favorite-button-off-196.png" style="height: 25px; width: 25px;"/>  
          icon to add them to your favorites.</p>
        </div>
    </div>
    <?php
}
