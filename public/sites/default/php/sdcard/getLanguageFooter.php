<?php
function getLanguageFooter($p)
{
    if (isset($p['sdcard_settings']->footer)) {
        $language_footer =  $p['sdcard_settings']->footer;
    } else {
        $language_footer =  'languageFooter.html';
    }
    $footer  =  myGetPrototypeFile($language_footer, $p['destination']);
    writeLogDebug('getLanguageFooter-11', $footer);
    return $footer;
}
