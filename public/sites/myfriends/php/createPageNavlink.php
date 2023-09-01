<?php

function createPageNavlink($p)
{
    $navlink = 'index.html';
    if ($p['library_code'] == 'library' && $p['folder_name'] == 'pages') {
        $navlink  = '../index.html';
    }
    return $navlink;
}
