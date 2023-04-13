<?php

/*
Input is:
 <a href="/content/M2/eng/tc/tc01.html">
 Transferable Concept #1: How You Can Be Sure You Are A Christian
 </a>
*/
myRequireOnce('myGetPrototypeFile.php');
myRequireOnce('modifyLinksMakeRelative.php');

function  modifyLinksInternal($text, $find, $p)
{
    $template = myGetPrototypeFile('linkInternal.html');
    // $rand= random_int(0, 9999);
    $length_find = strlen($find);
    $count = substr_count($text, $find);
    $pos_start = 1;
    for ($i = 1; $i <= $count; $i++) {
        $return = 'Return' . $i;
        // find link
        $pos_link_start = strpos($text, $find, $pos_start);
        $pos_link_end = strpos($text, '">', $pos_link_start + $length_find);
        $content_length = $pos_link_end - $pos_link_start -  $length_find;
        $link = substr($text, $pos_link_start + $length_find, $content_length);
        // this line is unique to capacitor
        $link = str_replace('.html', '', $link);
        // end of unique
        $relative_link = modifyLinksMakeRelative($link);
        // find words
        $pos_words_start = $pos_link_end + 2;
        $pos_words_end = strpos($text, '</a>', $pos_words_start);
        $words_length = $pos_words_end -  $pos_words_start;
        $words = substr($text, $pos_words_start, $words_length);
        // find total length
        $length = $pos_words_end  - $pos_link_start + 4;

        $old = array(
            '[id]',
            '[link]',
            '[relative_link]',
            '[text]',
            '[return]',
        );
        $new = array(
            $i,
            $link,
            $relative_link,
            $words,
            $return,
        );
        $new_template = str_replace($old, $new, $template);
        writeLogAppend(' modifyLinksInternal-capacitor-141', $new_template);
        $text = substr_replace($text, $new_template, $pos_link_start, $length);
        $pos_start = $pos_words_end + 4;
    }
    return $text;
}
