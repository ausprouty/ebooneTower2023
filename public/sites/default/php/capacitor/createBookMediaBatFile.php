<?php

myRequireOnce('audioMakeRefFileForOffline.php');
myRequireOnce('dirMake.php');
myRequireOnce('modifyRevealVideo.php');
myRequireOnce('videoBibleFindForOffline.php');
myRequireOnce('videoIntroFindForOffline.php');
myRequireOnce('videoMakeBatFileForOfflineConsiderConcat.php');
myRequireOnce('videoMakeBatFileForOfflineSingle.php');
myRequireOnce('videoMakeBatFileForOfflineWrite.php');
myRequireOnce('videoMakeBatFileToCheckSource.php');
myRequireOnce('writeLog.php');



function createBookMediaBatFile($p)
{
    $progress = new stdClass;
    audioMakeRefFileForOffline($p);
    $output = 'mkdir video' . "\n";
    $output .= 'cd video' . "\n";
    $output .= 'mkdir ' . $p['folder_name'] . "\n";
    $output .= 'cd ..' . "\n";
    $chapter_videos = [];
    $message = '';

    //find series data that has been prototyped
    $sql = "SELECT * FROM content
        WHERE  country_code = '" . $p['country_code'] . "'
        AND  language_iso = '" . $p['language_iso'] . "'
        AND folder_name = '" . $p['folder_name'] . "'  AND filename = 'index'
        AND prototype_date IS NOT NULL
        ORDER BY recnum DESC LIMIT 1";
    $data = sqlArray($sql);
    if (!isset($data['text'])) {
        $progress->message = '<br><br>Content not found for this series';
        $progress->progress = 'undone';
        return $progress;
    }
    // find chapters that have been prototyped
    $text = json_decode($data['text']);
    if (isset($text->chapters)) {
        foreach ($text->chapters as $chapter) {
            if (!isset($chapter->prototype)) {
                //writeLogAppennd('videoMakeBatFileForOffline', $chapter);
            } elseif ($chapter->prototype) {
                $response = (object) videoBibleFindForOffline($p, $chapter->filename);
                //writeLogAppend('capacitior-createBookMediaBatFile-45', $response);
                $message .= $response->message;
                $bible_videos = $response->chapter_videos;
                $count = count($bible_videos);
                //writeLog('videoMakeBatFileForOffline-32-count-'. $chapter->filename , $count);
                $dir = 'video/' . $p['folder_name'];
                if ($count == 1) {
                    $output .= videoMakeBatFileForOfflineSingle($bible_videos[0], $dir);
                }
                if ($count > 1) {
                    $output .= videoMakeBatFileForOfflineConsiderConcat($bible_videos,  $p, $chapter->filename, $dir);
                }
                // spanish MC2 has intro videos
                $response = (object) videoIntroFindForOffline($p, $chapter->filename);
                $message .= $response->message;
                $intro_videos = $response->chapter_videos;
                if (isset($intro_videos[0])) {
                    $output .= videoMakeBatFileForOfflineSingle($intro_videos[0], $dir);
                    $chapter_videos = array_merge($bible_videos, $intro_videos);
                }
                videoMakeBatFileToCheckSource($chapter_videos, $p);
            }
        }
    }
    //writeLogDebug('capacitior-createBookMediaBatFile-69', $output);
    videoMakeBatFileForOfflineWrite($output, $p);
    $progress->message = $message;
    $progress->progress = 'done';
    //writeLogDebug('capacitior-createBookMediaBatFile-76', $progress);

    return $progress;
}
