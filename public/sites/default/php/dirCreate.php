<?php
myRequireOnce('dirMake.php');
myRequireOnce('getBuild.php', 'apk');

/*
define("ROOT_EDIT", '/home/globa544/edit.mc2.online/');
define("ROOT_EDIT_CONTENT", '/home/globa544/edit.mc2.online/sites/' . SITE_CODE . '/content/');
define("ROOT_LOG", '/home/globa544/edit.mc2.online/sites/logs/');
define("ROOT_STAGING", '/home/globa544/test_staging.mc2.online/');
define("ROOT_WEBSITE", '/home/globa544/test_publish.mc2.online/');
define("ROOT_SDCARD", ROOT . 'mc2.sdcard');
*/
function dirCreate($scope, $destination,  $p, $folders = null, $create = true)
{
    $dir = '';
    switch ($destination) {
        case 'apk':
            $dir = ROOT_APK . getBuild($p) . '/folder/content/';
            break;
        case 'edit':
            //define("ROOT_EDIT", '/home/globa544/edit.mc2.online/');
            $dir = ROOT_EDIT . '/sites/' . SITE_CODE . '/content/';
            break;
        case 'nojs':
            $dir = ROOT_SDCARD . $p['sdcard_settings']->subDirectory . '/folder/nojs/';
            break;
        case 'staging':
            $dir = ROOT_STAGING . 'content/';
            break;
        case 'website':
            $dir = ROOT_WEBSITE . 'content/';
            break;
        case 'sdcard':
            $dir = ROOT_SDCARD;
            if (isset($p['sdcard_settings']->subDirectory)) {
                $dir .= $p['sdcard_settings']->subDirectory . '/views/';
            }
            break;
        case 'root':
        case 'default':
            $dir = '/';
            break;
    }
    switch ($scope) {
        case 'country':
            $dir .=  $p['country_code'] . '/';
            break;
        case 'language':
            $dir .= $p['country_code']  . '/' . $p['language_iso'] . '/';
            break;
        case 'library':
            $dir .= $p['country_code']  . '/' . $p['language_iso'] . '/' . $p['library_code'] . '/';
            break;
        case 'series':
            $dir .= $p['country_code']  . '/' . $p['language_iso'] . '/' . $p['folder_name'] . '/';
            break;
        case 'json_series':
            $good = '/sites/' . SITE_CODE . '/content/' . $p['country_code']  . '/' . $p['language_iso'] . '/' . $p['folder_name'] . '/';
            $bad = '/content/';
            $dir = str_replace($bad, $good, $dir);
            break;
        case 'default':
    }
    $dir .= $folders;
    if ($create) {
        $dir = dirMake($dir);
    }
    $debug = array(
        'dir' => $dir,
        'scope' => $scope,
        'destination' => $destination,
        'folder' => $folders
    );
    writeLogAppend('dirCreate-60', $debug);
    return $dir;
}
