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
<style>
    div.exp {
        display: flex;
        flex-direction: row;
        justify-content: center;
    }
    div.exp.ul {
        max-inline-size: max-content;
        margin-inline: auto;
    }
    #exp > * {
        flex: 1 1 33%;
        padding: 2rem;
    }
</style>
<div class="container marketing">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">Experiments</h3>
        </div>
    </div>
    <div>
        This <code>div</code> demonstrates using flex-box arrangement of 3 evenly spaced elements:
    </div>
    <div id="exp" class="exp">
        <div id="section-side">
            <p style="text-align: center;"><strong>1</strong></p>
            <p>This is an area where we explain things about what is going on with this selection of games.</p>
        </div>
        <div id="section-1">
            <p style="text-align: center;"><strong>2</strong></p>
            <p>This is a list of games.</p>
            <ul>
                <li>Game 1</li>
                <li>Game 2</li>
                <li>Game 3</li>
                <li>Game 4</li>
            </ul>
        </div>
        <div id="section-2">
            <p style="text-align: center;"><strong>3</strong></p>
            <p>This is another list of games.</p>
            <ul>
                <li>Game 5</li>
                <li>Game 6</li>
                <li>Game 7</li>
                <li>Game 8</li>
            </ul>
        </div>
    </div>
</div>
<?php
include_once(VIEWS_ROOT . 'footer.php');
?>
<script src="./index.js"></script>
</body>
</html>