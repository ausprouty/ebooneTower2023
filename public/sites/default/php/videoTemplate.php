<?php


function videoTemplateOnline($old, $title_phrase, $url, $bookmark){
    $new = videoTemplateLink($bookmark, $url);
    // find start and end times
    $start_time = modifyVideoRevealFindTime ($old, 7);
    $end_time = modifyVideoRevealFindTime ($old, 9);
    $duration = ($end_time -$start_time) * 1000;
    // find type of video and trim url
    if (strpos($url, 'api.arclight.org/videoPlayerUrl?') != FALSE){
        $new .=  $template_options; // JESUS project videos are available in many languages
        //https://api.arclight.org/videoPlayerUrl?refId=6_529-GOMatt2512
        $url = str_ireplace('https://api.arclight.org/videoPlayerUrl?refId=', '', $url); //6_529-GOMatt2512
        $video_type_string = substr($url, 0, 1); //6
        switch ($video_type_string){
            case 1:
                $video_type = 'jfilm';
                break;
            case 2:
                $video_type = 'acts';
                break;
            case 6:
                $video_type = 'lumo';
                break;
            default:
            $video_type = $video_type_string;
        }
        if (strpos($url, '-') !== FALSE){
            $dash = strpos($url, '-');
            $url = substr($url, $dash);
        }
        if ($start_time || $end_time){
            $url .= '&start='. $start_time . '&end=' .$end_time;
        }
    }
    elseif (strpos($url, 'https://vimeo.com/') !== FALSE){  //https://vimeo.com/162977296
        $video_type = 'vimeo';
        $url = str_ireplace('https://vimeo.com/', '', $url); //162977296
    }
    elseif (strpos($url, 'https://www.youtube.com/watch?v=') !== FALSE){  //https://www.youtube.com/watch?v=I7ks0udfjOw
        $video_type = 'youtube';
        $url = str_ireplace('https://www.youtube.com/watch?v=', '', $url); //I7ks0udfjOw
        if ($start_time || $end_time){
            $url .= '?start='. $start_time . '&end=' .$end_time;
        }
    }
    elseif (strpos($url, 'https://youtu.be/') !== FALSE){  //https://youtu.be/I7ks0udfjOw?t=732
        $video_type = 'youtube';
        $url = str_ireplace('https://youtu.be/', '', $url); //I7ks0udfjOwt=732
        if ($start_time || $end_time){
            $url .= '?start='. $start_time . '&end=' .$end_time;
        }
    }
    elseif (strpos($url, 'https://4.dbt.io') !== FALSE){  //https://youtu.be/I7ks0udfjOw?t=732
        $video_type = 'dbt';
        $url = str_ireplace('https://4.dbt.io/api/bible/filesets/', '', $url); //I7ks0udfjOwt=732
         //writeLogDebug ('modifyRevealVideo-196', $url);
    }
    else{
        $video_type = 'url';
    }
    // make replacements
    $video = '['. $video_type . ']' . $url; //[lumo]-GOMatt2512

    $new = str_replace('[video]', $video, $new);
    $new = str_replace('[video_type]', $video_type, $new);
    $new = str_replace('[id]', $i, $new);
    $new = str_replace('[title_phrase]', $title_phrase, $new);
    $new = str_replace('[play_list]', $url, $new);
    $new = str_replace('[start_time]', $start_time, $new);
    $new = str_replace('[duration]', $duration, $new);
    return $new;
}


function videoTemplateLink($bookmark, $url){
    if (strpos($url, 'https://4.dbt.io') !== FALSE){
        $template_link = '<button id="revealButton[id]"type="button" class="external-dbt-movie"
        onclick="dbtVideoPlay(\'[id]\', \'[play_list]\', \'[start_time]\', \'[duration]\')">[title_phrase]</button>
            <div class="collapsed">
                <video preload="none" id="dbtVideo[id]"  controls crossorigin></video>
            </div>'
       
    }
    else{
        $template_link = '<button id="revealButton[id]" type="button" class="external-movie">[title_phrase]</button>
        <div class="collapsed">[video]</div>
        <div id="ShowOptionsFor[video]"></div>'; // [ChangeLanguage] is changed in local.js;';
    }
    return  $template_link;

}

function videoTemplateWatchPhrase($bookmark){
   $watch_phrase = $bookmark['language']->watch;
    return $watch_phrase;
}
