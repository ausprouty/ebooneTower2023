<?php
/* 
/home/globa544/edit.mc2.online/sites/mc2/content/M2/eng/multiply2/Period5.png
->
/home/globa544/mc2.capacitor/eng/public/images/zoom/eng/multiply2/Period5.png"
*/
function copyFilesForCapacitor($from, $to, $line)
{
    $to = str_replace('//', '/', $to);
    $message = "$to\n$from\n$line\n\n";
    writeLogAppend('capacitor-copyFilesForCapacitor-7', $message);
    $route_guard = ['assets', 'public', 'router', 'views'];
    if (strpos($to, ROOT_CAPACITOR) === false) {
        writeLogError('copyFilesForCapacitor-9', $to);
        return;
    }
    $test = str_replace(ROOT_CAPACITOR, '', $to);
    $parts = explode('/', $test);
    //eng/public/images/zoom/eng/multiply2/Period5.png"
    if (in_array($parts[1], $route_guard)) {
        createDirectory($to);
        copy($from, $to);
    } else {
        /* $bad = '/sites/' . SITE_CODE . '/images/';
        if (strpos($to, $bad) !== false) {
            $good = '/assets/images/';
            $to = str_replace($bad, $good, $to);
            createDirectory($to);
            copy($from, $to);
        } else {
            writeLogError('copyFilesForCapacitor-19', $from . '->' . $to);
        }
    
    */
        $message = 'Unguarded route for ' . $to;
        trigger_error($message, E_USER_ERROR);
    }
}
