<?php
myRequireOnce('writeLog.php');


function version2Text(string $text): string
{
    //writeLog('version2Text-6', $text);
    $text = str_ireplace('"/sites/default/images/back.png', '"/sites/default/images/back.png', $text);
    $text = str_ireplace('"/sites/default/images/up.png', '"/sites/default/images/up.png', $text);
    $text = str_ireplace('"/sites/default/images/forward.png', '"/sites/default/images/forward.png', $text);
    $text = str_ireplace('"/content/', '"/sites/default/content/', $text);
    $text = str_ireplace('"/sites/myfriends/sites/mc2/', '/sites/myfriends/sites/mc2/', $text);

    $text = str_ireplace('"/content/AU/images/standard/', '', $text);
    $text = str_ireplace('"/content/ZZ/styles/myfriendsGLOBAL.css"', '', $text);
    $text = removeBibleBlock($text);
    $text = removeBibleLinkSpans($text);
    writeLogDebug('version2Text-Japanese', $text);
    return $text;
}



function removeBibleBlock(string $text): string
{
    return trim(preg_replace(
        '/<p\b[^>]*>\s*\[BibleBlock\]\s*<\/p>/i',
        '',
        $text
    ));
}
function removeBibleLinkSpans(string $text): string
{
    return preg_replace(
        '/<span\s+class=["\']bible-link["\']>(.*?)<\/span>/su',
        '$1',
        $text
    );
}
