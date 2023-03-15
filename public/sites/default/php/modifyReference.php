<?php
myRequireOnce(DESTINATION, 'writeLog.php');

/* input is:text=&quot;Jerry Trousdale wrote&quot; ref=&quot;The Kingdom Unleashed: How Jesus’ 1st-century  kingdom values are transforming thousand of culture and awakening his Church. - Jerry Trousdale, Glenn Shunshine&quot;}

output is
<a href="javascript:popUp('ref3')">Jerry Trowsdale Wrote</a>;
	<div class="popup invisible" id="ref3">
     The Kingdom Unleashed: How Jesus’ 1st-century  kingdom values are transforming thousand of culture and awakening his Church. - Jerry Trousdale, Glenn Shunshine
	</div>

*/

function modifyReference($text)
{
    //writeLogDebug('modifyReference-15', $text);
    $template =  $template = '<a href="javascript:popUp(\'ref[id]\')">[display]</a>
        <div class="popup" id="ref[id]">[reference]</div>';
    $find_begin = '{text=&quot;';
    $find_begin_length = strlen($find_begin);
    $find_middle = '&quot; ref=&quot;';
    $find_middle_length = strlen($find_middle);
    $find_end = '&quot;}';
    $find_end_length = strlen($find_end);
    $count = substr_count($text, $find_begin);
    $pos_end = 0;
    for ($i = 1; $i <= $count; $i++) {
        $pos_start = $pos_end;
        if (strpos($text, $find_begin, $pos_start) !== FALSE) {
            $pos_start = strpos($text, $find_begin, $pos_start);
            $display_start = $pos_start +  $find_begin_length;
            $pos_middle = strpos($text, $find_middle, $word_start);
            $display_length = $pos_middle - $display_start;
            $display = substr($text, $display_start, $display_length);
            $pos_end = strpos($text, $find_end, $word_start);
            $reference_start = $pos_middle + $find_middle_length;
            $reference_length = $pos_end - $pos_middle - $find_middle_length;
            $reference = substr($text, $reference_start, $reference_length);
            $old = array(
                '[id]',
                '[display]',
                '[reference]',

            );
            $new = array(
                $i,
                $display,
                $reference,
            );
            $popup = str_replace($old, $new, $template);
            $span =  $pos_end -  $pos_start + $find_end_length;
            $text = substr_replace($text, $popup, $pos_start, $span);
        }
    }
    //writeLogDebug('modifyReference-46', $text);
    return $text;
}
