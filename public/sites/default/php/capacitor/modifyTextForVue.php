<?php


myRequireOnce('copyFilesForCapacitor.php');
myRequireOnce('dirStandard.php');
myRequireOnce('progressMerge.php');


function modifyTextForVue($text, $bookmark, $p)
{
    $out = new stdClass;
    $response = new stdClass;
    $progress = new stdClass;
    if (isset($p['progress]'])) {
        $progress = $p['progress'];
    }

    //writeLogDebug('Object-ModifyTextForVue-18', $text); // text is string of text only
    $bad = array(
        '<form class = "auto_submit_item">',
        '<form>',
        '</form>'
    );
    $text = str_replace($bad, '', $text);
    $response = (object) modifyTextForImages($text, $p);
    //writeLogDebug('Object-modifyTextForVue-26', $response);  // object with both text and progress
    $progress = progressMergeObjects($progress, $response, ' modifyTextForVue-27');
    //writeLogDebug('Object-modifyTextForVue-28', $response->progress);
    //writeLogDebug('Object-modifyTextForVue-29', $progress);

    $response = (object) modifyTextForVuePopUp($response->text);
    //writeLogDebug('Object-modifyTextForVue-32', $response->progress);
    //writeLogDebug('Object-modifyTextForVue-33', $progress);
    $progress = progressMergeObjects($progress, $response, ' modifyTextForVue-34');
    $response = (object) modifyTextForVueReadMore($response->text, $bookmark);
    $out->text = $response->text;
    $out->progress = $progress;
    return $out;
}
// modify image and copy (it is much easier to do now)
function modifyTextForImages($text, $p)
{
    //writeLogDebug('capacitor-modifyTextForImages-29', $text);
    $out = new stdClass;
    $progress = new stdClass;
    $new_progress = new stdClass;
    $find = '<img';
    $bad = array(' ', '"');
    $count = substr_count($text, $find);
    $pos_start = 0;
    for ($i = 1; $i <= $count; $i++) {
        $img_start = strpos($text, $find, $pos_start);
        $img_end = strpos($text, '>', $img_start);
        $img_length = $img_end - $img_start + 1;
        //<img class="lesson-icon" src="/sites/mc2/images/standard/look-forward.png">
        $img_div = substr($text, $img_start, $img_length);
        $src_start = strpos($img_div, ' src') + 4;
        $src_quote1 = strpos($img_div, '"', $src_start) + 1;
        $src_quote2 = strpos($img_div, '"', $src_quote1);
        $src_length = $src_quote2 - $src_quote1;
        $src = substr($img_div, $src_quote1, $src_length);
        $source = str_replace($bad, '', $src);
        // do not replace any that start with @
        if (strpos($source, '@') === false) {
            //writeLogAppend('capacitor-modifyTextForImages-44', $message);
            $new_progress = (object) modifyTextForImagesCopy($source, $p);
            $progress = progressMergeObjects($progress, $new_progress, 'modifyTextForImages-67');
            $new_source = '@/assets/' . $source;
            $new_source = str_replace('//', '/', $new_source);
            $new_div = str_replace($src, $new_source, $img_div);
            //writeLogAppend('capacitor-modifyTextForImages-55', "$img_div\n$new_div\n\n");
            $text = substr_replace($text, $new_div, $img_start, $img_length);
        } else {
            $new_progress->progress = 'error';
            $new_progress->message = "Image $source starts with @ in modifyTextForImages";
            $progress = progressMergeObjects($progress, $new_progress, 'modifyTextForImages-76');
        }
        $pos_start = $img_end;
        //writeLogDebug('capacitor-modifyTextForImages-61-' . $i, $text);
    }
    $out->text = $text;
    $out->progress = $progress;
    return $out;
}
function modifyTextForImagesCopy($source, $p)
{
    $progress = new stdClass;
    $cap_dir = dirStandard('assets', DESTINATION,  $p, $folders = null, $create = true);
    $destination = $cap_dir . $source;
    $destination = str_replace('//', '/', $destination,);
    $source = ROOT_WEBSITE . $source;
    $source = str_replace('//', '/', $source);
    $progress = copyFilesForCapacitor($source, $destination, 'modifyTextForImages');
    return $progress;
}


/*need to change 

<a href="javascript:popUp('pop1')">1 Timothy 2:4</a>
	
to 

<span class="popup" @click = "popUp('pop1')"> 1 Timothy 2:4 </span>
*/

function modifyTextForVuePopUp($text)
{
    $out = new stdClass;
    //writeLogDebug('Object-modifyTextForVuePopup', $text); // text only as string
    $template = '<span class="popup-link" @click = "popUp(\'[id]\')"> [reference]</span>';
    $count = substr_count($text, '<a href="javascript:popUp');
    $pos_start = 0;
    for ($i = 1; $i <= $count; $i++) {
        $needle_href = '<a href="javascript:popUp';
        $pos_href_start = strpos($text, $needle_href);
        // get id
        $needle_semiquote = "'";
        $pos_id_begin = strpos($text, $needle_semiquote,  $pos_href_start) + 1;
        $pos_id_end = strpos($text,  $needle_semiquote, $pos_id_begin);
        $length = $pos_id_end - $pos_id_begin;
        $popup_id = substr($text,  $pos_id_begin, $length);

        // get reference
        $needle_gt = '>';
        $pos_label_begin = strpos($text, $needle_gt, $pos_href_start) + 1;
        $needle_lt = '<';
        $pos_label_end = strpos($text, $needle_lt, $pos_label_begin);
        $length = $pos_label_end - $pos_label_begin;
        $reference = substr($text,  $pos_label_begin, $length);

        $old = array(
            '[id]',
            '[reference]'
        );
        $change = array(
            $popup_id,
            $reference
        );
        $new = str_replace($old, $change, $template);
        $length =  $pos_label_end + 4 -  $pos_href_start;
        $text = substr_replace($text, $new, $pos_href_start, $length);
    }
    $out->text = $text;
    $out->message = '';
    $out->progress = '';
    return $out;
}

function modifyTextForVueReadMore($text, $bookmark)
{
    $debug = '';
    $out = new stdClass;
    $find = array();
    $find[] = '<a class="readmore"';
    $find[] = '<a class="bible-readmore"';
    $read_more = $bookmark['language']->read_more;
    $read_more_online = $bookmark['language']->read_more_online;
    $new = '';
    foreach ($find as $find_now) {
        $count = substr_count($text, $find_now);
        $pos_start = 0;
        for ($i = 0; $i < $count; $i++) {
            $debug .= "\n\n\nCount: $i \n\n";
            $pos_start = strpos($text, $find_now, $pos_start);
            $debug .= "\n\nPos Start: $pos_start \n";
            $needle_a = '</a>';
            $pos_end =  strpos($text, $needle_a, $pos_start);
            $length = $pos_end - $pos_start + 4;
            $old = substr($text, $pos_start, $length);
            // //writeLog('modifyReadMore-24-old',  $old);
            $new = '';
            $text = substr_replace($text, $new, $pos_start, $length);
            $pos_start = $pos_end;
        }
    }
    $out->text = $text;
    $out->message = '';
    return $out;
}
