<?php

myRequireOnce('findFilesInText.php');

function publishJsonSeriesIndex($p)
{
    //
    //find series data
    //
    if (DESTINATION == 'staging') {
        $sql = "SELECT * FROM content
            WHERE  country_code = '" . $p['country_code'] . "'
            AND  language_iso = '" . $p['language_iso'] . "'
            AND folder_name = '" . $p['folder_name'] . "'  AND filename = 'index'
            ORDER BY recnum DESC LIMIT 1";
    } else {
        $sql = "SELECT * FROM content
            WHERE  country_code = '" . $p['country_code'] . "'
            AND  language_iso = '" . $p['language_iso'] . "'
            AND folder_name = '" . $p['folder_name'] . "'  AND filename = 'index'
            AND prototype_date IS NOT NULL
            ORDER BY recnum DESC LIMIT 1";
    }
    $data = sqlArray($sql);
    if (!$data) {
        $message = 'No data found for: ' . $sql;
        writeLogError('publishSeries-29', $message);
        return $p;
    }
    $files_in_pages = [];
    $text = json_decode($data['text']);
    if (isset($text->chapters)) {
        $series_dir =  dirStandard('series', DESTINATION,  $p, $folders = null, $create = false);
        foreach ($text->chapters as $chapter) {
            if ($p['destination'] == 'staging') {
                if (isset($chapter->prototype)) {
                    $status = $chapter->prototype;
                }
            } else {
                $status = $chapter->publish;
            }
            //_write_series_log($p, $chapter);
            if ($status  == true) {
                $file_name = $series_dir . $chapter->filename . '.html';
                $text = file_get_contents($file_name);
                $find_begin = 'src="';
                $files_in_pages = findFilesinText($find_begin, $text, $p, $files_in_pages);
                $find_begin = 'href="';
                $files_in_pages = findFilesinText($find_begin, $text, $p, $files_in_pages);
            }
        }
    }
    writeLogDebug('publishJsonSeriesIndex-54', $files_in_pages);
}
