<?php

function createSeriesNavlink($p){
    writeLogDebug('createSeriesNavLink-myfriends', $p);
    $navlink = '../index.html';
    // for libraries that are nested
    if ($p['language_iso'] == 'eng' && $p['country_code']== 'AU'){
        $navlink = '../'. $p['library_code'];
    }
    if ($p['language_iso'] == 'deu' 
        && $p['country_code']== 'AT'
        && $p['library_code'] != 'library'){
            $navlink = '../'. $p['library_code'] . '.html';
    }
    return $navlink;
}