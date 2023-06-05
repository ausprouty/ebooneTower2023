<?php
myRequireOnce('getLatestContent.php');
myRequireOnce('create.php');

function restorePage($p)
{
    $p['scope'] = 'page';
    $data = getLatestContent($p);
    $dir = ROOT_EDIT .  'sites/leapfrog/content/';
    $filename =  $dir . $data['country_code'] . '/' . $data['language_iso'] . '/' . $data['folder_name'] . '/' . $data['filename'] . '.html';
    writeLogDebug('restorePage-10', $filename);
    $text = file_get_contents($filename);
    writeLogDebug('restorePage-12', $text);
    if (strpos($text, '<div class="lesson">') !== false) {
        $first = strpos($text, '<div class="lesson">');
    } else if (strpos($text, '<div class="rtl myfriends-lesson">') !== false) {
        $first = strpos($text, '<div class="rtl myfriends-lesson">');
    } else if (strpos($text, '<div class="rtl lesson">') !== false) {
        $first = strpos($text, '<div class="rtl lesson">');
    } else if (strpos($text, '<div dir="rtl" id="mycontent">') !== false) {
        $first = strpos($text, '<div dir="rtl" id="mycontent">');
    } else if (strpos($text, '<div dir="ltr" id="mycontent">') !== false) {
        $first = strpos($text, '<div dir="ltr" id="mycontent">');
    }
    /*    202
<div class="rtl myfriends-lesson"><img class="myfriend-lesson-rtl-icon" src="/sites/default/images/sharing-life.png" />
<div class="rtl myfriends-lesson-subtitle-rtl">شارك</div>
</div>

201
 <div class="rtl lesson"><img class="lesson-icon rtl" src="/sites/default/images/sharing-life.png" />
<div class="lesson-subtitle rtl">شارك</div>
</div>*/

    $bad = array('"rtl myfriends-lesson"', '"myfriend-lesson-rtl-icon"', '"rtl myfriends-lesson-subtitle-rtl"');
    $good = array('"rtl lesson"', '"lesson-icon rtl"', '"lesson-subtitle rtl"');
    $text = str_replace($bad, $good, $text);
    $last = strpos($text, '<table class="social"');
    $length = $last - $first;
    $new = substr($text, $first, $length);
    $bad = array('<html>', '</html>');
    $new = str_replace($bad, '', $new);
    if ($data['country_code'] == 'PK') {
        /*
        <div class="lesson"><img class="lesson-icon" src="/sites/default/images/sharing-life.png" />
        <div class="lesson-subtitle">بانٹنا</div>
        </div>
        */
        $new = str_replace('class="ltr"', 'class="rtl"', $new);
        $new = str_replace('<li>', '<li class="rtl">', $new);
        $new = str_replace('<p class="myfriends-reference">', '<p class="rtl myfriends-reference">', $new);
        $new = str_replace('class="readmore"', 'class="rtl readmore"', $new);


        $new = str_replace('<div class="lesson">', '<div class="rtl lesson">', $new);
        $new = str_replace('<img class="lesson-icon"', '<img class="lesson-icon rtl"', $new);
        $new = str_replace('<div class="lesson-subtitle">', '<div class="lesson-subtitle rtl">', $new);
    }
    $data['text'] = $new;
    $data['my_uid'] = 996;
    $data['prototype_date'] = null;
    $data['prototype_uid'] = null;
    $data['publish_date'] = null;
    $data['publish_uid'] = null;
    createContent($data);
    writeLogDebug('restorePage-66', $new);
    return;
}
