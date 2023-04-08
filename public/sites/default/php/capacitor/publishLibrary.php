<?php
myRequireOnce('writeLog.php');
myRequireOnce('createLibrary.php');
myRequireOnce('publishDestination.php');
myRequireOnce('publishFiles.php');
myRequireOnce('modifyTextForVue.php');
myRequireOnce('bookmark.php');


function publishLibrary($p)
{

    $progress = new stdClass();
    $response = new stdClass();
    $bookmark = bookmark($p);
    //
    // get data for current library
    //
    $filename =  $p['library_code'];
    $sql = "SELECT text, recnum  FROM content
        WHERE  country_code = '" . $p['country_code'] . "'
        AND language_iso = '" . $p['language_iso'] . "'
        AND folder_name = '' AND filename = '$filename'
        AND prototype_date IS NOT NULL
        ORDER BY recnum DESC LIMIT 1";
    $data = sqlArray($sql);
    $text = json_decode($data['text']);
    $p['recnum'] = $data['recnum'];
    // set style
    if (isset($text->format->style)) {
        $selected_css = $text->format->style;
    } else {
        $selected_css = '/sites/default/styles/cardGLOBAL.css';
    }
    $progress = createLibrary($p, $text);
    writeLogDebug('publishLibrary-default-33', $progress);
    $body = $progress->body;

    //
    // write file
    // if filename == 'library', switch to 'index' because it means there is no
    // LibraryIndex file
    //   
    $dir  = dirStandard('library', DESTINATION,  $p, $folders = null, $create = true);
    if ($filename == 'library') {
        $filename = 'index';
    }
    $filetype = '.vue';
    $fname = $dir . $filename . $filetype;
    writeLogDebug('publishLibrary-capacitor-50', $fname);
    $body = publishLibraryAdjustText($body);
    $body .= '<!--- Created by publishLibrary-->' . "\n";
    $response = modifyTextForVue($body, $bookmark, $p);
    $progress = progressMergeObjects($progress, $response, $source = 'publishLibrary-55');
    $body = $response->text;
    $response = publishFiles($p, $fname, $body, STANDARD_CARD_CSS, $selected_css);
    $progress = progressMergeObjects($progress, $response, $source = 'publishLibrary-58');
    return $progress;
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
    //writeLogDebug('publishLibraryAdjustText-114', $body);
    return $body;
}
