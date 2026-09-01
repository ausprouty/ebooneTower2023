<?php

require_once __DIR__ . '/../.env.api.remote.php';

function contentDbConnection()
{
    $conn = new mysqli(HOST, USER, PASS, DATABASE_CONTENT, DATABASE_PORT);

    if ($conn->connect_error) {
        die("Connection has failed: " . $conn->connect_error);
    }

    $conn->set_charset('utf8mb4');

    return $conn;
}

function looksMojibaked($value)
{
    if (!is_string($value)) {
        return false;
    }

    return preg_match(
        '/(Ã.|Â.|â[€™€œ€“]|ã.|ä.|å.|æ.|ï¼|ï½|á.)/u',
        $value
    ) === 1;
}

function repairMojibakeString($value)
{
    if (!looksMojibaked($value)) {
        return $value;
    }

    $fixed = mb_convert_encoding($value, 'Windows-1252', 'UTF-8');

    if (!mb_check_encoding($fixed, 'UTF-8')) {
        return $value;
    }

    return $fixed;
}

function repairJsonValue($value)
{
    if (is_string($value)) {
        return repairMojibakeString($value);
    }

    if (is_array($value)) {
        foreach ($value as $key => $child) {
            $value[$key] = repairJsonValue($child);
        }

        return $value;
    }

    return $value;
}

function repairJsonText($text)
{
    $decoded = json_decode($text, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        return null;
    }

    $repaired = repairJsonValue($decoded);

    return json_encode(
        $repaired,
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );
}

$conn = contentDbConnection();

$languageIso = 'mya';

$dryRun = false;

$sql = "
    SELECT c.*
    FROM content c
    INNER JOIN (
        SELECT folder_name, filename, filetype, MAX(recnum) AS latest_recnum
        FROM content
        WHERE language_iso = ?
        AND filetype IN ('html', 'json')
        GROUP BY folder_name, filename, filetype
    ) latest
    ON c.recnum = latest.latest_recnum
    ORDER BY c.folder_name, c.filetype, c.filename
";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param('s', $languageIso);

if (!$stmt->execute()) {
    die("Execute failed: " . $stmt->error);
}

$result = $stmt->get_result();

if (!$result) {
    die("get_result failed: " . $stmt->error);
}

$rows = [];

while ($row = $result->fetch_assoc()) {
    $rows[] = $row;
}

echo "Found " . count($rows) . " latest records.\n\n";

foreach ($rows as $row) {
    $recnum = $row['recnum'];
    $filename = $row['filename'];
    $originalText = $row['text'];

    if ($row['filetype'] === 'json') {
        $newText = repairJsonText($originalText);

        if ($newText === null) {
            echo "SKIP {$recnum} {$filename}: JSON error\n";
            continue;
        }
    } else {
        $newText = repairMojibakeString($originalText);
    }

    if ($newText === $originalText) {
        echo "NO CHANGE {$recnum} {$filename}\n";
        continue;
    }

    echo "REPAIR {$recnum} {$filename}\n";

    if ($dryRun) {
        echo "Dry run only. No insert made.\n";
        echo "Original sample:\n";
        echo mb_substr($originalText, 0, 300) . "\n\n";
        echo "Repaired sample:\n";
        echo mb_substr($newText, 0, 300) . "\n";
        echo "-----------------------------\n";
        continue;
    }

    $insertSql = "
        INSERT INTO content (
            version,
            edit_date,
            edit_uid,
            prototype_uid,
            prototype_date,
            publish_uid,
            publish_date,
            language_iso,
            country_code,
            folder_name,
            filetype,
            page,
            title,
            filename,
            text
        ) VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
        )
    ";

    $insertStmt = $conn->prepare($insertSql);

    $editDate = time();

    $insertStmt->bind_param(
        'siiiiiissssisss',
        $row['version'],
        $editDate,
        $row['edit_uid'],
        $row['prototype_uid'],
        $row['prototype_date'],
        $row['publish_uid'],
        $row['publish_date'],
        $row['language_iso'],
        $row['country_code'],
        $row['folder_name'],
        $row['filetype'],
        $row['page'],
        $row['title'],
        $row['filename'],
        $newText
    );

    $insertStmt->execute();

    echo "Inserted new repaired record for {$filename}. ";
    echo "New recnum: " . $conn->insert_id . "\n";
}

echo "\nDone.\n";