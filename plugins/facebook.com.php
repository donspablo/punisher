<?php


$options['stripJS'] = true;
$options['allowCookies'] = true;

function preRequest()
{
    global $URL;
    if ($URL['host'] != 'm.facebook.com') {
        $URL['host'] = preg_replace('/((www\.)?facebook\.com)/', 'm.facebook.com', $URL['host']);
        $URL['href'] = preg_replace('/\/\/((www\.)?facebook\.com)/', '//m.facebook.com', $URL['href']);
    }
}
