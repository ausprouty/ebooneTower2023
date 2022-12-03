<?php

myRequireOnce('fileWrite.php');

function routesCreateForSeries($data, $p)
{
    $series = json_decode($data['text']);
    writeLogDebug('PublishRoutes-6', $data);
    writeLogDebug('PublishRoutes-7', $text);
    $template = '
    {
        path: "[path]",
        name: "[name]",
        component: function () {
          return import(
            /* webpackChunkName: "prototype" */ "../views/[vue].vue"
        );
    },';
    $old = array(
        '[path]',
        '[name]',
        '[vue]'
    );
    $text = "export default[\n";
    foreach ($series->chapters as $chapter) {
        $path = $data['country_code'] . '/' . $data['language_iso']  . '/' . $data['folder_name'] . '/' . $chapter['filename'];
        $name = $data['language_iso'] . '-' . $chapter['filename'];
        $vue =  $data['country_code'] . '/' . $data['language_iso']  . '/' . $data['folder_name'] . '/Session' . ucfirst($chapter['filename']);
        $new = array(
            $path,
            $name,
            $vue,

        );
        $new = str_replace($old, $new, $template);
        $text .= $new . "\n";
    }
    $text .= '];';
    $filename = '/router/routes/' . ucfirst($data['language_iso'])  . ucfirst($data['folder_name']);
    fileWrite($filename, $text, $p);
    return;
}
