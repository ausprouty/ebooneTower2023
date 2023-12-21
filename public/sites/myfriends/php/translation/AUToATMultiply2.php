<?php
/echo 'in LifE   ';
define("DESTINATION",  'staging');
require_once('/home/myfriends/edit.myfriends.network/sites/myfriends/.env.api.remote.php');
require_once('/home/myfriends/edit.myfriends.network/sites/default/php/myRequireOnce.php');
require_once('/home/myfriends/edit.myfriends.network/sites/default/php/sql.php');
myRequireOnce('writeLog.php');
myRequireOnce('getLatestContent.php');
myRequireOnce('create.php');
myRequireOnce('correctEncoding.php');
echo 'after use';

$fixing = 'life';


$sql = 'SELECT DISTINCT filename FROM content 
    WHERE language_iso = "eng"
    AND country_code = "AU"
    AND folder_name = "multiply2"
    AND filename != "index"
    ORDER BY filename';
$query  = sqlMany($sql);
while ($data = $query->fetch_array()) {
    $filename = $data['filename'];
    echo (" $filename <br>\n");
    $p = array(
        'scope' => 'page',
        'country_code' => 'AU',
        'language_iso' => 'eng',
        'folder_name' => 'multiply2',
        'filename' => $data['filename']
    );
    $res = getLatestContent($p);
    $new = $res;
    // $new['text'] = recodeGerman($res['text']);
    $new['country_code'] = 'AT';
    $new['language_iso'] = 'deu';
    $new['my_uid'] = 996; // done by computer
    _writeThisLog($data['filename'], $new);
    createContent($new);
}
echo ('finished');
return;


function _writeThisLog($filename, $content)
{
    if (!is_array($content)) {
        $text = $content;
    } else {
        $text = '';
        foreach ($content as $key => $value) {
            $text .= $key . ' => ' . $value . "\n";
        }
    }
    $fh = fopen(ROOT_LOG . $filename . '.txt', 'w');
    fwrite($fh, $text);
    fclose($fh);
}
