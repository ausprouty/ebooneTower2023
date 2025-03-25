<?php

myRequireOnce('bookmark.php');
myRequireOnce('copyGlobal.php');
myRequireOnce('dirMake.php');
myRequireOnce('createDirectory.php');
myRequireOnce('fileWrite.php');
myRequireOnce('getTitle.php');
myRequireOnce('languageHtml.php');

myRequireOnce('makePathsRelative.php');
myRequireOnce('modifyHeaders.php');
myRequireOnce('modifyImages.php');
myRequireOnce('modifyVersion.php');
//myRequireOnce ('publishCopyImagesAndStyles.php');
myRequireOnce('publishFilesInPage.php');
myRequireOnce('createLanguageFooter.php');
myRequireOnce('publishCSS.php');
myRequireOnce('writeLog.php');
myRequireOnce('myGetPrototypeFile.php');
myRequireOnce('languageSpecificJavascripts.php');
myRequireOnce('onLoadJS.php');

// destination must be 'staging', 'website', 'pdf'  or 'sdcard'
function publishFiles($p, $fname, $text, $standard_css, $selected_css)
{
    writeLogDebug('publishFiles-27', $text);
    $file_name_parts = explode('/', $fname);
    $fsname = array_pop($file_name_parts);
    $fsname = str_replace('.html', '', $fsname);
    //// //writeLogDebug('publishFile-24-'. $fsname, $text);
    // some libary indexes have a name of meet.html with then gets appended with another html
    if (strpos($fname, '.html.html') !== false) {
        $fname = str_replace('.html.html', '.html', $fname);
    }
    // webpage is used by Whatsapp 
    $pos = strpos($fname,'/content/');
    $webPage = substr($fname, $pos);
    // start with header
    $output = myGetPrototypeFile('header.html', $p['language_iso']);
    // see if there are links for Whatsapp or X
    if (strpos($output, '[ShareLinks]') !== FALSE){
        writeLogDebug('publishFiles-41', $output);
        $shareLinks = myGetPrototypeFile('shareLinks.html', $p['language_iso']);
        writeLogDebug('publishFiles-43', $shareLinks);
        $output = str_replace('[ShareLinks]', $shareLinks, $output);
    }
    writeLogDebug('publishFiles-48', $output);
    // add onload only if notes  are here
    $onload_note_js = '';
    if (strpos($text, '<form') !== false) {
        $pos = strrpos($fname, '/') + 1;
        $filename = substr($fname, $pos);
        $note_index = $p['country_code'] . '-' . $p['language_iso'] . '-' . $p['folder_name'] . '-' . $filename;
        $onload_note_js = onLoadJS($note_index);

        $output .= '<!--- publishFiles added onLoad -->' . "\n";
    }
    if (strpos($text, '<div class="header') !== false) {
        $result = modifyHeaders($text);
        $text = $result['text'];
        $headers = $result['headers'];
    } else {
        $headers = ' ';
    }
    if ( DESTINATION != 'staging') {
        // class="nobreak" need to be changed to class="nobreak-final" so color is correct
        $text = str_ireplace("nobreak", "nobreak-final", $text);
    }
    $title = WEBSITE_TITLE;
    if (isset($p['recnum'])) {
        $title .= ' ' . getTitle($p['recnum']);
    }
    writeLogDebug('publishFiles-74', $output);
    $language_iso = isset($p['language_iso']) ? $p['language_iso'] : DEFAULT_LANGUAGE_ISO;
    $language_google = languageHtml($p['language_iso']);
    writeLogDebug('publishFiles-76',  $language_google);
    $placeholders = array(
        '{{ language.google }}',
        '{{ webPage }}',
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
        $webPage,
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
    writeLogDebug('publishFiles-103', $output);
    // insert text
    $output .= $text;
    writeLogDebug('publishFiles-106', $output);
    // remove dupliate CSS
    $output = publishCSS($output, $p);
    writeLogDebug('publishFiles-108', $output);
    $footer = 'footer.html';
    $output .= myGetPrototypeFile($footer,  $p['language_iso'],);
    // copy all images and styles to the publish directory
    //$response = publishCopyImagesAndStyles($output, $destination);
    writeLogDebug('publishFiles-113', $output);
    $output = modifyImages($output, $p);
    // make sure  all files are copied to destination directory
    publishFilesInPage($output, $p);

    fileWrite($fname, $output, $p);
    return $output;
}
