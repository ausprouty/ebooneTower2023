<?php

myRequireOnce('bookmark.php');
myRequireOnce('copyGlobal.php');
myRequireOnce('dirMake.php');
myRequireOnce('createDirectory.php');
myRequireOnce('fileWrite.php');
myRequireOnce('getTitle.php');
myRequireOnce('languageHtml.php');
myRequireOnce('languageSpecificJavascripts.php');
myRequireOnce('makePathsRelative.php');
myRequireOnce('modifyHeaders.php');
myRequireOnce('modifyImages.php');
myRequireOnce('modifyVersion.php');
myRequireOnce('myGetPrototypeFile.php');
//myRequireOnce ('publishCopyImagesAndStyles.php');
myRequireOnce('publishFilesInPage.php');
myRequireOnce('createLanguageFooter.php');
myRequireOnce('publishCSS.php');
myRequireOnce('writeLog.php');



// destination must be 'staging', 'website', 'pdf'  or 'sdcard'
function publishFiles($p, $fname, $text, $standard_css, $selected_css)
{
    $progress = new stdClass;

    $destination = DESTINATION;
    $file_name_parts = explode('/', $fname);
    $fsname = array_pop($file_name_parts);
    $fsname = str_replace('.html', '', $fsname);
    //// //writeLogDebug('capacitor - publishFile-24-'. $fsname, $text);
    // some libary indexes have a name of meet.html with then gets appended with another html
    if (strpos($fname, '.html.html') !== false) {
        $fname = str_replace('.html.html', '.html', $fname);
    }
    // start with header
    $output = myGetPrototypeFile('header.html');
    // add onload only if files are here
    $onload_note_js = '';
    if (strpos($text, '<div class="header') !== false) {
        $result = modifyHeaders($text);
        $text = $result['text'];
        $headers = $result['headers'];
    } else {
        $headers = ' ';
    }
    // class="nobreak" need to be changed to class="nobreak-final" so color is correct
    $text = str_ireplace("nobreak", "nobreak-final", $text);
    $title = WEBSITE_TITLE;
    if (isset($p['recnum'])) {
        $title .= ' ' . getTitle($p['recnum']);
    }
    $language_iso = isset($p['language_iso']) ? $p['language_iso'] : DEFAULT_LANGUAGE_ISO;
    $language_google = languageHtml($p['language_iso']);
    $placeholders = array(
        '{{ language.google }}',
        '{{ title }}',
        '{{ standard.css }}',
        '{{ selected.css }}',
        '{{ headers }}',
        '{{ onload-note-js }}',
        '{{ language_iso }}',
        '</html>',
        '</body>'
    );
    $replace = array(
        $language_google,
        $title,
        $standard_css,
        $selected_css,
        $headers,
        $onload_note_js,
        $language_iso,
        '',
        ''
    );
    $output = str_replace($placeholders, $replace,  $output);
    //writeLogDebug('capacitor - publishFile-82-'. $fsname, $output);
    // insert text
    $output .= $text;
    // remove dupliate CSS
    $output = publishCSS($output, $p);

    // append footer
    $footer = 'footer.html';
    $output .= myGetPrototypeFile($footer,  $p['language_iso'],);
    // copy all images and styles to the publish directory
    //$response = publishCopyImagesAndStyles($output, $destination);

    $output = modifyImages($output, $p);
    // make sure  all files are copied to destination directory
    $progress = (object) publishFilesInPage($output, $p);
    writeLogAppend('capacitor - publishFiles-95',  $progress);
    writeLogAppend('capacitor - publishFiles-95',  "\n===========\n\n");

    //writeLogDebug('capacitor - publishFile-106-ZOOM',  $output);
    $output = makePathsRelative($output, $fname);
    writeLogAppend('capacitor - publishFile-109',  $progress);
    fileWrite($fname, $output, $p);
    return $progress;
}
