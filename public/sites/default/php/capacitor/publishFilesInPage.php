<?php
/*
  I only want to list files that are in the content directory but this will return all files now

  returns:

  object(stdClass)#802 (1) {
  ["files_in_page"]=>
  array(4) {
    [0]=>
    string(36) "sites/mc2/images/ribbons/mc2back.png"
    [1]=>
    string(39) "sites/mc2/images/standard/look-back.png"
    [2]=>
    string(37) "sites/mc2/images/standard/look-up.png"
    [3]=>
    string(42) "sites/mc2/images/standard/look-forward.png"
  }
}
*/

//define("ROOT_EDIT", '/home/globa544/edit.mc2.online/');
myRequireOnce('progressMerge.php');
myRequireOnce('publishDestination.php');
myRequireOnce('progressMerge.php');
myRequireOnce('writeLog.php');
myRequireOnce('version2Text.php');
myRequireOnce('copyFilesForCapacitor.php');


function  publishFilesInPage($text, $p)
{
    //writeLog('Progress-publishFilesInPage-17', 'I came into this routine in Capacitor');
    $progress = new stdClass();
    $response = new stdClass();
    $out = new stdClass();
    $files_in_page = [];
    $text = version2Text($text);
    $find_begin = 'src="';
    $response = (object) publishFilesInPageFind($find_begin, $text, $p);
    //writeLogAppend('Progress-publishFilesInPage-40', $response);
    //writeLogAppend('Progress-publishFilesInPage-40', "------");
    $files_in_page = progressMergeArrays($files_in_page, $response->files_in_page);
    //writeLogAppend('Progress-publishFilesInPage-40', $progress);
    //writeLogAppend('Progress-publishFilesInPage-40', "------");
    $progress = progressMergeObjects($progress, $response, 'publishFilesInPage-42');
    //writeLogAppend('Progress-publishFilesInPage-40', $progress);
    //writeLogAppend('Progress-publishFilesInPage-40', "\n----------------------------------\n");

    $find_begin = 'href="';
    $response = (object) publishFilesInPageFind($find_begin, $text, $p);
    //writeLogAppend('Progress-publishFilesInPage-46', $response);
    //writeLogAppend('Progress-publishFilesInPage-46', "------");
    $files_in_page = progressMergeArrays($files_in_page, $response->files_in_page);
    //writeLogAppend('Progress-publishFilesInPage-46', $progress);
    //writeLogAppend('Progress-publishFilesInPage-46', "------");
    $progress = progressMergeObjects($progress, $response, 'publishFilesInPage-46');
    //writeLogAppend('Progress-publishFilesInPage-46', $progress);
    //writeLogAppend('Progress-publishFilesInPage-46', "\n----------------------------------\n");
    //$progress->message = 'I was in publishFilesInPage';
    $out = (object) $progress;
    $out->files_in_page = $files_in_page;
    writeLogAppend('Progress-publishFilesInPage-53', $out);
    return  $out;
}

/* returns:

object(stdClass)#294 (3) {
  ["progress"]=>
  string(5) "error"
  ["message"]=>
  string(682) "<br><br>Source file does not exist /home/globa544/edit.mc2.online/assets/sites/mc2/images/ribbons/mc2back.png when called by publishFilesInPage in copyFilesForCapacitor<br><br>
  ["files_in_page"]=>
  array(0) {
  }
}
*/

function publishFilesInPageFind($find_begin, $text, $p)
{
    $progress = new stdClass;
    $new_progress = new stdClass;
    $out = new stdClass;
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
            $from = ROOT_EDIT .  $filename;
            $from = str_replace('//', '/', $from);
            if (file_exists($from)) {
                $files_in_page[] = $filename;
                // I think I want to include html
                if (!is_dir($from) && strpos($from, '.html') === false) {
                    $new_progress = publishFilesInPageWrite($filename, $p);
                    //writeLogAppend('capacitor-publishFilesInPageFind-94', $new_progress);
                    //writeLogAppend('capacitor-publishFilesInPageFind-94', "\n====\n");
                    //writeLogAppend('capacitor-publishFilesInPageFind-94', $progress);
                    //writeLogAppend('capacitor-publishFilesInPageFind-94', "\n====\n");
                    $progress = progressMergeObjects($progress, $new_progress, 'publishFilesInPageFind-94');
                    //writeLogAppend('capacitor-publishFilesInPageFind-94', $progress);
                    //writeLogAppend('capacitor-publishFilesInPageFind-94', "\n=======================================\n");
                }
            } else { // we do not need to copy html files; they may not have been rendered yet.
                if (strpos($filename, '.html') == false) {
                    if (strpos($filename, 'void(0)') == false && strpos($filename, '://') == false) {
                        if (strpos($filename, 'script:popUp') == false) { // no need to copy from popups
                            $find = 'localVideoOptions.js';
                            if (strpos($filename, $find)  == false) {
                                $new_progress =  publishFilesInPageWrite($filename, $p);
                                //writeLogAppend('capacitor-publishFilesInPageFind-107', $new_progress);
                                //writeLogAppend('capacitor-publishFilesInPageFind-107', "\n====\n");
                                //writeLogAppend('capacitor-publishFilesInPageFind-107', $progress);
                                //writeLogAppend('capacitor-publishFilesInPageFind-107', "\n====\n");
                                $progress = progressMergeObjects($progress, $new_progress, 'publishFilesInPageFind-107');
                                writeLogAppend('capacitor-publishFilesInPageFind-107', $progress);
                                //writeLogAppend('capacitor-publishFilesInPageFind-107', "\n=======================================\n");
                            }
                        }
                    }
                }
            }
            $text = substr($text, $pos_end);
        }
    }
    //writeLogAppend('Progress-publishFilesInPage-115', $files_in_page);
    //$progress->message = 'I was in publishFilesInPageFind';
    $out = (object) $progress;
    $out->files_in_page = $files_in_page;
    writeLogAppend('Progress-publishFilesInPageFind-119', $out);
    //writeLogAppend('Progress-publishFilesInPage-119', "\n\n\n---------------------------\n\n\n");
    return $out;
}
/*
returns 

object(stdClass)#93 (2) {
  ["progress"]=>
  string(6) "undone"
  ["message"]=>
  string(174) "<br><br>Copied /home/globa544/edit.mc2.online/sites/mc2/content/M2/images/standard/Stories-of-the-Prophets.png when called by publishFilesInPageWrite in copyFilesForCapacitor"
}
*/
function publishFilesInPageWrite($filename, $p)
{
    $progress = new stdClass;
    $from = ROOT_EDIT . $filename;
    $from = str_replace('//', '/', $from);
    $dir = dirStandard('assets', DESTINATION,  $p, $folders = null, $create = true);
    $to = $dir . $filename;
    $to = str_replace('//', '/', $to);
    //writeLogAppend('capacitor-publishFilesInPageWrite-81', "$from -> $to");
    $progress  = copyFilesForCapacitor($from, $to,  'publishFilesInPageWrite');
    //writeLogAppend('capacitor-publishFilesInPageWrite-81', "\n====\n");
    //writeLogAppend('capacitor-publishFilesInPageWrite-81', $progress);
    //writeLogAppend('capacitor-publishFilesInPageWrite-81', "\n============================\n");
    return $progress;
}
