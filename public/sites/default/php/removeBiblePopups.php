<?php

/* <a href="javascript:popUp('pop1')">2 Corinthians 5:17</a>

	<div class="popup" id="pop1"><!-- begin bible -->
	<div>
	<div>
	<p><sup class="versenum">17&nbsp;</sup>Therefore, if anyone is in Christ, the new creation has come: The old has gone, the new is here!</p>
	</div>
	</div>
	<!-- end bible --></div>

    to

    <span class="bible-link">2 Corinthians 5:17</span>

    */


function removeBiblePopups($text)
{
    $find = '<a href="javascript:popUp';
    $find_reference_start = ')">';
    $find_reference_end = '<div class="popup"';
    $count = substr_count($text, $find);
    $template = '<span class="bible-link">[ref]</span>';
    $bad = array('</a>', "\n");
    for ($i = 0; $i < $count; $i++) {
        // find whole block
        $pos_start = strpos($text, $find);
        $pos_end = strpos($text, '<!-- end bible --></div>');
        $length = $pos_end - $pos_start + strlen($find);
        // find reference
        $pos_ref_start = strpos($text, $find_reference_start, $pos_start) + strlen($find_reference_start);
        $pos_ref_end = strpos($text, $find_reference_end, $pos_start);
        $length_ref = $pos_ref_end - $pos_ref_start;
        $ref = substr($text, $pos_ref_start, $length_ref);
        $ref = rtrim($ref);
        $ref = str_replace($bad, '', $ref);
        $new = str_replace('[ref]', $ref, $template);
        // update $text
        $text = substr_replace($text, $new, $pos_start, $length);
    }
    return $text;
}
