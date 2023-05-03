<?php
myRequireOnce('dirMake.php');


/*
define("ROOT_EDIT", '/home/globa544/edit.mc2.online/');
define("ROOT_EDIT_CONTENT", '/home/globa544/edit.mc2.online/sites/' . SITE_CODE . '/content/');
define("ROOT_LOG", '/home/globa544/edit.mc2.online/sites/logs/');
define("ROOT_STAGING", '/home/globa544/test_staging.mc2.online/');
define("ROOT_WEBSITE", '/home/globa544/test_publish.mc2.online/');
define("ROOT_SDCARD", ROOT . 'mc2.sdcard');
*/
function dirStandard($scope, $destination,  $p, $folders = null, $create = true)
{
    $dir = '';
    switch ($destination) {
        case 'content':
            $dir = '/content/';
            break;
        case 'edit':
            //define("ROOT_EDIT", '/home/globa544/edit.mc2.online/');
            $dir = ROOT_EDIT . '/sites/' . SITE_CODE . '/content/';
            break;
        case 'staging':
            $dir = ROOT_STAGING . 'content/';
            break;
        case 'website':
            $dir = ROOT_WEBSITE . 'content/';
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
        case 'json_series':
        case 'series':
            $dir .= $p['country_code']  . '/' . $p['language_iso'] . '/' . $p['folder_name'] . '/';
            break;
        case 'page':
            $dir .= $p['country_code']  . '/' . $p['language_iso'] . '/pages/';
            break;
        case 'xjson_series':
            $good = '/sites/' . SITE_CODE . '/content/' . $p['country_code']  . '/' . $p['language_iso'] . '/' . $p['folder_name'] . '/';
            $bad = '/content/';
            $dir = str_replace($bad, $good, $dir);
            writeLogDebug('dirStandard-52', $dir);
            break;
        case 'default':
    }
    if ($folders) {
        $dir .= $folders;
    }
    if ($create) {
        $dir2 = dirMake($dir);
        writeLogAppend('dirStandard-60', "$dir  |  $dir2");
        $dir = $dir2;
    }
    $debug = array(
        'dir' => $dir,
        'scope' => $scope,
        'destination' => $destination,
        'folder' => $folders
    );
    writeLogAppend('dirStandard-70', $debug);
    return $dir;
}
