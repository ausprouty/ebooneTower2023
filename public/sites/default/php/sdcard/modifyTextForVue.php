<?php


function modifyTextForVue($text){
    $text = str_ireplace('sites/mc2/images/standard/', '@assets/images/', $text);
    $text = modifyTextForVuePopUp($text);
    return $text;
}

/*need to change 

<a href="javascript:popUp('pop1')">1 Timothy 2:4</a>
	
to 

<span class="popup" @click = "popUp('pop1')"> 1 Timothy 2:4 </span>
*/

function modifyTextForVuePopUp($text){
    $template = '<span class="popup-link" @click = "popUp(\'[id]\')"> [reference]</span>';
    $count = substr_count($text, '<a href="javascript:popUp');
    $pos_start = 0;
    for ($i = 1; $i <= $count; $i++){
        $pos_href_start = strpos($text, '<a href="javascript:popUp');
        // get id
        $pos_id_begin = strpos ($text, "'",  $pos_href_start) +1;
        $pos_id_end = strpos ($text,  "'", $pos_id_begin) ;
        $length = $pos_id_end - $pos_id_begin;
        $popup_id = substr($text,  $pos_id_begin, $length);
        writeLogDebug ('modifyTextForVuePopUp-30', $popup_id );
        // get reference
        $pos_label_begin = strpos($text, '>', $pos_href_start) +1;
        $pos_label_end = strpos($text, '<', $pos_label_begin) ;
        $length = $pos_label_end - $pos_label_begin ;
        $reference = substr($text,  $pos_label_begin, $length);
        writeLogDebug ('modifyTextForVuePopUp-35',  $reference);
        $old = array(
            '[id]',
            '[reference]'
        );
        $change = array(
            $popup_id,
            $reference
        );
        $new = str_replace($old, $change, $template);
        $length =  $pos_label_end +4 -  $pos_href_start;
        $text = substr_replace($text,$new, $pos_href_start,$length);
    }
    return $text;


}