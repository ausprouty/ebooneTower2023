<?php
echo 'AT Clean';
define("DESTINATION",  'staging');
require_once('/home/myfriends/edit.myfriends.network/sites/myfriends/.env.api.remote.php');
require_once('/home/myfriends/edit.myfriends.network/sites/default/php/myRequireOnce.php');
require_once('/home/myfriends/edit.myfriends.network/sites/default/php/sql.php');
myRequireOnce('writeLog.php');
myRequireOnce('getLatestContent.php');
myRequireOnce('create.php');
echo 'after use';



$sql = 'SELECT DISTINCT filename FROM content 
    WHERE language_iso = "deu"
    AND country_code = "AT"
    AND filename != "index"
    AND folder_name = "life"
    ORDER BY filename';
$query  = sqlMany($sql);
while ($data = $query->fetch_array()) {
    $filename = $data['filename'];
    echo (" $filename <br>\n");
    $p = array(
        'scope' => 'page',
        'country_code' => 'AT',
        'language_iso' => 'deu',
        'folder_name' => 'life',
        'filename' => $data['filename']
    );
    $new = getLatestContent($p);
    $new['my_uid'] = 996; // done by computer
    $text = $new['text'];
    $text = str_ireplace('Der Text wird zweimal laut vorgelesen', 
        'Jeder liest den Text für sich.', $text);
    $text = str_ireplace('Jemand erzählt in eigenen Worten den Text nach, mit Unterstützung der Gruppe.',
            'Jemand liest den Text laut vor.' , $text);
    $text = str_ireplace('Wurde etwas weggelassen oder ergänzt?', 
            'Jemand erzählt den Text nach, mit Unterstützung der Gruppe (ohne im Text nachzuschauen).', $text);
    $text = str_ireplace('<ol', '<ul', $text);
    $text = str_ireplace('</ol', '</ul', $text);
    $new['text']=$text;
    createContent($new);
}
echo ('finished');
return;





