<?php
/*
Looking for
 <video id="video"  width = "100%" controls>
        <source src="../../../../media/M2/spa/video/multiply1/102.mp4" type="video/mp4">
        see if directory exists on SD Card -- if not, copy media there.
        pull each html file
        find the videos in it
        see if it exists on directory
    $p['dir_capacitor'] = ROOT_CAPACITOR . _verifyBookClean($p['capacitor_settings']->subDirectory) .'/';
    $p['dir_video_list'] = ROOT_EDIT . 'sites/' . SITE_CODE .'/capacitor/' .$p['country_code'] .'/'. $p['language_iso'] .'/';
    $p['dir_series'] =  $p['country_code'] .'/'. $p['language_iso'] . '/'. $p['folder_name'];
*/
myRequireOnce('writeLog.php');
myRequireOnce('verifyBookDir.php');
myRequireOnce('dirListFiles.php');
myRequireOnce('dirMake.php');

function verifyBookMedia($p)
{
    $progress = new stdClass;
    $progress->progress = 'undone';
    return $progress;
}
