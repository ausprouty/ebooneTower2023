<?php

myRequireOnce('findFilesInText.php');
myRequireOnce('dirStandard.php');
// we need to get files for the WHOLE site because you can bounce from series to series or back 

function publishJsonSiteIndex($p)
{
    $files_in_pages = [];
    $books = publishJsonSiteIndexGetLibrary($p);
    if ($books == null){
        return;
    }
    foreach ($books as $book){
        if ($book->format == 'series'){
            $series_dir_full =  dirStandard('series', DESTINATION,  $p, $folders = null, $create = false);
            $series_dir_short =  dirStandard('series', 'content',  $p, $folders = null, $create = false);
            $p['folder_name'] = $book->code;
            $chapters = publishJsonSiteIndexGetSeries($p);
            foreach ($chapters as $chapter){
                if (DESTINATION == 'staging') {
                    if (isset($chapter->prototype)) {
                        $status = $chapter->prototype;
                    }
                } else {
                    $status = $chapter->publish;
                }
                if ($status  == true) {
                    $chapter_name =  $series_dir_short .  $chapter->filename . '.html';
                    $files_in_pages[$chapter_name] = $chapter_name;
                    $file_name = $series_dir_full . $chapter->filename . '.html';
                    $text = file_get_contents($file_name);
                    $find_begin = 'src="';
                    $files_in_pages = findFilesInText($find_begin, $text, $p, $files_in_pages);
                    $find_begin = 'href="';
                    $files_in_pages = findFilesInText($find_begin, $text, $p, $files_in_pages);
                    $files_in_pages = publishJsonSiteIndexGetFilesInPages($p,  $files_in_pages );
                }
            }
            
        }
        if ($book->format == 'page'){ 
            if (DESTINATION == 'staging') {
                if (isset($page->prototype)) {
                    $status = $page->prototype;
                }
            } else {
                $status = $page->publish;
            }
            if ($status  == true) {
                $page_dir_full =  dirStandard('page', DESTINATION,  $p, $folders = null, $create = false);
                $page_dir_short =  dirStandard('page', 'content',  $p, $folders = null, $create = false);
                $page_name =  $series_dir_short .  $page->code . '.html';
                $files_in_pages[$page_name] = $page_name;
                $text = file_get_contents($file_name);
                $find_begin = 'src="';
                $files_in_pages = findFilesInText($find_begin, $text, $p, $files_in_pages);
                $find_begin = 'href="';
                $files_in_pages = findFilesInText($find_begin, $text, $p, $files_in_pages);
                $files_in_pages = publishJsonSiteIndexGetFilesInPages($p,  $files_in_pages );
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
    writeLogDebug('publishJsonSeriesIndex-68', $json_text);
    $json_series_dir = dirStandard('language', DESTINATION,  $p, $folders = null);
    $filename =  $json_series_dir . 'files.json';
    fileWrite($filename, $json_text, $p);
}

function publishJsonSiteIndexGetLibrary($p){
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
    $data = sqlArray($sql);
    if (!$data) {
        $message = 'No data found for: ' . $sql;
        writeLogError('publishSeries-29', $message);
        return ;
    }
    $books = json_decode($data['text']);
    return $books;
    }

    function publishJsonSiteIndexGetSeries($p){
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