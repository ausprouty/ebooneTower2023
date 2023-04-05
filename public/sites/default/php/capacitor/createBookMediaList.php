<?php
myRequireOnce('dirStandard.php');
myRequireOnce('dirList.php');
myRequireOnce('createBookMediaListVideos');

function createBookMediaList($p)
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
            $files_missing .= $file . '<br>';
            $progress->progress = 'error';
        }
    }
    if ($progress->progress == 'error') {
        $progress->message = '<br><br>CreateBookMediaList says the following videos are missing:<br>' . $files_missing;
    }
    return $progress;
}
