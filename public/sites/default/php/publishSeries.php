<?php

myRequireOnce('createSeries.php');
myRequireOnce('dirStandard.php');
myRequireOnce('publishFiles.php');
myRequireOnce('publicationCache.php');

function publishSeries($p)
{
    // when coming in with only book information the folder_name is not yet set
    if (!isset($p['folder_name'])) {
        if (isset($p['code'])) {
            $p['folder_name'] = $p['code'];
        }
    }
    //
    //find series data
    //
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
        return $p;
    }
    $text = json_decode($data['text']);
    if ($text) {
        // create Series
        //writeLogDebug('publishSeries-48', 'I am going to createSeries');
        $result = createSeries($p, $data);
        //writeLogDebug('publishSeries-50', 'I returned from  createSeries');
        if ($result['text']) {
            // find css
            if (isset($p['recnum'])) {
                $b['recnum'] = $p['recnum'];
                $b['library_code'] = $p['library_code'];
            } else {
                $b = $p;
            }
            $bookmark  = bookmark($b);
            $selected_css = isset($bookmark['book']->style) ? $bookmark['book']->style : STANDARD_CSS;
            $dir = dirStandard('series', DESTINATION,  $p, $folders = null, $create = true);
            $fname = $dir . 'index.html';
            $result['text'] .= '<!--- Created by publishSeries-->' . "\n";
            publishFiles($p, $fname, $result['text'],  STANDARD_CSS, $selected_css);
            $time = time();
            if (DESTINATION == 'staging') {
                $sql = "UPDATE content
                    SET prototype_date = '$time', prototype_uid = '" . $p['my_uid'] . "'
                    WHERE  country_code = '" . $p['country_code'] . "' AND
                    language_iso = '" . $p['language_iso'] . "'
                    AND folder_name = '" . $p['folder_name'] . "'  AND filename = 'index'
                    AND prototype_date IS NULL";
                sqlArray($sql, 'update');
            }
            if (DESTINATION == 'website') {
                $sql = "UPDATE content
                    SET publish_date = '$time', publish_uid = '" . $p['my_uid'] . "'
                    WHERE  country_code = '" . $p['country_code'] . "' AND
                    language_iso = '" . $p['language_iso'] . "'
                    AND folder_name = '" . $p['folder_name'] . "'  AND filename = 'index'
                    AND prototype_date IS NOT NULL
                    AND publish_date IS NULL";
                sqlArray($sql, 'update');
            }
        }
    } else {
        $message = 'No text found for ' .  $query . "\n";
        writeLogAppend('ERROR- publishSeries-93', $message);
    }
    return $p;
}
