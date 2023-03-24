<?php
myRequireOnce('create.php');
myRequireOnce('dirMake.php');
myRequireOnce('fileWrite.php');
myRequireOnce('publishFiles.php');
myRequireOnce('publishSeries.php');
myRequireOnce('publishPage.php');
myRequireOnce('writeLog.php');
myRequireOnce('publicationCache.php');


function publishSeriesAndChapters($p)
{
    if (!isset($p['resume'])) {
        $p['resume'] = 'false';
    }

    // first prototype the Series Index
    $out = publishSeries($p);
    writeLogDebug('publishSeriesAndChapters -20', $out);

    if (!isset($out['files_json'])) {
        $message = 'No files_json returned from Publish Series';
        writeLogError('capacitor-publishSeriesAndChapters-17', $message);
        writeLogError('capacitor-publishSeriesAndChapters-18', $p);
        // this will happen if you have a sublibrary
    }
    $files_json = $out['files_json']; // this starts file for download of series
    //writeLogDebug('publishSeriesAndChapters-21', $files_json);
    $files_in_pages = [];
    // find the list of chapters that are ready to publish
    $series = contentArrayFromRecnum($p['recnum']);
    $text = json_decode($series['text']);
    // restore cache from previous publication attempt
    $cache = getCache($p);
    //writeLogAppend('publishSeriesAndChapters-30', $cache);
    $files_in_pages =  $cache['files_included'];
    $chapters = $text->chapters;
    writeLogDebug('capacitor-publishSeriesAndChapters-33', $p);
    foreach ($chapters as $chapter) {
        // it is possible that the server has finished the previous task and has
        // deleted the cache.  You do not want to do everything over again.
        if (count($cache['sessions_published']) < 1 && $p['resume'] !== 'false') {
            // writeLogAppend('publishSeriesAndChapters-39', $chapter->filename);
            continue;
        }
        // skip if in cache 
        if (in_array($chapter->filename, $cache['sessions_published'])) {
            //writeLogAppend('publishSeriesAndChapters-45', $chapter->filename);
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
                }
            } else {
                $message = 'No data found for ' . $chapter->filename;
                trigger_error($message, E_USER_ERROR);
            }
        }

        $cache['sessions_published'][] = $chapter->filename;
        $cache['files_included'] = $files_in_pages;
        updateCache($cache, DESTINATION);
    }
    clearCache($cache, DESTINATION);
    return $out;
}
function publishSeriesAndChaptersCombineArrays($files_in_pages, $new_files)
{
    foreach ($new_files as $new) {
        writeLogAppend('ppublishSeriesAndChaptersCombineArrays-104', $new);
        if (!in_array($new, $files_in_pages)) {
            array_push($files_in_pages, $new);
        }
    }
    return $files_in_pages;
}
