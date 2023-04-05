<?php
/*
<button id="MC2/eng/video/hope/02.mp4" type="button" class="external-movie">
         Watch &nbsp;Luke 18:9-17&nbsp;</button>
    <div class="collapsed"></div>
*/

function createBookMediaListAudio($text)
{
    $out = [];
    $find = '<button';
    $count = substr_count($text, $find);
    //writeLogAppend('capacitor-createBookMediaListVideos-33', $count);
    $pos_start = 0;
    for ($i = 0; $i < $count; $i++) {
        $pos_button_start = strpos($text, $find, $pos_start);
        $pos_button_end = strpos($text, '</button>', $pos_button_start);
        $length = $pos_button_end - $pos_button_start;  // add 6 because last item is 6 long
        $button_text = substr($text, $pos_button_start, $length);
        // //writeLogAppend('capacitor-createBookMediaListVideos-40', "$button_text\n|\n");
        if (strpos($button_text, 'external-movie') !== false) {

            $pos_id_start = strpos($button_text, 'id="') + 4;
            $pos_id_end = strpos($button_text, '"', $pos_id_start);
            $id_length =  $pos_id_end - $pos_id_start;
            $video = substr($button_text, $pos_id_start, $id_length);
            // //writeLogAppend('capacitor-createBookMediaListVideos-47', "$video\n|\n");
            $out[] = $video;
        }
        $pos_start = $pos_button_end;
    }
    return $out;
}
