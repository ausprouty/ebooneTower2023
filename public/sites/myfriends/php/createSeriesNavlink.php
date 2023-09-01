<?php

function createSeriesNavlink($p){
    writeLogDebug('createSeriesNavLink-myfriends', $p);
    $navlink = '../index.html';
    if ($p['language_iso'] == 'eng' && $p['country_code']== 'AU'){
        $navlink = '../'. $p['library_code'];
    }
    return $navlink;
}