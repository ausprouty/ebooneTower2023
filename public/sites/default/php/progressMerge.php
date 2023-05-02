<?php
function  progressMergeObjects($progress, $new_progress, $source = null)
{
    $out = new stdClass;

    if (isset($progress->progress)) {
        $out->progress = $progress->progress;
    }
    if (isset($new_progress->progress)) {
        if ($new_progress->progress == 'error') {
            $out->progress = 'error';
        }
    }
    if (isset($new_progress->message)) {
        if (isset($progress->message)) {
            $progress->message .= $new_progress->message;
        } else {
            $out->message = $new_progress->message;
        }
    }
    if (isset($progress->message)) {
        $out->message = $progress->message;
    }
    //writeLogAppend('progressMerge-24', "\n\n----IN---\n");
    //writeLogAppend('progressMerge-24', "\n$source\n");
    //writeLogAppend('progressMerge-24', $progress);
    //writeLogAppend('progressMerge-24', "\n----OUT---\n");
    //writeLogAppend('progressMerge-24', $out);
    //writeLogAppend('progressMerge-24', "\n-------Next Record-------\n\n\n");
    return $out;
}
function progressMergeArrays($array1, $array2)
{
    if (is_array($array1) & is_array($array2)) {
        return array_merge($array1, $array2);
    }
    if (!is_array($array1) & is_array($array2)) {
        return $array2;
    } else {
        return $array2;
    }
}
