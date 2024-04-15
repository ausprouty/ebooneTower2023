<?php

myRequireOnce('publishFiles.php');
myRequireOnce('sql.php');

myRequireOnce('writeLog.php');
myRequireOnce('myGetPrototypeFile.php');

/* return latest content (with all fi)
*/
function getLatestContent($p)
{
    myRequireOnce('version2Text.php');
    if (!isset($p['scope'])) {
        $message =  'In getLatestContent No scope was set';
        writeLogError('getLatestContent', $message);
        trigger_error($message, E_USER_ERROR);
        return NULL;
    }
    switch ($p['scope']) {
        case "countries":
            $sql = 'SELECT * FROM content
                WHERE filename = "countries"
                ORDER BY recnum DESC LIMIT 1';
            break;
        case "languages":
            $sql = "SELECT * from content
                WHERE country_code = '" . $p['country_code'] . "'
                AND filename = 'languages'
                AND folder_name = ''
                ORDER BY recnum DESC LIMIT 1";
            break;
        case "library":
            if (!isset($p['library_code'])) {
                $p['library_code'] = 'library';
            } else {
                if (strpos($p['library_code'], '.html') !== FALSE) {
                    //$debug .= 'library code contains .html' . "\n";
                    $p['library_code'] = str_ireplace('.html', '', $p['library_code']);
                } else {
                    //$debug .=  $p['library_code']  . ' does not contain .html' . "\n";
                }
            }
            $sql = "SELECT * from content
                WHERE country_code = '" . $p['country_code'] . "'
                AND language_iso = '" . $p['language_iso'] . "'
                AND folder_name = ''
                AND filename = '" . $p['library_code'] . "'
                ORDER BY recnum DESC LIMIT 1";
            break;
        case "libraryNames":
            //$debug .= 'Case is libraryNames' . "\n";
            $sql = "SELECT DISTINCT filename FROM content
                WHERE country_code = '" . $p['country_code'] . "'
                AND language_iso = '" . $p['language_iso'] . "'
                AND folder_name = ''
                ORDER BY recnum DESC";
            break;
        case "libraryIndex":
            //$debug .= 'Case is libraryIndex' . "\n";
            $text_file = true;
            $sql = "SELECT * FROM content
                WHERE country_code = '" . $p['country_code'] . "'
                AND language_iso = '" . $p['language_iso'] . "'
                AND folder_name = ''
                AND filename = 'index'
                ORDER BY recnum DESC LIMIT 1";
            break;
        case "series":
            //$debug .= 'Case is series' . "\n";
            $sql = "SELECT * from content
                WHERE country_code = '" . $p['country_code'] . "'
                AND language_iso = '" . $p['language_iso'] . "'
                AND folder_name  = '" . $p['folder_name'] . "'
                AND  filename = 'index'
                ORDER BY recnum DESC LIMIT 1";
            break;
        case "page":
            //$debug .= 'Case is page' . "\n";
            $sql = "SELECT * from content
                WHERE country_code = '" . $p['country_code'] . "'
                AND language_iso = '" . $p['language_iso'] . "'
                AND folder_name = '" . $p['folder_name'] . "'
                AND  filename = '" . $p['filename'] . "'
                ORDER BY recnum DESC LIMIT 1";
            break;
        default:
            $sql = null;
            //$debug .= "no match for  " . $p['scope'] . "\n";
    }
    //$debug .= $sql . "\n";

    // execute query
    if ($sql) {
        $out = sqlArray($sql);
        if (isset($out['recnum'])) {
            writeLogDebug('getLatestContent-101', $out['text']);
            $out['text'] = version2Text($out['text']);
            writeLogDebug('getLatestContent-103', $out['text']);

        } else {
            if ($p['scope'] == 'library') {
                //$debug .= 'NOTE: USING DEFAULT LIBRARY  FROM LIBRARY.json' . "\n";
                if (!isset($p['destination'])) {
                    $p['destination'] = 'website';
                }
                $out['text'] =  myGetPrototypeFile('library.json');
            } else {
                //$debug .= 'No default ' . "\n";
                $out['text'] =  null;
            }
        }
    }
    //writeLog('getLatestContent-debug', //$debug );
    //writeLog('getLatestContent', $out );
    return $out;
}
