<?php
myRequireOnce('writeLog.php');

function verifyBookRouter($p)
{
    $progress = new stdClass();
    $dir = dirStandard('router', DESTINATION,  $p);
    $filename = 'routes' . ucfirst($p['language_iso'])  . ucfirst($p['folder_name'] . '.js');
    $router = $dir . $filename;
    writeLogAppend('capacitor-verifyBookRouter', $router);
    if (file_exists($router)) {
        $progress->progress =  'done';
    } else {
        $progress->progress =  'undone';
    }
    return $progress;
}
