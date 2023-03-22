<?php
/* 
/home/globa544/edit.mc2.online/sites/mc2/content/M2/eng/multiply2/Period5.png
->
/home/globa544/mc2.capacitor/eng/public/images/zoom/eng/multiply2/Period5.png"
*/
myRequireOnce('dirMake.php');
function copyFilesForCapacitor($from, $to, $called_by)

{
    $top_guard = ['src', 'public'];
    $src_guard = ['assets', 'components', 'router', 'views'];
    if (strpos($to, '@') !== false) {
        writeLogError('copyFilesForCapacitor-10', $to);
        return;
    }
    if (strpos($from, '@') !== false) {
        writeLogError('copyFilesForCapacitor-14', $to);
        return;
    }
    $to = str_replace('//', '/', $to);
    $message = "$to\n$from\n$called_by\n\n";
    writeLogAppend('capacitor-copyFilesForCapacitor-7', $message);

    if (strpos($to, ROOT_CAPACITOR) === false) {
        writeLogError('copyFilesForCapacitor-18', $to);
        return;
    }
    $test = str_replace(ROOT_CAPACITOR, '', $to);
    $parts = explode('/', $test);
    //eng/public/images/zoom/eng/multiply2/Period5.png"
    if (in_array($parts[1], $top_guard)) {
        if ($parts[1] == 'src') {
            if (!in_array($parts[2], $src_guard)) {
                writeLogError('capacitor-copyFilesForCapacitor-34', $parts);
                $message = "Unguarded route of   $parts[2] in  $to";
                trigger_error($message, E_USER_ERROR);
            }
        }
        createDirectory($to);
        if (file_exists($from)) {
            dirMake($to);
            if (!is_dir($to)) {
                copy($from, $to);
                writeLogAPPEND('CHECK- capacitor-copyFilesForCapacitor-45', $to);
            } else {
                writeLogAPPEND('ERROR- capacitor-copyFilesForCapacitor-47', $to);
            }
        } else {
            $message = "Source file does not exist $from when called by $called_by";
            trigger_error($message, E_USER_ERROR);
        }
    } else {
        writeLogError('capacitor-copyFilesForCapacitor-42', $parts);
        $message = "Unguarded route of   $parts[1] in  $to";
        trigger_error($message, E_USER_ERROR);
    }
}
