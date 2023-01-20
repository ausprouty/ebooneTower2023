<?php
myRequireOnce('writeLog.php');

function modifyExternalJavascript($text)
{
    $debug = '';
    $template = '<script [src]>' . "\n";
    $bad = ['<hr />', '<p>', '</p>', '<br>', '<br/>', '<br />', '&nbsp;', ' '];
    $output = array();
    $find = '<div class="external-javascript';
    $count = substr_count($text, $find);
    $debug .= $count . "\n";
    for ($i = 0; $i < $count; $i++) {
        $pos_start = strpos($text, $find);
        $pos_end = strpos($text, '</div>', $pos_start);
        $length = $pos_end - $pos_start + 6;  // add 6 because last item is 6 long
        $old = substr($text, $pos_start, $length);
        // clean javascript
        $src = str_ireplace($bad, '', $old);
        $src = str_replace('&quot;', '"', $src);
        $script = str_replace('[src]', $src, $template);
        // replace javascript
        $text = substr_replace($text, $script, $pos_start, $length);
    }
    //writeLog('modifyJavascript-25', $debug);
    return $text;
}
