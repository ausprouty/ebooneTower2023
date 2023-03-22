<?php
myRequireOnce('videoMakeBatFileForOfflineSingle.php');
myRequireOnce('videoMakeBatFileForOfflineConcat.php');

function videoMakeBatFileForOfflineConsiderConcat($chapter_videos,  $p, $filename, $dir)
{
    $output = '';
    $concat = [];
    // find out if videos are a sequence
    foreach ($chapter_videos as $key => $video) {
        if ($video['follows']) {
            $previous = $key - 1;
            $chapter_videos[$previous]['preceeds'] = $video['url'];
        }
    }
    foreach ($chapter_videos as $key => $video) {
        if (!$video['follows'] && !isset($video['preceeds'])) {
            $output .= videoMakeBatFileForOfflineSingle($video, $dir);
        }
        if (isset($video['preceeds']) && !$video['follows']) {
            $concat = [];
            $concat[] = $video;
            $next_video = $video['preceeds'];
        }
        if ($video['follows']) {
            if ($video['url'] == $next_video) {
                $concat[] = $video;
                if (!isset($video['proceeds'])) {
                    $output .= videoMakeBatFileForOfflineConcat($concat, $p,  $filename);
                    $concat = [];
                } else {
                    $next_video = $video['preceeds'];
                }
            }
        }
    }
    if (count($concat) > 1) {
        $output .= videoMakeBatFileForOfflineConcat($concat, $p,  $filename);
    }
    return $output;
}
