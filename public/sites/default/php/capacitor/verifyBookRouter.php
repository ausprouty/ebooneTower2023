<?php
myRequireOnce('writeLog.php');

function verifyBookRouter($p)
{
    $dir = dirCreate('router', DESTINATION,  $p);
    $filename = 'routes' . ucfirst($p['language_iso'])  . ucfirst($p['folder_name'] . '.js');
    $router = $dir . $filename;
    writeLogAppend('verifyBookRouter', $router);
    if (file_exists($router)) {
        return 'done';
    } else {
        return 'undone';
    }
}
