<?php

myRequireOnce('dirStandard.php');

function findFilesInText($find_begin, $text, $p, $files_in_page = [])
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
            $pos_end = strpos($text, $find_end);
            $filename = substr($text, 0, $pos_end);
            // filename = /sites/mc2/images/standard/look-back.png
            //               or
            //          resource.html
            if (strpos($filename, '/' != 0)) {
                $dir = dirStandard('content', DESTINATION,  $p, $folders = null, $create = false);
                $filename = $dir . $filename;
            }
            $from = ROOT_EDIT . $filename;
            $from = str_replace('//', '/', $from);
            $debug .= "from is $from\n";
            if (file_exists($from)) {
                // creating list of files to include in download for pwa
                $files_in_page[$filename] = $filename;
                if (!is_dir($from)) {
                    if ($p['destination'] == 'sdcard' || $p['destination'] == 'capacitor') {
                        publishFilesInSDCardPage($filename, $p, $destination);
                    } else {
                        $to = $destination . $filename;
                        createDirectory($to);
                        copy($from, $to);
                        //$message =  "$filename copied from $from to  $to\n";
                        //writeLogAppend('publishFilesInPageFind-65', $message);
                    }
                }
            } elseif (strpos($from, '#') === false) {
                /* "/home2/citylead/public_html/cojourners-edit/favicon-16x16.png not found"
                    /home2/citylead/public_html/cojourners-edit/sites/cojourners/root/favicon-16x16.png may be found

                */
                $dir = dirStandard('root', DESTINATION,  $p, $folders = null, $create = false);
                $from = $dir . $filename;
                $from = str_replace('//', '/', $from);
                if (file_exists($from)) {
                    if ($p['destination'] == 'sdcard' || $p['destination'] == 'capacitor') {
                        if (strpos($filename, 'localVideoOptions.js')  == false) { // no video options in capacitor
                            publishFilesInSDCardPage($filename, $p, $destination);
                        }
                    } else {
                        $files_in_page[$filename] = $filename;
                    }
                } else {
                    if (strpos($filename, 'void(0)') == false && strpos($filename, '://') == false) {
                        if (strpos($filename, 'script:popUp') == false) { // no need to copy from popups
                            $source = $p['filename'];
                            $message = "$filename found in  $source";
                            writeLogAppend('ERRORS-findFilesInText-68', $message);
                        }
                    }
                }
            }
            $text = substr($text, $pos_end);
        }
    }
    return $files_in_page;
}
