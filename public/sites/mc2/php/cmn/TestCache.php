<?php
echo "In Fix Cache<br>\n";

require_once('../../.env.api.remote.php');
require_once('../../../default/php/myRequireOnce.php');
myRequireOnce(DESTINATION, 'sql.php');
myRequireOnce(DESTINATION, 'writeLog.php');
$output = getCache();
var_dump($output);


function getCache()
{
    $sql = 'SELECT * FROM cache';
    $data =  sqlArray($sql);
    $key = 'test';
    writeLogAppend('testCache', $data['sessions_published']);
    $test = json_decode($data);
    writeLogAppend('testCache', $test);
    return $test;
}
