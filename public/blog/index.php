<?php
require_once('../../services/common.php');
require_once('../../views/sections.php');
processSearchRequest();
require_once('../../services/blog.php');
$showSubscribe = getPostOrRequestVar('s', '0');
$page = 'blog';
$topGamesListId = 5;

$topicId = getPostOrRequestVar('tid', 0);
// Get 3 most recent topics
$topicList = $blog->getTopicList('', null, null, 1, 3);
if (empty($topicList)) {
    $errorCode = $enginesis->getLastErrorCode();
    $errorMessage = $enginesis->getLastErrorDescription();
} elseif ($topicId == 0) {
    $topicId = $topicList[0]->topic_id;
}
$blog->setConferenceTopic($topicId);
$pageTitle = $blog->getTopicTitle($topicId);
if (empty($pageTitle)) {
    $pageTitle = 'Varyn Blog';
}
$pageDescription = $blog->getTopicAbstract($topicId);
if (empty($pageDescription)) {
    $pageDescription = 'Read the latest news and hot game topics here at Varyn.com. We offer great insights to help you get the most out of your online games and entertainment experience.';
}
include_once(VIEWS_ROOT . 'header.php');
?>
<div class="container">
    <div class="row conf-topic-container">
        <div id="conf-topic" class="col-sm-8">
            <?php echo($blog->getTopicContentAsHTML($topicId));?>
        </div>
        <div id="conf-sidebar" class="col-md-4">
            <div id="boxAd300" class="ad300">
            <?php
            $adProvider = 'cpmstar';
            include_once(VIEWS_ROOT . 'ad-spot.php');
            ?>
            </div>
            <p id="ad300-subtitle">Advertisement</p>
            <?php echo($blog->getCurrentPromo());?>
            <div id="conf-nav" class="conf-nav">
                <?php echo($blog->getCurrentTopicListPreview($topicList, $topicId, 2, [$topicId]));?>
            </div>
        </div>
    </div>
    <?php echo($blog->getCurrentTopicRepliesPanel());?>
</div>
<div class="container">
    <?php buildGamesSection($topGamesListId, 'Hot games'); ?>
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
<script src="/common/varynBlog.js"></script>
</body>
</html>
