<?php
myRequireOnce('dirStandard.php');
myRequireOnce('publishFiles.php');
myRequireOnce('myGetPrototypeFile.php');


function publishLanguagesAvailable($p)
{

    $available = [];
    $selected_css = '/sites/default/styles/cardGLOBAL.css';
    $footer  = '';
    // flags
    $sql = "SELECT * FROM content
                WHERE filename = 'countries'
                AND publish_date != ''
                ORDER BY recnum DESC LIMIT 1";
    $data = sqlArray($sql);
    $countries_array = json_decode($data['text']);
    // $footer = createLanguageFooter($p);
    // get main template
    $main_template = $book_template = myGetPrototypeFile('languagesAvailable.html');
    //
    //find prototype countries data
    //
    $sql = "SELECT distinct country_code FROM content
        WHERE  publish_date != ''
        AND country_code != '' ";
    $query = sqlMany($sql);
    while ($country = $query->fetch_array()) {
        // get published languages from each published country
        $sql = "SELECT * FROM content
            WHERE  country_code = '" . $country['country_code'] . "'
            AND filename = 'languages'  AND publish_date != ''
            ORDER BY recnum DESC LIMIT 1";
        $data = sqlArray($sql);
        //writeLogAppend('publishLanguagesAvailable-37', $data['recnum']);
        $text = json_decode($data['text']);
        //writeLogAppend('publishLanguagesAvailable-38', $text);
        if (!isset($text->languages)) {
            $message = '$text->languages not published for ' . $country['country_code'];
            //writeLogError('publishLanguagesAvailable', $message);
            return ($p);
        }
        // look for flag
        $flag = 'unknown';
        if (is_array($countries_array)) {
            foreach ($countries_array as $country_object) {
                if ($country_object->code == $country['country_code']) {
                    $flag = '/sites/default/images/country/' . $country_object->image;
                }
            }
            $debug .= "$flag is flag for " .  $country['country_code'] . " \n";
        }
        foreach ($text->languages as $language) {
            if (isset($language->publish)) {
                if ($language->publish) {
                    $pos = strpos($language->folder, '/content');
                    if ($pos){
                        $folder = substr($language->folder, $pos);
                    }
                    else{
                        $folder = $language->folder;
                    }
                    $available[] = array(
                        'language_iso' => $language->iso,
                        'language_name' => $language->name,
                        'country_name' => $country['country_code'],
                        'folder' => $folder,
                        'flag' => $flag
                    );
                }
            }
        }
       
    }
    //writeLogDebug('publishLanguagesAvailable-75', $available);
    usort($available, function($a, $b) {
        return $a['language_name'] <=> $b['language_name'];
    });
    //writeLogDebug('publishLanguagesAvailable-77', $available);
    // get language template
    $sub_template = myGetPrototypeFile('languageAvailable.html');
    $placeholders = array(
        '{{ link }}',
        '{{ country.image }}',
        '{{ language.name }}',
    );
    $temp = '';
    foreach ($available  as $show) {
        $replace = array(
            $show['folder'],
            $show['flag'],
            $show['language_name']
        );
        $temp .= str_replace($placeholders, $replace, $sub_template);
    }

    $body = str_replace('[[languages]]', $temp,  $main_template);
    // write file
    //
    $fname =  '/content/languages.html';
    //writeLogDebug('publishLanguagesAvailable-88', "Copied Languages available to $fname \n");
    $body .= '<!--- Created by publishLanguagesAvailable-->' . "\n";
    publishFiles($p, $fname, $body, STANDARD_CSS, $selected_css);
    return $p;
}

function _flag($country_code)
{
    $flag = '';
    $sql = "SELECT * FROM content
            AND filename = 'countries'  AND publish_date != ''
            ORDER BY recnum DESC LIMIT 1";
    $countries = sqlArray($sql);
    foreach ($countries as $country) {
        if ($country['code'] == $country_code) {
            $flag = '../images/country/' . $country['image'];
        }
    }
    return $flag;
}

