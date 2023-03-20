<?php

function videoMakeBatFileToCheckSource($chapter_videos, $p)
{
    $output = '';
    $dir = ROOT_EDIT . 'sites/' . SITE_CODE . '/apk/' . $p['country_code'] . '/' . $p['language_iso'] . '/';
    dirMake($dir);
    $filename = 'CheckSource-' . $p['folder_name'] . '.txt';
    foreach ($chapter_videos as $video) {
        $output .= $video['download_name'] . "\n";
    }
    $fh = $dir . $filename;
    file_put_contents($fh, $output, FILE_APPEND | LOCK_EX);
    return;
}
