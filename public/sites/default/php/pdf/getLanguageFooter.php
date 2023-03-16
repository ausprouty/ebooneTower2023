<?php
function getLanguageFooter($p)
{
    $language_footer =  $p['sdcard_settings']->footer;
    $footer  =  myGetPrototypeFile($language_footer);
    return $footer;
}
