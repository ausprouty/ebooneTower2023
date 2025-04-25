<?php
/*
Expects
    language_iso
    entries separated by ;)
    version_ot
    version_nt
*/
myRequireOnce('bibleDbtArray.php');
myRequireOnce('bibleGetPassage.php');
myRequireOnce('myGetPrototypeFile.php');

function getBibleBlockToInsert($p)
{
    $template = myGetPrototypeFile('bibleBlock.html');
    $output = array();
    $block = '';
    $passages = createBibleDbtArrayFromPassage($p);
    foreach ($passages as $passage) {
        writeLogDebug('getBibleBlockToInsert-20', $passage);
        $passage['version_ot'] = $p['version_ot'];
        $passage['version_nt'] = $p['version_nt'];
        if ($passage['collection_code'] == 'NT') {
            $passage['bid'] = $p['version_nt'];
        } else {
            $passage['bid'] = $p['version_ot'];
        }
        writeLogDebug('getBibleBlockToInsert-28', $passage);
        $response = bibleGetPassage($passage);
        writeLogDebug('getBibleBlockToInsert-30', $response);
        writeLogDebug('getBibleBlockToInsert-31', $response['text']);
        writeLogDebug('getBibleBlockToInsert-32', $response['reference']);
        $replace = array(
            '[Reference]',
            '[Text]',
            '[Link]',
            '[ReadMore]'
        );
        $good = array(
            $response['reference'],
            $response['text'],
            $response['link'],
            $p['read_more']
        );
        $block .= str_replace($replace, $good, $template);
        $output['bible_block'] = $block;
    }
    $output['reference'] = $p['entry'];
    return $output;
}
