<?php

/*
input:
<span class="zoom">
<img alt="Four Fields"
    src="https://launch-prototype.sent67.com/sites/launch/content/U1/eng/images/standard/FourFields.png"
     style="width:100%" />
</span>
*/
function modifyZoomImageGetAlt($text)
{
    $find_start = '<img alt="';
    $find_end = '"';
    $pos_start = strpos($text, $find_start) + strlen($find_start);
    $pos_end = strpos($text, $find_end, $pos_start);
    $length = $pos_end - $pos_start;
    $alt = substr($text, $pos_start, $length);
    writeLogAppend('modifyZoomImageGetAlt', $alt);
    return $alt;
}

function modifyZoomImageGetImage($text)
{
    $find_start = 'src="';
    $find_end = '"';
    $pos_start = strpos($text, $find_start) + strlen($find_start);
    $pos_end = strpos($text, $find_end, $pos_start);
    $length = $pos_end - $pos_start;
    $image = substr($text, $pos_start, $length);
    return $image;
}
