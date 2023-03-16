<?php

myRequireOnce('createSeries.php');
myRequireOnce('dirCreate.php');
myRequireOnce('publishFiles.php');
myRequireOnce('publicationCache.php');
myRequireOnce('modifyTextForVue.php');
myRequireOnce('createBookRouter.php');

function createBookContent($p)
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
    $sql = "SELECT * FROM content
            WHERE  country_code = '" . $p['country_code'] . "'
            AND  language_iso = '" . $p['language_iso'] . "'
            AND folder_name = '" . $p['folder_name'] . "'  AND filename = 'index'
            AND prototype_date IS NOT NULL
            ORDER BY recnum DESC LIMIT 1";
    $data = sqlArray($sql);
    if (!$data) {
        $message = 'No data found for: ' . $sql;
        writeLogError('capacitor-createBookContent-29', $message);
        return 'undone';
    }
    $text = json_decode($data['text']);
    if ($text) {
        // create Series
        //writeLogDebug('capacitor-createBookContent-capacitor-48', 'I am going to createSeries');
        $result = createSeries($p, $data);
        // writeLogDebug('capacitor-createBookContent-capacitor-50', 'I returned from  createSeries');
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
            $dir = dirCreate('series', DESTINATION,  $p, $folders = null, $create = false);
            $fname = $dir . ucfirst($p['language_iso']) . ucfirst($p['folder_name']) . 'Index.vue';
            writeLogDebug('capacitor-createBookContent-50', $fname);
            //$fname = str_replace('mc2.capacitor/M2/', 'mc2.capacitor/views/M2/', $fname);
            $result['text'] = modifyTextForVue($result['text'], $bookmark);
            //writeLogDebug('capacitor-createBookContent-capacitor-78', $result['text']);
            createBookRouter($data, $p);
            $result['text'] .= '<!--- Created by createBookContent-->' . "\n";
            publishFiles($p, $fname, $result['text'],  STANDARD_CSS, $selected_css);
        }
    } else {
        $message = 'No text found for ' .  $sql . "\n This may be an index.";
        writeLogAppend('ERROR- capacitor- createBookContent-63', $message);
    }
    return 'done';
}
