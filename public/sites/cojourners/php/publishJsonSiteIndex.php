<?php

myRequireOnce('findFilesInText.php');
myRequireOnce('dirStandard.php');
myRequireOnce('publishJsonSiteIndex.php');
// we need to get files for the WHOLE site because you can bounce from series to series or back 

function publishJsonSiteIndex($p)
{
    writeLogDebug('publishJsonSeriesIndex-cojourners-9', DESTINATION);
    $files_in_pages = [];
    $library = publishJsonSiteIndexGetLibrary($p);
    foreach ($library->books as $book) {
        if ($book->format == 'series') {
            $p['folder_name'] = $book->code;
            $series_dir_full =  dirStandard('series', DESTINATION,  $p, $folders = null, $create = false);
            $series_dir_short =  dirStandard('series', 'content',  $p, $folders = null, $create = false);
            writeLogDebug('publishJsonSiteIndex-18',  $p['folder_name']);
            $series = publishJsonSiteIndexGetSeries($p);
            writeLogDebug('publishJsonSiteIndex-20', $series);
            foreach ($series->chapters as $chapter) {
                if (DESTINATION == 'staging') {
                    if (isset($chapter->prototype)) {
                        $status = $chapter->prototype;
                    }
                } else {
                    $status = $chapter->publish;
                }
                if ($status  == true) {
                    $p['filename'] = $chapter->filename;
                    $chapter_name =  $series_dir_short .  $chapter->filename . '.html';
                    $files_in_pages[$chapter_name] = $chapter_name;
                    $file_name = $series_dir_full . $chapter->filename . '.html';
                    $text = file_get_contents($file_name);
                    $find_begin = 'src="';
                    $files_in_pages = findFilesInText($find_begin, $text, $p, $files_in_pages);
                    if ($chapter->filename == 'resource07') {
                        writeLogDebug('publishJsonSiteIndex-36', $file_name);
                        writeLogDebug('publishJsonSiteIndex-37', $text);
                        writeLogDebug('publishJsonSiteIndex-38', $files_in_pages);
                    }
                    $find_begin = 'href="';
                    $files_in_pages = findFilesInText($find_begin, $text, $p, $files_in_pages);
                }
            }
        }
        if ($book->format == 'page') {
            if (DESTINATION == 'staging') {
                if (isset($book->prototype)) {
                    $status = $book->prototype;
                }
            } else {
                $status = $book->publish;
            }
            if ($status  == true) {
                $p['filename'] = $book->code;
                $page_dir_full =  dirStandard('page', DESTINATION,  $p, $folders = null, $create = false);
                $page_dir_short =  dirStandard('page', 'content',  $p, $folders = null, $create = false);
                $page_name =  $page_dir_short .  $book->code . '.html';

                $files_in_pages[$page_name] = $page_name;
                $file_name = $page_dir_full . $book->code . '.html';
                $text = file_get_contents($file_name);
                $find_begin = 'src="';
                $files_in_pages = findFilesInText($find_begin, $text, $p, $files_in_pages);
                $find_begin = 'href="';
                $files_in_pages = findFilesInText($find_begin, $text, $p, $files_in_pages);
            }
        }
    }
    $json_text = '[';
    foreach ($files_in_pages as $page) {
        if (strpos($page, '/')  != 0) {
            $page = '/' . $page;
        }
        if ($page != "") {
            $json_text .= '{"url":"' . $page . '"},' . "\n";
        }
    }
    $json_text = substr($json_text, 0, -2);
    $json_text .= ']';
    writeLogDebug('publishJsonSiteIndex-cojourners-75', $json_text);
    $json_series_dir = dirStandard('language', DESTINATION,  $p, $folders = null);
    $filename =  $json_series_dir . 'files.json';
    fileWrite($filename, $json_text, $p);
}

function publishJsonSiteIndexGetLibrary($p)
{
    //
    //get library data
    //
    if (DESTINATION == 'staging') {
        $sql = "SELECT * FROM content
            WHERE  country_code = '" . $p['country_code'] . "'
            AND  language_iso = '" . $p['language_iso'] . "'
            AND filename = 'library'
            ORDER BY recnum DESC LIMIT 1";
    } else {
        $sql = "SELECT * FROM content
            WHERE  country_code = '" . $p['country_code'] . "'
            AND  language_iso = '" . $p['language_iso'] . "'
            AND filename = 'library'
            AND prototype_date IS NOT NULL
            ORDER BY recnum DESC LIMIT 1";
    }
    $data = sqlArray($sql);
    if (!$data) {
        $message = 'No data found for: ' . $sql;
        writeLogError('publishSeries-29', $message);
        return;
    }
    $books = json_decode($data['text']);
    return $books;
}

function publishJsonSiteIndexGetSeries($p)
{
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
        return null;
    }
    $chapters = json_decode($data['text']);
    return $chapters;
}
