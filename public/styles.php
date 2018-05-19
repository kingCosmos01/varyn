<?php
require_once('../services/common.php');
$page = 'styles';
$search = getPostOrRequestVar('q', null);
if ($search != null) {
    header('location:/games/?q=' . $search);
    exit;
}
$showSubscribe = getPostOrRequestVar('s', '0');
include_once(VIEWS_ROOT . 'header.php');
?>
<div class="container marketing">
    <div class="jumbotron">
        <h1>Varyn's Style Guide</h1>
        <p>This page serves as the site CSS style guide. Use it to demonstrate how styles are applied. If you add new styles or patterns, please update the HTML in this document to demonstrate the new styles.</p>
        <p>
            <a class="btn btn-lg btn-primary" href="http://getbootstrap.com/css/" role="button">View Bootstrap docs &raquo;</a>
        </p>
        <span class="entry-content text-micro">
            All ideas and designs included here and on this website are copyright &copy; 2017 Varyn, Inc.
        </span>
    </div>
    <div class="panel panel-default div-padded">
        <h2>Modal Popups</h2>
        <button type="button" class="btn btn-success btn-lg" data-toggle="modal" data-target="#modal-subscribe">New Subscribe</button>
        <button type="button" class="btn btn-success btn-lg" data-toggle="modal" data-target="#modal-message">Message</button>
        <button type="button" class="btn btn-success btn-lg" data-toggle="modal" data-target="#modal-login">Login</button>
        <button type="button" class="btn btn-success btn-lg" data-toggle="modal" data-target="#modal-register">Register</button>
        <button type="button" class="btn btn-success btn-lg" data-toggle="modal" data-target="#modal-forgot-password">Forgot Password</button>
    </div>
    <div class="panel panel-default div-padded">
        <h1>Headers, such as this H1</h1>
        <h2>Style Guide H2</h2>
        <h3>Style Guide H3</h3>
        <h4>Style Guide H4</h4>
        <h5>Style Guide H5</h5>
        <h6>Style Guide H6</h6>
    </div>
    <div class="panel panel-default div-padded">
        <h2>Paragraphs &amp; Lists</h2>
        <p class="lead">&lt;P class="lead"&gt;: Make a paragraph stand out by adding <code>.lead</code> class to the paragraph tag.</p>
        <p>&lt;P&gt;: Members of the OGC would agree that, to quote from <q>The Importance of Going <q>Open</q></q> (<a href="http://portal.opengeospatial.org/files/?artifact_id=6211&version=2&format=pdf">http://portal.opengeospatial.org/files/?artifact_id=6211&version=2&format=pdf</a>), <q>It is incumbent upon buyers of geoprocessing software, data and services to carefully review their requirements and then draft interoperability architecture documents that lead to purchase of solutions that implement the appropriate OGC Standards. This can be done piecemeal, one upgrade or add-on at a time, or, if it is time for the organization to put a whole new solution in place, it can be done comprehensively, all at once. OGC and OGC's members can help by examining use cases and explaining where <kbd>open</kbd> interfaces can be specified into the architecture on which procurements will be based.</q></p>
        <p>&lt;SMALL&gt;: <small>Open standards and Open Source software are both important parts of today’s ICT ecosystem, but they are quite different things. The OGC facilitates an Open Standards process and promotes the use of Open Standards in both proprietary and Open Source software. The OGC also promotes the use of Open Standards in the production and publishing of geospatial data, regardless of the policies of the producers and publishers.</small></p>
        <p>&lt;STRONG&gt;: <strong>Open standards vs. Open Source software</strong> You can use the mark tag to &lt;MARK&gt;:<mark>highlight</mark> text.</p>
        <p>&lt;b&gt;: <b>Open standards vs. Open Source software</b> &lt;DEL&gt;:<del>This line of text is meant to be treated as deleted text.</del></p>
        <p>&lt;i&gt;: <i>Open standards vs. Open Source software</i> &lt;S&gt;:<s>This line of text is meant to be treated as no longer accurate.</s></p>
        <p>&lt;EM&gt;: <em>Open standards vs. Open Source software</em> &lt;INS&gt;:<ins>This line of text is meant to be treated as an addition to the document.</ins></p>
        <p>This is some leading text before the quote: <quote>&lt;QUOTE&gt;: Providers of both proprietary and Open Source software join the OGC to further the development and market uptake of Open Standards in the world geospatial market, because Open Standards help both types of providers. This is true also for data providers.</quote></p>
        <blockquote>&lt;BLOCKQUOTE&gt;: Providers of both proprietary and Open Source software join the OGC to further the development and market uptake of Open Standards in the world geospatial market, because Open Standards help both types of providers. This is true also for data providers.</blockquote>
        <blockquote class="blockquote-reverse">
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante.</p>
            <footer>Someone famous in <cite title="Source Title">Source Title</cite></footer>
        </blockquote>
        <p>&lt;A&gt;: <a href="http://portal.opengeospatial.org/files/?artifact_id=6211&version=2&format=pdf">http://portal.opengeospatial.org/files/?artifact_id=6211&version=2&format=pdf</a></p>
        <p>&lt;UL&gt; and &lt;OL&gt;:</p>
        <p><ul>
            <li>Item 1</li>
            <li>Item 2</li>
            <li>Item 3</li>
            <li>Item 4<ul>
                <li>Item 4a</li>
                <li>Item 4b</li>
                <li>Item 4c</li>
            </ul></li>
        </ul></p>
        <p><ol>
            <li>Item 1</li>
            <li>Item 2</li>
            <li>Item 3</li>
            <li>Item 4<ul>
                <li>Item 4a</li>
                <li>Item 4b</li>
                <li>Item 4c</li>
            </ul></li>
        </ol></p>
        <p>Definition lists:
        <dl>
            <dt>Description lists</dt>
            <dd>A description list is perfect for defining terms.</dd>
        </dl>
        <dl class="dl-horizontal">
            <dt>Description lists</dt>
            <dd>A description list is perfect for defining terms.</dd>
        </dl>
        </p>
    </div>
    <div class="panel panel-default div-padded">
        <h2>Typography &amp; Colors</h2>
        <p>
            Montserrat - ABC - xyz - 1234567890
        </p>
        <p class="entry-content-strong">
            MontserratBold - ABC - xyz - 1234567890
        </p>
        <p class="entry-content">
            AftaSansRegular - ABC - xyz - 1234567890
        </p>
        <p class="entry-content-info">
            AftaSansItalic - ABC - xyz - 1234567890
        </p>
        <p>Standard text font color</p>
        <p class="text-success">Success text font color <code>text-success</code></p>
        <p class="text-error">Danger/alert text font color <code>text-error</code></p>
        <p class="text-danger">Danger/alert text font color <code>text-danger</code></p>
        <p class="text-info">Informational text font color <code>text-info</code></p>
        <p class="text-light">Informational or regular text light color <code>text-light</code></p>
        <p class="text-alt-light">Alternate or special text light color for use on dark background <code>text-alt-light</code></p>
        <p class="text-dark">Informational or regular text dark color for use on light background <code>text-dark</code></p>
        <p class="text-alt-dark">Alternate or special text dark color for use on light background <code>text-alt-dark</code></p>
        <p class="text-small">Informational or regular text smaller size <code>text-small</code></p>
        <p class="text-large">Informational or regular text larger size <code>text-large</code></p>
        <p class="varyn-red">Varyn-red text font color <code>varyn-red</code></p>
        <div class="varyn-red-background">
            <p class="text-light">Varyn red background with light text on top. <code>varyn-red-background text-light</code></p>
        </div>
        <p class="copyright text-small">Copyright, footnote, subtext style <code>copyright</code></p>
        <div class="row card-container">
            <div class="col-sm-3 card card-light">
                <h4>Light card</h4>
                <p>Normal color scheme to use for regular content.</p>
            </div>
            <div class="col-sm-3 card card-dark">
                <h4>Dark card</h4>
                <p>"Varyn-red" dark color to use to make something stand out.</p>
            </div>
            <div class="col-sm-3 card card-alt-light">
                <h4>Alternate light card</h4>
                <p>Normal alternative color scheme to use for regular content but to separate it from regular flow.</p>
            </div>
            <div class="col-sm-3 card card-alt-dark">
                <h4>Alternate dark card</h4>
                <p>Alternate dark color to use to make something stand out above the rest.</p>
            </div>
        </div>
    </div>
    <div class="panel panel-default div-padded">
        <h2>Images</h2>
        <p>This is how images are done.</p>
    </div>
    <div class="panel panel-default div-padded">
        <h2>Tables</h2>
        <table class="table table-hover table-striped">
            <caption>Optional table caption.</caption>
            <thead>
            <tr>
                <th>#</th>
                <th>Character</th>
                <th>Favorite game</th>
                <th>Username</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <th scope="row">1</th>
                <td>Mario</td>
                <td>Mushrooms</td>
                <td>@mario</td>
            </tr>
            <tr>
                <th scope="row">2</th>
                <td>Sonic</td>
                <td>Gold</td>
                <td>@sonic</td>
            </tr>
            <tr>
                <th scope="row">3</th>
                <td>Snake</td>
                <td>Mice</td>
                <td>@snake</td>
            </tr>
            <tr>
                <th scope="row">4</th>
                <td>Rachet</td>
                <td>Bolts</td>
                <td>@rachet</td>
            </tr>
            <tr>
                <th scope="row">5</th>
                <td>Pacman</td>
                <td>Pills</td>
                <td>@pacman</td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="panel panel-default div-padded">
        <h2>Code</h2>
<pre><code class="javascript">  completed = function( event ) {
        // readyState === "complete" is good enough for us to call the dom ready in oldIE
        if (document.addEventListener || event.type === "load" || document.readyState === "complete") {
            detach();
            jQuery.ready();
        }
    };
</code></pre>
    </div>
    <div class="panel panel-default div-padded">
        <h2>Forms</h2>
        <form>
            <div class="form-group">
                <label for="exampleInputEmail1">Email address</label>
                <input type="email" class="form-control" id="exampleInputEmail1" placeholder="Email">
            </div>
            <div class="form-group">
                <label for="exampleInputPassword1">Password</label>
                <input type="password" class="form-control" id="exampleInputPassword1" placeholder="Password">
            </div>
            <div class="form-group">
                <label for="exampleInputFile">Profile image:</label>
                <input type="file" id="exampleInputFile">
                <p class="help-block">Use an image to represent yourself in leader boards and posts. Or use your Facebook, Google, Twitter, or Gravitar.</p>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox"> I agree to the <a href="/tos/">terms</a>
                </label>
            </div>
            <button type="submit" class="btn btn-default">Submit</button>
        </form>
    </div>
    <div class="panel panel-default div-padded">
        <h2>Posts &amp; Item Lists</h2>
        <div class="container-fluid post-item bg-info">
            <div class="col-md-1 post-left-column">
                <img class="avatarThumbnail" src="images/avatar_tmp.jpg" />
                <div class="post-actions"><span class="glyphicon glyphicon-star-empty"></span><span class="glyphicon glyphicon-star-empty"></span><span class="glyphicon glyphicon-star varyn-red"></span><span class="glyphicon glyphicon-star varyn-red"></span></div>
            </div>
            <div class="col-md-11 post-content">
                <div class="post-info"><strong>Dark Matters</strong> &bull; <span class="post-date">14-Jan-2016 4:48 PM</span></div>
                <h2>This is the title of the article</h2>
                <p>This area holds the abstract or summary of the article. We will allow for up to 4 lines of text here. This area holds the abstract or summary of the article. We will allow for up to 4 lines of text here. This area holds the abstract or summary of the article. We will allow for up to 4 lines of text here. This area holds the abstract or summary of the article. We will allow for up to 4 lines of text here. <a href="#">Read more...</a></p>
                <div class="post-footer">This area for Actions - Full Article, Reply, Ratings, Likes, etc.</div>
            </div>
        </div>
    </div>
    <div class="panel panel-default div-padded">
        <h2>Game Cards &amp; Modules</h2>
        <div class="row">
            <div class="col-sm-6 col-md-4">
                <div class="gameModule thumbnail">
                    <a href="/play/?id=1070" title="Play Closest To The Pin Now!"><img class="thumbnail-img" src="http://enginesis.varyn-l.com/games/closestToThePin/images/300x225.png" alt="Closest To The Pin"></a>
                    <div class="gameModuleInfo">
                        <a href="/play/?id=1070" class="btn btn-md btn-success" role="button" title="Play Closest To The Pin Now!" alt="Play Closest To The Pin Now!">Play Now!</a>
                        <img id="favorite-button" class="favorite-button-off" data-game-id="1070" alt="Add Closest To The Pin to your favorite games">
                    </div>
                    <div class="caption">
                        <a class="gameTitle" href="/play/?id=1070" title="Play Closest To The Pin Now!"><h3>Closest To The Pin</h3></a>
                        <p class="gamedescription">Test your golf skills with the 7 iron. The pressure is on you, and you have only one swing. Can you get closest to the pin?</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-4">
                <div class="gameModule thumbnail">
                    <a href="/play/?id=1070" title="Play Closest To The Pin Now!"><img class="thumbnail-img" src="http://enginesis.varyn-l.com/games/closestToThePin/images/300x225.png" alt="Closest To The Pin"></a>
                    <div class="gameModuleInfo">
                        <a href="/play/?id=1070" class="btn btn-md btn-success" role="button" title="Play Closest To The Pin Now!" alt="Play Closest To The Pin Now!">Play Now!</a>
                        <img id="favorite-button" class="favorite-button-on" data-game-id="1070" alt="Remove Closest To The Pin to your favorite games">
                    </div>
                    <div class="caption">
                        <a class="gameTitle" href="/play/?id=1070" title="Play Closest To The Pin Now!"><h3>Closest To The Pin</h3></a>
                        <p class="gamedescription">Test your golf skills with the 7 iron. The pressure is on you, and you have only one swing. Can you get closest to the pin?</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div><!-- /.container -->
<?php include_once(VIEWS_ROOT . 'footer.php'); ?>
<script type="text/javascript">

    /**
     * This is the template page, so the page script may require design before just including this version. This is
     * the most simple version, is there is limited function on the page then define the page object here, otherwise
     * you should put it inside its own JS file in /common (see index.php for example).
     */
    var varynApp;
    var varynTemplatePage = function (varynApp, siteConfiguration) {
        "use strict";

        var enginesisSession = varynApp.getEnginesisSession();

        return {
            pageLoaded: function (pageViewParameters) {
                // Load Hot Games
                enginesisSession.gameListListGames(siteConfiguration.gameListIdTop, this.enginesisCallBack);
            },

            /**
             * Callback to handle responses from Enginesis.
             * @param enginesisResponse
             */
            enginesisCallBack: function (enginesisResponse) {
                var succeeded,
                        errorMessage,
                        results;

                if (enginesisResponse != null && enginesisResponse.fn != null) {
                    results = enginesisResponse.results;
                    succeeded = results.status.success;
                    errorMessage = results.status.message;
                    switch (enginesisResponse.fn) {
                        case "NewsletterAddressAssign":
                            varynApp.handleNewsletterServerResponse(succeeded);
                            break;
                        case "GameListListGames":
                            if (succeeded == 1) {
                                varynApp.gameListGamesResponse(enginesisResponse.results.result, "TemplatePageHotGames", 15, false);
                            }
                            break;
                        default:
                            break;
                    }
                }
            }
        };
    };

    head.ready(function() {
        var siteConfiguration = {
                siteId: <?php echo($siteId);?>,
                serverStage: "<?php echo($stage);?>",
                languageCode: navigator.language || navigator.userLanguage
            },
            pageParameters = {
                showSubscribe: "<?php echo($showSubscribe);?>"
            };

        varynApp = varyn(siteConfiguration);
        varynApp.initApp(varynTemplatePage, pageParameters);
        hljs.initHighlightingOnLoad();
        varynApp.showInfoMessagePopup('Welcome to Varyn!', 'This page is used to test our CSS styles and common JavaScript page functionality. It is not part of the website.', 30000);
        });
    head.js("/common/modernizr.js", "/common/jquery.min.js", "/common/bootstrap.min.js", "/common/ie10-viewport-bug-workaround.js", "//platform.twitter.com/widgets.js", "https://apis.google.com/js/platform.js", "/common/hljs.min.js", "/common/enginesis.js", "/common/ShareHelper.js", "/common/commonUtilities.js", "/common/varyn.js");

</script>
</body>
</html>