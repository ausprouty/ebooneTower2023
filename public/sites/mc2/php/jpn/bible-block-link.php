<?php

function linkJapaneseReadAloudReferences(string $html): string
{
    return preg_replace_callback(
        '/<li\b([^>]*)\bclass\s*=\s*(["\'])([^"\']*\bup\b[^"\']*)\2([^>]*)>\s*(.*?)\s*、\s*声を出して\s*[２2]\s*回読む。\s*<\/li>/su',
        function (array $matches): string {
            $beforeClass = $matches[1];
            $quote       = $matches[2];
            $classValue  = $matches[3];
            $afterClass  = $matches[4];
            $reference   = trim(strip_tags($matches[5]));

            if ($reference === '') {
                return $matches[0];
            }

            $url = seishoLinkFromJapaneseReference($reference);

            if (!$url) {
                return $matches[0];
            }

            $safeUrl = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
            $safeReference = htmlspecialchars($reference, ENT_QUOTES, 'UTF-8');

            $link =  '<li'
                . $beforeClass
                . 'class=' . $quote . $classValue . $quote
                . $afterClass
                . '><a href="' . $safeUrl . '" target="_blank">'
                . $safeReference
                . '</a>を、声を出して２回読む。</li>';
            echo $link . "\n";
            return $link;
        },
        $html
    );
}
