<?php

declare(strict_types=1);

include __DIR__ . '/../../.env.api.remote.php';
include './linkJapaneseReadAloudReferences.php';

$mysqli = new mysqli(
    HOST,
    USER,
    PASS,
    DATABASE_CONTENT
);

if ($mysqli->connect_error) {
    die('Database connection failed: ' . $mysqli->connect_error);
}

$mysqli->set_charset('utf8mb4');

$folderName = 'hope';

migrateJapaneseBibleLinksForFolder($mysqli, $folderName);

$mysqli->close();


function migrateJapaneseBibleLinksForFolder(
    mysqli $mysqli,
    string $folderName
): void {
    $languageIso = 'jpn';
    $filetype = 'html';

    $selectSql = "
        SELECT c.*
        FROM content c
        INNER JOIN (
            SELECT filename, MAX(recnum) AS latest_recnum
            FROM content
            WHERE language_iso = ?
              AND folder_name = ?
              AND filetype = ?
            GROUP BY filename
        ) latest
            ON c.filename = latest.filename
           AND c.recnum = latest.latest_recnum
        WHERE c.language_iso = ?
          AND c.folder_name = ?
          AND c.filetype = ?
        ORDER BY c.filename
    ";

    $stmt = $mysqli->prepare($selectSql);

    if (!$stmt) {
        throw new RuntimeException('Folder select prepare failed: ' . $mysqli->error);
    }

    $stmt->bind_param(
        'ssssss',
        $languageIso,
        $folderName,
        $filetype,
        $languageIso,
        $folderName,
        $filetype
    );

    $stmt->execute();

    $result = $stmt->get_result();

    $foundCount = 0;
    $changedCount = 0;
    $unchangedCount = 0;

    while ($row = $result->fetch_assoc()) {
        $foundCount++;

        $filename = $row['filename'] ?? '';

        echo "Processing {$folderName}/{$filename}, recnum {$row['recnum']}...\n";

        $wasChanged = insertUpdatedBibleLinkRowIfChanged($mysqli, $row);

        if ($wasChanged) {
            $changedCount++;
        } else {
            $unchangedCount++;
        }

        echo "\n";
    }

    $stmt->close();

    echo "Finished folder {$folderName}.\n";
    echo "Files found: {$foundCount}\n";
    echo "Files changed: {$changedCount}\n";
    echo "Files unchanged: {$unchangedCount}\n";
}


function insertUpdatedBibleLinkRowIfChanged(
    mysqli $mysqli,
    array $row
): bool {
    $oldText = $row['text'] ?? '';
    $newText = linkJapaneseReadAloudReferences($oldText);

    if ($newText === $oldText) {
        echo "No Bible reference links added.\n";
        return false;
    }

    $oldRecnum = $row['recnum'];

    unset($row['recnum']);

    $row['text'] = $newText;
    $row['edit_date'] = time();

    $columns = array_keys($row);

    $columnSql = implode(', ', array_map(
        fn($column) => "`$column`",
        $columns
    ));

    $placeholderSql = implode(', ', array_fill(0, count($columns), '?'));

    $insertSql = "
        INSERT INTO content ($columnSql)
        VALUES ($placeholderSql)
    ";

    $insertStmt = $mysqli->prepare($insertSql);

    if (!$insertStmt) {
        throw new RuntimeException('Insert prepare failed: ' . $mysqli->error);
    }

    $values = array_values($row);
    $types = str_repeat('s', count($values));

    $insertStmt->bind_param($types, ...$values);

    if (!$insertStmt->execute()) {
        throw new RuntimeException('Insert execute failed: ' . $insertStmt->error);
    }

    $newRecnum = $mysqli->insert_id;

    $insertStmt->close();

    echo "Inserted updated row. Old recnum: {$oldRecnum}. New recnum: {$newRecnum}.\n";

    return true;
}
