<?php

myRequireOnce('createPage.php');
myRequireOnce('getTitle.php');
myRequireOnce('modifyBibleLinks.php');
myRequireOnce('modifyExternalJavascript.php');
myRequireOnce('modifyJavascript.php');
myRequireOnce('modifyLinks.php');
myRequireOnce('modifyNextSteps.php');
myRequireOnce('modifyNoteArea.php');
//myRequireOnce ('modifyReadMore.php');
myRequireOnce('modifyTextForVue.php');
myRequireOnce('modifyReference.php');
myRequireOnce('modifyRevealAudio.php');
myRequireOnce('modifyRevealBible.php');
myRequireOnce('modifyRevealSummary.php');
myRequireOnce('modifyRevealTrainer.php');
myRequireOnce('modifyRevealVideo.php');
myRequireOnce('modifyRevealVideoIntro.php');
myRequireOnce('modifySendAction.php');
myRequireOnce('updateProgress.php');
myRequireOnce('version2Text.php');

myRequireOnce('writeLog.php');


function modifyPage($text, $p, $data, $bookmark)

{
    $out = new stdClass;
    //writeLogDebug('capacitor-modifyPage-27', $text);
    $text = version2Text($text);
    //writeLogDebug('capacitor-modifyPage-31', $text);
    $response = (object) modifyTextForVue($text, $bookmark, $p);
    $text = $response->text;
    $progress = $response->progress;
    //writeLogDebug('Object - modifyPage', $progress);
    //writeLogDebug('capacitor-modifyPage-33', $text);
    /* you must modify reveal video and audio before modifying external links
       reveal_big is used by site generations
    */
    if (strpos($text, '<div class="reveal film">') !== FALSE || strpos($text, '<div class="reveal_big film') !== FALSE) {
        $text =  modifyRevealVideo($text, $bookmark, $p);
    }
    // used by Spanish MC2
    if (strpos($text, '<div class="reveal film intro">') !== FALSE) {
        $text =  modifyRevealVideoIntro($text, $bookmark, $p);
    }

    if (strpos($text, '<div class="reveal audio">') !== FALSE) {
        $text = modifyRevealAudio($text, $bookmark, $p);
    }
    //writeLogDebug('capacitor-modifyPages-49', $text);
    //
    // modify note fields
    //
    if (strpos($text, '"note-area"')  !== false) {
        $text =  modifyNoteArea($text, $bookmark, $p);
    }
    if (strpos($text, '-step-area"')  !== false) {
        $text =  modifyNextSteps($text, $bookmark, $p);
    }
    if (strpos($text, '{text=&quot;')  !== false) {
        $text = modifyReference($text);
    }
    //writeLogDebug('capacitor-modifyPage-73-ZOOM', $text);
    //
    // strip out open new tab so that modifyLinks is called
    //
    $bad = array(
        'target="_blank"',
        'target="blank"',
        'target = "_blank"',
        'class="bible-ref external-link"  target="_blank'
    );
    $text = str_replace($bad, '', $text);
    $text = str_replace('<a  ', '<a ', $text);
    //  change internal links for easy return:
    // for SDCard we may need to remove all external references; esp those to Bible sites
    if (strpos($text, '<a href=') !== FALSE || strpos($text, '<a class="readmore"') !== FALSE) {
        $text = modifyLinks($text, $p);
    }
    // popup text needs to be visible to editor but hidden in prototype and production
    if (strpos($text, 'class="popup"') !== FALSE) {
        $text = str_ireplace('class="popup"', 'class="popup invisible"', $text);
    }
    if (strpos($text, '<span class="bible-link">') !== FALSE) {
        $text = modifyBibleLinks($text, $p);
    }
    if (strpos($text, '<div class="reveal">') !== FALSE ||  strpos($text, '<div class="reveal_big">') !== FALSE) {
        $text = modifyRevealSummary($text, $p);
    }
    if (strpos($text, '<div class="reveal bible">') !== FALSE) {
        $text = modifyRevealBible($text, $bookmark, $p);
    }
    if (strpos($text, 'class="zoom"') !== FALSE) {
        myRequireOnce('modifyZoomImage.php', $p['destination']);
        $reply = modifyZoomImage($text, $p);
        $text = $reply->text;
        $progress = updateProgress($progress, $reply->progress);
    }
    if (strpos($text, '<div class="javascript') !== false) {
        $text  = modifyJavascript($text);
    }
    if (strpos($text, '<div class="external-javascript') !== false) {
        $text  = modifyExternalJavascript($text);
    }
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
    $out->text = $text;
    $out->progress = $progress;

    return $out;
}
