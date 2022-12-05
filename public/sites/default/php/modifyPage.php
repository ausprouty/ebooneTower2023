<?php

myRequireOnce('createPage.php');
myRequireOnce('getTitle.php');
myRequireOnce('modifyBibleLinks.php');
myRequireOnce('modifyHeaders.php');
myRequireOnce('modifyJavascript.php');
myRequireOnce('modifyLinks.php');
myRequireOnce('modifyNextSteps.php');
myRequireOnce('modifyNoteArea.php');
//myRequireOnce ('modifyReadMore.php');
myRequireOnce('modifyReference.php');
myRequireOnce('modifyRevealAudio.php');
myRequireOnce('modifyRevealBible.php');
myRequireOnce('modifyRevealSummary.php');
myRequireOnce('modifyRevealTrainer.php');
myRequireOnce('modifyRevealVideo.php');
myRequireOnce('modifySendAction.php');
myRequireOnce('modifyZoomImage.php');
myRequireOnce('version2Text.php');

myRequireOnce('writeLog.php');


function modifyPage($text, $p, $data, $bookmark)
{
    $text = version2Text($text);
    if (isset($p['destination'])) {
        if ($p['destination'] == 'sdcard') {
            myRequireOnce('modifyTextForVue.php', 'sdcard');
            $text = modifyTextForVue($text, $bookmark);
        }
    }
    ////writeLog('modifyPages-43-version2text', $text);
    //
    // modify note fields
    //
    if (strpos($text, '"note-area"')  !== false) {
        $text =  modifyNoteArea($text, $bookmark, $p);
        if ($p['destination'] !== 'sdcard') {
            //add markers used by javascript
            $page = $p['country_code'] . '-' . $p['language_iso'] . '-' . $p['folder_name'] . '-' . $data['filename'] . '.html';
            $note_form_begin = '<form>' . "\n";
            $note_page =  '<input type="hidden" name ="notes_page"  id ="notes_page" value="' . $page . '">' . "\n";
            $note_form_end = '</form>';
            $text .= $note_form_begin . $note_page .  $note_form_end;
        }
    }
    if (strpos($text, '-step-area"')  !== false) {
        $text =  modifyNextSteps($text, $bookmark, $p);
    }
    if (strpos($text, '{text=&quot;')  !== false) {
        $text = modifyReference($text);
    }
    //
    // strip out open new tab so that modifyLinks is called
    //
    $text = str_replace('target="_blank"', '', $text);
    $text = str_replace('target="blank"', '', $text);
    $text = str_replace('<a  ', '<a ', $text);
    //$text = str_replace('href="http' ,' target="_blank" href="http', $text);
    //
    ////writeLog('modify-page-65', $text);
    //  change internal links for easy return:
    // for SDCard we may need to remove all external references; esp those to Bible sites
    if (strpos($text, '<a href=') !== FALSE || strpos($text, '<a class="readmore"') !== FALSE) {
        $text = modifyLinks($text, $p);
    }
    //writeLog('modifyPage-73', $text);
    // popup text needs to be visible to editor but hidden in prototype and production
    if (strpos($text, 'class="popup"') !== FALSE) {
        $text = str_ireplace('class="popup"', 'class="popup invisible"', $text);
    }
    if (strpos($text, '<span class="bible-link">') !== FALSE) {
        $text = modifyBibleLinks($text, $p);
    }
    //writeLog('modifyPage-81', $text);
    if (strpos($text, '<div class="reveal">') !== FALSE ||  strpos($text, '<div class="reveal_big">') !== FALSE) {
        $text = modifyRevealSummary($text, $p);
    }
    //writeLog('modifyPage-85', $text);
    if (strpos($text, '<div class="reveal bible">') !== FALSE) {
        $text = modifyRevealBible($text, $bookmark, $p);
    }
    //writeLog('modifyPage-92', $text);
    // reveal_big is used by generations
    if (strpos($text, '<div class="reveal film') !== FALSE || strpos($text, '<div class="reveal_big film') !== FALSE) {
        $text =  modifyRevealVideo($text, $bookmark, $p);
    }

    if (strpos($text, '<div class="reveal audio">') !== FALSE) {
        $text = modifyRevealAudio($text, $bookmark, $p);
    }
    //writeLogDebug('modifyPage-109', $text);
    if (strpos($text, 'class="zoom"') !== FALSE) {
        $text = modifyZoomImage($text);
    }
    if (strpos($text, '<div class="javascript') !== false) {
        $text  = modifyJavascript($text);
    }
    /* if (strpos($text, '<div class="header') !== false){
This needs to come later in the process
    }
    */
    if (strpos($text, '<div class="trainer">') !== FALSE) {
        $text = modifyRevealTrainer($text, $p);
    }
    $bad = ['<div id="bible">', '<div class="bible_container bible">'];
    $text = str_replace($bad, '<div class="bible_container">', $text);
    $text = str_replace('bible-readmore', 'readmore', $text);

    //action button
    if (strpos($text, '<button class="action">') !== FALSE) {
        $text = modifySendAction($text, $p, $data);
    }
    // get rid of horizontal lines and other odd things
    $text = str_replace('<hr />', '', $text);
    $text = str_replace('<li>&nbsp;', '<li>', $text);
    $text = str_replace('</a> )', '</a>)', $text);
    //writeLog('modifyPage-120', $text);
    return $text;
}
