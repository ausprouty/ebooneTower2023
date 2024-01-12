<?php

function createLibraryNavlink($p)
{   // this is for countries that have libraries stacked
    if ($p['country_code'] == 'AU' && $p['language_iso'] == 'eng'){
        $navlink  = 'index.html';
    }
    elseif ($p['country_code'] == 'AT' && $p['language_iso'] == 'deu'){
        $navlink  = 'index.html';
    }
    else{
        $navlink  = '../index.html';
    }
    return $navlink;
}
