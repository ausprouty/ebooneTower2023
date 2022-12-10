<?php
/*
  I only want to list files that are in the content directory
*/

//define("ROOT_EDIT", '/home/globa544/edit.mc2.online/');
myRequireOnce('publishDestination.php');
myRequireOnce('writeLog.php');
myRequireOnce('version2Text.php');


function  publishFilesInPage($text, $p)
{
    $files_in_page = [];
    // we do not copy files for nojs since they are copied by sdcard
    if (isset($p['destination'])) {
        if ($p['destination'] == 'nojs' || $p['destination'] == 'pdf') {
            return $files_in_page;
        }
    }
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
    $destination = publishDestination($p);
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
                    $to = $destination . $filename;
                    createDirectory($to);
                    copy($from, $to);
                    $message =  "$filename copied from $from to  $to\n";
                    writeLogAppend('publishFilesInPageFind-65', $message);
                }
            } else { // we do not need to copy html files; they may not have been rendered yet.
                if (strpos($filename, '.html') == false) {
                    if (strpos($filename, 'void(0)') == false && strpos($filename, '://') == false) {
                        if (strpos($filename, 'script:popUp') == false) { // no need to copy from popups
                            $find = 'localVideoOptions.js';
                            if (strpos($filename, $find)  == false) {
                                if ($p['destination'] == 'sdcard') {
                                    publishFilesInSDCardPage($filename, $p, $destination);
                                } else {
                                    $message = "$from not found";
                                    writeLogAppend('ERRORS-PublishFilesInPage-72', $message);
                                }
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
if this file is not found:
/assets/images/eng/tc/transferable-concepts-image-22.png 

look here:

/sites/mc2/content/M2/eng/tc/transferable-concepts-image-22.png

and copy to 

ROOT_SDCARD . assets/images/eng/tc/transferable-concepts-image-22.png 


*/

function publishFilesInSDCardPage($filename, $p, $destination)
{
    writeLogDebug('publishFilesInSDCardPage-101', $p);
    if (strpos($filename, '/assets/images') !== false) {
        $old_dir = '/assets/images';
        $new_dir = '/sites/' . SITE_CODE . '/content/' . $p['country_code']; // mc2/content/M2
        $from =  ROOT_EDIT  . str_replace($old_dir, $new_dir, $filename);
        if (file_exists($from)) {
            $to = $destination . substr($filename, 1); // getting rid of intital '/'
            createDirectory($to);
            copy($from, $to);
            $message = "$from \n  $to \n\n";
            writeLogAppend('publishFilesInSDCardPage-115', $message);
        } else {
            $message = "$from -- not found\n$filename -- original file\n\n";
            writeLogAppend('ERRORS-publishFilesInSDCardPage-118', $message);
        }
    } else {
        writeLogAppend('ERRORS-publishFilesInSDCardPage-122', "$filename -- original file");
    }
}
