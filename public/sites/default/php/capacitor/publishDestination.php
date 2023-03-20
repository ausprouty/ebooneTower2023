<?php
/*
define("ROOT_STAGING", '/home/globa544/staging.mc2.online/');
define("ROOT_WEBSITE", '/home/globa544/app.mc2.online/');



*/

function publishDestination($p)
{
    $destination = DESTINATION;
    if ($destination == 'staging') {
        return ROOT_STAGING;
    } elseif ($destination == 'website') {
        return ROOT_WEBSITE;
    } elseif ($destination == 'capacitor') {
        if (!isset($p['capacitor_settings'])) {
            $message = 'No Capacitor Settings';
            writeLogError('_publishDestinationCapacitor-p ', $p);
            trigger_error($message, E_USER_ERROR);
        }
        if (isset($p['capacitor_settings']->subDirectory)) {
            $bad = ['/', '..'];
            $clean = str_replace($bad, '', $p['capacitor_settings']->subDirectory);
        }
        return ROOT_CAPACITOR . $clean;
    }
    $message = 'In publishDestination invalid destination:  ' . $destination;
    writeLogError('capacitor-publishDestination-30', $p);
    trigger_error($message, E_USER_ERROR);
}
