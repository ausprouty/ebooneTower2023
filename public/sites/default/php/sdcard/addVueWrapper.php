<?php

myRequireOnce('myGetPrototypeFile.php');

function addVueWrapperPage($html)
{
    $needle = '<vue-image-zoomer';
    if (strpos($html, $needle) === false) {
        $template = myGetPrototypeFile('page.vue');
    } else {
        $template = myGetPrototypeFile('pageZoom.vue');
    }
    $old = '[html]';
    $new = $html;
    $out = str_replace($old, $new, $template);
    return $out;
}
