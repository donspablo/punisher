<?php


function preRequest()
{
    global $toSet;
    $toSet[CURLOPT_USERAGENT] = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows CE; IEMobile 6.1)';
}
