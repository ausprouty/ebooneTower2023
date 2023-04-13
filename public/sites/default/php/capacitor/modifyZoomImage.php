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
myRequireOnce('copyFilesForCapacitor.php');
myRequireOnce('writeLog.php');


function modifyZoomImage($text, $p)
{
    $progress = new stdClass;
    //writeLogDebug('capacitor-modifyZoomImage-sd-27', $text);
    $template = '   
    <div class="zoom-image">
    <vue-image-zoomer
    regular="[regular_image]" 
    zoom="[zoom_image]" :zoom-amount="3" img-class="img-fluid" alt="[alt]">
    <img src="[source_image]" img-class="img-fluid" />
    </vue-image-zoomer>
    </div>';

    $find_start = '<span class="zoom">';
    $find_end = '</span>';
    $count = substr_count($text, $find_start);
    for ($i = 0; $i < $count; $i++) {
        $pos_start = strpos($text, $find_start);
        $pos_end = strpos($text, $find_end, $pos_start);
        $length_span = $pos_end - $pos_start + strlen($find_end);
        $length_words = $pos_end - $pos_start;
        $old = substr($text, $pos_start, $length_words);
        //writeLogAppend('modifyZoomImage-sd-43', $old);
        $alt =  modifyZoomImageGetAlt($old);
        $source_image = modifyZoomImageGetImage($old);
        $regular_image = modifyZoomImageGetImageRegular($source_image, $p);
        $zoom_image = modifyZoomImageGetImageZoom($source_image, $p);
        $new_progress = copyFilesForCapacitor($source_image, $zoom_image, 'modifyZoomImage-53');
        $progress = progressMergeObjects($progress, $new_progress, ' modifyZoomImage-54');
        $new_progress = copyFilesForCapacitor($source_image, $regular_image, 'modifyZoomImage-54');
        $progress = progressMergeObjects($progress, $new_progress,  'modifyZoomImage-56');
        // replace placeholders with values
        $placeholders = array('[regular_image]', '[zoom_image]', '[alt]', '[source_image]');
        $values = array($regular_image, $zoom_image, $alt, $source_image);
        $new = str_replace($placeholders, $values, $template);
        $text = substr_replace($text, $new, $pos_start, $length_span);
    }
    //writeLogDebug('capacitor-modifyZoomImage-sd-57', $text);
    $out = new stdClass;
    $out->text = $text;
    $out->progress = $progress;
    return $out;
}

/*
  input: @/assets/sites/mc2/content/M2/eng/multiply2/Period1.png 
  output: /images/zoom/sites/mc2/content/M2/eng/multiply2/Period1.png"
      also copy this to 
*/

function  modifyZoomImageGetImageRegular($image, $p)
{
    $find = '@/assets/';
    $pos_start = strpos($image, $find) + strlen($find);
    $raw = substr($image, $pos_start); //   sites/mc2/content/M2/eng/multiply2/Period1.png 
    $to = '/images/zoom/' . $raw;
    writeLogAppend('capacitor-modifyZoomImageGetImageRegular-82', " $image => $to");
    $source = ROOT_EDIT . $raw;
    $dir = dirStandard('zoom_root', DESTINATION,  $p, $folders = null, $create = true);

    $destination = $dir . $raw;
    writeLogAppend('capacitor-modifyZoomImageGetImageRegular-86', " $source => $destination");
    copyFilesForCapacitor($source, $destination, 'modifyZoomImageGetImageRegular');
    return $to;
}
function   modifyZoomImageGetImageZoom($image, $p)
{
    $find = '@/assets/';
    $pos_start = strpos($image, $find) + strlen($find);
    $raw = substr($image, $pos_start);
    $to = '/images/zoom/' . $raw;
    // writeLogAppend('capacitor-modifyZoomImageGetImageRegular-85', " $image => $to");
    return $to;
}
