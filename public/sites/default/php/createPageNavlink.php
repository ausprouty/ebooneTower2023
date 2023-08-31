<?php

function createPageNavlink($p)
{
    $navlink = 'index.html';
    if ($p['library_code'] != 'library') {
        $navlink = $p['library_code'] . '.html';
    }
    if ($p['library_code'] == 'library' && $p['folder_name'] == 'pages') {
        $navlink  = '../index.html';
    }
    //writeLogDebug('createPageNavlink-10', $p);
    return $navlink;
}
