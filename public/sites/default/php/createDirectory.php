<?php

/*  creates directories for copying files
input may be  "/home/globa544/apk.mc2/cmn.m1/folder/sites/mc2/images/icons/favicon-32x32.png/"
*/
myRequireOnce('writeLog.php');

function createDirectory($dir)
{
    writeLogAppend('createDirectory-all', "\n\n$dir");
    // we can not handle relative 
    if (strpos($dir, './') !== FALSE) {
        writeLogAppend('createDirectory-all', "ERROR line 13 - $dir");
        writeLogAppend('ERROR-createDirectory', $dir);
        return;
    }
    $out = null;
    $parts = explode('/', $dir);
    $length = count($parts);
    $count = 0;
    $path = null;
    foreach ($parts as $part) {
        $count++;
        if ($count < $length) {
            $path .= $part . '/';
        } elseif ($count == $length && strpos($part, '.') === FALSE) {
            $path .= $part . '/';
        }
        $path = str_replace('//', '/', $path);
        if (!file_exists($path)) {
            writeLogAppend('createDirectory-all', "$path created");
            mkdir($path);
        } else {
            writeLogAppend('createDirectory-all', "$path EXISTS");
        }
    }
    return $out;
}
