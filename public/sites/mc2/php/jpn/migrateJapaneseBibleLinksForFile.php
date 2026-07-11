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

migrateJapaneseBibleLinksForFile($mysqli, 'hope', 'hope01');

$mysqli->close();


function migrateJapaneseBibleLinksForFile(
    mysqli $mysqli,
    string $folderName,
    string $fileName
): void {
    $languageIso = 'jpn';
    $filetype = 'html';

    $selectSql = "
        SELECT *
        FROM content
        WHERE language_iso = ?
          AND folder_name = ?
          AND filetype = ?
          AND filename = ?
        ORDER BY recnum DESC
        LIMIT 1
    ";

    $stmt = $mysqli->prepare($selectSql);

    if (!$stmt) {
        throw new RuntimeException('Select prepare failed: ' . $mysqli->error);
    }

    $stmt->bind_param(
        'ssss',
        $languageIso,
        $folderName,
        $filetype,
        $fileName
    );

    if (!$stmt->execute()) {
        throw new RuntimeException('Select execute failed: ' . $stmt->error);
    }

    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    $stmt->close();

    if (!$row) {
        echo "No row found for {$folderName}/{$fileName}.\n";
        return;
    }

    echo "Processing {$folderName}/{$fileName}, recnum {$row['recnum']}...\n";

    $wasChanged = insertUpdatedBibleLinkRowIfChanged($mysqli, $row);

    if (!$wasChanged) {
        echo "No Bible reference links added for {$folderName}/{$fileName}.\n";
    }
}


function insertUpdatedBibleLinkRowIfChanged(
    mysqli $mysqli,
    array $row
): bool {
    $oldText = $row['text'] ?? '';
    $newText = linkJapaneseReadAloudReferences($oldText);

    if ($newText === $oldText) {
        return false;
    }

    $oldRecnum = $row['recnum'];

    // Remove primary key so MariaDB creates a new record.
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

    if ($insertStmt->affected_rows !== 1) {
        throw new RuntimeException('Insert failed or inserted unexpected number of rows.');
    }

    $newRecnum = $mysqli->insert_id;

    $insertStmt->close();

    echo "Inserted updated row. Old recnum: {$oldRecnum}. New recnum: {$newRecnum}.\n";

    return true;
}
