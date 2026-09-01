<?php

declare(strict_types=1);

include __DIR__ . '/../../.env.api.remote.php';
include './linkJapaneseReadAloudReferences.php';

// Change folderName to the folder you want to process.
$folderName = 'multiply3';

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
        throw new RuntimeException(
            'Folder select prepare failed: ' . $mysqli->error
        );
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

    if (!$stmt->execute()) {
        throw new RuntimeException(
            'Folder select execute failed: ' . $stmt->error
        );
    }

    $result = $stmt->get_result();

    $foundCount = 0;
    $changedCount = 0;
    $alreadyProcessedCount = 0;
    $regexFailureCount = 0;
    $wrongClassCount = 0;
    $noInstructionCount = 0;

    $regexFailures = [];
    $wrongClassFiles = [];

    while ($row = $result->fetch_assoc()) {
        $foundCount++;

        $filename = (string) ($row['filename'] ?? '');
        $recnum = (string) ($row['recnum'] ?? '');

        echo "Processing {$folderName}/{$filename}, recnum {$recnum}...\n";

        $resultDetails = insertUpdatedBibleLinkRowIfChanged(
            $mysqli,
            $row
        );

        switch ($resultDetails['status']) {
            case 'changed':
                $changedCount++;
                break;

            case 'already-processed':
                $alreadyProcessedCount++;
                echo "Already processed; existing link retained.\n";
                break;

            case 'regex-failure':
                $regexFailureCount++;

                $regexFailures[] = [
                    'folder'   => $folderName,
                    'filename' => $filename,
                    'recnum'   => $recnum,
                    'snippets' => $resultDetails['snippets'],
                ];

                echo "WARNING: Read-aloud instruction found, "
                    . "but no link was added.\n";
                break;

            case 'wrong-class':
                $wrongClassCount++;

                $wrongClassFiles[] = [
                    'folder'   => $folderName,
                    'filename' => $filename,
                    'recnum'   => $recnum,
                    'snippets' => $resultDetails['snippets'],
                ];

                echo "WARNING: Read-aloud instruction found, "
                    . "but its li/p element does not have class "
                    . "\"up\" or \"flush\".\n";
                break;

            case 'no-instruction':
                $noInstructionCount++;
                echo "No matching read-aloud instruction found.\n";
                break;

            default:
                throw new RuntimeException(
                    'Unknown migration status: '
                        . ($resultDetails['status'] ?? '(missing)')
                );
        }

        echo "\n";
    }

    $stmt->close();

    echo "\n";
    echo "========================================\n";
    echo "Finished folder {$folderName}\n";
    echo "========================================\n";
    echo "Files found: {$foundCount}\n";
    echo "Files changed: {$changedCount}\n";
    echo "Already processed: {$alreadyProcessedCount}\n";
    echo "Possible regex failures: {$regexFailureCount}\n";
    echo "Read-aloud text with wrong class: {$wrongClassCount}\n";
    echo "No read-aloud instruction: {$noInstructionCount}\n";

    printProblemFiles(
        'POSSIBLE REGEX FAILURES',
        $regexFailures
    );

    printProblemFiles(
        'READ-ALOUD ELEMENTS WITHOUT UP OR FLUSH CLASS',
        $wrongClassFiles
    );
}


function insertUpdatedBibleLinkRowIfChanged(
    mysqli $mysqli,
    array $row
): array {
    $oldText = (string) ($row['text'] ?? '');

    $conversionFailures = [];
    $matchedCount = 0;

    $newText = linkJapaneseReadAloudReferences(
        $oldText,
        $conversionFailures,
        $matchedCount
    );

    echo "Read-aloud sections matched: {$matchedCount}\n";

    foreach ($conversionFailures as $failure) {
        echo "Failure: {$failure['reason']}\n";

        if (isset($failure['reference'])) {
            echo "Reference: {$failure['reference']}\n";
        }

        echo "HTML: {$failure['html']}\n";
    }

    if ($newText === $oldText) {
        return diagnoseUnchangedJapaneseReadAloudHtml($oldText);
    }

    $oldRecnum = $row['recnum'];

    unset($row['recnum']);

    $row['text'] = $newText;
    $row['edit_date'] = time();

    $columns = array_keys($row);

    $columnSql = implode(
        ', ',
        array_map(
            static fn(string $column): string => "`{$column}`",
            $columns
        )
    );

    $placeholderSql = implode(
        ', ',
        array_fill(0, count($columns), '?')
    );

    $insertSql = "
        INSERT INTO content ($columnSql)
        VALUES ($placeholderSql)
    ";

    $insertStmt = $mysqli->prepare($insertSql);

    if (!$insertStmt) {
        throw new RuntimeException(
            'Insert prepare failed: ' . $mysqli->error
        );
    }

    $values = array_values($row);
    $types = str_repeat('s', count($values));

    $insertStmt->bind_param($types, ...$values);

    if (!$insertStmt->execute()) {
        throw new RuntimeException(
            'Insert execute failed: ' . $insertStmt->error
        );
    }

    $newRecnum = $mysqli->insert_id;

    $insertStmt->close();

    echo "Inserted updated row. "
        . "Old recnum: {$oldRecnum}. "
        . "New recnum: {$newRecnum}.\n";

    return [
        'status'   => 'changed',
        'snippets' => [],
    ];
}


