<?php

myRequireOnce('dirMake.php');
myRequireOnce('dirStandard.php');
myRequireOnce('writeLog.php');
myRequireOnce('modifyRevealAudio.php');



function audioMakeRefFileForOffline($p)
{
    $output = '';
    $series_audios = [];
    $chapter_audios = [];
    //find series data that has been prototyped
    $sql = "SELECT * FROM content
        WHERE  country_code = '" . $p['country_code'] . "'
        AND  language_iso = '" . $p['language_iso'] . "'
        AND folder_name = '" . $p['folder_name'] . "'  AND filename = 'index'
        AND prototype_date IS NOT NULL
        ORDER BY recnum DESC LIMIT 1";
    $data = sqlArray($sql);
    // find chapters that have been prototyped
    $text = json_decode($data['text']);
    if (isset($text->chapters)) {
        foreach ($text->chapters as $chapter) {
            if (isset($chapter->prototype)) {
                if ($chapter->prototype) {
                    $chapter_audios = audioFindForOffline($p, $chapter->filename);
                    if (count($chapter_audios) > 0) {
                        foreach ($chapter_audios as $chapter_audio) {
                            array_push($series_audios, $chapter_audio);
                        }
                    }
                }
            } else {
                writeLogError('audioMakeRefFileForOffline-36', $chapter);
            }
        }
    }

    // create file even if no data; otherwise verify will not work
    $template = 'Rename and Resample [old_name] to [new_name]';
    foreach ($series_audios as $audio) {
        if ($audio['url']) {
            $new_name = strtoupper(SITE_CODE) . '/' . $p['language_iso']  . '/audio/' . $p['folder_name'] . '/' . $audio['new_name'] . '.mp3';
            $placeholders = array(
                '[old_name]',
                '[new_name]'
            );
            $replace = array(
                $audio['url'],
                $new_name
            );
            $output .= str_replace($placeholders, $replace,  $template) . "\n";
        }
    }
    audioMakeBatFileForOfflineWrite($output, $p);

    return $output;
}

function audioMakeBatFileForOfflineWrite($text, $p)
{
    //define("ROOT_EDIT", '/home/vx5ui10wb4ln/public_html/myfriends.edit/');
    $dir =  dirStandard('media_batfile', DESTINATION,  $p, $folders = null, $create = true);
    $filename =  $dir . $p['folder_name'] .  'audio.txt';
    //writeLogDebug('audioMakeBatFileForOfflineWrite-67', $filename);
    $fh = fopen($filename, 'w');
    fwrite($fh, $text);
    fclose($fh);
    return;
}
/*Input is:
    <div class="reveal film">&nbsp;
        <hr />
        <table class="audio" border="1">
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
                    <td class="audio instruction"  colspan="2" style="text-align:center">
                    <h2><strong>Set times if you do not want to play the entire audio</strong></h2>
                    </td>
                </tr>
                <tr class="audio" >
                    <td class="audio label" >Start Time: (min:sec)</td>
                    <td class="audio" >start</td>
                </tr>
                <tr class="audio" >
                    <td class="audio label" >End Time: (min:sec)</td>
                    <td class="audio" >end</td>
                </tr>
            </tbody>
        </table>

    <hr /></div>';
*/
function audioFindForOffline($p, $filename)
{
    //writeLog('audioFindForOffline-113-p', $p);
    //writeLog('audioFindForOffline-114-filename', $filename);
    // find chapter that has been prototyped
    $chapter_audios = [];
    $audio = [];
    $audio['filename'] = $filename;
    $new_name = audioFindForOfflineNewName($filename);
    $audio['new_name'] = $new_name;
    $sql = "SELECT * FROM content
        WHERE  country_code = '" . $p['country_code'] . "'
        AND  language_iso = '" . $p['language_iso'] . "'
        AND folder_name = '" . $p['folder_name'] . "'
        AND filename = '" . $filename . "'
        AND prototype_date IS NOT NULL
        ORDER BY recnum DESC LIMIT 1";
    $data = sqlArray($sql);
    $text = $data['text'];
    //writeLog('audioFindForOffline-76-'. $filename, $text);
    $find = '<div class="reveal audio">';
    $count = substr_count($text, $find);
    $offset = 0;
    for ($i = 0; $i < $count; $i++) {
        // get old division
        $pos_start = strpos($text, $find, $offset);
        $needle = '</div>';
        $pos_end = strpos($text, $needle, $pos_start);
        $offset = $pos_end + 1;
        $length = $pos_end - $pos_start + 6;  // add 6 because last item is 6 long
        $old = substr($text, $pos_start, $length);
        // find title_phrase
        $audio['title'] = modifyRevealAudioFindText($old, 2);
        //find url
        $url = modifyRevealAudioFindText($old, 4);
        //writeLog('audioFindForOffline-95-'. $filename . $count, $url . "\n" . $find);
        $audio['url'] = $url;
        //if more than one audio in this chapter
        if ($i > 0) {
            $audio['new_name'] = $new_name . '-' . $i;
        }
        $chapter_audios[] = $audio;
    }
    //writeLog('audioFindForOffline-185-chapteraudios', $chapter_audios);
    return $chapter_audios;
}
function  audioFindForOfflineNewName($filename)
{
    return $filename;
}
