<?php

myRequireOnce('myGetPrototypeFile.php');

function addVueWrapperPage($html)
{
    $needle = '<span class="zoom">';
    if (strpos($html, $needle) === false) {
        $template = myGetPrototypeFile('page.vue', $subdirectory = 'sdcard');
    } else {
        $template = myGetPrototypeFile('pageZoom.vue', $subdirectory = 'sdcard');
    }
    $old = '[html]';
    $new = $html;
    $out = str_replace($old, $new, $template);
    return $out;
}
