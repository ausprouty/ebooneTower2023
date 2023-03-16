<?php

function copyFilesForSDApp($from, $to, $line)
{
    $to = str_replace('//', '/', $to);
    $message = "$to\n$from\n$line\n\n";
    $route_guard = ['assets', 'public', 'router', 'views'];
    $remove = ROOT_CAPACITOR;
    if (strpos($to, ROOT_CAPACITOR) === false) {
        writeLogError('copyFilesForSDApp-9', $to);
        return;
    }
    $test = str_replace(ROOT_CAPACITOR, '', $to);
    $parts = explode('/', $test);
    if (in_array($parts[0], $route_guard)) {
        createDirectory($to);
        copy($from, $to);
    } else {
        $bad = '/sites/' . SITE_CODE . '/images/';
        if (strpos($to, $bad) !== false) {
            $good = '/assets/images/';
            $to = str_replace($bad, $good, $to);
            createDirectory($to);
            copy($from, $to);
        } else {
            writeLogError('copyFilesForSDApp-19', $from . '->' . $to);
        }
    }
}
