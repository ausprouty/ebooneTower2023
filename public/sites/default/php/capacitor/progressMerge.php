<?php
function  progressMerge($progress, $new_progress, $source = null)
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
    return $out;
}
