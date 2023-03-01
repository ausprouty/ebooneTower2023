<?php
echo "In Fix Cache<br>\n";

require_once('../../.env.api.remote.php');
require_once('../../../default/php/myRequireOnce.php');
myRequireOnce('sql.php');
myRequireOnce('writeLog.php');
$output = getCache();
var_dump($output);


function getCache()
{
    $sql = 'SELECT * FROM cache';
    $data =  sqlArray($sql);
    $key = 'test';
    writeLogAppend('testCache', $data['sessions_published']);
    $test = json_decode($data['sessions_published']);
    writeLogAppend('testCache', $test);
    return $test;
}
