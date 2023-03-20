<?php

myRequireOnce('audioMakeRefFileForOffline.php');
myRequireOnce('dirMake.php');
myRequireOnce('modifyRevealVideo.php');
myRequireOnce('videoBibleFindForOffline');
myRequireOnce('videoFindForOfflineNewName.php');
myRequireOnce('videoFollows.php');
myRequireOnce('videoIntroFindForOffline.php');
myRequireOnce('videoMakeBatFileForOfflineConsiderConcat.php');
myRequireOnce('videoMakeBatFileForOfflineSingle.php');
myRequireOnce('videoMakeBatFileForOfflineWrite.php');
myRequireOnce('videoMakeBatFileToCheckSource.php');
myRequireOnce('videoReference.php');
myRequireOnce('writeLog.php');



function createBookMediaBatFile($p)
{
    //audioMakeRefFileForOffline($p);
    $output = 'mkdir video' . "\n";
    $output .= 'cd video' . "\n";
    $output .= 'mkdir ' . $p['folder_name'] . "\n";
    $output .= 'cd ..' . "\n";
    $series_videos = [];
    $chapter_videos = [];

    //find series data that has been prototyped
    $sql = "SELECT * FROM content
        WHERE  country_code = '" . $p['country_code'] . "'
        AND  language_iso = '" . $p['language_iso'] . "'
        AND folder_name = '" . $p['folder_name'] . "'  AND filename = 'index'
        AND prototype_date IS NOT NULL
        ORDER BY recnum DESC LIMIT 1";
    $data = sqlArray($sql);
    // find chapters that have been prototyped
    $text = json_decode($data['text']);
    if (isset($text->chapters)) {
        foreach ($text->chapters as $chapter) {
            if (!isset($chapter->prototype)) {
                //writeLogAppennd('videoMakeBatFileForOffline', $chapter);
            } elseif ($chapter->prototype) {
                $bible_videos = videoBibleFindForOffline($p, $chapter->filename);
                //writeLogAppennd('videoMakeBatFileForOffline-37', $bible_videos);
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
                $intro_videos = videoIntroFindForOffline($p, $chapter->filename);
                writeLogAppennd('videoMakeBatFileForOffline-49', $intro_videos);
                if (is_array($intro_videos)){}
                    $output .= videoMakeBatFileForOfflineSingle($intro_videos[0], $dir);
                    // merge together
                    $chapter_videos = array_merge($bible_videos, $intro_videos);
                    //writeLogAppennd('videoMakeBatFileForOffline-54', $chapter_videos);
                }
                videoMakeBatFileToCheckSource($chapter_videos, $p);
            }
        }
    }
    //writeLogAppennd('videoMakeBatFileForOffline-59', $output);
    videoMakeBatFileForOfflineWrite($output, $p);
    return $output;
}
