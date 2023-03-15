<?php
myRequireOnce('publishDestination.php');

function languageSpecificJavascripts($p)
{
    $out = '';
    // define("ROOT_EDIT_CONTENT", '/home/globa544/edit.mc2.online/sites/' . SITE_CODE . '/content/');
    $folder = ROOT_EDIT_CONTENT . $p['country_code'] . '/' . $p['language_iso'] . '/javascript/';
    if (file_exists($folder)) {
        $javascript_folder = '/sites/' . SITE_CODE . '/content/' .
            $p['country_code'] . '/' . $p['language_iso'] . '/javascript/';
        $out = '<!--- Language Specific Javascripts-->' . "\n";
        $files = scandir($folder);
        foreach ($files as $file) {
            if (substr($file, -3) == '.js') {
                $out .=  '<script src="' . $javascript_folder . $file . '"></script>'  . "\n";
            }
        }
    }

    return $out;
}
