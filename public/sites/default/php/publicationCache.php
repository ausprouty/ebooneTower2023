<?php
myRequireOnce('writeLog.php');

function keyCache($p)
{
    $key = $p['country_code'] . '-' . $p['language_iso'] . '-' . $p['folder_name'];
    return $key;
}
function getCache($p)
{
    $key =  keyCache($p);
    $sql = 'SELECT * FROM cache WHERE series ="' . $key . '" LIMIT 1';
    writeLogAppend('getCache-13', $sql);
    $data =  sqlArray($sql);
    writeLogAppend('getCache-15', $data);
    if ($data) {
        writeLogAppend('getCache-15', $data);
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
    writeLogAppend('getCache-28', $cache);
    return $cache;
}

function updateCache($cache)

{
    $published = htmlspecialchars(json_encode($cache['sessions_published']), ENT_QUOTES);
    $included = htmlspecialchars(json_encode($cache['files_included']), ENT_QUOTES);
    writeLogAppend('updateCache-37', $cache);
    $sql = 'SELECT * FROM cache WHERE series ="' . $cache['key'] . '" LIMIT 1';
    $data =  sqlArray($sql);
    if ($data) {
        $sql = 'UPDATE cache SET sessions_published = "' . $published . '", 
        files_included = "' . $included . '" WHERE series = "' . $cache['key'] . '"';
        writeLogAppend('updateCache-43', $sql);
        sqlArray($sql, 'update');
    } else {
        $sql = 'INSERT INTO cache (series, sessions_published,files_included)
           VALUES ("' . $cache['key'] . '","' . $published . '", "' . $included . '")';
        writeLogAppend('updateCache-48', $sql);
        sqlInsert($sql);
    }
}
function clearCache($cache)
{
    $sql = 'DELETE  FROM cache WHERE series ="' . $cache['key'] . '" LIMIT 1';
    sqlDelete($sql);
}
