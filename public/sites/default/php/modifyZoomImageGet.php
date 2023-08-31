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
    $find_start = 'alt="';
    if (strpos($text, $find_start) === false) {
        return null;
    }
    $find_end = '"';
    $pos_start = strpos($text, $find_start) + strlen($find_start);
    $pos_end = strpos($text, $find_end, $pos_start);
    $length = $pos_end - $pos_start;
    $alt = substr($text, $pos_start, $length);
    writeLogAppend('modifyZoomImageGetAlt',  $text . " > " . $alt);
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
    $message = "text is: " . $text . "\n And PosStart is: " . $pos_start . "\n And PosEng is: " . $pos_end .
        "length is: " . $length . "\n And imaget is: " . $image;
    //writeLogDebug('modifyZoomImageGetImage-31', $message);
    return $image;
}
