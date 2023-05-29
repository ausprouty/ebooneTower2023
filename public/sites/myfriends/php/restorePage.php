<?php
myRequireOnce('getLatestContent.php');

function restorePage($p)
{
    $p['scope'] = 'page';
    $data = getLatestContent($p);
    $dir = ROOT_EDIT .  'sites/leapfrog/content/';
    $filename =  $dir . $data['country_iso'] . '/' . $data['language_code'] . '/' . $data['folder_name'] . '/' . $data['filename'] . '.html';
    $text = file_get_contents_utf8(($filename));
    writeLogDebug('restorePage-10', $text);
}
