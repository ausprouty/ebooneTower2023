<?php

function onLoadJS($note_index)
{
    if ($note_index == "U1-eng-resource-resource03.html") {
        $note_index = 'U1-eng-pages-testimony.html';
    }
    $output = ' onLoad= "notesShow(\'' . $note_index . '\')" ';
    return $output;
}
