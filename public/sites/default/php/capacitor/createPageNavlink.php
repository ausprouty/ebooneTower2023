<?php

function createPageNavlink($p)
{
    $navlink = $p['language_iso'] . '-' . $p['folder_name'] . '-index';
    return $navlink;
}