function diagnoseUnchangedJapaneseReadAloudHtml(
    string $html
): array {
    $snippets = extractReadAloudSnippets($html);

    /*
     * First check whether a read-aloud instruction already contains a link.
     */
    $alreadyProcessedPattern = <<<'REGEX'
~<(li|p)\b[^>]*>
    (?:
        (?!</?\1\b).*
    )?
    <a\b[^>]*href\s*=\s*(["'])[^"']*seisho\.or\.jp[^"']*\2[^>]*>
    .*?
    </a>
    .*?
    声を出して\s*[２2]\s*回読む
    .*?
</\1>
~sixu
REGEX;

    if (preg_match($alreadyProcessedPattern, $html) === 1) {
        return [
            'status'   => 'already-processed',
            'snippets' => $snippets,
        ];
    }

    /*
     * Look broadly for the wording, without requiring the correct class.
     */
    $hasReadAloudInstruction = preg_match(
        '~声を出して\s*[２2]\s*回読む~u',
        $html
    ) === 1;

    if (!$hasReadAloudInstruction) {
        return [
            'status'   => 'no-instruction',
            'snippets' => [],
        ];
    }

    /*
     * Determine whether the instruction is inside an li or p carrying
     * either the up or flush class.
     */
    $hasExpectedClass = preg_match(
        '~<(?:li|p)\b[^>]*'
            . '\bclass\s*=\s*(["\'])'
            . '[^"\']*\b(?:up|flush)\b[^"\']*'
            . '\1[^>]*>'
            . '.*?声を出して\s*[２2]\s*回読む'
            . '.*?</(?:li|p)>~su',
        $html
    ) === 1;

    if (!$hasExpectedClass) {
        return [
            'status'   => 'wrong-class',
            'snippets' => $snippets,
        ];
    }

    /*
     * It has the wording and the expected class, but the linking function
     * did not change it. This is the category most likely to expose a
     * mismatch in the main regular expression.
     */
    return [
        'status'   => 'regex-failure',
        'snippets' => $snippets,
    ];
}


function extractReadAloudSnippets(string $html): array
{
    $snippets = [];

    $matchCount = preg_match_all(
        '~<(li|p)\b[^>]*>.*?声を出して.*?</\1>~su',
        $html,
        $matches
    );

    if ($matchCount === false || $matchCount === 0) {
        return [];
    }

    foreach ($matches[0] as $snippet) {
        $snippet = preg_replace('~\s+~u', ' ', trim($snippet));

        if ($snippet !== null && $snippet !== '') {
            $snippets[] = $snippet;
        }
    }

    return array_values(array_unique($snippets));
}


function printProblemFiles(
    string $heading,
    array $files
): void {
    if ($files === []) {
        return;
    }

    echo "\n";
    echo "========================================\n";
    echo "{$heading}\n";
    echo "========================================\n";

    foreach ($files as $index => $file) {
        $number = $index + 1;

        echo "\n";
        echo "{$number}. {$file['folder']}/{$file['filename']}";
        echo " — recnum {$file['recnum']}\n";

        if ($file['snippets'] === []) {
            echo "   No complete li/p snippet could be extracted.\n";
            continue;
        }

        foreach ($file['snippets'] as $snippetIndex => $snippet) {
            $snippetNumber = $snippetIndex + 1;
            echo "   Snippet {$snippetNumber}:\n";
            echo "   {$snippet}\n";
        }
    }
}
