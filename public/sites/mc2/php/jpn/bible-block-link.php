<?php

function linkJapaneseReadAloudReferences(string $html): string

{
    //echo "entered linkJapaneseReadAloudReferences\n";
    $updatedHtml = preg_replace_callback(
        '~<(li|p)\b([^>]*)\bclass\s*=\s*(["\'])([^"\']*\b(?:up|flush)\b[^"\']*)\3([^>]*)>\s*(.*?)\s*を\s*、\s*声を出して\s*[２2]\s*回読む。\s*</\1>~su',

        function (array $matches): string {
            $tagName     = $matches[1]; // li or p
            $beforeClass = $matches[2];
            $quote       = $matches[3];
            $classValue  = $matches[4];
            $afterClass  = $matches[5];
            $reference   = trim(strip_tags($matches[6]));

            //echo "\n--- MATCH FOUND ---\n";
            //echo "Full match: " . $matches[0] . "\n";
            //echo "Tag name: " . $tagName . "\n";
            //echo "Before class: " . $beforeClass . "\n";
            //echo "Quote: " . $quote . "\n";
            //echo "Class value: " . $classValue . "\n";
            //echo "After class: " . $afterClass . "\n";
            //echo "Reference: " . $reference . "\n";

            if ($reference === '') {
                //echo "Processing empty reference.\n";
                return $matches[0];
            }
            //echo "Processing reference: {$reference}\n";
            $url = seishoLinkFromJapaneseReference($reference);

            //echo "URL: " . ($url ?: 'NO URL RETURNED') . "\n";
            //echo "--- END MATCH ---\n\n";

            if (!$url) {
                return $matches[0];
            }

            $safeUrl = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
            $safeReference = htmlspecialchars($reference, ENT_QUOTES, 'UTF-8');

            return '<' . $tagName
                . $beforeClass
                . 'class=' . $quote . $classValue . $quote
                . $afterClass
                . '><a href="' . $safeUrl . '" target="_blank">'
                . $safeReference
                . '</a>を、声を出して２回読む。</' . $tagName . '>';
        },
        $html
    );

    if ($updatedHtml === null) {
        throw new RuntimeException('preg_replace_callback failed: ' . preg_last_error_msg());
    }

    return $updatedHtml;
}
