<?php


function preParse($html, $type)
{
    if (stripos($html, 'loadingError')) {
        header("Location: " . proxyURL('http://mail.google.com/mail/?ui=html'));
        exit;
    }
    return $html;
}

function postParse(&$in, $type)
{
    $in = preg_replace('# style="padding-top:\d+px"#', '', $in);
    return $in;
}
