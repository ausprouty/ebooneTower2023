<?php
/*Input is:
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
*/
myRequireOnce('videoOfflineNewName.php');
myRequireOnce('modifyViedoTextAndTime.php');

function videoBibleFindForOffline($p, $filename)
{
    //todo clean this
    $chapter_videos = [];

    //writeLog('videoFindForOffline-113-p', $p);
    //writeLog('videoFindForOffline-114-filename', $filename);
    // find chapter that has been prototyped
    $chapter_videos = [];
    $videoReference = videoReference();
    $video = [];
    $video['filename'] = $filename;
    $new_name = videoOfflineNewName($filename);
    $video['new_name'] = $new_name;
    $sql = "SELECT * FROM content
        WHERE  country_code = '" . $p['country_code'] . "'
        AND  language_iso = '" . $p['language_iso'] . "'
        AND folder_name = '" . $p['folder_name'] . "'
        AND filename = '" . $filename . "'
        AND prototype_date IS NOT NULL
        ORDER BY recnum DESC LIMIT 1";
    $data = sqlArray($sql);
    if (!isset($data['text'])) {
        writeLogError('videoFindForOffline -' . $filename, $sql);
        return $chapter_videos;
    }
    $text = $data['text'];
    ////writeLog('videoFindForOffline-76-'. $filename, $text);
    $find = '<div class="reveal film">';
    $count = substr_count($text, $find);
    //writeLog('videoFindForOffline-140-count', $count);
    $offset = 0;
    $previous_url = NULL;
    for ($i = 0; $i < $count; $i++) {
        // get old division
        $pos_start = strpos($text, $find, $offset);
        $pos_end = strpos($text, '</div>', $pos_start);
        $offset = $pos_end;
        $length = $pos_end - $pos_start + 6;  // add 6 because last item is 6 long
        $old = substr($text, $pos_start, $length);
        // find title_phrase
        $video['title'] = modifyVideoRevealFindText($old, 2);
        //find url
        $url = modifyVideoRevealFindText($old, 4);
        $arc = 'api.arclight.org';
        ////writeLog('videoFindForOffline-95-'. $filename . $count, $url . "\n" . $find);

        if (strpos($url, $arc)) {
            $url = str_ireplace('https://api.arclight.org/videoPlayerUrl?refId=', '', $url);
            ////writeLog('videoFindForOffline-99-'. $filename . $count, $url);
            $start = strpos($url, '-') + 1;
            $url = substr($url, $start);
            if (isset($videoReference[$url])) {
                $video['download_name'] = $videoReference[$url];
            } else {
                $video['download_name'] = NULL;
                $message = 'Download name not found for ' . $url;
                writeLogError('videoFindForOffline-216-' . $p['language_iso'] . '-' . $filename, $message);
            }
        } else {
            if (isset($videoReference[$url])) {
                $video['download_name'] = $videoReference[$url];
            } else {
                $video['download_name'] = NULL;
                $message = 'Download name not found for ' . $url;
                writeLogError('videoFindForOffline-226-' . $p['language_iso'] . '-' .  $filename, $message);
            }
        }
        $video['url'] = $url;
        // find start and end times
        $video['start_time'] = modifyVideoRevealFindTime($old, 7);
        $video['end_time'] = modifyVideoRevealFindTime($old, 9);
        //does this follow on from previous video? If so record $url
        $video['follows'] = videoFollows($previous_url, $url);
        $previous_url = $url;
        //if more than one video in this chapter
        if ($i > 0) {
            $video['new_name'] = $new_name . '-' . $i;
        }
        $chapter_videos[] = $video;
    }
    //writeLog('videoFindForOffline-185-chaptervideos', $chapter_videos);
    return $chapter_videos;
}
