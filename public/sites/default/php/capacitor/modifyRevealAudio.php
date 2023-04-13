<?php
myRequireOnce('writeLog.php');
myRequireOnce('audioReference.php');
/*
Input is:
    <--Start Audio Template-->
    <div class="reveal audio">&nbsp;
        <hr />
        <table class="" border="1">
            <tbody  class="audio">
                <tr class="audio" >
                    <td class="audio label" ><strong>Title:</strong></td>
                    <td class="audio" >[Title]</td>
                </tr>
                <tr class="audio" >
                    <td class="audio label" ><strong>URL:</strong></td>
                    <td class="audio" >[Link]</td>
                </tr>
                <tr class="audio" >
                    <td class="audio label" ><strong>Optional Text</strong></td>
                    <td class="audio" >[Text]</td>
                </tr>
            </tbody>
        </table>
    <hr /></div>
    <--Start Audio Template-->';




 Output

<button id="MC2/eng/audio/tc/tc01.mp3" type="button" class="external-audio">
           Listen to &nbsp;"TC #1: How to be Sure You Are a Christian"&nbsp; 
</button>
<div class="collapsed"></div>

*/
function modifyRevealAudio($text, $bookmark, $p)
{
    $listen_phrase = $bookmark['language']->listen_offline;
    $local_template = '
    <button id="[url]" type="button" class="external-audio">
        [title_phrase]</button>
        <div class="collapsed"></div>
    ';
    // [ChangeLanguage] is changed in local.js
    $find = '<div class="reveal audio">';
    $count = substr_count($text, $find);
    for ($i = 0; $i < $count; $i++) {
        // get old division
        $pos_start = strpos($text, $find);
        $pos_end = strpos($text, '</div>', $pos_start);
        $length = $pos_end - $pos_start + 6;  // add 6 because last item is 6 long
        $old = substr($text, $pos_start, $length);
        // find title_phrase
        $title = '&nbsp;"' . modifyRevealAudioFindText($old, 2) . '"&nbsp;';
        $title_phrase =  $word = str_replace('%', $title, $listen_phrase);
        //find url
        $url = modifyRevealAudioFindText($old, 4);
        $audio_text = modifyRevealAudioFindText($old, 6);
        $new = $local_template;
        $url = modifyRevealAudioSDCardUrl($url);
        // make replacements
        $new = str_replace('[id]', $i, $new);
        $new = str_replace('[title_phrase]', $title_phrase, $new);
        $new = str_replace('[url]', $url, $new);
        $new = str_replace('[audio_text]', $audio_text, $new);
        // replace old
        // from https://stackoverflow.com/questions/1252693/using-str-replace-so-that-it-only-acts-on-the-first-match
        $length = $pos_end - $pos_start + 6;  // add 6 because last item is 6 long
        $text = substr_replace($text, $new, $pos_start, $length);
    }
    //writeLog('modifyaudioReveal-147-text', $text);
    return $text;
}
// return the text from the td_segment
function modifyRevealAudioFindText($old, $td_number)
{
    $pos_td = 0;
    for ($i = 1; $i <= $td_number; $i++) {
        $pos_td = strpos($old, '<td', $pos_td + 1);
    }
    $pos_start = strpos($old, '>', $pos_td) + 1;
    $pos_end = strpos($old, '</', $pos_td);
    $length = $pos_end - $pos_start;
    $text = substr($old, $pos_start, $length);
    $text = strip_tags($text);
    return $text;
}
function modifyRevealAudioSDCardUrl($url)
{

    $url = trim($url);
    $link = audioReference();
    if (isset($link[$url])) {

        return $link[$url];
    } else {
        if (strpos($url, 'https%3A')) {
            $url = str_ireplace('https%3A', 'https:', $url);
            if (isset($link[$url])) {
                return $link[$url];
            }
        }
        return 'unknown';
    }
}
