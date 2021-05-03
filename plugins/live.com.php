<?php


function preParse($html, $type)
{
    if (stripos($html, 'JavaScript required to sign in')) {
        header("Location: " . proxyURL('https://mid.live.com/si/login.aspx'));
        exit;
    }
    return $html;
}
