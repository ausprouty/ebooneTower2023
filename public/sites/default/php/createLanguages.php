<?php

myRequireOnce('dirStandard.php');
myRequireOnce('myGetPrototypeFile.php');
myRequireOnce('writeLog.php');

//creates languages for country

function createLanguages($p, $data, $allowed = ['all'])
{
    $text = json_decode($data['text']);
    if (!isset($text->languages)) {
        $message = "in createLanguages and  no value for text->languages ";
        writeLogError('createLanguages-10-message', $message);
        trigger_error($message, E_USER_ERROR);
        return null;
    }
    // replace placeholders in template
    $main_template = $book_template = myGetPrototypeFile('languages.html');
    $placeholders = array(
        '{{ choose_language }}',
        '{{ more_languages }}',
    );
    $choose = isset($text->choose_language) ? $text->choose_language : 'Choose Language';
    $more = isset($text->more_languages) ? $text->more_languages : 'More Languages';
    $replace = array(
        $choose,
        $more
    );
    $main_template = str_replace($placeholders, $replace, $main_template);
    // get chapter template
    $sub_template = myGetPrototypeFile('language.html');
    $placeholders = array(
        '{{ link }}',
        '{{ language.name }}',
    );
    $temp = '';
    foreach ($text->languages as $language) {
        $rand = random_int(0, 9999);
        $status = false;
        if ($p['destination'] == 'website') {
            $status = $language->publish;
        } else {
            $status = $language->prototype;
        }
        if ($status  == true) {
            //sd cards may want to limit the languages offered
            if ($allowed[0] == 'all' || in_array($language->iso, $allowed)) {
                $link =  $language->folder . '/index.html';
                $pos = strpos($link, '/content/');
                if ($pos){
                    $link = substr($link, $pos);
                }
                $replace = array(
                    $link,
                    $language->name
                );
                $temp .= str_replace($placeholders, $replace, $sub_template);
            }
        }
    }
    $text = str_replace('[[languages]]', $temp,  $main_template);
    writeLogDebug('createLanguages-68', $text);
    return $text;
}
