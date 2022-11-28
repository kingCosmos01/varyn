<?php /** Experimental page.
 * Use this page for testing and proof of concept ideas.
 */
require_once('../../services/common.php');
processSearchRequest();
$page = 'home';
$pageTitle = 'Experimental test page';
$pageDescription = 'Use this page for conducting tests and experiments.';

processTrackBack();
$showSubscribe = getPostOrRequestVar('s', '0');
include_once(VIEWS_ROOT . 'header.php');
?>
<div class="container">
    <div class="card m-4">
        <div class="card-header">
            <h3 class="card-title">Experiments</h3>
        </div>
        <div class="card-body">
            <p>There is nothing to show here right now.</p>
        </div>
        <div class="card-footer">
            <p>Check back later.</p>
        </div>
    </div>
</div>
<?php
include_once(VIEWS_ROOT . 'footer.php');
?>
<script src="./index.js"></script>
</body>
</html>