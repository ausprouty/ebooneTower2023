<?php

function videoMakeBatFileForOfflineSingle($video, $dir)
{
    $output = '';
    $template_with_end = 'ffmpeg  -accurate_seek -i [old_name] -ss [start] -to [end]   -vf scale=[width]:-1  [dir]/[new_name].mp4';
    $template_without_end = 'ffmpeg  -accurate_seek -i [old_name] -ss [start]  -vf scale=[width]:-1    [dir]/[new_name].mp4';
    if (isset($video['download_name'])) {
        $placeholders = array(
            '[old_name]',
            '[start]',
            '[end]',
            '[width]',
            '[new_name]',
            '[dir]'
        );
        $replace = array(
            $video['download_name'],
            $video['start_time'],
            $video['end_time'],
            VIDEO_WIDTH,
            $video['new_name'],
            $dir
        );
        if ($video['end_time'] == NULL) {
            $template = $template_without_end;
        } else {
            $template = $template_with_end;
        }
        $output = str_replace($placeholders, $replace,  $template) . "\n";
    }

    return $output;
}
