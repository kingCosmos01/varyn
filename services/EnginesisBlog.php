<?php
/**
 * Blog settings and functions to abstract and simplify working with Enginesis Conference API.
 * @author: jf
 * @date: 10/1/2017
 */
class EnginesisBlog
{
    private $siteId;
    private $conferenceId;
    private $conferenceInternalId;
    private $enginesisSession;
    private $conferenceData;
    private $conferenceTopicData;
    private $promotionId;
    private $promotionData;


    public function __construct ($siteId, $conferenceId = null, $enginesisSession = null) {
        $this->siteId = $siteId;
        $this->setEnginesisSession($enginesisSession);
        $this->setConference($conferenceId);
        $this->promotionId = null;
        $this->promotionData = null;
    }

    public function setConference($conferenceId) {
        $this->conferenceId = $conferenceId;
        $this->loadConference();
        $this->conferenceTopicData = null;
    }

    public function setPromotionId($promotionId) {
        $this->promotionId = $promotionId;
    }

    public function setConferenceTopic($topicId) {
        if ( ! $this->isConferenceTopicLoaded($topicId)) {
            $this->loadConferenceTopic($topicId);
        }
        return $this->conferenceTopicData != null;
    }

    public function isConferenceTopicLoaded($topicId) {
        return $this->conferenceTopicData != null && $this->conferenceTopicData->topic_id == $topicId;
    }

    public function setEnginesisSession($enginesisSession) {
        $this->enginesisSession = $enginesisSession;
    }

    public function getAssetRootPath($conferenceId = null) {
        if ($conferenceId != null && $conferenceId != $this->conferenceId) {
            $this->setConference($conferenceId);
        }
        $path = $this->enginesisSession->conferenceAssetRootPath($this->conferenceInternalId);
        return $path;
    }

    public function getConferenceTitle($conferenceId = null) {
        if ($conferenceId != null && $conferenceId != $this->conferenceId) {
            $this->setConference($conferenceId);
        }
        $conferenceTitle = $this->conferenceData->title;
        return $conferenceTitle;
    }

    public function getConferenceDescription($conferenceId = null) {
        if ($conferenceId != null && $conferenceId != $this->conferenceId) {
            $this->setConference($conferenceId);
        }
        $conferenceDescription = $this->conferenceData->description;
        return $conferenceDescription;
    }

    public function getTopicList($tags = null, $startDate = null, $endDate = null, $startItem = 1, $numItems = 5) {
        if ($this->conferenceId != null && $this->enginesisSession != null) {
            if ($tags == null) {
                $tags = '';
            }
            if ($startDate == null) {
                $startDate = '1970-01-01';
            }
            if ($endDate == null) {
                $endDate = '2036-12-31';
            }
            if ($startItem < 1) {
                $startItem = 1;
            }
            if ($numItems > 100) {
                $numItems = 100;
            }
            $results = $this->enginesisSession->conferenceTopicList($this->conferenceId, $tags, $startDate, $endDate, $startItem, $numItems);
        } else {
            $results = null;
        }
        return $results;
    }

    public function getTopicAttribute($attribute, $topicId = null) {
        $topicAttributeValue = null;
        $conferenceTopicData = $this->conferenceTopicData;
        if ($conferenceTopicData != null && ! is_array($conferenceTopicData)) {
            $conferenceTopicData = (array)$conferenceTopicData;
        }
        if ($topicId == null && $conferenceTopicData != null) {
            $topicAttributeValue = $conferenceTopicData[$attribute];
        } elseif ($this->isConferenceTopicLoaded($topicId)) {
            $topicAttributeValue = $conferenceTopicData[$attribute];
        } else {
            $this->setConferenceTopic($topicId);
            if ($this->isConferenceTopicLoaded($topicId)) {
                $topicAttributeValue = $this->getTopicAttribute($attribute, $topicId);
            }
        }
        return $topicAttributeValue;
    }

    public function getTopicTitle($topicId = null) {
        return $this->getTopicAttribute('topic_title', $topicId);
    }

    public function getTopicTags($topicId = null) {
        return $this->getTopicAttribute('tags', $topicId);
    }

    public function getTopicAbstract($topicId = null) {
        return $this->getTopicAttribute('abstract', $topicId);
    }

    public function getTopicContent($topicId = null) {
        return $this->getTopicAttribute('content', $topicId);
    }

    public function getTopicContentAsHTML($topicId = null) {
        return '<h1>' . $this->getTopicTitle($topicId) . '</h1>' . $this->renderContent($this->getTopicContent($topicId));
    }

