<?php

myRequireOnce('fileWrite.php');

function createBookRouter($data, $p)
{
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
    $filename = '/router/routes' . ucfirst($data['language_iso'])  . ucfirst($data['folder_name'] . '.js');
    fileWrite($filename, $text, $p);
    writeLogDebug('routesCreateForSeries-51', $filename);
    return;
}
