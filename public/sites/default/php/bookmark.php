<?php

// see  https://stackoverflow.com/questions/7381900/php-decoding-and-encoding-json-with-unicode-characters

myRequireOnce('getLatestContent.php');
myRequireOnce('publishFiles.php');
myRequireOnce('writeLog.php');


/* returns bookmark values
   requires: $p['recnum'] and $p['library_code];
   returns array $bookmark

*/
function bookmark($p)
{
    myRequireOnce('version2Text.php');
    $rand =  random_int(0, 99999);
    //writeLogDebug('bookmark-17-p-' . $rand, $p);
    $b['bookmark'] = null;
    if (isset($p['recnum'])) {
        // be sure to add this in for library since you can not yet derive it.
        $b['library_code'] = isset($p['library_code']) ? $p['library_code'] : null;
        // find other parameters for bookmark from recnum
        $starting = contentObjectFromRecnum($p['recnum']);
        //writeLog('bookmark-29-starting', $starting);
        $b['country_code'] = $starting->country_code;
        $b['language_iso'] = $starting->language_iso;
        $b['folder_name'] = $starting->folder_name;
        $b['filename'] = $starting->filename;
    } else {
        $b['country_code'] = isset($p['country_code']) ? $p['country_code'] : null;
        if ($b['country_code'] == 'undefined') {
            $b['country_code']  = null;
        }
        $b['language_iso'] = isset($p['language_iso']) ? $p['language_iso'] : null;
        if ($b['language_iso'] == 'undefined') {
            $b['language_iso']  = null;
        }
        $b['library_code'] = isset($p['library_code']) ? $p['library_code'] : null;
        $b['folder_name'] = isset($p['folder_name']) ? $p['folder_name'] : null;
        if ($b['folder_name'] == 'undefined') {
            $b['folder_name']  = null;
        }
        $b['filename'] = isset($p['filename']) ? $p['filename'] : null;
        if ($b['folder_name'] == 'undefined') {
            $b['folder_name']  = null;
        }
    }
    if ($b['country_code']) {
        $b['bookmark']['country'] = checkBookmarkCountry($b);
        if ($b['language_iso']) {
            $b['bookmark']['language']  = checkBookmarkLanguage($b);
            if (isset($b['library_code'])) {
                $b['bookmark']['library'] = checkBookmarkLibrary($b);
                if ($b['folder_name']) {
                    $b['bookmark']['series'] = checkBookmarkSeries($b);
                    $b['bookmark']['book'] = checkBookmarkBook($b);
                    if ($b['filename']) {
                        $b['bookmark']['page'] = checkBookmarkPage($b);
                    }
                }
            }
        }
    }
    $out = $b['bookmark'];
    //writeLogDebug('bookmark-58', $out);
    $out = version2Text($out);
    return $out;
}
function checkBookmarkCountry($b)
{

    $out = null;
    $b['scope'] = 'countries';
    $content = getLatestContent($b);
    //writeLog('checkBookmarkCountry-67-content', $content);
    $response = json_decode($content['text']);
    if (!$response) {
        writeLogDebug('checkBookmarkCountry', $b);
        return NULL;
    }
    foreach ($response as $country) {
        if ($country->code == $b['country_code']) {
            $out = $country;
        }
    }
    //writeLog('checkBookmarkCountry-95-content', $out);
    return $out;
}
function checkBookmarkLanguage($b)
{
    $debug = 'in checkBookmarkLanguage' . "\n";
    $out = null;
    $b['scope'] = 'languages';
    $content = getLatestContent($b);
    $response = json_decode($content['text']);
    if (!$response) {
        writeLogError('checkBookmarkLanguage', $b);
        return NULL;
    }
    if (isset($response->languages)) {
        foreach ($response->languages as $language) {
            if ($language->iso == $b['language_iso']) {
                $out = $language;
            }
        }
    } else {
        $debug .= 'NO response for languages' .  "\n";
    }

    return $out;
}
// no longer used
function checkBookmarkLibraries($b)
{
    $debug = 'in checkBookmarkLibraries' . "\n";
    $out = null;
    $b['scope'] = 'libraryNames';
    // find possible libraries with books
    $content = getLatestContent($b);
    $names = json_decode($content['text']);
    if (!$names) {
        writeLogError('checkBookmarkLibraries', $b);
        return NULL;
    }
    foreach ($names as $name) {
        if ($name !== 'index' && $name !== 'languages') {
            $b['library_code'] = $name;
            $b['scope'] = 'library';
            $content = getLatestContent($b);
            $response = json_decode($content['text']);
            $books = $response->books;
            foreach ($books as $book) {
                $out[] = $book;
            }
        }
    }
    return $out;
}
function checkBookmarkLibrary($b)
{
    $out = null;
    $response = new stdClass();
    if ($b['library_code'] !== 'index') {
        $b['scope'] = 'library';
    } else {
        $b['scope'] = 'libraryIndex';
    }
    $content = getLatestContent($b);
    $library = json_decode($content['text']);
    if (!$library) {
        writeLogError('checkBookmarkLibrary-140', $b);
        return NULL;
    }
    // legacy data does not have ['books'] so move data there
    if (isset($library->books)) {
        $response = $library;
    } else {
        $response->books = $library;
    }
    $out  = $response;
    //writeLogDebug('checkBookmarkLibrary-152', $out);
    return $out;
}
function checkBookmarkBook($b)
{
    $debug = 'In check BookmarkBook' . "\n";
    $out = null;
    $this_book = $b['folder_name'];
    if ($this_book == 'pages') {
        $this_book = $b['filename'];
    }
    $debug .= 'This book is ' .  $this_book . "\n";
    if (isset($b['bookmark']['library']->books)) {
        $books = $b['bookmark']['library']->books;
        foreach ($books as $book) {
            if (!isset($book->code)) {
                $debug .= 'Book code is not set' . "\n";
                $code = isset($book->name) ? $book->name : NULL;
            } else {
                $code = $book->code;
            }
            if ($code == $this_book) {
                $debug .= 'Found this code in book array' . "\n";
                $debug .= json_encode($book, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n";
                $out = $book;
            }
        }
    }
    return $out;
}
function checkBookmarkSeries($b)
{
    // pages have a folder name of 'pages' and are not part of a series
    if ($b['folder_name'] == 'pages') {
        return NULL;
    }
    $out = NULL;
    $b['scope'] = 'series';
    $content = getLatestContent($b);
    if (isset($content['text'])) {
        $out = json_decode($content['text']);
    }
    // //writeLogDebug('checkBookmarkSeries-192', $out);
    return $out;
}

function checkBookmarkPage($b)
{
    $out = null;
    // is this part of a series?
    if (isset($b['bookmark']['series'])) {
        // go through series bookmark to find chapter information
        if (isset($b['bookmark']['series']->chapters)) {
            $chapters = $b['bookmark']['series']->chapters;
            foreach ($chapters as $chapter) {
                if ($chapter->filename == $b['filename']) {
                    $out = $chapter;
                }
            }
        }
        // this should not happen
        else {
            $out =  $b['bookmark']['book'];
        }
    }
    // this is a basic page from the library and does not have any data?
    else {
        $out =  $b['bookmark']['book'];
    }
    return $out;
}
