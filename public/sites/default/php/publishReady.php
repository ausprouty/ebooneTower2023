<?php
myRequireOnce('writeLog.php');
// is this item authorized to be published?
function publishReady($item, $destination)
{

    switch ($destination) {
        case "staging":
            if ($item->prototype) {
                return true;
            } else {
                return false;
            }
        case "apk":
        case "capacitor":
        case "nojs":
        case "sdcard":
            case "website":
            if ($item->publish) {
                return true;
            } else {
                return false;
            }
        default:
            return false;
    }
}
