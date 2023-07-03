<?php
myRequireOnce('writeLog.php');
myRequireOnce('myGetPrototypeFile.php');

/*

Input is:

<div class="note-area" id="note1">
<form id="note1">Notes: (click outside box to save)<br />
<textarea rows="5"></textarea></form>
</div>

Output depends on destination

This is an attempt to have a growing note area on the iphone
SEE https://css-tricks.com/auto-growing-inputs-textareas/

  */
function modifyNoteArea($text,  $bookmark, $p)
{
    $template = myGetPrototypeFile('note.html');
    writeLogDebug('modifyNoteArea-23', $p['destination']);
    writeLogDebug('modifyNoteArea-24', $template);
    if ($p['destination'] == 'nojs') {
        $template = '<div class="note-removed">';
    }
    $count = substr_count($text, '<div class="note-area"');
    for ($i = 1; $i <= $count; $i++) {
        $pos_start = strpos($text, '<div class="note-area"');
        $pos_end = strpos($text, '</div>', $pos_start);
        $length = $pos_end - $pos_start + 6;
        $block = substr($text, $pos_start, $length);
        $row_start = strpos($block, 'rows="') + 6;
        $row_end = strpos($block, '">', $row_start);
        $row_length = $row_end - $row_start;
        $rows = substr($block, $row_start, $row_length);
        if ($rows == 1) {
            $label_start = strpos($block, '<form id="note#">') + 17;
            $label_end = strpos($block, '<br />', $label_start);
            $label_length = $label_end - $label_start;
        }
        $standard_instruction = $bookmark['language']->notes;
        $bad = array(
            '[id]',
            '[rows]',
            '[user_instruction]'
        );
        $good = array(

            'note' . $i . 'Text',
            $rows,
            $standard_instruction
        );
        $new_template = str_replace($bad, $good, $template);
        // from https://stackoverflow.com/questions/1252693/using-str-replace-so-that-it-only-acts-on-the-first-match
        $text = substr_replace($text, $new_template, $pos_start, $length);
    }
    return $text;
}

