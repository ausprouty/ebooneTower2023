<?php
/*change 
<a class="bible-readmore" href="https://biblegateway.com/passage/?search=Matthew%202:19-23&amp;version=NIV">

//to <a class="bible-readmore" href="https://biblegateway.com/passage/?search=Matthew%202:19-&amp;version=NIV">
*/
function modifyLinksReadmoreBible($text)
{
    //writeLogDebug('modifyLinksReadmoreBibler-6', $text);
    $readmore = array();
    $readmore[] = '<a class="readmore"';
    $readmore[] = '<a class="bible-readmore"';

    foreach ($readmore as $find) {
        $length_find = strlen($find);
        $count = substr_count($text, $find);
        $pos_start = 1;
        for ($i = 1; $i <= $count; $i++) {
            $pos_start = strpos($text, $find, $pos_start);
            $pos_end = strpos($text, '</a>', $pos_start + $length_find);
            $length = $pos_end - $pos_start + 4;
            $old_link = substr($text, $pos_start, $length);
            if (strpos($text, '-') !== false) {
                $pos_href = strpos($old_link, 'href');
                $pos_dash = strpos($old_link, '-', $pos_href) + 1;
                $pos_amp = strpos($old_link, '&amp;', $pos_dash);
                $verse_len = $pos_amp - $pos_dash;
                $remove = substr($old_link, $pos_dash, $verse_len);
                $new_link = substr_replace($old_link, '', $pos_dash, $verse_len);
            }
            $message = $old_link . " -- " . $new_link;
            //writeLogAppend('readmoreLinksRepair-27', $message);
            $text = substr_replace($text, $new_link, $pos_start, $length);
            $pos_start = $pos_end;
        }
    }
    //writeLogDebug('modifyLinksReadmoreBible-28', $text);
    return $text;
}
