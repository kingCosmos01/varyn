<?php
/**
 * PHP include file to help standardize and abstract our advertising providers.
 * Ad providers supported are:
 * 'google'
 * 'cpmstar'|'gsn'
 */
if ( ! isset($adProvider)) {
    $adProvider = 'google';
}
if ($adProvider == 'google') {
?>
        <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
        <ins class="adsbygoogle"
                style="display:block"
                data-ad-client="ca-pub-9118730651662049"
                data-ad-slot="5571172619"
                data-ad-format="auto"></ins>
        <script>
            (adsbygoogle = window.adsbygoogle || []).push({});
        </script>
<?php
} elseif ($adProvider == 'gsn' || $adProvider == 'cpmstar') {
?>
        <iframe src="<?php echo($webServer);?>/common/ad300.html" frameborder="0" scrolling="no" style="width: 300px; height: 250px; overflow: hidden; z-index: 9999; left: 0px; bottom: 0px; display: inline-block;"></iframe>
<?php
}
