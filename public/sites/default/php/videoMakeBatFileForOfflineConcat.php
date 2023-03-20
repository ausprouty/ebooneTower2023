<?php

myRequireOnce('videoOfflineNewName.php');
myRequireOnce('videoMakeBatFileForOfflineSingle.php');

function videoMakeBatFileForOfflineConcat($chapter_videos, $p,  $filename)
{
    // see https://trac.ffmpeg.org/wiki/Concatenate#samecodec
    $output = '';
    $template = 'ffmpeg -f concat -safe 0 -i concat/[list_name] -c copy video/[dir]/[new_name].mp4';
    $file_list = '';
    $dir = 'concat';
    foreach ($chapter_videos as $video) {
        $file_list .= 'file ' . $video['new_name']  . '.mp4' . "\n";
        $output .= videoMakeBatFileForOfflineSingle($video, $dir);
    }
    videoMakeBatFileForOfflineWriteConcat($file_list, $p, $filename);
    $placeholders = array(
        '[list_name]',
        '[width]',
        '[dir]',
        '[new_name]'
    );
    $list_name = $filename . '.txt';
    $new_name = videoOfflineNewName($filename);
    $replace = array(
        $list_name,
        VIDEO_WIDTH,
        $p['folder_name'],
        $new_name,

    );
    $concat = str_ireplace($placeholders,  $replace, $template);
    $output .= $concat . "\n";
    return $output;
}
