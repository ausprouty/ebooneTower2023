<?php
myRequireOnce('writeLog.php');
myRequireOnce('dirMake.php');
myRequireOnce('verifyBookContent.php');
myRequireOnce('verifyBookDir.php');
myRequireOnce('verifyBookMediaList.php');
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
    $progress = json_decode($p['progress']);
    foreach ($progress as $key => $value) {
        $out->$key = $value;
        switch ($key) {
            case "content":
                $out->content = verifyBookContent($p);
                break;
            case "media":
                $out->media = verifyBookMedia($p);
                break;
            case "medialist":
                $out->medialist = verifyBookMediaList($p);
                break;
            case "router":
                $out->router = verifyBookRouter($p);
                break;
            default:
        }
    }
    return $out;
}
