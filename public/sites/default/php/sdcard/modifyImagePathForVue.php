<?php

function modifyImagePathForVue($text, $p)
{
    $image_types = array('.png', '.jpg', '.svg');
    $find = '<img';
    $count = substr_count($text, $find);
    $pos_start = 0;
    for ($i = 0; $i < $count; $i++) {
        $pos_image_start = strpos($text, $find, $pos_start);
        $pos_image_end = strpos($text, '>', $pos_start);
        $image_length = $pos_image_end - $pos_image_start;
        $img_link = substr($text, $pos_image_start, $image_length);
        $pos_src_start = strpos($img_link, 'src="') + 5;
        $pos_src_end = strpos($img_link, '"',  $pos_src_start);
        $src_length =  $pos_src_end - $pos_src_start;
        $src = substr($img_link, $pos_src_start, $src_length);
        writeLogAppend('modifyImagePath-18', $src);
        $pos_start = $pos_image_end;
    }
    return $text;
}
