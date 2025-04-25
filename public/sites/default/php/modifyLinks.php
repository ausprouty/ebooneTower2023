<?php

myRequireOnce('writeLog.php');
myRequireOnce('modifyPrototypeAndFinalLinks.php');
myRequireOnce('modifyLinksMakeRelative.php');
myRequireOnce('modifyLinksReadmoreBible.php');
myRequireOnce('modifyLinksInternal.php');

function modifyLinks($text, $p)
{
    /* We are no longer removing Zoom links
    if ($p['destination'] == 'sdcard' || $p['destination'] == 'capacitor' ) {
        $text = _removeZoomLinks($text);
    }
    */

    // take these out so we can put in proper links later.  The editors like the URL so they can follow links in the editor.
    $text = str_ireplace('target="_self"', '', $text);
    $find = '<a href="' . WEBADDRESS_EDIT;  //
    if (strpos($text, $find) !== false) {
        $text =  modifyLinksContent($text, $find);
    }
    if (WEBADDRESS_STAGING) {
        $find = '<a href="' . WEBADDRESS_STAGING;  //
        if (strpos($text, $find) !== false) {
            $text  =  modifyPrototypeAndFinalLinks($text, WEBADDRESS_STAGING, $p);
        }
    }
    if (WEBADDRESS_FINAL) {
        $find = '<a href="' . WEBADDRESS_FINAL;  //
        if (strpos($text, $find) !== false) {
            $text  =  modifyPrototypeAndFinalLinks($text, WEBADDRESS_FINAL, $p);
        }
    }
    // version2 content references are /sites/mc2/content
    $find = '<a href="/sites/' . SITE_CODE;  //
    if (strpos($text, $find) !== false) {
        $text  =  str_replace($find, '<a href="', $text);
    }
    // the above should convert all links that are to edit or prototype
    // be changed to '<a href="/content"
    $find = '<a href="/content';
    if (strpos($text, $find) !== false) {
        $text = str_ireplace('" >', '">', $text);
        $text  =  modifyLinksInternal($text, $find, $p);
    }
    writeLogDebug('modifyLinks-47', $text);
    $find1 = '<a class="readmore"';
    $find2 = '<a class="bible-readmore"';
    if (strpos($text, $find1) !== false || strpos($text, $find2) !== false) {
        writeLogDebug('modifyLinks-50', $text);
        $text = modifyLinksReadmoreBible($text);
        writeLogDebug('modifyLinks-52', $text);
        myRequireOnce('removeLinksExternal.php', $p['destination']);
        if (removeLinksExternal($p)) {
            $text = _removeReadmoreLinks($text, $p);
        }
    }
    writeLogDebug('modifyLinks-57', $text);
    $find = '"http';
    if (strpos($text, $find) !== false) {
        $text =  modifyLinksExternal($text, $find, $p);
    }
    if ($p['destination'] == 'nojs' || $p['destination'] == 'pdf') {
        $find = '<a href="javascript:popUp';
        if (strpos($text, $find) !== false) {
            $text = modifyLinksPopup($text, $find);
        }
    }

    writeLogDebug('modifyLinks-69', $text);

    return $text;
}
/*   <a href="javascript:popUp('pop2')">Philippians 1:6</a>
     to
    Philippians 1:6
    (only used by nojs)
*/

function modifyLinksPopup($text, $find)
{
    $length_find = strlen($find);
    $count = substr_count($text, $find);
    $pos_start = 1;
    for ($i = 1; $i <= $count; $i++) {
        $pos_start = strpos($text, $find, $pos_start);
        $pos_java_end = strpos($text, '">', $pos_start + $length_find);
        $length = $pos_java_end - $pos_start + 2; //because need end of ">
        $old = substr($text, $pos_start, $length);
        $pos_a_start = strpos($text, '</a>', $pos_java_end);
        $text = substr_replace($text, '', $pos_a_start, 4);
        $text = str_replace($old, '', $text);
    }
    writeLog('modifyLinks-92', $text );
    return $text;
}
/*  <a href="https://generations.edit.myfriends.network/preview/page/A2/eng/library/emc/mc201">
      to
    <a href='/content/A2/eng/emc/mc201.html">
*/
function  modifyLinksContent($text, $find)
{

    $length_find = strlen($find);
    $count = substr_count($text, $find);
    $pos_start = 1;
    for ($i = 1; $i <= $count; $i++) {
        $pos_start = strpos($text, $find, $pos_start);
        $pos_end = strpos($text, '"', $pos_start + $length_find);
        $length = $pos_end - $pos_start;
        $old = substr($text, $pos_start, $length);
        $new_link = str_ireplace($find . '/preview/page/', '/content/', $old);
        $new_link = str_ireplace('/library/', '/', $new_link) . '.html';
        $new = '<a href="' . $new_link . '">';
        $text = str_replace($old, $new, $text);
    }
    writeLog('modifyLinks-115',$text );
    return $text;
}

