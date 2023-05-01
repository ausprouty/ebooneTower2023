<?php

function onLoadJS($note_index)
{
    $output = ' onLoad= "showNotes(\'' . $note_index . '\')" ';
    return $output;
}
