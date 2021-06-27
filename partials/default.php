<?php

$requirements = array();

$phpVersion = ($tmp = strpos(PHP_VERSION, '-')) ? substr(PHP_VERSION, 0, $tmp) : PHP_VERSION;

if (!($ok = version_compare($phpVersion, '5', '>='))) {
    $error->add('Punisher requires at least PHP 5 or greater.');
}

$requirements[] = array(
    'name' => 'PHP version',
    'value' => $phpVersion,
    'ok' => $ok
);

if (!($ok = function_exists('curl_version'))) {
    $error->add('Punisher requires cURL/libcurl.');
}

$curlVersion = $ok && ($tmp = curl_version()) ? $tmp['version'] : 'not available';

$requirements[] = array(
    'name' => 'cURL version',
    'value' => $curlVersion,
    'ok' => $ok
);

?>
    <h2>Latest Punisher news...</h2>
    <iframe scrolling="no" src="<?= $self ?>?fetch=news" style="width: 100%; height:150px; border: 1px solid #ccc;"
            onload="setTimeout('updateLatestVersion()',1000);"></iframe>
    <br><br>

    <h2>Checking environment...</h2>
    <ul class="green">
        <?php

        # Print requirements
        foreach ($requirements as $li) {
            echo "<li>{$li['name']}: <span class=\"bold" . (!$li['ok'] ? ' error-color' : '') . "\">{$li['value']}</span></li>\r\n";
        }

        ?>
    </ul>
<?php

if ($error->hasMsg()) {
    echo '<p><span class="bold error-color">Environment check failed</span>. You will not be able to run Punisher until you fix the above issue(s).</p>';
} else {
    echo '<p><span class="bold ok-color">Environment okay</span>. You can run Punisher on this server.</p>';
}

$acpVersion = ADMIN_VERSION;
$proxyVersion = isset($SETTINGS['version']) ? $SETTINGS['version'] : 'unknown - pre 1.0';

$javascript = "function updateLatestVersion(response) {document.getElementById('current-version').innerHTML = '<img src=\"http://www.ţ.com/feeds/proxy-version.php?cb=" . $cache_bust . "\" border=\"0\" alt=\"version\" />';}";

$output->addJavascript($javascript);

?>
    <br>
    <h2>Checking script versions...</h2>
    <ul class="green">
        <li>Control Panel version: <b><?= $acpVersion ?></b></li>
        <li>Punisher version: <b><?= $proxyVersion ?></b></li>
        <li>Latest version: <span class="bold" id="current-version">unknown</span></li>
    </ul>
<?php

function forCompare($val)
{
    return str_replace(' ', '', $val);
}

if ($proxyVersion != 'unknown - pre 1.0' && version_compare(forCompare($acpVersion), forCompare($proxyVersion), '>')) {
    echo "<p><span class=\"bold error-color\">Note:</span> Your settings file needs updating. Use the <a href=\"{$self}?settings\">Edit Settings</a> page and click Update.</p>";
}


$output->addFooterLinks('Punisher support forum at Proxy.org', 'http://proxy.org/forum/punisher-proxy/');
