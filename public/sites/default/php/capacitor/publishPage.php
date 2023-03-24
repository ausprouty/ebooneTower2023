<?php
myRequireOnce('createPage.php');
myRequireOnce('modifyPage.php');
myRequireOnce('publishDestination.php');
myRequireOnce('publishFiles.php');
myRequireOnce('publishFilesInPage.php');
myRequireOnce('writeLog.php');
myRequireOnce('addVueWrapper.php');


// needs to return files in Page so we can include these when downloading a series for offline use.
// required by publishSeriesAndChapters.php on line 44

function publishPage($p)
{

    $p['files_in_page'] = isset($p['files_in_page']) ? $p['files_in_page'] : [];
    $rand = random_int(0, 9999);
    $debug = '';
    if (!isset($p['recnum'])) {
        $message = "in PublishPage no value for recnum ";
        trigger_error($message, E_USER_ERROR);
        return ($p);
    }
    $sql = 'SELECT * FROM content
        WHERE  recnum  = ' . $p['recnum'];
    $debug .= $sql . "\n";
    $data = sqlArray($sql);
    //
    // create page
    //
    foreach ($data as $key => $value) {
        $debug .= $key . ' => ' . $value . "\n";
    }
    //writeLogError ('capacitor-publishPage-30-debug', $debug);
    $text  = createPage($p, $data);
    writeLogDebug('Object-PublishPage-37', $text);

    $files_in_page  = publishFilesInPage($text, $p);
    $p['files_in_page'] = array_merge($p['files_in_page'], $files_in_page);
    // get bookmark for stylesheet
    if (isset($p['recnum'])) {
        $b['recnum'] = $p['recnum'];
        $b['library_code'] = $p['library_code'];
    } else {
        $b = $p;
    }
    $bookmark  = bookmark($b);
    $selected_css = isset($bookmark['book']->style) ? $bookmark['book']->style : STANDARD_CSS;
    //
    // class="nobreak" need to be changed to class="nobreak-final" so color is correct
    $text = str_ireplace("nobreak", "nobreak-final", $text);
    //
    // modify the page for notes and links
    //
    $response =  modifyPage($text, $p, $data, $bookmark);
    $text = $response->text;
    $progress = $response->progress;
    $text .= '<!--- Created by publishPage-->' . "\n";
    writeLogDebug('publishPage-ZOOM-54', $text);
    $text = addVueWrapperPage($text);
    //writeLogDebug('capacitor-publishPage-58', DESTINATION );
    $series_dir = dirStandard('series', DESTINATION,  $p, $folders = null, $create = true);
    $fname = $series_dir .  ucfirst($data['language_iso'])  . ucfirst($data['filename']) . '.vue';
    //writeLogAppend('publishPage-64', $fname);
    // go to publishFiles
    //writeLogAppend('capacitor-publishPage-81', DESTINATION . '    ' . $fname);
    publishFiles($p, $fname, $text,  STANDARD_CSS, $selected_css);
    //writeLog ('capacitor-publishPage-72-debug', $debug);//
    $p['progress'] = $progress;
    return ($p);
}
