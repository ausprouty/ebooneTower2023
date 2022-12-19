<?php
myRequireOnce('writeLog.php');
myRequireOnce('myGetPrototypeFile.php');

// requires $p['recnum'] and $p['library_code']
function createLanguageFooter($p)
{

    myRequireOnce('languageSpecificJavascripts.php', $p['destination']);

    $debug = 'In createLanguageFooter' . "\n";
    if (isset($p['recnum'])) {
        $b['recnum'] = $p['recnum'];
        $b['library_code'] = isset($p['library_code']) ? $p['library_code'] : 'library';
    } else {
        $b = $p;
    }
    $bookmark  = bookmark($b);
    $country_url = isset($bookmark['country']->url) ?  $bookmark['country']->url : null;
    $page_url = '/content/' . $p['country_code'] . '/' . $p['language_iso'] . '/';
    if (isset($bookmark['page']->filename)) {
        $page_url  .=  $p['folder_name'] . '/' . $bookmark['page']->filename . '.html';
    } else {
        if (isset($p['folder_name'])) {
            $page_url  .=  $p['folder_name'] . '/index.html';
        } else {
            if ($b['library_code'] == 'library') {
                $page_url  .= 'index.html';
            } else {
                $page_url  .=  $b['library_code'] . '.html';
            }
        }
    }
    $website = isset($bookmark['country']->website) ? $bookmark['country']->website : null;
    if (!isset($debug)) {
        $debug = '';
    }
    if (!isset($p['language_iso'])) {
        $p['language_iso'] = '';
    }
    $footer  = null;
    $debug .= 'Looking for Language Footer' . "\n";
    $sql = "SELECT * FROM content
        WHERE  country_code = '" . $p['country_code'] . "'
        AND  language_iso = '" . $p['language_iso'] . "'
        AND folder_name = ''  AND filename = 'index'
        ORDER BY recnum DESC LIMIT 1";
    $debug .= $sql . "\n";
    $data = sqlArray($sql);
    if ($data) {
        $text = json_decode($data['text']);
        $footer  = isset($text->footer) ? $text->footer : null;
    }
    if (!$footer) {
        myRequireOnce('getLanguageFooter.php', $p['destination']);
        $footer = getLanguageFooter($p);
    }
    $page_title = 'Link: ';
    if (isset($p['recnum'])) {
        $page_title = getTitle($p['recnum']) . ': ';
        $page_title = str_replace("'", "\'", $page_title); // this will go right before link when shared.
    }
    if (isset($bookmark['page']->filename)) {
        //$page = $p['country_code'] . '/'. $p['language_iso']. '/'. $p['folder_name'] .'/'.  $bookmark['page']->filename .'.html';
        $note_id = $p['country_code'] . '-' . $p['language_iso'] . '-' . $p['folder_name'] . '-' .  $bookmark['page']->filename  . '.html';
    } else {
        // $page = '';
        $note_id = 'must be an error somewhere';
    }
    $language_iso = isset($p['language_iso']) ? $p['language_iso'] : DEFAULT_LANGUAGE_ISO;
    $placeholders = array(
        '{{ url }}',
        '{{ page_url }}',
        '{{ website }}',
        '{{ page_title }}',
        '{{ note_id }}',
        '{{ country_code }}',
        '{{ language_iso }}'
    );
    $values = array(
        $country_url,
        $page_url,
        $website,
        $page_title,
        $note_id,
        $p['country_code'],
        $language_iso
    );
    $footer  = str_replace($placeholders, $values, $footer);
    $footer .= languageSpecificJavascripts($p);
    writeLogDebug('createLanguageFooter-75', $footer);
    return $footer;
}
