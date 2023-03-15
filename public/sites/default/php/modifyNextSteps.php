<?php
myRequireOnce('writeLog.php');

function modifyNextSteps($text, $bookmark, $p)
{
    $count = $bookmark['page']->count;
    if (strpos($text, '"previous-step-area"') !== false) {
        $text = modifyNextStepsPrevious($text, $count);
    }
    if (strpos($text, '"next-step-area"') !== false) {
        $text = modifyNextStepsNext($text, $count);
    }
    return $text;
}
/*finding
<div class="previous-step-area">
<h3>Previous Goal</h3>
<form><input id="previous-step" type="hidden" value="#" /><textarea class="next-steps" id="next-step-text#" onkeyup="saveStepWritten(#)" placeholder="My next step from the previous session" rows="3"></textarea>
<div class="action-progress">
<div><input id="next-step-complete#" type="checkbox" /> <label> Finished</label></div>
<div><button onclick="shareStep(#)">Share</button></div>
</div>
</form>
</div>

*/

function modifyNextStepsPrevious($text, $count)
{
    $previous_count = $count - 1;
    $template = '<div class="previous-step-area">
        <h3>Previous Goal</h3>
        <form>
        <input id="previous-step" type="hidden" value="#" />
        <textarea class="textarea resize-ta" id="next-step-text#" onkeyup="saveStepWritten(#)"
              placeholder="My next step from the previous session" rows="3"></textarea>
        <div class="action-progress">
        <div><input id="next-step-complete#" type="checkbox" onclick ="saveStepWritten(#)"/> <label> Finished</label></div>
        <div><button onclick="shareStep(#)">Share</button></div>
        </div>
        </form>';
    $begin = '<div class="previous-step-area">';
    $end = '</form>';
    $template = str_replace('#', $previous_count, $template);
    $pos_start = strpos($text, $begin);
    $pos_end = strpos($text, $end, $pos_start);
    $length = $pos_end - $pos_start + strlen($end);
    // from https://stackoverflow.com/questions/1252693/using-str-replace-so-that-it-only-acts-on-the-first-match
    $text = substr_replace($text, $template, $pos_start, $length);

    return $text;
}
/*
<div class="next-step-area">
<h3>Next Step Goal</h3>
<form><input id="next-step" type="hidden" value="#" /><textarea class="next-steps" id="next-step-text#" onkeyup="saveStepWritten(#)" placeholder="I will ___ by ____(when)" rows="3"></textarea>
<div class="action-progress">
<div><input id="next-step-complete#" type="checkbox" />Share</div>
</div>
</form>

*/
function modifyNextStepsNext($text, $count)
{
    $template = '<div class="next-step-area">
        <h3>Next Step Goal</h3>
        <form>
        <input id="next-step" type="hidden" value="#" />
        <textarea class="textarea resize-ta" id="next-step-text#" onkeyup="saveStepWritten(#)"
         placeholder="I will _______ (do) by_______ (when)" rows="3"></textarea>
        <div class="action-progress">
        <div><input id="next-step-complete#" type="checkbox" onclick ="saveStepWritten(#)"/> <label> Finished</label></div>
        <div><button onclick="shareStep(#)">Share</button></div>
        </div>
        </form>';
    $template = str_replace('#', $count, $template);
    $begin = '<div class="next-step-area">';
    $end = '</form>';
    $pos_start = strpos($text, $begin);
    $pos_end = strpos($text, $end, $pos_start);
    $length = $pos_end - $pos_start + strlen($end);
    $text = substr_replace($text, $template, $pos_start, $length);
    return $text;
}
