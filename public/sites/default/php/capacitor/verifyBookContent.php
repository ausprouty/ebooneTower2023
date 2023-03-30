<?php
myRequireOnce('getLatestContent.php');
myRequireOnce('writeLog.php');
myRequireOnce('dirMake.php');
myRequireOnce('verifyBookDir.php');
myRequireOnce('verifyBookMedia.php');



myRequireOnce('dirStandard.php');

function verifyBookContent($p)
{
    $progress = new stdClass;
    $p = verifyBookDir($p);
    $p['scope'] = 'series';
    writeLogAppend('verifyBookContent-capacitor-17', $p);
    $content = getLatestContent($p);
    $text = json_decode($content['text']);
    $dir_series =  dirStandard('series', DESTINATION,  $p, $folders = null, $create = true);
    if (!file_exists($dir_series)) {
        $progress->progress =  'undone';
        return $progress;
    }
    // now see if all items are there
    if (isset($text->chapters)) {
        foreach ($text->chapters as $chapter) {
            if ($chapter->publish) {
                $filename = $dir_series .  ucfirst($p['language_iso'])  . ucfirst($chapter->filename) . '.vue';
                if (!file_exists($filename)) {
                    $progress->progress = 'ready';
                    $progress->message = "<br><br>$filename  not found";
                    return $progress;
                }
            }
        }
        $progress->progress =  'done';
        return $progress;
    }
    $progress->progress =  'undone';
    return $progress;
}
