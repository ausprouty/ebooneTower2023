<?php
/*
  I only want to list files that are in the content directory
*/

//define("ROOT_EDIT", '/home/globa544/edit.mc2.online/');
myRequireOnce('findFilesInText.php');
myRequireOnce('publishDestination.php');
myRequireOnce('writeLog.php');
myRequireOnce('version2Text.php');


function  publishFilesInPage($text, $p)
{
    writeLog('publishFilesInPage-17', 'I came into this routine in Default');
    $files_in_page = [];
    // we do not copy files for nojs since they are copied by sdcard
    if (isset($p['destination'])) {
        if ($p['destination'] == 'nojs' || $p['destination'] == 'pdf') {
            return $files_in_page;
        }
    }
    $text = version2Text($text);
    $find_begin = 'src="';
    $files_in_page = findFilesInText($find_begin, $text, $p, $files_in_page);
    $find_begin = 'href="';
    $files_in_page = findFilesInText($find_begin, $text, $p, $files_in_page);
    return  $files_in_page;
}
