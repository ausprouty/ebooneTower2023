<?php

myRequireOnce ('writeLog.php');
myRequireOnce('modifyPrototypeAndFinalLinks.php');
myRequireOnce('modifyReadmoreLinksRepair.php');

function modifyLinks($text, $p){
   
    // take these out so we can put in proper links later.  The editors like the URL so they can follow links in the editor.
    $text=str_ireplace ('target="_self"', '', $text);
    $out=[];
    $find = '<a href="' . WEBADDRESS_EDIT;  //
    if (strpos($text, $find) !== false){
        $text = _modifyEditLinks($text, $find);
    }
    if (WEBADDRESS_STAGING){
        $find = '<a href="' . WEBADDRESS_STAGING;  //
        if (strpos($text, $find) !== false){
             $text  =  modifyPrototypeAndFinalLinks($text, WEBADDRESS_STAGING, $p);
        }
    }
    if (WEBADDRESS_FINAL){
        $find = '<a href="' . WEBADDRESS_FINAL;  //
        if (strpos($text, $find) !== false ){
            $text  =  modifyPrototypeAndFinalLinks($text, WEBADDRESS_FINAL, $p);
        }
    }
    // version2 content references are /sites/mc2/content
    $find = '<a href="/sites/'. SITE_CODE ;  //
    if (strpos($text, $find) !== false){
        $text  =  str_replace( $find, '<a href="', $text);
    }
    // the above should convert all links that are to edit or prototype
    // be changed to '<a href="/content"
    $find = '<a href="/content';
    if (strpos($text, $find) !== false){
        $text = str_ireplace('" >', '">', $text);
        $text  = _modifyInternalLinks($text, $find, $p);
    }
    writeLogDebug('modifyLinks-40', $text);
    $find1 = '<a class="readmore"';
    $find2 = '<a class="bible-readmore"';
    if (strpos($text, $find1) !== false || strpos($text, $find2) !== false){
        $text = modifyReadmoreLinksRepair($text);
        myRequireOnce('removeExternalLinks.php', $p['destination']);
        if (removeExternalLinks($p)){
            $text = _removeReadmoreLinks($text, $p);
        } 
    }
    $find = '"http';
    if (strpos($text, $find) !== false){
        $text = _modifyExternalLinks($text, $find, $p);
    }
    if ( $p['destination'] == 'nojs' || $p['destination']== 'pdf' ){
        $find = '<a href="javascript:popUp';
        if (strpos($text, $find) !== false){
            $text = _modifyPopupLinks($text, $find);
        }
    }

   //writeLog('modifyLinks', $debug);

    return $text;
}
/*   <a href="javascript:popUp('pop2')">Philippians 1:6</a>
     to
    Philippians 1:6
    (only used by nojs)
*/

function _modifyPopupLinks($text, $find){
    $out=[];
    $length_find = strlen($find);
    $count = substr_count($text, $find);
    $pos_start = 1;
    for ($i= 1; $i <= $count; $i++){
        $pos_start = strpos($text, $find, $pos_start);
        $pos_java_end = strpos($text, '">', $pos_start + $length_find );
        $length = $pos_java_end - $pos_start + 2; //because need end of ">
        $old = substr($text, $pos_start, $length);
        $pos_a_start = strpos($text, '</a>', $pos_java_end);
        $text = substr_replace($text, '', $pos_a_start, 4);
        $text = str_replace($old, '', $text);
    }
    //writeLog('ModifyEditLinks',$text );
    return $text;
}
/*  <a href="https://generations.edit.myfriends.network/preview/page/A2/eng/library/emc/mc201">
      to
    <a href='/content/A2/eng/emc/mc201.html">
*/
function _modifyEditLinks($text, $find){
    $out=[];
    $length_find = strlen($find);
    $count = substr_count($text, $find);
    $pos_start = 1;
    for ($i= 1; $i <= $count; $i++){
        $pos_start = strpos($text, $find, $pos_start);
        $pos_end = strpos($text, '"', $pos_start + $length_find );
        $length = $pos_end - $pos_start;
        $old = substr($text, $pos_start, $length);
        $new_link = str_ireplace($find .'/preview/page/', '/content/', $old);
        $new_link = str_ireplace('/library/', '/', $new_link) . '.html';
        $new = '<a href="'. $new_link. '">';
        $text = str_replace($old, $new, $text);
    }
    //writeLog('ModifyEditLinks',$text );
    return $text;

}

