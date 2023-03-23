<?php
function  progressMerge($progress, $new_progress){
    if ($new_progress->progress == 'error'){
        $progress->progress = 'error';
    }
    if (isset($new_progress->message)){
        if (isset($progress->message)){
            $progress->message .= $new_progress->message;
        }
        else{
            $progress->message = $new_progress->message;
        }
    }
    return $progress;
}