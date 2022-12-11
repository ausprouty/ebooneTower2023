<?php
myRequireOnce('create.php');
myRequireOnce('dirMake.php');
myRequireOnce('fileWrite.php');
myRequireOnce('publishFiles.php');
myRequireOnce('publishSeries.php');
myRequireOnce('publishPage.php');
myRequireOnce('writeLog.php');


function publishSeriesAndChapters($p)
{
    // first prototype the Series Index
    $out = publishSeries($p);
    if (!isset($out['files_json'])) {
        $message = 'No files_json returned from Publish Series';
        writeLogError('publishSeriesAndChapters-17', $message);
        writeLogError('publishSeriesAndChapters-18', $p);
        // this will happen if you have a sublibrary
    }
    $files_json = $out['files_json']; // this starts file for download of series
    //writeLogDebug('publishSeriesAndChapters-21', $files_json);
    $files_in_pages = [];
    // find the list of chapters that are ready to publish
    $series = contentArrayFromRecnum($p['recnum']);
    $text = json_decode($series['text']);
    $chapters = $text->chapters;
    foreach ($chapters as $chapter) {
        $sql = NULL;
        if ($p['destination'] == 'staging' && $chapter->prototype) {
            $sql = "SELECT recnum FROM  content
                WHERE  country_code = '" . $series['country_code'] . "'
                AND language_iso = '" . $series['language_iso'] . "'
                AND folder_name = '" . $series['folder_name'] . "'
                AND filename = '" . $chapter->filename . "'
                ORDER BY recnum DESC LIMIT 1";
        } elseif ($chapter->publish) {
            $sql = "SELECT recnum FROM  content
                WHERE  country_code = '" . $series['country_code'] . "'
                AND language_iso = '" . $series['language_iso'] . "'
                AND folder_name = '" . $series['folder_name'] . "'
                AND filename = '" . $chapter->filename . "'
                AND prototype_date IS NOT NULL
                ORDER BY recnum DESC LIMIT 1";
        }
        if ($sql) {
            $data = sqlArray($sql);
            if ($data) {
                $p['recnum'] = $data['recnum'];
                // need to find latest record for recnum
                $result =  publishPage($p);
                if (is_array($result)) {
                    if (isset($result['files_in_page'])) {
                        $files_in_pages = array_merge($files_in_pages, $result['files_in_page']);
                    }
                }
            } else {
                // find file and add to database
                $series_dir = dirCreate('series', $p['destination'],  $p, $folders = null, $create = false);
                $file =   $series_dir .  $chapter->filename . '.html';
                if (file_exists($file)) {
                    $p['text'] = file_get_contents($file);
                    $p['filename'] = $chapter->filename;
                    createContent($p);
                    $data = sqlArray($sql);
                    $p['recnum'] = $data['recnum'];
                    $result =  publishPage($p);
                    if (is_array($result['files_in_page'])) {
                        $files_in_pages = array_merge($files_in_pages, $result['files_in_page']);
                    }
                } else {
                    $message = 'NO RESULT for ' . $file . "\n";
                    writeLogError('publishSeriesAndChapters-66', $message);
                }
            }
        }
    }
    if ($p['destination'] == 'website' || $p['destination'] == 'staging') {
        publishSeriesAndChaptersMakeJsonIndex($files_json, $files_in_pages, $p);
    }
    return true;
}

function publishSeriesAndChaptersMakeJsonIndex($files_json, $files_in_pages, $p)
{
    if ($p['destination'] == 'sdcard') {
        writeLogDebug('publishSeriesAndChaptersMakeJsonIndex90', $p['destination']);
        return;
    }

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
    //writeLogDebug('publishSeriesAndChapters-92', $p['destination']);
    $json_series_dir = dirCreate('json_series', $p['destination'],  $p, $folders = null);
    //writeLogDebug('publishSeriesAndChapters-94', $p);
    $filename =  $json_series_dir . 'files.json';
    writeLogDebug('publishSeriesAndChapters-96', $filename);
    fileWrite($filename, $files_json, $p);
}
