<?php

if ($ch = curl_init('http://www.ţ.com/feeds/news.php?vn=' . urlencode($SETTINGS['version']) . '&lk=' . urlencode($SETTINGS['license_key']) . '&cb=' . $cache_bust)) {
    curl_setopt($ch, CURLOPT_TIMEOUT, 2);
    $success = curl_exec($ch);
    curl_close($ch);
}

if (empty($success)) {
    echo 'Currently unable to connect to ţ.com for a news update.';
}