<?php
myRequireOnce('dirList.php');
myRequireOnce('dirMake.php');
function copyStandardFiles($p)
{
    $dirStandard = 'sites/' . SITE_CODE . '/images/standard/';
    $dirSource =  ROOT_EDIT . $dirStandard;
    $dirDestination = ROOT_CAPACITOR . $p['language_iso'] . '/folder/' . $dirStandard;
    dirMake($dirDestination);
    $files = dirList($dirStandard);
    foreach ($files as $file) {
        $sourceFile = $dirSource . $file;
        $destinationFile = $dirDestination . $file;
        copy($sourceFile, $destinationFile);
    }
}
