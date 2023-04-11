<?php

function createPageNavlink($p)
{
    $index = 'index.html';
    if ($p['library_code'] != 'library') {
        $index = $p['library_code'] . '.html';
    }
    $navlink = '../' . $index;
    return $navlink;
}
