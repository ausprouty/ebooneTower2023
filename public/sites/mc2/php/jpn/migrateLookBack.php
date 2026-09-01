<?php

declare(strict_types=1);

include __DIR__ . '/../../.env.api.remote.php';

// Change this to the folder you want to process.
$folderName = 'hope';

$mysqli = new mysqli(
    HOST,
    USER,
    PASS,
    DATABASE_CONTENT
);

if ($mysqli->connect_error) {
    die('Database connection failed: '
        . $mysqli->connect_error);
}

$mysqli->set_charset('utf8mb4');

replaceJapaneseTextForFolder(
    $mysqli,
    $folderName,
    '振りろう',
    '振り返ろう'
);

$mysqli->close();


function replaceJapaneseTextForFolder(
    mysqli $mysqli,
    string $folderName,
    string $searchText,
    string $replacementText
): void {
    $languageIso = 'jpn';
    $filetype = 'html';

    /*
     * Select only the latest record for each filename.
     */
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

    $selectStmt = $mysqli->prepare($selectSql);

    if (!$selectStmt) {
        throw new RuntimeException(
            'Select prepare failed: ' . $mysqli->error
        );
    }

    $selectStmt->bind_param(
        'ssssss',
        $languageIso,
        $folderName,
        $filetype,
        $languageIso,
        $folderName,
        $filetype
    );

    if (!$selectStmt->execute()) {
        throw new RuntimeException(
            'Select execute failed: ' . $selectStmt->error
        );
    }

    $result = $selectStmt->get_result();

    $filesFound = 0;
    $filesChanged = 0;
    $totalReplacements = 0;

    while ($row = $result->fetch_assoc()) {
        $filesFound++;

        $filename = (string) ($row['filename'] ?? '');
        $oldRecnum = (string) ($row['recnum'] ?? '');
        $oldText = (string) ($row['text'] ?? '');

        $replacementCount = 0;

        $newText = str_replace(
            $searchText,
            $replacementText,
            $oldText,
            $replacementCount
        );

        echo "Processing {$folderName}/{$filename}";
        echo " — recnum {$oldRecnum}\n";

        if ($replacementCount === 0) {
            echo "No occurrences found. No row inserted.\n\n";
            continue;
        }

        /*
         * Remove the primary key so the database creates a new recnum.
         */
        unset($row['recnum']);

        $row['text'] = $newText;
        $row['edit_date'] = time();

        /*
         * These must be SQL NULL in the new record.
         */
        $row['prototype_date'] = null;
        $row['publish_date'] = null;
        $row['prototype_uid'] = null;
        $row['publish_uid'] = null;

        $newRecnum = insertContentRow(
            $mysqli,
            $row
        );

        $filesChanged++;
        $totalReplacements += $replacementCount;

        echo "Replacements: {$replacementCount}\n";
        echo "Inserted new row: {$newRecnum}\n\n";
    }

    $selectStmt->close();

    echo "========================================\n";
    echo "Finished folder: {$folderName}\n";
    echo "========================================\n";
    echo "Files found: {$filesFound}\n";
    echo "Files changed: {$filesChanged}\n";
    echo "Total replacements: {$totalReplacements}\n";
}


function insertContentRow(
    mysqli $mysqli,
    array $row
): int {
    $columns = array_keys($row);

    $columnSql = implode(
        ', ',
        array_map(
            static function (string $column): string {
                return "`{$column}`";
            },
            $columns
        )
    );

    $placeholderSql = implode(
        ', ',
        array_fill(0, count($columns), '?')
    );

    $insertSql = "
        INSERT INTO content ({$columnSql})
        VALUES ({$placeholderSql})
    ";

    $insertStmt = $mysqli->prepare($insertSql);

    if (!$insertStmt) {
        throw new RuntimeException(
            'Insert prepare failed: ' . $mysqli->error
        );
    }

    /*
     * Variables are required because bind_param binds by reference.
     * PHP null values are inserted as SQL NULL.
     */
    $values = array_values($row);
    $types = str_repeat('s', count($values));

    $bindValues = [];
    $bindValues[] = $types;

    foreach ($values as $index => $value) {
        $bindValues[] = &$values[$index];
    }

    if (!call_user_func_array(
        [$insertStmt, 'bind_param'],
        $bindValues
    )) {
        throw new RuntimeException(
            'Insert bind failed: ' . $insertStmt->error
        );
    }

    if (!$insertStmt->execute()) {
        throw new RuntimeException(
            'Insert execute failed: ' . $insertStmt->error
        );
    }

    $newRecnum = (int) $mysqli->insert_id;

    $insertStmt->close();

    return $newRecnum;
}
