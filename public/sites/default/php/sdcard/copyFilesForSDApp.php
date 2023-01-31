<?php

function copyFilesForSDApp($from, $to, $line)
{
    $message = "$to\n$from\n$line\n\n";
    $route_guard = ['assets', 'public', 'router', 'views'];
    $remove = ROOT_SDCARD;
    if (strpos($to, ROOT_SDCARD) === false) {
        writeLogError('copyFilesForSDApp-9', $to);
        return;
    }
    $test = str_replace(ROOT_SDCARD, '', $to);
    $parts = explode('/', $test);
    if (in_array($parts[0], $route_guard)) {
        createDirectory($to);
        copy($from, $to);
    } else {
        writeLogError('copyFilesForSDApp-19', $to);
    }
}
