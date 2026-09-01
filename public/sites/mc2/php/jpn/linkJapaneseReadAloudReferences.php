<?php

declare(strict_types=1);

include './seishoLinkFromJapaneseReference.php';

function linkJapaneseReadAloudReferences(
    string $html,
    array &$failures = [],
    int &$matchedCount = 0
): string {
    if (trim($html) === '') {
        return $html;
    }

    $dom = new DOMDocument('1.0', 'UTF-8');

    $previousLibxmlSetting = libxml_use_internal_errors(true);

    $wrapperId = 'japanese-read-aloud-root';

    $loaded = $dom->loadHTML(
        '<?xml encoding="UTF-8">'
            . '<div id="' . $wrapperId . '">'
            . $html
            . '</div>',
        LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
    );

    libxml_clear_errors();
    libxml_use_internal_errors($previousLibxmlSetting);

    if (!$loaded) {
        $failures[] = [
            'type'   => 'html-parse-failed',
            'reason' => 'DOMDocument could not parse the HTML.',
        ];

        return $html;
    }

    $xpath = new DOMXPath($dom);

    $headingResult = $xpath->query(
        '//h2['
            . 'contains('
            . 'concat(" ", normalize-space(@class), " "),'
            . '" up "'
            . ')'
            . ' and normalize-space(.) = "読む"'
            . ']'
    );

    if ($headingResult === false) {
        throw new RuntimeException(
            'Could not search for 読む headings.'
        );
    }

    /*
     * Copy headings to a normal array because the DOM may be changed
     * during processing.
     */
    $headings = [];

    foreach ($headingResult as $heading) {
        if ($heading instanceof DOMElement) {
            $headings[] = $heading;
        }
    }

    foreach ($headings as $heading) {
        processJapaneseReadAloudSection(
            $dom,
            $heading,
            $failures,
            $matchedCount
        );
    }

    $wrapper = $dom->getElementById($wrapperId);

    if (!$wrapper instanceof DOMElement) {
        throw new RuntimeException(
            'Could not recover the HTML wrapper.'
        );
    }

    return getJapaneseReadAloudInnerHtml($wrapper);
}


function processJapaneseReadAloudSection(
    DOMDocument $dom,
    DOMElement $heading,
    array &$failures,
    int &$matchedCount
): void {
    $node = $heading->nextSibling;
    $sectionMatchCount = 0;
    $foundBibleBlock = false;

    while ($node !== null) {
        /*
         * Stop immediately when we reach:
         *
         * <p>[BibleBlock]</p>
         */
        if (
            $node instanceof DOMElement
            && isJapaneseBibleBlockMarker($node)
        ) {
            $foundBibleBlock = true;
            break;
        }

        /*
         * Do not continue into the next LOOK section if malformed HTML
         * has no BibleBlock marker.
         */
        if (
            $node instanceof DOMElement
            && hasJapaneseReadAloudClass($node, 'lesson')
        ) {
            break;
        }

        $candidates = findJapaneseReadAloudCandidates($node);

        foreach ($candidates as $candidate) {
            $matchedCount++;
            $sectionMatchCount++;

            processJapaneseReadAloudCandidate(
                $dom,
                $candidate,
                $failures
            );
        }

        $node = $node->nextSibling;
    }

    if ($sectionMatchCount === 0) {
        $failures[] = [
            'type'   => 'instruction-not-found',
            'reason' => 'Found a 読む heading, but no read-aloud instruction was found before [BibleBlock].',
            'html'   => getJapaneseReadAloudNodeHtml(
                $dom,
                $heading
            ),
        ];
    }

    if (!$foundBibleBlock) {
        $failures[] = [
            'type'   => 'bible-block-not-found',
            'reason' => 'Found a 読む heading, but no [BibleBlock] marker followed it.',
            'html'   => getJapaneseReadAloudNodeHtml(
                $dom,
                $heading
            ),
        ];
    }
}


/**
 * Finds all qualifying p and li elements within the supplied node.
 *
 * @return DOMElement[]
 */