/*  <a href="/sites/mc2/content/M2/eng/tc/tc01.html">
       to
    <a  href="Return#" onclick="goToPageAndSetReturn('../tc/tc01.html');">
    This must be a relative path

    $find = '<a href="/content'
*/

/*  <a href="https://somewhere.com">
      to
    <a target="a_blank" href="https://somewhere.com">
*/
function  modifyLinksExternal($text, $find, $p)
{
    if ($p['destination']  !== 'sdcard' && $p['destination'] !== 'nojs') {
        $text = str_ireplace('href="http', ' target = "_blank" href="http', $text);
        return $text;
    } elseif (!isset($p['sdcard_settings'])) {
        $text = str_ireplace('href="http', ' target = "_blank" href="http', $text);
        return $text;
    } elseif (isset($p['sdcard_settings']->external_links)) {
        if ($p['sdcard_settings']->external_links == TRUE) {
            $text = str_ireplace('href="http', ' target = "_blank" href="http', $text);
            return $text;
        }
    }
    // remove external links
    $find = 'http';
    $length_find = strlen($find);
    $count = substr_count($text, $find);
    for ($i = 1; $i <= $count; $i++) {
        $pos_http_start = strpos($text, $find);
        $pos_link_end = strpos($text, '>', $pos_http_start);
        // now look in reverse for the opening of this link
        $truncated = substr($text, 0, $pos_http_start);
        $pos_link_start = strrpos($truncated, '<');
        $length = $pos_link_end -  $pos_link_start + 1;
        $substr_link = substr($text, $pos_link_start, $length);
        //<a href="https://vimeo.com/channels/movementbuilding">
        $pos_closing_tag_begin = strpos($text, '</a>', $pos_link_start);
        $length_words = $pos_closing_tag_begin - $pos_link_end - 1;
        $substr_words = substr($text, $pos_link_end + 1, $length_words);
        $pos_closing_tag_end = $pos_closing_tag_begin + 4;
        $length_total = $pos_closing_tag_end - $pos_link_start;
        $old = substr($text, $pos_link_start, $length_total);
        $values = array(
            'substr_link' => $substr_link,
            'substr_words' => $substr_words,
            'pos_link_start' => $pos_link_start,
            'old' => $old,
            'new' => $substr_words,
            'text' => $text
        );
        writeLogDebug('modifyLinksExternal-172-' . $i, $values);
        $text = str_replace($old, $substr_words, $text);
    }
    return $text;
}
// <a class="readmore"  href="https://biblegateway.com/passage/?search=John%2010:22-30&amp;version=NIV">Read More </a>
// these need to come out in sensetive countries
function _removeReadmoreLinks($text)
{
    writeLogError('_removeReadmoreLinks-173', $text);
    $find = '<a class="readmore"';
    $length_find = strlen($find);
    $count = substr_count($text, $find);
    $pos_start = 1;
    for ($i = 1; $i <= $count; $i++) {
        $pos_start = strpos($text, $find, $pos_start);
        $pos_end = strpos($text, '</a>', $pos_start + $length_find);
        $length = $pos_end - $pos_start + 4;
        $text = substr_replace($text, '', $pos_start, $length);
        $pos_start = $pos_end;
    }
    writeLogError('_removeReadmoreLinks-193', $text);
    return $text;
}
/*
This is old code and is no longer supported
<span class="zoom"><a href="https://staging.mc2.online/content/M2/eng/multiply2/Period1.png" 
     target="a_blank"><img alt="Stage of Ministry" class="lesson_image" 
     src="/sites/mc2/content/M2/eng/multiply2/Period1.png" /></a>
</span>
*/
function _removeZoomLinks($text)
{
    writeLogDebug('modifyLinks-20', $text);
    $find_begin = '<span class="zoom"';
    $find_end = '</span>';
    $length_find_end = strlen($find_end);
    $count = substr_count($text, $find_begin);
    $pos_start = 1;
    for ($i = 1; $i <= $count; $i++) {
        // find span
        $pos_start = strpos($text, $find_begin, $pos_start);
        $pos_end = strpos($text, $find_end, $pos_start);
        $length = $pos_end - $pos_start + $length_find_end;
        $span = substr($text, $pos_start, $length);
      
        //find image
        $pos_image_start = strpos($span, '<img');
        $pos_image_end = strpos($span, '>', $pos_image_start);
        $length_image = $pos_image_end - $pos_image_start + 1;
        $image = substr($span, $pos_image_start, $length_image);
        $text = str_replace($span, $image, $text);
        $pos_start = $pos_end;
    }
    writeLogDebug('modifyLinks-225', $text);
    return $text;
}
