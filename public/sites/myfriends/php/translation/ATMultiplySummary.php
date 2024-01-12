<?php
$bad = '<p><strong>ZUSAMMEFASSUNG</strong></p>';
$good = '<div class="reveal">&nbsp;
<hr />
<h2>ZUSAMMENFASSUNG</h2>

<hr /></div>';

echo 'in Multiply2   ';
define("DESTINATION",  'staging');
require_once('/home/myfriends/edit.myfriends.network/sites/myfriends/.env.api.remote.php');
require_once('/home/myfriends/edit.myfriends.network/sites/default/php/myRequireOnce.php');
require_once('/home/myfriends/edit.myfriends.network/sites/default/php/sql.php');
myRequireOnce('writeLog.php');
myRequireOnce('getLatestContent.php');
myRequireOnce('create.php');
myRequireOnce('correctEncoding.php');
echo 'after use';
$missing = '';

$sql = 'SELECT DISTINCT filename FROM content 
    WHERE language_iso = "deu"
    AND country_code = "AT"
    AND folder_name = "multiply2"
    AND filename != "index"
    ORDER BY filename';
$query  = sqlMany($sql);
while ($data = $query->fetch_array()) {
    $filename = $data['filename'];
    $p = array(
        'scope' => 'page',
        'country_code' => 'AT',
        'language_iso' => 'deu',
        'folder_name' => 'multiply2',
        'filename' => $data['filename']
    );
    $res = getLatestContent($p);
    $new = $res;
    // $new['text'] = recodeGerman($res['text']);
   if (strpos($new['text'], $bad) !== false){
    $new['text'] = str_ireplace($new['text'], $bad, $good);
    createContent($new);
   }
   else{
    if (strpos($new['text'], $good) == false){
     echo ($data['filename'] . '<br>');
    }
   }
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
