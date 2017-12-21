<?php
/**
 * Blog settings and functions.
 * @author: jf
 * @date: 10/1/2017
 */
require_once('EnginesisBlog.php');
$conferenceId = 'varyn-1';
$topicId = 1;     // the conference topic id that represents the blog
$promotionId = 4; // the promotion id to show on blog pages

$blog = new EnginesisBlog($siteId, $conferenceId, $enginesis);
$blog->setPromotionId($promotionId);
