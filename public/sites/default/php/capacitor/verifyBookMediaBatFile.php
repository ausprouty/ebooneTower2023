<?php

function verifyBookMediaBatFile($p)
{
    $progress = new stdClass();
    $progress->progress = 'undone';
    $dir = dirStandard('media_batfile', DESTINATION,  $p, $folders = null, $create = false);
    $filename =  $dir  . $p['folder_name'] . '.bat';
    if (file_exists($filename)) {
        $progress->progress = 'done';
    }
    return $progress;
}
