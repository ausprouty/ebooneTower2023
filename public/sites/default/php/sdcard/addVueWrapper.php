<?php

myRequireOnce('myGetPrototypeFile.php');

function addVueWrapperPage($html){
    $template = myGetPrototypeFile('page.vue', $subdirectory = 'sdcard');
    $old = '[html]';
    $new = $html;
    $out = str_replace($old, $new, $template);
    return $out;
}