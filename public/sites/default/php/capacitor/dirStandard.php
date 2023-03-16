<?php
myRequireOnce('dirMake.php');

function dirStandard($scope, $destination,  $p, $folders = null, $create = true)
{
    $dir = '';
    switch ($destination) {
        case 'capacitor':
            $dir = ROOT_CAPACITOR;
            if (isset($p['capacitorsettings']->subDirectory)) {
                $dir .= $p['capacitor_settings']->subDirectory;
            }
            break;
        case 'default':
            $dir = '/';
            break;
    }
    switch ($scope) {
        case 'assets':
            $dir .=  $p['language_iso'] . '/src/assets/';
            break;
        case 'public':
            $dir .=  $p['language_iso'] . '/public/';
            break;
        case 'router':
            $dir .=  $p['language_iso'] . '/src/router/';
            break;

        case 'series':
            $dir .=  $p['language_iso'] . '/src/views/' . $p['country_code']  . '/' . $p['language_iso'] . '/' . $p['folder_name'] . '/';
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
    writeLogAppend('capacitor-dirCreate-60', $debug);
    return $dir;
}
