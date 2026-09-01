<?php

declare(strict_types=1);
require_once('../.env.api.remote.php');

$dryRun = false; // change to false after checking the output

// Run:
// php scripts/update-japanese-structural-errors.php
echo "Starting Japanese section name update...\n";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$mysqli = new mysqli(
    HOST,
    USER,
    PASS,
    DATABASE_CONTENT
);

$mysqli->set_charset('utf8mb4');



$replacements = [
    '振り返り' => '振りろう',
    '見上げる' => '見上げよう',
    '将来を見る' => '前を見よう',
];

$selectSql = <<<SQL
SELECT recnum,
       country_code,
       language_iso,
       folder_name,
       filename,
       text
FROM content
WHERE language_iso = 'jpn'
  AND (
      text LIKE '%振り返り%'
      OR text LIKE '%見上げる%'
      OR text LIKE '%将来を見る%'
  )
ORDER BY recnum
SQL;

$updateSql = <<<SQL
UPDATE content
SET text = ?
WHERE recnum = ?
SQL;

$updateStmt = $mysqli->prepare($updateSql);

$result = $mysqli->query($selectSql);

$rowsChecked = 0;
$rowsChanged = 0;

while ($row = $result->fetch_assoc()) {
    $rowsChecked++;

    $recnum = (int) $row['recnum'];
    $originalText = (string) $row['text'];

    $updatedText = str_replace(
        array_keys($replacements),
        array_values($replacements),
        $originalText
    );

    if ($updatedText === $originalText) {
        continue;
    }

    $rowsChanged++;

    echo "Changed recnum {$recnum}: ";
    echo "{$row['country_code']}/{$row['language_iso']}/{$row['folder_name']}/{$row['filename']}\n";

    foreach ($replacements as $from => $to) {
        if (strpos($originalText, $from) !== false) {
            echo "  {$from} -> {$to}\n";
        }
    }

    if (!$dryRun) {
        $updateStmt->bind_param('si', $updatedText, $recnum);
        $updateStmt->execute();
    }
}

echo "Finished Japanese section name update.\n";
echo "\nRows checked: {$rowsChecked}\n";
echo "Rows changed: {$rowsChanged}\n";

if ($dryRun) {
    echo "\nDRY RUN ONLY. No database rows were updated.\n";
    echo "Set \$dryRun = false to write changes.\n";
} else {
    echo "\nDatabase updated.\n";
}
