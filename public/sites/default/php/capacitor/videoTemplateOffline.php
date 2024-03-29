<?php

myRequireOnce('videoFindForCapacitorNewName.php');

function videoTemplateOffline($title_phrase, $p, $offline_video_count, $bookmark)
{

    $template = videoTemplateOfflineLink();
    $filename = $bookmark['page']->filename;
    $video = '/' . strtoupper(SITE_CODE) . '/' . $p['language_iso'] . '/video/' .  $p['folder_name'] . '/';
    $video .= videoFindForCapacitorNewName($filename);
    if ($offline_video_count > 0) {
        $video .= '-' . $offline_video_count;
    }
    $video .= '.mp4';
    $old = array(
        '[video]',
        '[title_phrase]'
    );
    $new = array(
        $video,
        $title_phrase
    );
    $output = str_replace($old, $new, $template);
    return $output;
}




function videoTemplateOfflineLink()
{

    $template_link = '<button id="[video]" type="button" class="external-movie">
         [title_phrase]</button>
    <div class="collapsed"></div>';

    return  $template_link;
}

function videoTemplateOfflineWatchPhrase($bookmark)
{
    $watch_phrase = $bookmark['language']->watch_offline;
    return $watch_phrase;
}
