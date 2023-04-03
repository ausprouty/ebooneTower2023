<?php
myRequireOnce('createPage.php');
myRequireOnce('modifyPage.php');
myRequireOnce('publishDestination.php');
myRequireOnce('publishFiles.php');
myRequireOnce('publishFilesInPage.php');
myRequireOnce('progressMerge.php');
myRequireOnce('writeLog.php');
myRequireOnce('addVueWrapper.php');


// needs to return files in Page so we can include these when downloading a series for offline use.
// required by publishSeriesAndChapters.php on line 44

function publishPage($p)
{
    $out = new stdClass;
    $progress = new stdClass;
    $response = new stdClass;

    $files_in_page = isset($p['files_in_page']) ? $p['files_in_page'] : [];
    $rand = random_int(0, 9999);
    $debug = '';
    if (!isset($p['recnum'])) {
        $progress->message = "<br><br>in PublishPage no value for recnum ";
        $progress->progress = 'undone';
        $out->progress = $progress;
        //trigger_error($message, E_USER_ERROR);
        return $out;
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

    $response = (object) publishFilesInPage($text, $p);
    writeLogAppend('Progress-PublishPage-46', $response);
    writeLogAppend('Progress-PublishPage-47', $response->files_in_page);
    writeLogAppend('Progress-PublishPage-48', $files_in_page);
    $files_in_page = progressMergeArrays($files_in_page, $response->files_in_page);
    $progress = progressMerge($progress, $response->progress . 'PublishPage-47');
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
    $response =  (object) modifyPage($text, $p, $data, $bookmark);
    $text = $response->text;
    $progress = progressMergeObjects($progress, $response->progress, 'PublishPage-65');
    $text .= '<!--- Created by publishPage-->' . "\n";
    writeLogDebug('publishPage-ZOOM-54', $text);
    $text = addVueWrapperPage($text);
    $series_dir = dirStandard('series', DESTINATION,  $p, $folders = null, $create = true);
    $fname = $series_dir .  ucfirst($data['language_iso'])  . ucfirst($data['filename']) . '.vue';
    // go to publishFiles
    publishFiles($p, $fname, $text,  STANDARD_CSS, $selected_css);
    $out->progress = $progress;
    $out->files_in_page = $files_in_page;
    writeLogDebug('Progress-PublishPage-75', $text);
    return $out;
}
