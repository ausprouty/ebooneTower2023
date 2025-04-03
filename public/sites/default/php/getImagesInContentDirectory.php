<?php
myRequireOnce('writeLog.php');
// get images from folder (in /content) so it can transfer from edit to prototype
//also used to return list of images for selet
// assumes$p[ímage_dir] ='M2/eng/images'
function getImagesInContentDirectory(array $p): array
{
    $dir = ROOT_EDIT . $p['image_dir'];
    $dir = str_replace('//', '/', $dir); // str_ireplace not needed here

    writeLog('getImagesInContentDirectory', 'dir: ' . $dir);

    if (!is_dir($dir)) {
        return [];
    }

    $files = array_diff(scandir($dir), ['.', '..']);
    $images = [];

    foreach ($files as $file) {
        $images[] = rtrim($p['image_dir'], '/') . '/' . $file;
    }

    return $images;
}