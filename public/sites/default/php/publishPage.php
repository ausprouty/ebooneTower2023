<?php
myRequireOnce(DESTINATION, 'createPage.php');
myRequireOnce(DESTINATION, 'modifyPage.php');
myRequireOnce(DESTINATION, 'publishDestination.php');
myRequireOnce(DESTINATION, 'publishFiles.php');
myRequireOnce(DESTINATION, 'publishFilesInPage.php');
myRequireOnce(DESTINATION, 'writeLog.php');


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
    //writeLogError ('publishPage-30-debug', $debug);
    $text  = createPage($p, $data);
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
    $text = modifyPage($text, $p, $data, $bookmark);
    $text .= '<!--- Created by publishPage-->' . "\n";
    writeLogDebug('publishPage-ZOOM-54', $text);
    if ($p['destination'] == 'sdcard' || $p['destination'] == 'capacitor') {
        myRequireOnce(DESTINATION, 'addVueWrapper.php', 'sdcard');
        $text = addVueWrapperPage($text);
        //writeLogDebug('publishPage-58', $p['destination'] );
    }
    // write file
    $root_folder = array(
        'staging' => 'content/',
        'website' => 'content/',
        'apk' => 'content/',
        'sdcard' => 'views/',
        'nojs' => 'nojs/',
        'pdf' => 'pdf/'
    );
    $d = $p['destination'];
    $series_dir = publishDestination($p) . $root_folder[$d] . $data['country_code'] . '/' .
        $data['language_iso'] . '/' . $data['folder_name'] . '/';
    if ($p['destination'] != 'sdcard' && $p['destination'] != 'capacitor') {
        $fname = $series_dir . $data['filename'] . '.html';
    }
    if ($p['destination'] == 'sdcard' || $p['destination'] == 'capacitor') {
        $fname = $series_dir .  ucfirst($data['language_iso'])  . ucfirst($data['filename']) . '.vue';
    }

    writeLogDebug('publishPage-ZOOM-79', $text);
    // go to publishFiles
    // writeLogAppend('publishPage-81', $p['destination'] . '    '. $fname);
    publishFiles($p['destination'], $p, $fname, $text,  STANDARD_CSS, $selected_css);

    //writeLog ('publishPage-72-debug', $debug);//
    // update records
    //
    $time = time();
    $sql = null;
    if ($p['destination'] == 'website') {
        $sql = "UPDATE content
        SET publish_date = '$time', publish_uid = '" . $p['my_uid'] . "'
        WHERE  country_code = '" . $data['country_code'] . "' AND
        language_iso = '" . $data['language_iso'] . "'
        AND folder_name = '" . $data['folder_name'] . "'
        AND filename = '" . $data['filename'] . "'
        AND publish_date IS NULL";
    }
    if ($p['destination'] == 'staging') {
        $sql = "UPDATE content
        SET prototype_date = '$time', prototype_uid = '" . $p['my_uid'] . "'
        WHERE  country_code = '" . $data['country_code'] . "' AND
        language_iso = '" . $data['language_iso'] . "'
        AND folder_name = '" . $data['folder_name'] . "'
        AND filename = '" . $data['filename'] . "'
        AND prototype_date IS NULL";
    }
    if ($sql) {
        sqlArray($sql, 'update');
    }
    //$p['url'] = publishPageContentURL($p);
    //writeLog ('publishPage-98-debug', $debug);
    return ($p);
}
