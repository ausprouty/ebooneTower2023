<?php
/* 
/home/globa544/edit.mc2.online/sites/mc2/content/M2/eng/multiply2/Period5.png
->
/home/globa544/mc2.capacitor/eng/public/images/zoom/eng/multiply2/Period5.png"

/home/globa544/edit.mc2.online/sites/mc2/content/M2/eng/multiply2/Period5.png
->
/home/globa544/mc2.capacitor/eng/src/assets/sites/mc2/content/M2/eng/multiply2/Period5.png"
*/
myRequireOnce('dirMake.php');
function copyFilesForCapacitor($from, $to, $called_by)

{
    $progress = new stdClass();
    $progress->progress = 'undone';
    $top_guard = ['src', 'public'];
    $src_guard = ['assets', 'components', 'router', 'views'];
    if (strpos($to, '@') !== false) {
        writeLogError('copyFilesForCapacitor-10', $to);
        $progress->message = "$to starts with @ in copyFilesForCapacitor";
        $progress->progress = 'error';
        return $progress;
    }
    if (strpos($from, '@') !== false) {
        writeLogError('copyFilesForCapacitor-14', $to);
        $progress->message = "$from starts with @ in copyFilesForCapacitor";
        $progress->progress = 'error';
        return $progress;
    }
    $to = str_replace('//', '/', $to);
    if (strpos($to, ROOT_CAPACITOR) === false) {
        writeLogError('copyFilesForCapacitor-18', $to);
        $progress->message = "Unguarded route of   $parts[2] in  $to in copyFilesForCapacitor";
        $progress->progress = 'error';
        return $progress;
    }
    $test = str_replace(ROOT_CAPACITOR, '', $to);
    $parts = explode('/', $test);
    //eng/public/images/zoom/eng/multiply2/Period5.png"
    if (in_array($parts[1], $top_guard)) {
        if ($parts[1] == 'src') {
            if (!in_array($parts[2], $src_guard)) {
                writeLogError('capacitor-copyFilesForCapacitor-34', $parts);
                $progress->message = "Unguarded route of   $parts[2] in  $to in copyFilesForCapacitor";
                $progress->progress = 'error';
                return $progress;
            }
        }
        createDirectory($to);
        if (file_exists($from)) {
            dirMake($to);
            if (!is_dir($to)) {
                copy($from, $to);
                $progress->progress = 'done';
            } else {
                $progress->message = "Destination  of $to is a directory when called by $called_by in copyFilesForCapacitor";
                $progress->progress = 'error';
            }
        } else {
            $progress->message = "Source file does not exist $from when called by $called_by in copyFilesForCapacitor";
            $progress->progress = 'error';
        }
    } else {
        $progress->message = "Unguarded route of   $parts[1] in  $to in copyFilesForCapacitor";
        $progress->progress = 'error';
    }
    return $progress;
}
