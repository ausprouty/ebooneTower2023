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
        $progress->message = "<br><br>The following Videos are  is missing.<br>
            <ol>
            <ol>Look in the indicated lesson to see which video is being referenced</li>
            <li>Download files</li>
            <li>Update videoReference.php</li>
            <li>Reduce videos to 480px wide (maybe use the newly created MediaBat file</li>
            <li>Reduce audio files to something</li>
            <li>Add files to ROOT_MEDIA</li>
            </ol><br>Files Missing: <br> " . $files_missing;
    }
    //writeLogAppend('verifyBookMediaList', $progress);
    return $progress;
}
