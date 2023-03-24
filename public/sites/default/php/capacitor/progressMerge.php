<?php
function  progressMerge($progress, $new_progress)
{
    $out = new stdClass;
    if ($new_progress->progress == 'error') {
        $out->progress = 'error';
    } else {
        $out->progress = $progress->progress;
    }
    if (isset($new_progress->message)) {
        if (isset($progress->message)) {
            $progress->message .= $new_progress->message;
        } else {
            $progress->message = $new_progress->message;
        }
    }
    $out->message = $progress->message;
    return $out;
}
