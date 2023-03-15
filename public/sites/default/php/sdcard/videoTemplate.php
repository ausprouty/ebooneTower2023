<?php

myRequireOnce('videoFindForSDCardNewName.php', 'sdcard');

function videoTemplateOffline($title_phrase, $p, $offline_video_count, $bookmark)
{

    $template = videoTemplateLink();
    $filename = $bookmark['page']->filename;
    $video = '/MC2/' . $p['language_iso'] . '/video/' .  $p['folder_name'] . '/';
    $video .= videoFindForSDCardNewName($filename);
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




function videoTemplateLink()
{

    $template_link = '<button id="[video]" type="button" class="external-movie">
         [title_phrase]</button>
    <div class="collapsed"></div>';

    return  $template_link;
}

function videoTemplateWatchPhrase($bookmark)
{
    $watch_phrase = $bookmark['language']->watch_offline;
    return $watch_phrase;
}
