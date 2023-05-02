<?php

function findFilesinText($find_begin, $text, $p, $files_in_page = [])
{
    $destination = publishDestination($p);
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
                $clean_filename = '/' .  str_replace($bad, 'content/', $filename);
                $files_in_page[$clean_filename] = $clean_filename;
                // I think I want to include html
                if (!is_dir($from) && strpos($from, '.html') === false) {
                    if ($p['destination'] == 'sdcard' || $p['destination'] == 'capacitor') {
                        publishFilesInSDCardPage($filename, $p, $destination);
                    } else {
                        $to = $destination . $filename;
                        createDirectory($to);
                        copy($from, $to);
                        $message =  "$filename copied from $from to  $to\n";
                        //writeLogAppend('publishFilesInPageFind-65', $message);
                    }
                }
            } else { // we do not need to copy html files; they may not have been rendered yet.
                if (strpos($filename, '.html') == false) {
                    if (strpos($filename, 'void(0)') == false && strpos($filename, '://') == false) {
                        if (strpos($filename, 'script:popUp') == false) { // no need to copy from popups
                            $find = 'localVideoOptions.js';
                            if (strpos($filename, $find)  == false) {
                                if ($p['destination'] == 'sdcard' || $p['destination'] == 'capacitor') {
                                    publishFilesInSDCardPage($filename, $p, $destination);
                                } else {
                                    $message = "$from not found";
                                    //writeLogAppend('ERRORS-PublishFilesInPage-72', $message);
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
