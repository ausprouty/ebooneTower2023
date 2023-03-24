<?php
myRequireOnce('dirListRecursive.php');
myRequireOnce('writeLog.php');

function zipMediaBatFiles($p)
{
    $dir = ROOT_EDIT . 'sites/' . SITE_CODE . '/capacitor/' . $p['country_code'] . '/';
    // FROM https://stackoverflow.com/questions/1754352/download-multiple-files-as-a-zip-file-using-php
    $files = zipMediaBatFilesGet($p);
    $zipname = 'MediaBatFiles' . $p['capacitor_settings']->subDirectory . '.zip';
    $zip = new ZipArchive;
    $zip->open($zipname, ZipArchive::CREATE);
    foreach ($files as $file) {
        //writeLogAppend('zipMediaBatFiles', $file);
        //$zip->addFile($file);
        $filename = str_replace($dir, '', $file);
        $zip->addFromString($filename,  file_get_contents($file));
    }
    $zipname = 'sites/default/' . $zipname;
    $zip->close();

    return ($zipname);
}
//define("ROOT_EDIT", ROOT . 'edit.mc2.online/');
function zipMediaBatFilesGet($p)
{
    $output = [];
    $dir = ROOT_EDIT . 'sites/' . SITE_CODE . '/capacitor/' . $p['country_code'] . '/';
    $subDirectories = explode('.', $p['capacitor_settings']->subDirectory);
    //writeLogDebug('capacitor-zipMediaBatFilesGet', $subDirectories);
    foreach ($subDirectories as $sub) {
        if (strlen($sub) > 1) { // because first one will be blank
            $check_dir = $dir . $sub;
            $files = dirListRecursive($check_dir);
            $output = array_merge($files, $output);
        }
    }
    //writeLogDebug('capacitor-zipMediaBatFilesGet-33', $output);
    return $output;
}
