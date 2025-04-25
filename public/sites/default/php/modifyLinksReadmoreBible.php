<?php
/*change 
<a class="bible-readmore" href="https://biblegateway.com/passage/?search=Matthew%202:19-23&amp;version=NIV">

//to <a class="bible-readmore" href="https://biblegateway.com/passage/?search=Matthew%202:19-&amp;version=NIV">
// if no dash in the href, do nothing
*/
function modifyLinksReadmoreBible($text)
{
    writeLogDebug('modifyLinksReadmoreBible-10', $text);

    // Match <a> tags with class="readmore" or class="bible-readmore"
    $pattern = '/<a\s+class="(?:readmore|bible-readmore)"[^>]*href="([^"]*?)"[^>]*>(.*?)<\/a>/i';

    $text = preg_replace_callback($pattern, function ($matches) {
        $full_link = $matches[0]; // the whole <a>...</a>
        $href = $matches[1];      // the href value
        //$link_text = $matches[2]; // the visible link text

        // Only modify if the href contains a dash "-"
        if (strpos($href, '-') !== false) {
            $href = preg_replace('/-(\d+)(?=&amp;)/', '-', $href);
        }

        // Rebuild the <a> tag
        $new_link = str_replace($matches[1], $href, $full_link);

        // Log the change
        writeLogAppend('modifyLinksReadmoreBible-29', $full_link . ' -- ' . $new_link);

        return $new_link;
    }, $text);

    writeLogDebug('modifyLinksReadmoreBible-34', $text);
    return $text;
}
