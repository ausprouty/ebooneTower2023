<?php


function noteForm($page)
{
    // both of these pages point to the testimony; lets use the latter one
    if ($page == "U1-eng-resource-resource03.html") {
        $page = 'U1-eng-pages-testimony.html';
    }
    $note_form_begin = '<form>' . "\n";
    $note_page =  '<input type="hidden" name ="notes_page"  id ="notes_page" value="' . $page . '">' . "\n";
    $note_form_end = '</form>';
    $output = $note_form_begin . $note_page .  $note_form_end;
    return $output;
}
