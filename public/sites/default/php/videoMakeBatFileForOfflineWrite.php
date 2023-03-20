<?php

function videoMakeBatFileForOfflineWrite($text, $p)
{

    $dir = ROOT_EDIT  . 'sites/' . SITE_CODE  . DESTINATION . '/' . $p['country_code'] . '/' . $p['language_iso'] . '/';
    dirMake($dir);
    $filename =  $p['folder_name'] . '.bat';
    writeLogDebug('videoMakeBatFileForOfflineWrite-9', $filename);
    $fh = fopen($dir . $filename, 'w');
    fwrite($fh, $text);
    fclose($fh);
    return;
}
