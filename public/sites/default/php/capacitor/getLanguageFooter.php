<?php
function getLanguageFooter($p)
{
    if (isset($p['capacitor_settings']->footer)) {
        $language_footer =  $p['capacitor_settings']->footer;
    } else {
        $language_footer =  'languageFooter.html';
    }
    $footer  =  myGetPrototypeFile($language_footer, $p['destination']);
    //writeLogDebug('capacitor-getLanguageFooter-11', $footer);
    return $footer;
}
