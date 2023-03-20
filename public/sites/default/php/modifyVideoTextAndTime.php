<?php

// return the text from the td_segment
function modifyVideoRevealFindText($old, $td_number)
{
    $pos_td = 0;
    for ($i = 1; $i <= $td_number; $i++) {
        $pos_td = strpos($old, '<td', $pos_td + 1);
    }
    $pos_start = strpos($old, '>', $pos_td) + 1;
    $pos_end = strpos($old, '</', $pos_td);
    $length = $pos_end - $pos_start;
    $text = substr($old, $pos_start, $length);
    $text = strip_tags($text);
    return $text;
}

function modifyVideoRevealFindTime($old, $td_number)
{
    $text = modifyVideoRevealFindText($old, $td_number);
    if ($text == 'start') {
        $time = 0;
    } else if ($text == 'end') {
        $time = null;
    } else {
        $time = timeToSeconds($text);
    }
    return $time;
}
//function timeToSeconds(string $time): int
function timeToSeconds($time)
{
    if (strpos($time, ':') == FALSE) {
        return intval($time);
    }
    $arr = explode(':', $time);
    if (count($arr) == 3) {
        return $arr[0] * 3600 + $arr[1] * 60 + $arr[2];
    } else {
        return $arr[0] * 60 + $arr[1];
    }
}
