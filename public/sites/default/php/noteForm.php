<?php
function noteForm($page)
{

    $note_form_begin = '<form>' . "\n";
    $note_page =  '<input type="hidden" name ="notes_page"  id ="notes_page" value="' . $page . '">' . "\n";
    $note_form_end = '</form>';
    $output = $note_form_begin . $note_page .  $note_form_end;
    return $output;
}
