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
    // restore cache from previous publication attempt
    $cache = getCache($p);
    //writeLogAppend('publishSeriesAndChapters-30', $cache);
    $files_in_pages =  $cache['files_included'];
    $chapters = $text->chapters;
    writeLogDebug('publishSeriesAndChapters-33', $p);
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
                // find file and add to database
                $series_dir = dirCreate('series', DESTINATION,  $p, $folders = null, $create = false);
                $file =   $series_dir .  $chapter->filename . '.html';
                if (file_exists($file)) {
                    $p['text'] = file_get_contents($file);
                    $p['filename'] = $chapter->filename;
                    createContent($p);
                    $data = sqlArray($sql);
                    $p['recnum'] = $data['recnum'];
                    $result =  publishPage($p);
                    if (is_array($result['files_in_page'])) {
                        $files_in_pages = publishSeriesAndChaptersCombineArrays($files_in_pages, $result['files_in_page']);
                    }
                } else {
                    $message = 'NO RESULT for ' . $file . "\n";
                    writeLogError('publishSeriesAndChapters-66', $message);
                }
            }
        }

        $cache['sessions_published'][] = $chapter->filename;
        $cache['files_included'] = $files_in_pages;
        updateCache($cache, DESTINATION);
    }
    clearCache($cache, DESTINATION);
    return true;
}
function publishSeriesAndChaptersCombineArrays($files_in_pages, $new_files)
{
    //writeLogDebug('publishSeriesAndChaptersCombineArrays-101', $files_in_pages);
    //writeLogDebug('publishSeriesAndChaptersCombineArrays-102', $new_files);
    foreach ($new_files as $new) {
        writeLogAppend('ppublishSeriesAndChaptersCombineArrays-104', $new);
        if (!in_array($new, $files_in_pages)) {
            array_push($files_in_pages, $new);
        }
    }
    //writeLogAppend('publishSeriesAndChaptersCombineArrays-109', $files_in_pages);
    return $files_in_pages;
}
