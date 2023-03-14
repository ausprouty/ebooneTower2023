<?php

function removeLinksExternal($p)
{
    if (isset($p['capacitor_settings'])) {
        if (isset($p['capacitor_settings']->remove_external_links))
            return $p['capacitor_settings']->remove_external_links;
    }
    return false;
}
