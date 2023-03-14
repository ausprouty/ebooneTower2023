<?php
myRequireOnce('writeLog.php');
myRequireOnce('dirMake.php');
myRequireOnce('verifyBookContent.php', 'capacitor');
myRequireOnce('verifyBookDir.php', 'capacitor');
myRequireOnce('verifyBookMediaList.php', 'capacitor');
myRequireOnce('verifyRouter.php', 'capacitor');

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
                if (file_exists($p['dir_capacitor'] . '/views/')) {
                    $out->content = verifyBookContent($p);
                } else {
                    $out->content = 'undone';
                }
                break;
            case "media":
                $out->media = verifyBookMedia($p);
                break;
            case "medialist":
                if (file_exists($p['dir_capacitor'] . '/media/')) {
                    $out->medialist = verifyBookMediaList($p);
                } else {
                    $out->medialist = 'undone';
                }
                break;
            case "router":
                if (file_exists($p['dir_capacitor'] . '/router/')) {
                    $out->content = verifyRouter($p);
                } else {
                    $out->content = 'undone';
                }
                break;
            default:
        }
    }
    return $out;
}
