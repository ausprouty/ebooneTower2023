<?php
myRequireOnce('writeLog.php');

function verifyBookRouter($p)
{
    $dir = ROOT_CAPACITOR .  $p['language_iso'];
    $filename = '/router/routes' . ucfirst($p['language_iso'])  . ucfirst($p['folder_name'] . '.js');
    $router = $dir . $filename;
    writeLogAppend('verifyBookRouter', $router);
    if (file_exists($router)) {
        return 'done';
    } else {
        return 'undone';
    }
}
