<?php
// This may be redundant now  see PublishSeriesAndChapters

myRequireOnce('createSeries.php');
myRequireOnce('dirStandard.php');
myRequireOnce('publishFiles.php');
myRequireOnce('publicationCache.php');
myRequireOnce('modifyTextForVue.php');
myRequireOnce('createBookRouter.php');

function XcreateBookContent($p)

// I do not think this is ever called
{
    trigger_error('you have stubled into createBookContent.  I think you should be in publishSeriesAndChapters');

    $out = new stdClass;
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
        //writeLogError('capacitor-createBookContent-29', $message);
        $out->message = $message;
        $out->progress = 'undone';
        return $out;
    }
    $text = json_decode($data['text']);
    if ($text) {
        $message = 'Text found for ' .  $sql . "\n";
        // create Series Index
        // //writeLogDebug('capacitor-createBookContent-48', 'I am going to createSeries');
        $result = createSeries($p, $data);
        //writeLogDebug('ProgressCreateBookContent-39', $result);
        //writeLogDebug('capacitor-createBookContent-50', 'I returned from  createSeries');
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
            //writeLogDebug('capacitor-createBookContent-50', $fname);
            //$fname = str_replace('mc2.capacitor/M2/', 'mc2.capacitor/views/M2/', $fname);
            $result['text'] = modifyTextForVue($result['text'], $bookmark, $p);
            //writeLogDebug('capacitor-createBookContent-capacitor-78', $result['text']);
            createBookRouter($p);
            $result['text'] .= '<!--- Created by createBookContent-->' . "\n";
            publishFiles($p, $fname, $result['text'],  STANDARD_CSS, $selected_css);
            $out->message = $message;
            $out->progress = 'done';
            return $out;
        }
    } else {
        $message = 'No text found for ' .  $sql . "\n This may be an index.";
        $out->progress = 'undone';
        return $out;
        //writeLogAppend('ERROR- capacitor- createBookContent-63', $message);
    }
}
