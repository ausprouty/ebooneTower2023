<?php

/*
string(38) "@/assets/images/standard/look-back.png"
string(38) "@/assets/images/standard/look-back.png"
string(38) "@/assets/images/standard/look-back.png"
string(38) "@/assets/images/standard/look-back.png"
string(36) "@/assets/images/standard/look-up.png"
string(41) "@/assets/images/standard/look-forward.png"
string(53) "@/assets/images/eng/standard/TransferableConcepts.png"
string(50) "@/assets/eng/tc/transferable-concepts-image-11.png"
string(51) "@/assets/eng/tc//transferable-concepts-image-12.jpg"
string(51) "@/assets/eng/tc//transferable-concepts-image-13.jpg"
string(50) "@/assets/eng/tc/transferable-concepts-image-22.png"
string(69) "@/assets/images/eng/custom/Web-satisfied-trust-facts-not-feelings.jpg"

*/

function modifyImagePathForVue($text, $p)
{
    $find = '<img';
    $count = substr_count($text, $find);
    if ($count == 0) {
        return $text;
    }
    $pos_start = 0;
    for ($i = 0; $i < $count; $i++) {
        $pos_image_start = strpos($text, $find, $pos_start);
        $needle = '>';
        $pos_image_end = strpos($text, $needle, $pos_image_start);
        if ($pos_image_start !== false) {
            $image_length = $pos_image_end - $pos_image_start;
            $img_link = substr($text, $pos_image_start, $image_length);
            $needle = 'src="';
            $pos_src_start = strpos($img_link, $needle) + 5;
            $needle = '"';
            $pos_src_end = strpos($img_link, $needle,  $pos_src_start);
            $src_length =  $pos_src_end - $pos_src_start;
            $src = substr($img_link, $pos_src_start, $src_length);
            $needle = '@/assets/images';
            if (strpos($src, $needle) === false) {
                $needle = '@/assets/';
                $needle_sites = '/sites/';
                if (strpos($src, $needle) !== false) {
                    $new = str_replace('@/assets/', '@/assets/images/', $src);
                    $text = str_replace($src, $new, $text);
                }
                //  /sites/mc2/images/ribbons/back-ribbon-mc2.png
                elseif (strpos($src, $needle_sites) !== false) {
                    $old = '/sites/' . SITE_CODE;
                    $new = str_replace($old, '@/assets', $src);
                    $text = str_replace($src, $new, $text);
                } else {
                    writeLogAppend('ERROR--modifyImagePathForVue-48', $src);
                }
            }
        }
        $pos_start = $pos_image_end + 8;
    }
    return $text;
}
