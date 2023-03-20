<?php
function videoMakeBatFileForOfflineWriteConcat($text, $p, $filename)
{

    $dir = ROOT_EDIT  . 'sites/' . SITE_CODE  . '/apk/' . $p['country_code'] . '/' . $p['language_iso']  . '/concat/';
    dirMake($dir);
    $filename =  $filename . '.txt';
    $fh = fopen($dir . $filename, 'w');
    fwrite($fh, $text);
    fclose($fh);
    return;
}
