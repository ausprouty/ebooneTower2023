<?php
myRequireOnce('writeLog.php');
myRequireOnce('videoTemplateOffline.php');
myRequireOnce('videoFollows.php');
myRequireOnce('modifyRevealVideoRoutines.php');


/*
Input is:
    <div class="reveal film">&nbsp;
        <hr />
        <table class="video" border="1">
            <tbody  class="video">
                <tr class="video" >
                    <td class="video label" ><strong>Title:</strong></td>
                    <td class="video" >[Title]</td>
                </tr>
                <tr class="video" >
                    <td class="video label" ><strong>URL:</strong></td>
                    <td class="video" >[Link]</td>
                </tr>
                <tr class="video" >
                    <td class="video instruction"  colspan="2" style="text-align:center">
                    <h2><strong>Set times if you do not want to play the entire video</strong></h2>
                    </td>
                </tr>
                <tr class="video" >
                    <td class="video label" >Start Time: (min:sec)</td>
                    <td class="video" >start</td>
                </tr>
                <tr class="video" >
                    <td class="video label" >End Time: (min:sec)</td>
                    <td class="video" >end</td>
                </tr>
            </tbody>
        </table>

    <hr /></div>';

Data structure:
 video Types from ArcLight:  https://api.arclight.org/videoPlayerUrl?refId=1_529-jf6159-30-0
    1_ = jfilm
    2_ = acts
    6_= lumo

    Second set of numbers is language

For Output see appropriate VideoTemplate.php in 

*/
function modifyRevealVideo($text, $bookmark, $p)
{

    $debug = '';
    $previous_title_phrase = '';
    $watch_phrase = videoTemplateOfflineWatchPhrase($bookmark);
    $previous_url = '';
    $find = '<div class="reveal film">';
    $count = substr_count($text, $find);
    $offline_video_count = 0;
    for ($i = 0; $i < $count; $i++) {        // get old division
        $pos_start = strpos($text, $find);
        $pos_end = strpos($text, '</div>', $pos_start);
        $length = $pos_end - $pos_start + 6;  // add 6 because last item is 6 long
        $old = substr($text, $pos_start, $length);
        $new_title_phrase = null;
        // find title_phrase
        $title = modifyVideoRevealFindText($old, 2);
        $title = '&nbsp;' . $title . '&nbsp;';
        $title_phrase =  $word = str_replace('%', $title, $watch_phrase);
        //find url
        $url = modifyVideoRevealFindText($old, 4);

        // in these destinations we concantinate sequential videos (Acts#1 and Acts #2)
        $follows = videoFollows($previous_url, $url);
        $previous_url = $url;
        $old_title_phrase = $previous_title_phrase;
        if ($follows) {
            $new = '';
            $new_title_phrase = videoFollowsChangeVideoTitle($previous_title_phrase, $text, $bookmark);
        } else {
            $new = videoTemplateOffline($title_phrase, $p, $offline_video_count, $bookmark);
            $offline_video_count++;
        }
        $previous_title_phrase = $title_phrase;
        $start_time = 0;
        $duration = 0;

        // replace old  from https://stackoverflow.com/questions/1252693/using-str-replace-so-that-it-only-acts-on-the-first-match
        $length = $pos_end - $pos_start + 6;  // add 6 because last item is 6 long
        $text = substr_replace($text, $new, $pos_start, $length);
        if ($new_title_phrase) {
            $text = str_replace($old_title_phrase, $new_title_phrase, $text);
        }
    }
    return $text;
}