function findJapaneseReadAloudCandidates(
    DOMNode $node
): array {
    if (!$node instanceof DOMElement) {
        return [];
    }

    $candidates = [];

    if (isJapaneseReadAloudCandidate($node)) {
        $candidates[] = $node;
    }

    foreach ($node->getElementsByTagName('*') as $descendant) {
        if (
            $descendant instanceof DOMElement
            && isJapaneseReadAloudCandidate($descendant)
        ) {
            $candidates[] = $descendant;
        }
    }

    return $candidates;
}


function isJapaneseReadAloudCandidate(
    DOMElement $element
): bool {
    $tagName = strtolower($element->tagName);

    if (!in_array($tagName, ['p', 'li'], true)) {
        return false;
    }

    if (
        !hasJapaneseReadAloudClass($element, 'up')
        && !hasJapaneseReadAloudClass($element, 'flush')
    ) {
        return false;
    }

    return preg_match(
        '~声を出して\s*[２2]\s*回読む~u',
        $element->textContent
    ) === 1;
}


function processJapaneseReadAloudCandidate(
    DOMDocument $dom,
    DOMElement $element,
    array &$failures
): void {
    /*
     * The passage has already been processed.
     */
    if ($element->getElementsByTagName('a')->length > 0) {
        return;
    }

    $text = trim(
        html_entity_decode(
            $element->textContent,
            ENT_QUOTES | ENT_HTML5,
            'UTF-8'
        )
    );

    $matched = preg_match(
        '~^\s*(.*?)\s*を\s*、\s*'
            . '声を出して\s*[２2]\s*回読む'
            . '[。.]?\s*$~u',
        $text,
        $matches
    );

    if ($matched !== 1) {
        $failures[] = [
            'type'   => 'instruction-format-not-recognized',
            'reason' => 'The read-aloud instruction was found, but its wording was not recognized.',
            'text'   => $text,
            'html'   => getJapaneseReadAloudNodeHtml(
                $dom,
                $element
            ),
        ];

        return;
    }

    $reference = trim($matches[1]);

    if ($reference === '') {
        $failures[] = [
            'type'   => 'empty-reference',
            'reason' => 'The read-aloud instruction contained no Bible reference.',
            'html'   => getJapaneseReadAloudNodeHtml(
                $dom,
                $element
            ),
        ];

        return;
    }

    $url = seishoLinkFromJapaneseReference($reference);

    if (!$url) {
        $failures[] = [
            'type'      => 'url-not-created',
            'reason'    => 'The passage was found, but no Seisho URL was returned.',
            'reference' => $reference,
            'html'      => getJapaneseReadAloudNodeHtml(
                $dom,
                $element
            ),
        ];

        return;
    }

    /*
     * Retain the original p/li element and its attributes,
     * but replace its contents.
     */
    while ($element->firstChild !== null) {
        $element->removeChild($element->firstChild);
    }

    $link = $dom->createElement('a');
    $link->setAttribute('href', $url);
    $link->setAttribute('target', '_blank');
    $link->appendChild(
        $dom->createTextNode($reference)
    );

    $element->appendChild($link);

    $element->appendChild(
        $dom->createTextNode(
            'を、声を出して２回読む。'
        )
    );
}


function isJapaneseBibleBlockMarker(
    DOMElement $element
): bool {
    if (strtolower($element->tagName) !== 'p') {
        return false;
    }

    return trim($element->textContent) === '[BibleBlock]';
}


function hasJapaneseReadAloudClass(
    DOMElement $element,
    string $className
): bool {
    $classValue = trim(
        $element->getAttribute('class')
    );

    if ($classValue === '') {
        return false;
    }

    $classes = preg_split(
        '~\s+~u',
        $classValue
    );

    if ($classes === false) {
        return false;
    }

    return in_array(
        $className,
        $classes,
        true
    );
}


function getJapaneseReadAloudInnerHtml(
    DOMElement $element
): string {
    $html = '';

    $ownerDocument = $element->ownerDocument;

    if (!$ownerDocument instanceof DOMDocument) {
        return '';
    }

    foreach ($element->childNodes as $child) {
        $childHtml = $ownerDocument->saveHTML($child);

        if ($childHtml !== false) {
            $html .= $childHtml;
        }
    }

    return $html;
}


function getJapaneseReadAloudNodeHtml(
    DOMDocument $dom,
    DOMNode $node
): string {
    $html = $dom->saveHTML($node);

    if ($html === false) {
        return '';
    }

    return $html;
}
