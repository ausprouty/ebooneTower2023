<?php
myRequireOnce('writeLog.php');
myRequireOnce('createLibrary.php');
myRequireOnce('publishDestination.php');
myRequireOnce('publishFiles.php');
myRequireOnce('syncController.php');


function publishLibrary($p)
{

    $debug = 'In publishLibrary ' . "\n";
    syncController($p);
    //
    // get data for current library
    // there are some libraries in myfriends that have html in their name, this it not in the database
    //
    $filename =  $p['library_code'];
    $filename = str_replace('.html', '', $filename);
    //writeLogDebug('publishLibrary-default-18',  $filename);

    
    $sql = "SELECT text, recnum  FROM content
        WHERE  country_code = '" . $p['country_code'] . "'
        AND language_iso = '" . $p['language_iso'] . "'
        AND folder_name = '' AND filename = '$filename'
        AND prototype_date IS NOT NULL
        ORDER BY recnum DESC LIMIT 1";
    //writeLogDebug('publishLibrary-default-22',  $sql);
    $data = sqlArray($sql);
    $text = json_decode($data['text']);
    $p['recnum'] = $data['recnum'];
    // set style
    if (isset($text->format->style)) {
        $selected_css = $text->format->style;
    } else {
        $selected_css = '/sites/default/styles/cardGLOBAL.css';
    }
    //writeLogDebug('publishLibrary-default-31', $text);
    $res = createLibrary($p, $text);
    writeLogDebug('publishLibrary-default-33', $res);
    $body = $res->body;
    $p['books'] = $res->books;
    $p['progress'] = $res->progress;

    //
    // write file
    // if filename == 'library', switch to 'index' because it means there is no
    // LibraryIndex file
    //   
    if ($filename == 'library') {
        $filename = 'index';
    }
    if (DESTINATION != 'sdcard' && DESTINATION != 'capacitor') {
        $dir  = publishDestination($p) . 'content/' . $p['country_code'] . '/' . $p['language_iso'] . '/';
        $filetype = '.html';
    }
    if (DESTINATION == 'sdcard' || DESTINATION == 'capacitor') {
        $dir  = publishDestination($p) . 'views/' . $p['country_code'] . '/' . $p['language_iso'] . '/';
        $filetype = '.vue';
    }
    $fname = $dir . $filename . $filetype;
    //writeLogDebug('publishLibrary-default-55', $fname);
    //writeLogDebug('publishLibrary-default-56', $body);
    //writeLogDebug('publishLibrary-default-55', $fname);
    $body = publishLibraryAdjustText($body);
    writeLogDebug('publishLibrary-default-67', DESTINATION);
    $body .= '<!--- Created by publishLibrary-->' . "\n";
    writeLogDebug('publishLibrary-default-70', STANDARD_CARD_CSS);
    publishFiles($p, $fname, $body, STANDARD_CARD_CSS, $selected_css);
   
    //
    // update records
    //
    writeLogDebug('publishLibrary-default-73', DESTINATION );
    $time = time();
    if (DESTINATION == 'staging') {
        $sql = "UPDATE content
            SET prototype_date = '$time', prototype_uid = '" . $p['my_uid'] . "'
            WHERE country_code = '" . $p['country_code'] . "'
            AND language_iso = '" . $p['language_iso'] . "'
            AND folder_name = ''
            AND prototype_date IS NULL";
        $debug .= $sql . "\n";
        sqlArray($sql, 'update');
    }
    if (DESTINATION == 'website') {
        $sql = "UPDATE content
            SET publish_date = '$time', publish_uid = '" . $p['my_uid'] . "'
            WHERE country_code = '" . $p['country_code'] . "'
            AND language_iso = '" . $p['language_iso'] . "'
            AND folder_name = ''
            AND prototype_date IS NOT NULL
            AND publish_date IS NULL";
        $debug .= $sql . "\n";
        sqlArray($sql, 'update');
    }
    return $p;
}
/* Change navigation links in edit to navigation in production
  '/preview/series/AU/eng/family/youth-basics'
   needs to be changed to
 '/content/AU/eng/youth-basics/'

 and
'/preview/page/U1/eng/library/pages/steps'
  to
'çontent/U1/eng/pages/steps.html'
*/
function publishLibraryAdjustText($body)
{
    writeLogDebug('publishLibraryAdjustText-112', $body);
    $body = str_replace('/preview/library', '/content', $body);
    // see if anyone is bypassing the library (there is only one book in this language)
    if (strpos($body, '/preview/series/')) {
        $count = substr_count($body, '/preview/series/');
        for ($i = 1; $i <= $count; $i++) {
            $start = strpos($body, '/preview/series/');
            $end = strpos($body, '"', $start);
            $len = $end - $start;
            $link1 = substr($body, $start, $len); //preview/series/AU/eng/family/youth-basics
            $parts = explode('/',  $link1);
            $link2 =   '/content/' . $parts[3] . '/' . $parts[4] . '/' . $parts[6];
            $body = str_replace($link1, $link2, $body);
        }
    }
    if (strpos($body, '/preview/page/')) {
        $count = substr_count($body, '/preview/page/');
        for ($i = 1; $i <= $count; $i++) {
            $start = strpos($body, '/preview/page/');
            $end = strpos($body, '"', $start);
            $len = $end - $start;
            $link1 = substr($body, $start, $len); //preview/series/AU/eng/family/youth-basics
            $parts = explode('/',  $link1);
            $link2 =   '/content/' . $parts[3] . '/' . $parts[4] . '/' . $parts[6] . '/' . $parts[7] . '.html';
            $body = str_replace($link1, $link2, $body);
        }
    }
    writeLogDebug('publishLibraryAdjustText-138', $body);
    return $body;
}
