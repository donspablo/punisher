<?php
define('mobilemail', proxyURL('http://m.yahoo.com/mail'));
if (stripos($toLoad, 'mail.yahoo.com')) {
    header('Location: ' . mobilemail);
    exit;
}
function preParse($html, $type)
{
    if ($type == 'html') {
        $html = preg_replace('#r/(m6|lk|l6|m7|m2|l4)#', mobilemail, $html);
    }
    return $html;
}
