<?php
myRequireOnce('writeLog.php');

function keyCache($p)
{
    $key = $p['country_code'] . '-' . $p['language_iso'] . '-' . $p['folder_name'];
    return $key;
}
function getCache($p)
{
    // writeLogAppend('getCache-11', $p);
    $key =  keyCache($p);
    $destination = $p['destination'];
    $sql = 'SELECT * FROM cache WHERE series ="' . $key . '" AND source = "' . $destination . '" LIMIT 1';
    $data =  sqlArray($sql);
    if ($data) {
        $cache = array(
            'key' => $key,
            'sessions_published' => json_decode($data['sessions_published']),
            'files_included' => json_decode($data['files_included'])
        );
    } else {
        $cache = array(
            'key' => $key,
            'sessions_published' => [],
            'files_included' => []
        );
    }
    return $cache;
}

function updateCache($cache, $destination)

{
    $published = json_encode($cache['sessions_published']);
    $included = json_encode($cache['files_included']);
    $sql = 'SELECT * FROM cache WHERE series ="' . $cache['key'] . '" AND source = "' . $destination . '" LIMIT 1';
    $data =  sqlArray($sql);
    if ($data) {
        $sql = "UPDATE cache SET sessions_published = '" . $published . "', 
        files_included = '" . $included . "' WHERE series = '" . $cache['key'] . "' AND source = '" . $destination . "'";
        sqlArray($sql, 'update');
    } else {
        $sql = "INSERT INTO cache (series, source, sessions_published,files_included)
           VALUES ('" . $cache['key'] . "','" . $destination .  "','" . $published . "', '" . $included . "')";
        sqlInsert($sql);
    }
}
function clearCache($cache, $destination)
{
    $sql = 'DELETE  FROM cache WHERE series ="' . $cache['key'] . '" AND source = "' . $destination . '" LIMIT 1';
    sqlDelete($sql);
}
function checkCache($p)
{
    $output = '';
    $key = keyCache($p);
    $sql = 'SELECT * FROM cache WHERE series ="' . $key . '" AND source = "' . $p['destination'] . '" LIMIT 1';
    $data =  sqlArray($sql);
    if ($data) {
        $output = $data['source'];
    }
    return $output;
}
