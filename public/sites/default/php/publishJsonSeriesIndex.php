<?php

myRequireOnce('findFilesInText.php');
myRequireOnce('dirStandard.php');

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
        $series_dir_full =  dirStandard('series', DESTINATION,  $p, $folders = null, $create = false);
        $series_dir_short =  dirStandard('series', 'content',  $p, $folders = null, $create = false);
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
                $chapter_name =  $series_dir_short .  $chapter->filename . '.html';
                $files_in_pages[$chapter_name] = $chapter_name;
                $file_name = $series_dir_full . $chapter->filename . '.html';
                writeLogAppend('publishJsonSeriesIndex-49', $file_name);
                $text = file_get_contents($file_name);
                $find_begin = 'src="';
                $files_in_pages = findFilesinText($find_begin, $text, $p, $files_in_pages);
                $find_begin = 'href="';
                $files_in_pages = findFilesinText($find_begin, $text, $p, $files_in_pages);
            }
        }
    }
    writeLogDebug('publishJsonSeriesIndex-54', $files_in_pages);
    $json_text = '[';
    foreach ($files_in_pages as $page) {
        $page = str_replace('//', '/', $page);
        $json_text .= '{"url":"/' . $page . '"},' . "\n";
    }
    $json_text = substr($json_text, 0, -2);
    $json_text .= ']';
    writeLogDebug('publishJsonSeriesIndex-60', $json_text);
}

function publishSeriesAndChaptersMakeJsonIndex($files_json, $files_in_pages, $p)
{
    //
    // Create files.json with list of files to download of offline use.
    //list of html files is created in createSeries near line 125
    // this routine gets rid of duplicates
    $clean_files_in_pages = [];
    foreach ($files_in_pages as $f) {
        if ($f != '/') {
            $clean_files_in_pages[$f] = $f;
        }
    }
    foreach ($clean_files_in_pages as $json) {
        $files_json .= '{"url":"' . $json . '"},' . "\n";
    }
    $files_json = substr($files_json, 0, -2) . "\n" . ']' . "\n";
    // json file needs to be in sites/mc2/content/M2/eng/multiply1
    //writeLogDebug('publishSeriesAndChapters-92', DESTINATION);
    $json_series_dir = dirStandard('json_series', DESTINATION,  $p, $folders = null);
    //writeLogDebug('publishSeriesAndChapters-94', $p);
    $filename =  $json_series_dir . 'files.json';
    //writeLogDebug('publishSeriesAndChapters-96', $filename);
    fileWrite($filename, $files_json, $p);
}
