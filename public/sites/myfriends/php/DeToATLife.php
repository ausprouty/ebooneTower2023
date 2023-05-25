<?php
echo 'in LifE   ';
define("DESTINATION",  'staging');
require_once('/home/myfriends/edit.myfriends.network/sites/myfriends/.env.api.remote.php');
require_once('/home/myfriends/edit.myfriends.network/sites/default/php/myRequireOnce.php');
require_once('/home/myfriends/edit.myfriends.network/sites/default/php/sql.php');
require_once('/home/myfriends/edit.myfriends.network/sites/default/php/writeLog.php');
require_once('/home/myfriends/edit.myfriends.network/sites/default/php/getLatestContent.php');
myRequireOnce('create.php');
echo 'after use';

$fixing = 'life';


$sql = 'SELECT DISTINCT filename FROM content 
    WHERE language_iso = "deu"
    AND country_code = "DE"
    AND folder_name = "life"
    AND filename != "index"
    ORDER BY filename';
$query  = sqlMany($sql);
while ($data = $query->fetch_array()) {
    $filename = $data['filename'];
    echo (" $filename <br>\n");
    $p = array(
        'scope' => 'page',
        'country_code' => 'DE',
        'language_iso' => 'deu',
        'folder_name' => 'life',
        'filename' => $data['filename']
    );
    $res = getLatestContent($p);
    $new = $res['content'];
    $new['country_code'] = 'AT';
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
