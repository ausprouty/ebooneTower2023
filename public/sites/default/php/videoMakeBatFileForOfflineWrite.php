<?php
myRequireOnce('dirStandard.php');

function videoMakeBatFileForOfflineWrite($text, $p)
{
    $dir = dirStandard('media_batfile', DESTINATION,  $p, $folders = null, $create = true);
    $filename =  $dir  . $p['folder_name'] . '.bat';
    //writeLogDebug('videoMakeBatFileForOfflineWrite-8', $filename);
    $fh = fopen($filename, 'w');
    fwrite($fh, $text);
    fclose($fh);
    return;
}
