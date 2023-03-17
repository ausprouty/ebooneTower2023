<?php

myRequireOnce('fileWrite.php');
myRequireOnce('dirStandard.php');

function createBookRouter($p)
{
    // when coming in with only book information the folder_name is not yet set
    if (!isset($p['folder_name'])) {
        if (isset($p['code'])) {
            $p['folder_name'] = $p['code'];
        }
    }
    //
    //find series data
    //
    $sql = "SELECT * FROM content
            WHERE  country_code = '" . $p['country_code'] . "'
            AND  language_iso = '" . $p['language_iso'] . "'
            AND folder_name = '" . $p['folder_name'] . "'  AND filename = 'index'
            AND prototype_date IS NOT NULL
            ORDER BY recnum DESC LIMIT 1";
    $data = sqlArray($sql);
    if (!$data) {
        // most likely this is not a series
        $message = 'No data found for: ' . $sql;
        writeLogAppend('WARNING- capacitor-createBookRouter-51', $message);
        return 'undone';
    }
    $series = json_decode($data['text']);
    $template = '
    {
        path: "[path]",
        name: "[name]",
        component: function () {
          return import(
            /* webpackChunkName: "prototype" */ "../views/[vue].vue"
          );
        },
    },';
    $old = array(
        '[path]',
        '[name]',
        '[vue]'
    );
    $text = "export default[\n";
    // add index
    $path =     '/' . $data['country_code'] . '/' . $data['language_iso']  . '/' . $data['folder_name'] . '/Index';
    $name = $data['language_iso'] . '-' . $data['folder_name'] . '-index';
    $vue =  $data['country_code'] . '/' . $data['language_iso']  . '/' . $data['folder_name'] . '/' . ucfirst($data['language_iso']) .  ucfirst($data['folder_name']) . 'Index';
    $new = array(
        $path,
        $name,
        $vue,

    );
    $item = str_replace($old, $new, $template);
    $text .= $item . "\n";
    foreach ($series->chapters as $chapter) {
        $path =     '/' . $data['country_code'] . '/' . $data['language_iso']  . '/' . $data['folder_name'] . '/' . $chapter->filename;
        $name = $data['language_iso'] . '-' . $chapter->filename;
        $vue =  $data['country_code'] . '/' . $data['language_iso']  . '/' . $data['folder_name'] . '/' . ucfirst($data['language_iso']) . ucfirst($chapter->filename);
        $new = array(
            $path,
            $name,
            $vue,

        );
        $item = str_replace($old, $new, $template);
        $text .= $item . "\n";
    }
    $text .= '];';
    $dir = dirStandard('router', DESTINATION,  $p);
    $filename = 'routes' . ucfirst($p['language_iso'])  . ucfirst($p['folder_name'] . '.js');
    $router = $dir . $filename;
    fileWrite($router, $text, $p);
    writeLogDebug('capacitor-createBookRouter-51', $router);
    return 'done';
}
