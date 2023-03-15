<?php

myRequireOnce(DESTINATION, 'verifyBookDir.php', 'capacitor');
myRequireOnce(DESTINATION, 'verifyBook.php', 'capacitor');
myRequireOnce(DESTINATION, 'dirListSubdir.php');
myRequireOnce(DESTINATION, 'copyDirectory.php');
myRequireOnce(DESTINATION, 'createDirectories.php');

function copyBookMedia($p)
{
    $p = verifyBookDir($p); // set $p['dir_capacitor']
    /*
     $p['dir_capacitor'] = ROOT_CAPACITOR . _verifyBookClean($p['capacitor_settings']->subDirectory) .'/';
    $p['dir_video_list'] = ROOT_EDIT . 'sites/' . SITE_CODE .'/capacitor/' .$p['country_code'] .'/'. $p['language_iso'] .'/';
    $p['dir_series'] =  $p['country_code'] .'/'. $p['language_iso'] . '/'. $p['folder_name'];
*/
    $dir_source = ROOT_EDIT . 'sites/' . SITE_CODE . '/media/' . $p['country_code'] . '/' . $p['language_iso'] . '/';
    $destination = $p['dir_capacitor'] . 'folder/media/' . $p['country_code'] . '/' . $p['language_iso'] . '/';
    createDirectories($destination);
    $directories = dirListSubdir($dir_source);
    foreach ($directories as $directory) {
        $source = $dir_source . $directory . '/';
        $destination = $p['dir_capacitor'] . 'folder/media/' . $directory . '/';
        copyDirectory($source, $destination);
    }
    return;
}
