<?php

function removeExternalLinks($p){
    if (isset($p['sdcard_settings'])){
        if (isset($p['sdcard_settings']->remove_external_links))
        return $p['sdcard_settings']->remove_external_links;
    }
    return false;
}