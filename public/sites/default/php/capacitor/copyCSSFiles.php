<?php
myRequireOnce(DESTINATION, 'dirList.php');
myRequireOnce(DESTINATION, 'dirMake.php');
//define("ROOT_EDIT", ROOT . 'edit.mc2.online/');
function copyCSSFiles($p)
{
    $dirCSS = 'sites/' . SITE_CODE . '/images/css/';
    $dirSource =  ROOT_EDIT . $dirCSS;
    $dirDestination = ROOT_CAPACITOR . $p['language_iso'] . '/folder/' . $dirCSS;
    dirMake($dirDestination);
    $files = dirList($dirCSS);
    foreach ($files as $file) {
        $sourceFile = $dirSource . $file;
        $destinationFile = $dirDestination . $file;
        copy($sourceFile, $destinationFile);
    }
}
