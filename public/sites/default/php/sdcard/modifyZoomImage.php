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
myRequireOnce('copyFilesForSDApp.php', 'sdcard');
myRequireOnce('writeLog.php');


function modifyZoomImage($text, $p)
{
    writeLogDebug('modifyZoomImage-sd-27', $text);
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
        $length_span = $pos_end - $pos_start + strlen($find_end);
        $length_words = $pos_end - $pos_start;
        $old = substr($text, $pos_start, $length_words);
        writeLogAppend('modifyZoomImage-sd-43', $old);
        $alt =  modifyZoomImageGetAlt($old);
        $source_image = modifyZoomImageGetImage($old);
        $regular_image = modifyZoomImageGetImageRegular($source_image, $p);
        $zoom_image = modifyZoomImageGetImageZoom($source_image);

        // replace placeholders with values
        $placeholders = array('[regular_image]', '[zoom_image]', '[alt]', '[source_image]');
        $values = array($regular_image, $zoom_image, $alt, $source_image);
        $new = str_replace($placeholders, $values, $template);
        $text = substr_replace($text, $new, $pos_start, $length_span);
    }
    return $text;
}

/*
  input: https://launch-prototype.sent67.com/sites/launch/content/U1/eng/images/standard/FourFields.png

  output: /images/zoom/standard/FourFields.png
      also copy this to 
*/

function  modifyZoomImageGetImageRegular($image, $p)
{
    $find = '/images/';
    $pos_start = strpos($image, $find) + strlen($find);
    $raw = substr($image, $pos_start);
    $output = '/images/zoom/' . $raw;
    modifyZoomImageCopyImage($image, $output, $p);
    return $output;
}
function   modifyZoomImageGetImageZoom($image)
{
    $find = '/images/';
    $pos_start = strpos($image, $find) + strlen($find);
    $raw = substr($image, $pos_start);
    $output = '/images/zoom/' . $raw;
    return $output;
}
//root edit is sites/' . SITE_CODE . '/content/'
function  modifyZoomImageCopyImage($image_source, $image_destination, $p)
{
    $destination = ROOT_SDCARD . 'public' . $image_destination;
    $bad = "@/assets/images/";
    $good = ROOT_EDIT_CONTENT . $p['country_code'] . "/";
    $find_image = str_replace($bad, $good, $image_source);
    if (file_exists($find_image)) {
        writeLogAppend('modifyZoomImageCopyImage-91', $image_source . "\n" .  $find_image . "\n"  . $destination . "\n\n");
        copyFilesForSDApp($find_image, $destination, 'zoom');
    } else {
        writeLogAppend('modifyZoomImageCopyImage-94-ERROR', $image_source . "\n" .  $find_image . "\n"  . $destination . "\n\n");
    }
}
