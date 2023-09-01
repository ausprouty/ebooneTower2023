<?php

function createLibraryNavlink($p)
{
    if ($p['country_code'] == 'AU' && $p['language_iso'] == 'eng'){
    $navlink  = 'index.html';
    }
    else{
        $navlink  = '../index.html';
    }
    return $navlink;
}
