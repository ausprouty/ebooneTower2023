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
    $new_progress = new stdClass;
    if (isset($p['progress'])) {
        $progress = $p['progress'];
    }
    if (!isset($p['resume'])) {
        $p['resume'] = 'false';
    }

    // first prototype the Series Index
    $out = publishSeries($p);
    writeLogDebug('Progress-publishSeriesandChapters-26', $out);
    $new_progress = $out['progress'];
    $progress = progressMerge($progress, $new_progress, 'publishSeriesAndChapters-27');
    if (!isset($out['files_json'])) {
        $new_progress->message = 'No files_json returned from Publish Series. This may be a library';
        $progress = progressMerge($progress, $new_progress, 'publishSeriesAndChapters-30');
    }
    $files_json = $out['files_json']; // this starts file for download of series
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
        $sql = NULL;

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
                $result =  publishPage($p);
                if (is_array($result)) {
                    if (isset($result['files_in_page'])) {
                        $files_in_pages = publishSeriesAndChaptersCombineArrays($files_in_pages, $result['files_in_page']);
                    }
                    if (isset($result['progress'])) {
                        $progress = progressMerge($progress, $result['progress'], 'publishSeriesAndChapters-71');
                    }
                }
            } else {
                $new_progress->message = "No data found for  $chapter->filename in publishSeriesAndChapters";
                $new_progress->progress = 'error';
                $progress = progressMerge($progress, $new_progress, 'publishSeriesAndChapters-78');
            }
        }
        $cache['sessions_published'][] = $chapter->filename;
        $cache['files_included'] = $files_in_pages;
        updateCache($cache, DESTINATION);
    }
    clearCache($cache, DESTINATION);
    $out['progress'] = $progress;
    return $out;
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
