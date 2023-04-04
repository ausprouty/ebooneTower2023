<?php
myRequireOnce('dirStandard.php');
myRequireOnce('dirList.php');
myRequireOnce('createBookMediaListVideos');

function verifyBookMediaList($p)
{
    $progress = new stdClass;
    $files_missing = null;
    $progress->progress = 'done';
    $media_array = [];
    $directory = dirStandard('series', DESTINATION,  $p, $folders = null, $create = true);
    $files = dirList($directory);
    foreach ($files as $file) {
        $text = file_get_contents($directory . $file);
        $video_array = createBookMediaListVideos($text);
        foreach ($video_array as $video) {
            array_push($media_array, $video);
        }
    }
    foreach ($media_array as $media) {
        $file = ROOT_MEDIA . $media;
        if (!file_exists($file)) {
            $files_missing .= "$file \n";
            $progress->progress = 'error';
        }
    }
    if ($progress->progress == 'error') {
        $progress->message = "<br><br>The following media is missing:\n" . $files_missing;
    }
    //writeLogAppend('verifyBookMediaList', $progress);
    return $progress;
}
