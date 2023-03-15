<?php

myRequireOnce(DESTINATION, 'myGetPrototypeFile.php');

function addVueWrapperPage($html)
{
    $needle = '<vue-image-zoomer';
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
