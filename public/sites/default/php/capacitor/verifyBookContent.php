<?php
myRequireOnce('getLatestContent.php');
myRequireOnce('writeLog.php');
myRequireOnce('dirMake.php');
myRequireOnce('verifyBookDir.php', 'capacitor');
myRequireOnce('verifyBookMedia.php', 'capacitor');





function verifyBookContent($p)
{
    $p = verifyBookDir($p);
    $p['scope'] = 'series';
    $content = getLatestContent($p);
    $text = json_decode($content['text']);
    $dir_series = $p['dir_capacitor'] . 'views/' . $p['dir_series'] . '/';
    if (!file_exists($dir_series)) {
        return 'ready';
    }
    // now see if all items are there

    $ok = true;
    foreach ($text->chapters as $chapter) {
        if ($chapter->publish) {
            $filename =  $dir_series . $chapter->filename . '.vue';
            if (!file_exists($filename)) {
                $ok = false;
            }
        }
    }
    if ($ok) {
        return 'done';
    }
    return 'ready';
}