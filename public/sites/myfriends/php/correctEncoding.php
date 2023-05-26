<?php


function recodeGerman($text)
{
    $good = array('Ä', 'Ö', 'Ü', 'ä', 'ö', 'ü', 'ß', '▶', '«', '»', '');
    $bad = array('Ã„', 'Ã–', 'Ãœ', 'Ã¤', 'Ã¶', 'Ã¼', 'ÃŸ', 'â–¶', 'Â«', 'Â»', 'Â¬');
    $text = str_replace($bad, $good, $text);
    $good2 = array('„', '’', '“');
    $bad2 = array('â€ž', 'â€˜', 'â€œ');
    $text = str_replace($bad2, $good2, $text);
    return $text;
}
