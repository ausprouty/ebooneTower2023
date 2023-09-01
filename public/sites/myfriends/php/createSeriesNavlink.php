<?php

function createSeriesNavlink($p){
    writeLogDebug('createSeriesNavLink-myfriends', $p);
    $navlink = '../index.html';
    // in English all four of these are at the same level
    if ( $p['library_code'] =='friends.html'){
         $navlink = '../friends.html';
    }
    elseif ($p['library_code'] =='meet.html'){
        $navlink = '../meet.html';
    }
    return $navlink;
}