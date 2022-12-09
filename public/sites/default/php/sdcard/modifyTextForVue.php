<?php

myRequireOnce('modifyImagePathForVue.php', 'sdcard');


function modifyTextForVue($text, $bookmark)
{
    writeLogAppend('modifyTextForVue-5', $text);
    $bad = array(
        '<form class = "auto_submit_item">',
        '<form>',
        '</form>'
    );
    $text = str_replace($bad, '', $text);
    $text = modifyTextForImages($text, $bookmark);
    $text = modifyTextForVuePopUp($text);
    $text = modifyTextForVueReadMore($text, $bookmark);
    $text = modifyImagePathForVue($text, $bookmark);
    writeLogAppend('modifyTextForVue-16', $text);
    return $text;
}
function modifyTextForImages($text, $bookmark)
{
    $text = str_ireplace('sites/mc2/images/standard/', '@/assets/images/standard/', $text);
    $bad = array(
        '<img src="content/',
        'src="/sites/mc2/content/M2/'
    );
    $good = array(
        '<img src="@/assets/',
        'src="@/assets/'
    );
    $text = str_replace($bad, $good, $text);
    writeLogDebug('modifyTextForImages-31', $bookmark);
    $language_iso = $bookmark['language']->iso;
    writeLogDebug('modifyTextForImages-33', $language_iso);
    $bad = array(
        '/@/assets',
        '/' . $language_iso . '/images/',

    );
    $good = array(
        '@/assets',
        '/images/' . $language_iso . '/',
    );
    $text = str_replace($bad, $good, $text);
    return $text;
}

/*need to change 

<a href="javascript:popUp('pop1')">1 Timothy 2:4</a>
	
to 

<span class="popup" @click = "popUp('pop1')"> 1 Timothy 2:4 </span>
*/

function modifyTextForVuePopUp($text)
{
    $template = '<span class="popup-link" @click = "popUp(\'[id]\')"> [reference]</span>';
    $count = substr_count($text, '<a href="javascript:popUp');
    $pos_start = 0;
    for ($i = 1; $i <= $count; $i++) {
        $pos_href_start = strpos($text, '<a href="javascript:popUp');
        // get id
        $pos_id_begin = strpos($text, "'",  $pos_href_start) + 1;
        $pos_id_end = strpos($text,  "'", $pos_id_begin);
        $length = $pos_id_end - $pos_id_begin;
        $popup_id = substr($text,  $pos_id_begin, $length);

        // get reference
        $pos_label_begin = strpos($text, '>', $pos_href_start) + 1;
        $pos_label_end = strpos($text, '<', $pos_label_begin);
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
    return $text;
}

function modifyTextForVueReadMore($text, $bookmark)
{
    //writeLog('modifyReadMore-6-text', $text);
    $debug = '';
    $find = array();
    $find[] = '<a class="readmore"';
    $find[] = '<a class="bible-readmore"';
    $read_more = $bookmark['language']->read_more;
    $read_more_online = $bookmark['language']->read_more_online;
    //writeLog('modifyReadMore-12-bookmark',  $bookmark);
    //writeLog('modifyReadMore-13-readmore', $read_more . '|'. $read_more_online);
    $new = '';
    foreach ($find as $find_now) {
        $count = substr_count($text, $find_now);
        $pos_start = 0;
        for ($i = 0; $i < $count; $i++) {
            $debug .= "\n\n\nCount: $i \n\n";
            $pos_start = strpos($text, $find_now, $pos_start);
            $debug .= "\n\nPos Start: $pos_start \n";
            $pos_end =  strpos($text, '</a>', $pos_start);
            $length = $pos_end - $pos_start + 4;
            $old = substr($text, $pos_start, $length);
            writeLog('modifyReadMore-24-old',  $old);
            $new = '';
            $text = substr_replace($text, $new, $pos_start, $length);
            $pos_start = $pos_end;
        }
    }

    writeLog('modifyReadMore-21-text', $text);
    return $text;
}
