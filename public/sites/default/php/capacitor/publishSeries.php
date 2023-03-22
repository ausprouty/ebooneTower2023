<?php

myRequireOnce('createSeries.php');
myRequireOnce('dirStandard.php');
myRequireOnce('publishFiles.php');
myRequireOnce('publicationCache.php');

// returns $p[files_json] for use by publishSeriesandChapters
function publishSeries($p)
{
    // when coming in with only book information the folder_name is not yet set
    if (!isset($p['folder_name'])) {
        if (isset($p['code'])) {
            $p['folder_name'] = $p['code'];
        }
    }

    $sql = "SELECT * FROM content
        WHERE  country_code = '" . $p['country_code'] . "'
        AND  language_iso = '" . $p['language_iso'] . "'
        AND folder_name = '" . $p['folder_name'] . "'  AND filename = 'index'
        AND prototype_date IS NOT NULL
        ORDER BY recnum DESC LIMIT 1";
    $data = sqlArray($sql);
    if (!$data) {
        $message = 'No data found for: ' . $sql;
        writeLogError('capacitor-publishSeries-29', $message);
        trigger_error($message, E_USER_ERROR);
    }
    $text = json_decode($data['text']);
    if ($text) {
        // create Series
        $result = createSeries($p, $data);
        $p = $result['p']; // this gives us $p['files_json']
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
            $dir = dirStandard('series', DESTINATION,  $p, $folders = null, $create = false);
            $fname = $dir . ucfirst($p['language_iso']) . ucfirst($p['folder_name']) . 'Index.vue';
            writeLogAppend('capacitor- publishSeries-70', $fname);
            $result['text'] .= '<!--- Created by capacitor - publishSeries-->' . "\n";
            publishFiles($p, $fname, $result['text'],  STANDARD_CSS, $selected_css);
            $time = time();
        }
    } else {
        $message = 'No text found for ' .  $query . "\n";
        writeLogAppend('ERROR- capacitor-publishSeries-93', $message);
        trigger_error($message, E_USER_ERROR);
    }
    return $p;
}
