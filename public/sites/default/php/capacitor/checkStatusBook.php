<?php
myRequireOnce('writeLog.php');
myRequireOnce('dirMake.php');
myRequireOnce('verifyBookContent.php');
myRequireOnce('verifyBookDir.php');
myRequireOnce('verifyBookMediaList.php');
myRequireOnce('verifyMediaBatFile.php');
myRequireOnce('verifyBookRouter.php');

function checkStatusBook($p)
{
    if (!isset($p['capacitor_settings']->subDirectory)) {
        $message = 'p[capacitor_settings]->subDirectory not set';
        writeLogError('Capacitor-checkStatusBook-11', $message);
        writeLogError('Capacitor-checkStatusBook-12', $p);
        trigger_error($message, E_USER_ERROR);
    }
    $p = verifyBookDir($p); // set $p['dir_capacitor']
    $check = [];
    $out = new stdClass();
    $content_progress = verifyBookContent($p);
    $progress = json_decode($p['progress']);
    foreach ($progress as $key => $value) {
        $out->$key = $value;
        switch ($key) {
            case "content":
                $out->content =  $content_progress;
                break;
            case "media_batfile":
                $out->media_batfile = verifyMediaBatFile($p);
                break;
            case "medialist":
                if ($content_progress->progress == 'done') {
                    $out->medialist = verifyBookMediaList($p);
                } else {
                    $out->medialist = 'invisible';
                }

                break;
            case "router":
                $out->router = verifyBookRouter($p);
                break;
            default:
        }
    }
    writeLogDebug('capacitor-checkStatusBook-45', $out);
    return $out;
}
