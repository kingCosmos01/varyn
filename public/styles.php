<?php
    require_once('../services/common.php');
    $page = 'styles';
    $search = getPostOrRequestVar('q', null);
    if ($search != null) {
        header('location:/allgames.php?q=' . $search);
        exit;
    }
    $showSubscribe = getPostOrRequestVar('s', '0');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Varyn: Great games you can play anytime, anywhere</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Access-Control-Allow-Origin" content="*">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta http-equiv="cache-control" content="max-age=0" />
    <meta http-equiv="cache-control" content="no-cache" />
    <meta http-equiv="expires" content="0" />
    <meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT" />
    <meta http-equiv="pragma" content="no-cache" />
    <meta name="format-detection" content="telephone=no" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="mobile-web-app-capable" content="yes" />
    <meta name="description" content="Varyn makes games using technology that performs on the most popular platforms. Cross platform friendly technologies have created an opportunity to re-invent online games for an audience that moves seamlessly between desktop, tablet, and smart-phone.">
    <meta name="author" content="Varyn">
    <link href="/common/hljs.css" rel="stylesheet">
    <link href="/common/bootstrap.min.css" rel="stylesheet">
    <link href="/common/carousel.css" rel="stylesheet">
    <link href="/common/varyn.css" rel="stylesheet">
    <link rel="icon" href="/favicon.ico">
    <link rel="icon" type="image/png" href="/favicon-48x48.png" sizes="48x48"/>
    <link rel="icon" type="image/png" href="/favicon-196x196.png" sizes="196x196">
    <link rel="icon" type="image/png" href="/favicon-160x160.png" sizes="160x160">
    <link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96">
    <link rel="icon" type="image/png" href="/favicon-16x16.png" sizes="16x16">
    <link rel="icon" type="image/png" href="/favicon-32x32.png" sizes="32x32">
    <link rel="apple-touch-icon" href="/apple-touch-icon-60x60.png" sizes="60x60"/>
    <link rel="apple-touch-icon" href="/apple-touch-icon-72x72.png" sizes="72x72"/>
    <link rel="apple-touch-icon" href="/apple-touch-icon-76x76.png"/>
    <link rel="apple-touch-icon" href="/apple-touch-icon-76x76.png" sizes="76x76"/>
    <link rel="apple-touch-icon" href="/apple-touch-icon-114x114.png" sizes="114x114"/>
    <link rel="apple-touch-icon" href="/apple-touch-icon-120x120.png" sizes="120x120"/>
    <link rel="apple-touch-icon" href="/apple-touch-icon-152x152.png" sizes="152x152"/>
    <link rel="shortcut icon" href="/favicon-196x196.png">
    <meta property="fb:app_id" content="" />
    <meta property="fb:admins" content="726468316" />
    <meta property="og:title" content="Varyn: Great games you can play anytime, anywhere">
    <meta property="og:url" content="http://www.varyn.com">
    <meta property="og:site_name" content="Varyn">
    <meta property="og:description" content="Varyn makes games using technology that performs on the most popular platforms. Cross platform friendly technologies have created an opportunity to re-invent online games for an audience that moves seamlessly between desktop, tablet, and smart-phone.">
    <meta property="og:image" content="http://www.varyn.com/images/1200x900.png"/>
    <meta property="og:image" content="http://www.varyn.com/images/1024.png"/>
    <meta property="og:image" content="http://www.varyn.com/images/1200x600.png"/>
    <meta property="og:image" content="http://www.varyn.com/images/600x600.png"/>
    <meta property="og:image" content="http://www.varyn.com/images/2048x1536.png"/>
    <meta property="og:type" content="game"/>
    <meta name="twitter:card" content="photo"/>
    <meta name="twitter:site" content="@varyndev"/>
    <meta name="twitter:creator" content="@varyndev"/>
    <meta name="twitter:title" content="Varyn: Great games you can play anytime, anywhere"/>
    <meta name="twitter:image:src" content="http://www.varyn.com/images/600x600.png"/>
    <meta name="twitter:domain" content="varyn.com"/>
    <script src="/common/head.min.js"></script>
</head>
<body>
<?php include_once('common/header.php'); ?>
<div class="container marketing">
    <div class="jumbotron">
        <h1>Varyn's Style Guide</h1>
        <p>This page serves as the site CSS style guide. Use it to demonstrate how styles are applied. If you add new styles or patterns, please update the HTML in this document to demonstrate the new styles.</p>
        <p>
            <a class="btn btn-lg btn-primary" href="http://getbootstrap.com/css/" role="button">View Bootstrap docs &raquo;</a>
        </p>
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
        <p>Headers</p>
        <h1>Style Guide H1</h1>
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
    </div>
    <div class="panel panel-default div-padded">
        <h2>Images</h2>
    </div>
    <div class="panel panel-default div-padded">
        <h2>Tables</h2>
        <table class="table table-hover table-striped">
            <caption>Optional table caption.</caption>
            <thead>
            <tr>
                <th>#</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Username</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <th scope="row">1</th>
                <td>Mark</td>
                <td>Otto</td>
                <td>@mdo</td>
            </tr>
            <tr>
                <th scope="row">2</th>
                <td>Jacob</td>
                <td>Thornton</td>
                <td>@fat</td>
            </tr>
            <tr>
                <th scope="row">3</th>
                <td>Larry</td>
                <td>the Bird</td>
                <td>@twitter</td>
            </tr>
            <tr>
                <th scope="row">4</th>
                <td>Pete</td>
                <td>the Pistol</td>
                <td>@tpistolpete</td>
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
                    <input type="checkbox"> I agree to the <a href="/tos.php">terms</a>
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
                <div class="post-actions"><span class="glyphicon glyphicon-empty-star"></span></div>
            </div>
            <div class="col-md-11 post-content">
                <div class="help-block"><strong>Dark Matters</strong> &bull; <span class="post-date">14-Jan-2016 4:48 PM</span></div>
                <h2>This is the title of the article</h2>
                <p>This area holds the abstract or summary of the article. We will allow for up to 4 lines of text here. This area holds the abstract or summary of the article. We will allow for up to 4 lines of text here. This area holds the abstract or summary of the article. We will allow for up to 4 lines of text here. This area holds the abstract or summary of the article. We will allow for up to 4 lines of text here. <a href="#">Read more...</a></p>
                <div class="help-block">This area for Actions - Full Article, Reply, Ratings, Likes, etc.</div>
            </div>
        </div>
    </div>
</div><!-- /.container -->
<?php include_once('common/footer.php'); ?>
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

        });
    head.js("/common/modernizr.js", "/common/jquery.min.js", "/common/bootstrap.min.js", "/common/ie10-viewport-bug-workaround.js", "//platform.twitter.com/widgets.js", "https://apis.google.com/js/platform.js", "/common/hljs.min.js", "/common/enginesis.js", "/common/ShareHelper.js", "/common/commonUtilities.js", "/common/varyn.js");

</script>
</body>
</html>