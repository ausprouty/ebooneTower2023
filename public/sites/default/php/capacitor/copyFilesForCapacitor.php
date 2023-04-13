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
    $progress = new stdClass;
    $progress->progress = 'undone';
    $top_guard = ['src', 'public'];
    $src_guard = ['assets', 'components', 'router', 'views'];
    if (strpos($to, '@') !== false) {
        //writeLogError('copyFilesForCapacitor-10', $to);
        $progress->message = "<br><br>$to starts with @ in copyFilesForCapacitor";
        $progress->progress = 'error';
        return $progress;
    }
    if (strpos($from, '@') !== false) {
        //writeLogError('copyFilesForCapacitor-14', $to);
        $progress->message = "<br><br>$from starts with @ in copyFilesForCapacitor";
        $progress->progress = 'error';
        return $progress;
    }
    if ((strpos($from, '/assets/assets/') !== false) || (strpos($to, '/assets/assets/') !== false)) {
        //writeLogError('copyFilesForCapacitor-32', "$from => $to called by $called_by");
        $progress->message = "<br><br>$from => $to contains /assets/assets/ in copyFilesForCapacitor thanks to $called_by";
        $progress->progress = 'error';
        return $progress;
    }
    $to = str_replace('//', '/', $to);
    if (strpos($to, ROOT_CAPACITOR) === false) {
        //writeLogError('copyFilesForCapacitor-18', $to);
        $progress->message = "<br><br>Unguarded route of   $to in  $to in copyFilesForCapacitor";
        $progress->progress = 'error';
        return $progress;
    }
    $test = str_replace(ROOT_CAPACITOR, '', $to);
    $parts = explode('/', $test);
    //eng/public/images/zoom/eng/multiply2/Period5.png"
    if (in_array($parts[1], $top_guard)) {
        if ($parts[1] == 'src') {
            if (!in_array($parts[2], $src_guard)) {
                //writeLogError('capacitor-copyFilesForCapacitor-34', $parts);
                $progress->message = "<br><br>Unguarded route of   $parts[2] in  $to in copyFilesForCapacitor";
                $progress->progress = 'error';
                return $progress;
            }
        }
        createDirectory($to);
        if (file_exists($from)) {
            dirMake($to);
            if (!is_dir($to)) {
                copy($from, $to);
                // $progress->message = "<br><br>Copied $from when called by $called_by in copyFilesForCapacitor";
            } else {
                $progress->message = "<br><br>Destination  of $to is a directory when called by $called_by in copyFilesForCapacitor<br>";
                $progress->progress = 'error';
            }
        } else {
            $to = str_replace('/assets/assets/', '/assets/', $to);
            if (!file_exists($to)) {
                $progress->message = "<br><br>Source and Destination files do not exist.<br> Source: $from <br>Destination: $to <br>called by $called_by in copyFilesForCapacitor";
                $progress->progress = 'error';
            }
        }
    } else {
        $progress->message = "<br><br>Unguarded route of   $parts[1] in  $to in copyFilesForCapacitor";
        $progress->progress = 'error';
    }
    return $progress;
}
