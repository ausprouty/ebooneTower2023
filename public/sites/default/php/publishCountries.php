<?php

myRequireOnce('publishDestination.php');
myRequireOnce('publishFiles.php');
myRequireOnce('publishReady.php');
myRequireOnce('myGetPrototypeFile.php');
mhyRequireOnce('syncController.php');

/* This should only show countries which have been authorized for prototyping or  publishing.

   The link should take you to one of two locations:

    if more than one language is authorized for publishing:   CountryCode/languages.html
    if only one language is authorized for publishing:  CountryCode/LanguageIso/index.html
*/
function publishCountries($p){
    syncController($p);
    // declare variables
    $selected_css = STANDARD_CARD_CSS;
    //
    //find country page from recnum
    //
    $sql = 'SELECT * FROM content
        WHERE  recnum = "' .  $p['recnum'] . '"';
    $debug .= $sql . "\n";
    $data = sqlArray($sql);
    if (!$data) {
        writeLogAppend('ERROR- publishCountries-29', $p);
        return $p;
    }
    //
    // create page
    //
    // get main template and do some replacing
    $main_template = myGetPrototypeFile('countries.html');
    $main_template = str_replace('{{ version }}', VERSION, $main_template);
    $main_template = str_replace('{{ site }}', SITE_CODE, $main_template);
    // get sub template and do some replacing
    $sub_template = myGetPrototypeFile('country.html');
    $countries = json_decode($data['text']);
    usort($countries, function($a, $b) {
        return $a->name <=> $b->name;
    });
    $country_template = '';
    foreach ($countries as $country) {
        if (publishReady($country, DESTINATION)) {
            $image = '/' . DIR_DEFAULT_SITE . 'images/country/' . $country->image;
            $link =  publishCountryLink($country->code, $p['destination']);
            $placeholders = ['{{ link }}', '{{ country.image }}', '{{ country.name }}', '{{ country.english }}'];
            $replace = [$link, $image, $country->name, $country->english];
            $country_template .= str_replace($placeholders, $replace, $sub_template);
        }
    }
    // add sub template content
    $main_template = str_replace('[[countries]]', $country_template, $main_template);
    // write countries file in content folder (so we can use root for javascript to return to last page)
    $fname = publishDestination($p)  . 'content/index.html';
    $main_template .= '<!--- Created by prototypeCountries-->' . "\n";
    publishFiles($p, $fname, $main_template,   STANDARD_CSS,  $selected_css);

    //
    // update records
    //
    $time = time();
    $sql = null;
    if (DESTINATION == 'website') {
        $sql = "UPDATE content
            SET publish_date = '$time', publish_uid = '" . $p['my_uid'] . "'
            WHERE  filename = 'countries'
            AND prototype_date IS NOT NULL
            AND publish_date IS NULL";
    }
    if (DESTINATION == 'staging') {
        $sql = "UPDATE content
            SET prototype_date = '$time', prototype_uid = '" . $p['my_uid'] . "'
            WHERE  filename = 'countries'
            AND prototype_date IS  NULL";
    }
    if ($sql) {
        sqlArray($sql, 'update');
    }

    return $p;
}

function publishCountryLink($country_code, $destination)
{
    $sql = "SELECT text FROM content
        WHERE  country_code = '" . $country_code . "'
        AND filename = 'languages'
        AND prototype_date IS NOT NULL
        ORDER BY recnum DESC LIMIT 1";
    $data = sqlArray($sql);
    $languages = json_decode($data['text']);
    $link = null;
    $count = 0;
    foreach ($languages->languages as $language) {
        if (publishReady($language, $destination)) {
            /*
                "/sites/myfriends/content/AU/eng/index.html" 
                   needs to be reduced to 
                "/content/AU/eng/index.html" 
            */
            $link = $language->folder . '/index.html';
            $pos = strpos($link, '/content');
            if ($pos){
                $link = substr($link, $pos);
                $count++;
            }
        }
    }
    if ($count != 1) {
        $link = $country_code . '/languages.html';
    }
    return $link;
}
