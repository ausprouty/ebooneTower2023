<?php
function  modifyLinksInternal($text, $find, $p)
{
    if ($p['destination'])
        $template_sdcard = '<a id = "[id]" href="#" onclick="goToPageAndSetReturn(\'[link]\', \'#[id]\');">';
    $template_web = '<a id = "[id]" href="#" onclick="goToPageAndSetReturn(\'[relative_link]\', \'#[return]\');">';
    $template_pdf = '<a target= "_blank" href="/content/[link]">';

    // $rand= random_int(0, 9999);
    $length_find = strlen($find);
    $count = substr_count($text, $find);
    $pos_start = 1;
    for ($i = 1; $i <= $count; $i++) {
        $pos_start = strpos($text, $find, $pos_start);
        $pos_end = strpos($text, '">', $pos_start + $length_find);
        $content_length = $pos_end - $pos_start -  $length_find;
        $link = substr($text, $pos_start + $length_find, $content_length);
        $link_length = $pos_end - $pos_start + 2;
        $old = '<a href="/content' . $link . '">'; // plus two for the length of the end
        $relative_link = _modifyLinksMakeRelative($link);
        $return = 'Return' . $i;
        $old = array(
            '[id]',
            '[link]',
            '[relative_link]',
            '[return]',
        );
        $new = array(
            $i,
            $link,
            $relative_link,
            $return,
        );
        $new_template = str_replace($old, $new, $template);
        writeLogAppend(' modifyLinksInternal-141', $new);
        $text = substr_replace($text, $new_template, $pos_start, $link_length);
        $pos_start = $pos_end;
    }
    return $text;
}
