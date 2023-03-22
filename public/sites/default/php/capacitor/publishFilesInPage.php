<?php
/*
  I only want to list files that are in the content directory
*/

//define("ROOT_EDIT", '/home/globa544/edit.mc2.online/');
myRequireOnce('publishDestination.php');
myRequireOnce('publishFilesInSDCardPage.php');
myRequireOnce('writeLog.php');
myRequireOnce('version2Text.php');
myRequireOnce('copyFilesForCapacitor.php');


function  publishFilesInPage($text, $p)
{
    $files_in_page = [];
    $text = version2Text($text);
    $find_begin = 'src="';
    $result = publishFilesInPageFind($find_begin, $text, $p);
    if (is_array($result)) {
        $files_in_page = array_merge($files_in_page, $result);
    }
    $find_begin = 'href="';
    $result = publishFilesInPageFind($find_begin, $text, $p);
    if (is_array($result)) {
        $files_in_page = array_merge($files_in_page, $result);
    }
    return  $files_in_page;
}

function publishFilesInPageFind($find_begin, $text, $p)
{
    $destination = DESTINATION;
    $files_in_page = [];
    $debug = '';
    $find_end = '"';
    $intial_text = $text;
    if (strpos($text, $find_begin) !== false) {
        $count = 0;
        while (strpos($text, $find_begin) !== false) {
            $count++;
            $pos_begin = strpos($text, $find_begin);
            $text = substr($text, $pos_begin + strlen($find_begin));
            $pos_end = strpos($text, $find_end) - 1;
            $filename = substr($text, 1, $pos_end);
            // filename = /sites/mc2/images/standard/look-back.png
            $from = ROOT_EDIT . $filename;
            $from = str_replace('//', '/', $from);
            $debug .= "from is $from\n";
            if (file_exists($from)) {
                // creating list of files to include in download for pwa
                $bad = 'sites/' . SITE_CODE . '/content/';
                $clean_filename = str_replace($bad, 'content/', $filename);
                $files_in_page[] = '/' . $clean_filename;
                // I think I want to include html
                if (!is_dir($from) && strpos($from, '.html') === false) {
                    publishFilesInPageWrite($filename, $p);
                }
            } else { // we do not need to copy html files; they may not have been rendered yet.
                if (strpos($filename, '.html') == false) {
                    if (strpos($filename, 'void(0)') == false && strpos($filename, '://') == false) {
                        if (strpos($filename, 'script:popUp') == false) { // no need to copy from popups
                            $find = 'localVideoOptions.js';
                            if (strpos($filename, $find)  == false) {
                                publishFilesInPageWrite($filename, $p);
                            }
                        }
                    }
                }
            }
            $text = substr($text, $pos_end);
        }
    }
    return $files_in_page;
}
/*
sites/mc2/images/ribbons/back-ribbon-mc2.png
sites/mc2/content/M2/eng/images/standard/TransferableConcepts.png
sites/mc2/images/menu/languages.png
sites/mc2/images/standard/android.png
sites/mc2/images/standard/Share.png
*/
function publishFilesInPageWrite($filename, $p)
{
    $from = ROOT_EDIT . $filename;
    $dir = dirStandard('assets', DESTINATION,  $p, $folders = null, $create = true);
    $to = $dir . $filename;
    writeLogAppend('capacitor-publishFilesInPageWrite-81', "$from -> $to");
    copyFilesForCapacitor($from, $to, 'publishFilesInPageWrite');
}
