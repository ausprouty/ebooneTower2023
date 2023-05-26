<?php
echo 'in LifE   ';
define("DESTINATION",  'staging');
require_once('/home/myfriends/edit.myfriends.network/sites/myfriends/.env.api.remote.php');
require_once('/home/myfriends/edit.myfriends.network/sites/default/php/myRequireOnce.php');
myRequireOnce('writeLog.php');
myRequireOnce('getLatestContent.php');
myRequireOnce('create.php');
myRequireOnce('correctEncoding.php');
myRequireOnce('create.php');
echo 'after use';

$fixing = 'life';

echo (" $filename <br>\n");
$p = array(
    'scope' => 'series',
    'country_code' => 'AU',
    'language_iso' => 'eng',
    'folder_name' => 'multiply',
    'filename' => 'index'
);
$res = getLatestContent($p);
$new = $res;
//$new['text'] = recodeGerman($res['text']);
$new['country_code'] = 'AT';
$new['language_iso'] = 'deu';
$new['my_uid'] = 996; // done by computer
_writeThisLog('LifeIndex', $new);
createContent($new);
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
