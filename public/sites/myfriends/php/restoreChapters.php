<?php
myRequireOnce('restorePage.php');

function restoreChapters($p)
{
    writeLogDebug('restoreChapters-6', 'entered');
    $sql = "SELECT * FROM content
            WHERE  country_code = '" . $p['country_code'] . "'
            AND  language_iso = '" . $p['language_iso'] . "'
            AND folder_name = '" . $p['folder_name'] . "'  AND filename = 'index'
            AND prototype_date IS NOT NULL
            ORDER BY recnum DESC LIMIT 1";

    $data = sqlArray($sql);
    $text = json_decode($data['text']);
    writeLogDebug('restoreChapters-16', $text);
    foreach ($text->chapters as $chapter) {
        writeLogAppend('restoreChapters-18', $chapter);
        $status = false;
        if (isset($chapter->prototype)) {
            $status = $chapter->prototype;
        }
        if ($status  == true) {
            $new = $p;
            $new['filename'] = $chapter->filename;
            writeLogAppend('restoreChapters-26', $new);
            restorePage($new);
        }
    }
}
