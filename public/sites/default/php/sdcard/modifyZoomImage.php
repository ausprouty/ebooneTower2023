<?php

/*
Input: 

<span class="zoom">
<img alt="Four Fields"
    src="https://launch-prototype.sent67.com/sites/launch/content/U1/eng/images/standard/FourFields.png"
     style="width:100%" />
</span>

Output:

<vue-image-zoomer
     regular="/images/zoom/standard/FourFields.png" 
     zoom="/images/zoom/standard/FourFields.png" :zoom-amount="3" img-class="img-fluid" alt="FourFields">
    <img src=https://launch-prototype.sent67.com/sites/launch/content/U1/eng/images/standard/FourFields.png" img-class="img-fluid" />
</vue-image-zoomer>
*/

myRequireOnce('modifyZoomImageGet.php');


function modifyZoomImage($text)
{
    $template = '   
    <vue-image-zoomer
    regular="[regular_image]" 
    zoom="[zoom_image]" :zoom-amount="3" img-class="img-fluid" alt="[alt]">
    <img src=[source_image]" img-class="img-fluid" />
    </vue-image-zoomer>';

    $find_start = '<span class="zoom">';
    $find_end = '</span>';
    $count = substr_count($text, $find_start);
    for ($i = 0; $i < $count; $i++) {
        $pos_start = strpos($text, $find_start);
        $pos_end = strpos($text, $find_end, $pos_start);
        $length = $pos_end - $pos_start + strlen($find_end);
        $old = substr($text, $pos_start, $length);
        $alt =  modifyZoomImageGetAlt($old);
        $source_image = modifyZoomImageGetImage($old);
        $regular_image = modifyZoomImageGetImageRegular($old);
        $zoom_image = modifyZoomImageGetImageZoom($old);

        // replace placeholders with values
        $placeholders = array('[regular_image]', '[zoom_image]', '[alt]', '[source_image]');
        $values = array($regular_image, $zoom_image, $alt, $source_image);
        $new = str_replace($placeholders, $values, $template);
        $text = substr_replace($text, $new, $pos_start, $length);
    }
    return $text;
}

/*
  input: https://launch-prototype.sent67.com/sites/launch/content/U1/eng/images/standard/FourFields.png

  output: /images/zoom/standard/FourFields.png
      also copy this to 
*/

function  modifyZoomImageGetImageRegular($image)
{
    $find = '/images/';
    $pos_start = strpos($image, $find) + strlen($find);
    $raw = substr($image, $pos_start);
    $output = '/images/zoom/' . $raw;
    return $output
}
function   modifyZoomImageGetImageZoom($image)
{
    $find = '/images/';
    $pos_start = strpos($image, $find) + strlen($find);
    $raw = substr($image, $pos_start);
    $output = '/images/zoom/' . $raw;
    return $output
}
