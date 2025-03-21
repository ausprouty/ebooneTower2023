<?php
myRequireOnce('writeLog.php');
myRequireOnce('videoTemplate.php');
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
    1_21028-wl604444-0-0  is Magadela   https://api.arclight.org/videoPlayerUrl?refId=1_21028-wl604444-0-0
    2_ = acts
    6_= lumo

    Second set of numbers is language

For Output see appropriate VideoTemplate.php in 

*/
function modifyRevealVideo($text, $bookmark, $p)
{
    $debug = '';
    $previous_title_phrase = '';
    $watch_phrase = videoTemplateWatchPhrase($bookmark);
    $previous_url = '';
    $find = '<div class="reveal film">';
    $count = substr_count($text, $find);
    for ($i = 0; $i < $count; $i++) {        // get old division
        $pos_start = strpos($text, $find);
        $pos_end = strpos($text, '</div>', $pos_start);
        $length = $pos_end - $pos_start + 6;  // add 6 because last item is 6 long
        $old = substr($text, $pos_start, $length);
        // find title_phrase
        $title = modifyVideoRevealFindText($old, 2);
        $title = '&nbsp;' . $title . '&nbsp;';
        $title_phrase =  $word = str_replace('%', $title, $watch_phrase);
        //find url
        $url = modifyVideoRevealFindText($old, 4);
        $new = videoTemplateOnline($old, $title_phrase, $url, $bookmark, $i);
        // replace old  from https://stackoverflow.com/questions/1252693/using-str-replace-so-that-it-only-acts-on-the-first-match
        $length = $pos_end - $pos_start + 6;  // add 6 because last item is 6 long
        $text = substr_replace($text, $new, $pos_start, $length);
    }
    return $text;
}