    public function getCurrentTopicListPreview($topicList, $topicSelected = 1, $numberOfTopics = 2, $topicsNotToShow = array()) {
        $html = '';
        $header = '<div class="panel panel-primary"><div class="panel-heading"><h4 class="panel-title">Recent topics:</h4></div></div>';
        $topicNav = '';
        if ($topicList != null && count($topicList) > 0) {
            $countTopicsAvailable = count($topicList);
            if ( ! is_array($topicsNotToShow)) {
                $topicsNotToShow = [$topicsNotToShow];
            }
            if ($numberOfTopics < 1) {
                $numberOfTopics = $countTopicsAvailable;
            }
            $blogPage = '/blog/?tid=';
            $previousTopicId = null;
            $nextTopicId = null;
            for ($i = 0; $i < $countTopicsAvailable && $numberOfTopics > 0; $i ++) {
                $topic = $topicList[$i];
                $topicId = $topic->topic_id;
                if ($topicId != $topicSelected && ! in_array($topicId, $topicsNotToShow)) {
                    $html .= '<div class="conf-topic-preview"><h4><a href="' . $blogPage . $topicId . '">' . $topic->topic_title . '</a></h4><p>' . substr($topic->abstract, 0, 140) . '</p></div>';
                    $numberOfTopics --;
                } elseif ($topicId == $topicSelected) {
                    // determine next page and previous page fromt eh current page, but this assumes the current
                    // page is in the topic list.
                    if ($i == 0) {
                        $previousTopicId = $topicList[$countTopicsAvailable - 1]->topic_id;
                    } else {
                        $previousTopicId = $topicList[$i - 1]->topic_id;
                    }
                    if ($i == ($countTopicsAvailable - 1)) {
                        $nextTopicId = $topicList[0]->topic_id;
                    } else {
                        $nextTopicId = $topicList[$i + 1]->topic_id;
                    }
                }
            }
            if ($previousTopicId != null && $nextTopicId != null) {
                $nextButtonAttribute = ' onclick="location.href=\'' . $blogPage . $nextTopicId . '\';"';
                $previousButtonAttribute = ' onclick="location.href=\'' . $blogPage . $previousTopicId . '\';"';
                $topicNav = '<div class="text-center"><div class="btn-group" role="group" aria-label="Navigate topics"><button type="button" class="btn btn-secondary" ' . $previousButtonAttribute . '><span class=" glyphicon glyphicon-chevron-left"></span> Previous topic</button><button type="button" class="btn btn-secondary" ' . $nextButtonAttribute . '>Next topic <span class="glyphicon glyphicon-chevron-right"></span></span></button></div></div>';
            }
        } else {
            $html = '<div class="conf-topic-preview"><p>No topics available.</p></div>';

        }
        return $header . $html . $topicNav;
    }

    public function getCurrentTopicRepliesPanel($topicId = null) {
        $html = '';
        /*
        $numReplies = 7;
        if ($numReplies == 0) {
            $replyConditional = '<p>There are no replies. <button>Reply</button></p>';
        } elseif ($numReplies == 1) {
            $replyConditional = '<p>There is 1 reply. <button>Show</button> | <button>Reply</button></p>';
        } else {
            $replyConditional = '<p>There are ' . $numReplies . ' replies. <button>Show</button> | <button>Reply</button></p>';
        }
        $html = '<div class="row conf-replies-container">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">Replies</h3>
                </div>
            </div>' . $replyConditional . '</div>';
        */
        return $html;
    }

    public function promotionAssetURL($promotionItem) {
        $assetId = $promotionItem->promotion_item_thumb_img;
        if (strlen($assetId) < 1) {
            $assetId = $promotionItem->promotion_item_img;
        }
        if (startsWith($assetId, '/') || startsWith($assetId, 'http')) {
            $url = $assetId;
        } elseif (strlen($assetId) > 0) {
            $url = $this->enginesisSession->getServiceRoot() . 'sites/' . $this->siteId . '/promo/' . $promotionItem->promotion_id . '/' . $assetId;
        } else {
            $url = '';
        }
        return $url;
    }

    /**
     * Get an item we are promoting along side the blog and generate an HTML div for it.
     * @param $promotionId int identifies the promotion to use.
     * @return string html div tag
     */
    public function getCurrentPromo($promotionId = null) {
        if (empty($promotionId)) {
            $promotionId = $this->promotionId;
        }
        $queryDate = $this->enginesisSession->mySqlDate('now');
        $this->promotionData = $this->enginesisSession->promotionItemList($promotionId, $queryDate);
        if (is_array($this->promotionData) && count($this->promotionData) > 0) {
            if (count($this->promotionData) > 1) {
                $promotionItemIndex = rand(0, count($this->promotionData) - 1);
            } else {
                $promotionItemIndex = 0;
            }
            $promotionItem = $this->promotionData[$promotionItemIndex];
            $link = $promotionItem->promotion_item_link;
            $title = $promotionItem->promotion_item_title;
            $image = $this->promotionAssetURL($promotionItem);
            $description = $promotionItem->promotion_item_description;
        } elseif (is_object($this->promotionData)) {
            $link = $this->promotionData->promotion_item_link;
            $title = $this->promotionData->promotion_item_title;
            $image = $this->promotionData->promotion_item_img;
            $description = $this->promotionData->promotion_item_description;
        } else {
            $link = '/play/?gameid=1083';
            $title = 'Play Match Master 3000 Now!';
            $image = '//enginesis.varyn.com/games/MatchMaster3000/images/300x225.png';
            $description = 'You think you have a good memory? See how many levels you can master in our memory challenge to take the Match Master Crown!';
        }
        $html = '<div id="conf-promo" class="conf-promo"><a href="' . $link . '" title="' . $title . '"><img class="thumbnail-img" src="' . $image . '" alt="' . $title . '"></a><p>' . $description . '</p></div>';
        return $html;
    }

    // =================================================================================================================
    // Private Conference methods
    // =================================================================================================================

    private function loadConference() {
        if ($this->conferenceId != null && $this->enginesisSession != null) {
            $this->conferenceData = $this->enginesisSession->conferenceGet($this->conferenceId);
            // TODO: Handle error
            if ($this->conferenceData != null && isset($this->conferenceData->conference_id)) {
                $this->conferenceInternalId = $this->conferenceData->conference_id;
            }
        }
    }

    private function loadConferenceTopic($topicId) {
        if ($this->conferenceId != null && $this->enginesisSession != null && ! empty($topicId)) {
            $this->conferenceTopicData = $this->enginesisSession->conferenceTopicGet($this->conferenceId, $topicId);
            // TODO: Handle error
        }
    }

    private function renderContent($content) {
        require_once dirname(__FILE__) . '/lib/vendor/php-markdown/MarkdownEnginesis.inc.php';
        $markdownParser = new \Enginesis\MarkdownEnginesis;
        $markdownParser->setImagePath($this->getAssetRootPath());
        return $markdownParser->transform($content);
    }
}