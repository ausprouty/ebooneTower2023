<?php
myRequireOnce('writeLog.php');
myRequireOnce('dirMake.php');
myRequireOnce('verifyBook.php', 'capacitor');
myRequireOnce('verifyBookDir.php', 'capacitor');

function checkStatusBook($p)
{
    if (!isset($p['capacitor_settings']->subDirectory)) {
        $message = 'p[capacitor_settings]->subDirectory not set';
        writeLogError('checkStatusBook', $message);
        trigger_error($message, E_USER_ERROR);
    }
    $p = verifyBookDir($p); // set $p['dir_capacitor']
    $check = [];
    $out = new stdClass();
    $progress = json_decode($p['progress']);
    foreach ($progress as $key => $value) {
        $out->$key = $value;
        switch ($key) {
            case "capacitor":
                if (file_exists($p['dir_capacitor'] . '/folder/content/')) {
                    $out->capacitor = verifyBookCapacitor($p);
                } else {
                    $out->capacitor = 'undone';
                }
                break;
            case "nojs":
                if (file_exists($p['dir_capacitor'] . '/folder/nojs/')) {
                    $out->nojs = verifyBookNoJS($p);
                } else {
                    $out->nojs = 'undone';
                }
                break;
            case "pdf":
                if (file_exists($p['dir_capacitor'] . '/folder/pdf/')) {
                    $out->pdf = verifyBookPDF($p);
                } else {
                    $out->pdf = 'undone';
                }
                break;
            case "videolist":
                $fn = $p['dir_video_list'];
                if (file_exists($fn)) {
                    $out->videolist = verifyBookVideoList($p);
                } else {
                    $out->videolist = 'undone';
                }
                break;
            default:
        }
    }
    return $out;
}