/*  <a href="https://generations.prototype.myfriends.network/content/A2/eng/emc/mc201.html">
      to
    <a href='/content/A2/eng/emc/mc201.html">
*/
function _modifyPrototypeAndFinalLinks($text, $replace){
    $text = str_replace ($replace, '', $text);
    return $text;
}
/*  <a href="/sites/mc2/content/M2/eng/tc/tc01.html">
       to
    <a  href="#" onclick="goToPageAndSetReturn('../tc/tc01.html');">
    This must be a relative path

    $find = '<a href="/content'
*/
function _modifyInternalLinks($text, $find, $p){
   // $rand= random_int(0, 9999);
    $length_find = strlen($find);
    $count = substr_count($text, $find);
    $pos_start = 1;
    for ($i= 1; $i <= $count; $i++){
        $pos_start = strpos($text, $find, $pos_start) ;
        $pos_end = strpos($text, '">', $pos_start + $length_find);
        $content_length = $pos_end - $pos_start -  $length_find;
        $link = substr($text, $pos_start + $length_find , $content_length);
        $link_length=$pos_end-$pos_start + 2;
        $old = '<a href="/content'. $link .'">'; // plus two for the length of the end
        if (strpos($link, '.html')){
            $relative_link =_modifyLinksMakeRelative($link);
            $new = '<a id = "{id}" href="#" onclick="goToPageAndSetReturn(\''. $relative_link. '\', \'#{id}\');">';
            $new = str_replace('{id}', 'Return' . $i , $new );
        }else{ // for .pdf
             $new = '<a target= "_blank" href="/content'. $link .'">';
        }
        writeLogAppend ('modifyInternalLinks-141', $new);
        $text = substr_replace($text, $new, $pos_start, $link_length);
        $pos_start = $pos_end;
    }
    return $text;
}

/*  <a href="https://somewhere.com">
      to
    <a target="a_blank" href="https://somewhere.com">
*/
function _modifyExternalLinks($text, $find, $p){
    if ($p['destination']  !=='sdcard' && $p['destination'] !=='nojs'){
        $text = str_ireplace ('href="http', ' target = "_blank" href="http', $text);
        return $text;
    }
    elseif(!isset($p['sdcard_settings'])){
        $text = str_ireplace ('href="http', ' target = "_blank" href="http', $text);
        return $text;
    }
    elseif (isset($p['sdcard_settings']->remove_external_links)){
        if ($p['sdcard_settings']->remove_external_links == FALSE){
            $text = str_ireplace ('href="http', ' target = "_blank" href="http', $text);
            return $text;
        }
    }
    // now you want to get rid of external links
    // look for http  find< before and> after;
    $find = 'http';
    $length_find = strlen($find);
    $count = substr_count($text, $find);
    for ($i= 1; $i <= $count; $i++){
        $pos_http_start = strpos($text, $find) ;
        $pos_link_end= strpos($text, '>', $pos_http_start);
        $truncated = substr($text, 0, $pos_http_start );
        $pos_link_start= strrpos($truncated, '<');
        $length = $pos_link_end -  $pos_link_start +1;
        $substr = substr($text, $pos_link_start, $length );
        $values = array(
            'substr' => $substr,
            'length'=> $length,
            'text' =>$text
        );
        //writeLogDebug('modifyExternalLinks-180-' . $i, $values);
        $text = str_replace($substr, '', $text);
    }
    return $text;
}
 // <a class="readmore"  href="https://biblegateway.com/passage/?search=John%2010:22-30&amp;version=NIV">Read More </a>
// these need to come out in sensetive countries
function _removeReadmoreLinks($text){
    //writeLogError('_removeReadmoreLinks-173', $text);
    $find = '<a class="readmore"';
    $length_find = strlen($find);
    $count = substr_count($text, $find);
    $pos_start = 1;
    for ($i= 1; $i <= $count; $i++){
        $pos_start = strpos($text, $find, $pos_start) ;
        $pos_end = strpos($text, '</a>', $pos_start + $length_find);
        $length = $pos_end - $pos_start + 4;
        $text = substr_replace($text, '', $pos_start, $length);
        $pos_start = $pos_end;
    }
    //writeLogError('_removeReadmoreLinks-185', $text);
    return $text;
}
// '/sites/mc2/content/M2/eng/tc/tc01.html'
//      to
//    '../tc/tc01.html'

function _modifyLinksMakeRelative($link){
    $parts = explode('/', $link);
    $filename = array_pop($parts);
    $directory = array_pop($parts);
    $new = '../' . $directory . '/'. $filename;
    return $new;

}