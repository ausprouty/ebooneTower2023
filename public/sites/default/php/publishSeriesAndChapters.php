<?php
myRequireOnce('create.php');
myRequireOnce('dirMake.php');
myRequireOnce('fileWrite.php');
myRequireOnce('publishJsonSeriesIndex.php');
myRequireOnce('publishFiles.php');
myRequireOnce('publishSeries.php');
myRequireOnce('publishPage.php');
myRequireOnce('writeLog.php');
myRequireOnce('publicationCache.php');


function publishSeriesAndChapters($p)
{
    $progress = new stdClass();
    $response = new stdClass();
    if (!isset($p['resume'])) {
        $p['resume'] = 'false';
    }
    // first prototype the Series Index
    $out = publishSeries($p);
    $files_in_pages = [];
    // find the list of chapters that are ready to publish
    $series = contentArrayFromRecnum($p['recnum']);
    $text = json_decode($series['text']);
    // restore cache from previous publication attempt
    $cache = getCache($p);
    //writeLogAppend('publishSeriesAndChapters-30', $cache);
    $chapters = $text->chapters;
    writeLogDebug('publishSeriesAndChapters-33', $p);
    foreach ($chapters as $chapter) {
        // it is possible that the server has finished the previous task and has
        // deleted the cache.  You do not want to do everything over again.
        if (count($cache['sessions_published']) < 1 && $p['resume'] !== 'false') {
            continue;
        }
        // skip if in cache 
        if (in_array($chapter->filename, $cache['sessions_published'])) {
            continue;
        }
        $sql = NULL;
        if (DESTINATION == 'staging' && $chapter->prototype) {
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
                $response =  publishPage($p);
                $progress = progressMerge($progress, $response->progress, 'publishSeriesAndChapters-75');
            } else {
                $response->message = "Record not found for $sql";
                $progress->progress = 'notdone';
                return $progress;
            }
        }

        $cache['sessions_published'][] = $chapter->filename;
        $cache['files_included'] = $files_in_pages;
        updateCache($cache, DESTINATION);
    }
    publishJsonSeriesIndex($p);
    clearCache($cache, DESTINATION);
    $progress->message = 'I finished publishSeriesAndChapters';
    $progress->progress = 'done';
    return $progress;
}
function publishSeriesAndChaptersCombineArrays($files_in_pages, $new_files)
{
    //writeLogDebug('publishSeriesAndChaptersCombineArrays-101', $files_in_pages);
    //writeLogDebug('publishSeriesAndChaptersCombineArrays-102', $new_files);
    foreach ($new_files as $new) {
        writeLogAppend('publishSeriesAndChaptersCombineArrays-104', $new);
        if (!in_array($new, $files_in_pages)) {
            array_push($files_in_pages, $new);
        }
    }
    //writeLogAppend('publishSeriesAndChaptersCombineArrays-109', $files_in_pages);
    return $files_in_pages;
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
