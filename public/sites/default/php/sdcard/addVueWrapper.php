<?php

myRequireOnce('myGetPrototypeFile.php');

function addVueWrapperPage($html){
    writeLogDebug('addVueWrapperPage-6', $html);
    $template = myGetPrototypeFile('page.vue', $subdirectory = 'sdcard');
    writeLogDebug('addVueWrapperPage-7', $template);
    $old = '[html]';
    $new = $html;
    $out = str_replace($old, $new, $template);
    writeLogDebug('addVueWrapperPage-11', $out);
    return $out;
}