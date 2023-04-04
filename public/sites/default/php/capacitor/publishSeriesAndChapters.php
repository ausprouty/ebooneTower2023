<?php
myRequireOnce('create.php');
myRequireOnce('dirMake.php');
myRequireOnce('fileWrite.php');
myRequireOnce('progressMerge.php');
myRequireOnce('publishFiles.php');
myRequireOnce('publishSeries.php');
myRequireOnce('publishPage.php');
myRequireOnce('writeLog.php');
myRequireOnce('publicationCache.php');


function publishSeriesAndChapters($p)
{
    $progress = new stdClass;
    $response = new stdClass;
    if (isset($p['progress'])) {
        $progress = $p['progress'];
    }
    if (!isset($p['resume'])) {
        $p['resume'] = 'false';
    }
    // first prototype the Series Index
    $response = (object) publishSeries($p);
    //writeLogDebug('Progress-publishSeriesandChapters-26', $response);
    $progress = progressMergeObjects($progress, $response, 'publishSeriesAndChapters-27');
    if (!isset($response->files_json)) {
        $response->message = 'No files_json returned from Publish Series. This may be a library';
        $response->progress = 'undone';
        $progress = progressMergeObjects($progress, $response, 'publishSeriesAndChapters-30');
        $out = $progress;
        return $out;
    }
    $files_json = $response->files_json; // this starts file for download of series
    $files_in_pages = [];
    // find the list of chapters that are ready to publish
    $series = contentArrayFromRecnum($p['recnum']);
    $text = json_decode($series['text']);
    // restore cache from previous publication attempt
    $cache = getCache($p);
    $files_in_pages =  $cache['files_included'];
    $chapters = $text->chapters;
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
        if ($chapter->publish) {
            $sql = "SELECT recnum FROM  content
                WHERE  country_code = '" . $series['country_code'] . "'
                AND language_iso = '" . $series['language_iso'] . "'
                AND folder_name = '" . $series['folder_name'] . "'
                AND filename = '" . $chapter->filename . "'
                AND prototype_date IS NOT NULL
                ORDER BY recnum DESC LIMIT 1";
            $data = sqlArray($sql);
            if ($data) {
                $p['recnum'] = $data['recnum'];
                // need to find latest record for recnum
                $response =  (object) publishPage($p);
                //writeLogAppend('progress-publishSeriesAndChapters-71', $response);
                if (isset($response->files_in_page)) {
                    $files_in_pages = publishSeriesAndChaptersCombineArrays($files_in_pages, $response->files_in_page);
                }
                $progress = progressMergeObjects($progress, $response, 'publishSeriesAndChapters-71');
                //writeLogAppend('progress-publishSeriesAndChapters-75', $progress);
            }
        }
        $cache['sessions_published'][] = $chapter->filename;
        $cache['files_included'] = $files_in_pages;
        updateCache($cache, DESTINATION);
    }
    clearCache($cache, DESTINATION);
    //writeLogDebug('progress-publishSeriesAndChapters-89', $progress);
    return $progress;
}



function publishSeriesAndChaptersCombineArrays($files_in_pages, $new_files)
{
    foreach ($new_files as $new) {
        if (!in_array($new, $files_in_pages)) {
            array_push($files_in_pages, $new);
        }
    }
    return $files_in_pages;
}
