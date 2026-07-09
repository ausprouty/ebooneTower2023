<?php

declare(strict_types=1);
include __DIR__ . '/../../.env.api.remote.php';
include './bible-blocks.php';
include './bible-block-link.php';


$mysqli = new mysqli(
    'HOST',
    'USER',
    'PASS',
    'DATABASE_CONTENT'
);

if ($mysqli->connect_error) {
    die('Database connection failed: ' . $mysqli->connect_error);
}

$mysqli->set_charset('utf8mb4');

migrateJapaneseBibleLinksForFile($mysqli, 'hope', 'hope01');

function migrateJapaneseBibleLinksForFile(
    mysqli $mysqli,
    string $folderName,
    string $fileName
): void {
    $selectSql = "
        SELECT *
        FROM content
        WHERE language_iso = ?
          AND folder_name = ?
          AND filetype = ?
          AND file_name = ?
        ORDER BY created_at DESC, id DESC
        LIMIT 1
    ";

    $stmt = $mysqli->prepare($selectSql);

    if (!$stmt) {
        throw new RuntimeException('Prepare failed: ' . $mysqli->error);
    }

    $languageIso = 'jpn';
    $filetype = 'html';

    $stmt->bind_param(
        'ssss',
        $languageIso,
        $folderName,
        $filetype,
        $fileName
    );

    $stmt->execute();

    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    $stmt->close();

    if (!$row) {
        echo "No row found for {$folderName}/{$fileName}.\n";
        return;
    }

    $oldText = $row['text'];
    $newText = linkJapaneseReadAloudReferences($oldText);

    if ($newText === $oldText) {
        echo "No Bible reference links added for {$folderName}/{$fileName}.\n";
        return;
    }

    unset($row['id']);

    $row['text'] = $newText;
    echo $newText . "\n";
    $row['created_at'] = date('Y-m-d H:i:s');

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
    // $insertStmt->execute();

    if ($insertStmt->affected_rows !== 1) {
        throw new RuntimeException('Insert failed or inserted unexpected number of rows.');
    }

    $insertStmt->close();

    echo "Inserted updated row for {$folderName}/{$fileName}.\n";
}
