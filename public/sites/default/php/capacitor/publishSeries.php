<?php

myRequireOnce('createSeries.php');
myRequireOnce('dirStandard.php');
myRequireOnce('publishFiles.php');
myRequireOnce('publicationCache.php');

// returns $p[files_json] for use by publishSeriesandChapters
function publishSeries($p)
{
    $progress = new stdClass;
    $result = new stdClass;
    $out = new stdClass;
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
        $progress->message = "<br><br>No data found for:  $sql";
        $progress->progress = 'error';
        $p['progress'] = $progress;
        return $p;
    }
    $text = json_decode($data['text']);
    if ($text) {
        // create Series
        $result = createSeries($p, $data);
        $text = $result->text;
        $files_json = $result->files_json;
        // returns
        if ($text) {
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
            //writeLogAppend('capacitor- publishSeries-70', $fname);
            $text .= '<!--- Created by capacitor - publishSeries-->' . "\n";
            publishFiles($p, $fname, $text,  STANDARD_CSS, $selected_css);
            $time = time();
        }
    } else {
        $progress->message = "<br><br>No data found for: $sql";
        $progress->progress = 'error';
        $p['progress'] = $progress;
        return $p;
    }
    $progress->progress = 'done';
    $out->files_json = $files_json;
    $out->p = $p;
    $out->progress = $progress;

    return $out;
}
