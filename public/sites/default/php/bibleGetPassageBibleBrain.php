<?php
/*returns an array:
    $output= [
		'reference' => 
		'text' => 
		'link' => 
	];
*/
myRequireOnce('bibleBrainGet.php');

function bibleGetPassageBibleBrain($p)
{
    $output = [];
    $output['content'] = [];

    $filesetVariants = [
        substr($p['damId'], 0, 6),
        $p['damId'],
        str_replace('2', '_', $p['damId']),
    ];

    $text = '';
    $filesetUsed = '';

    foreach ($filesetVariants as $fileset) {
        $url = 'https://4.dbt.io/api/bibles/filesets/';
        $url .=  $fileset . '/' . $p['bookId'] . '/' . $p['chapterId'] . '?';
        $url .= 'verse_start=' . $p['verseStart'] . '&verse_end=' . $p['verseEnd'] . '&';

        $text = bibleGetPassageBibleBrainText($url);

        if (isValidBibleText($text)) {
            $filesetUsed = $fileset;
			writeLogDebug('bibleGetPassageBibleBrain-34-'. $p['damId'], 'Valid text found for fileset: ' . $fileset);
            break;
        }
		writeLogAppend('bibleGetPassageBibleBrain-37-'. $p['damId'], 'Invalid URL: ' . $url);
    }

    // Still no valid text found
    if (empty($filesetUsed)) {
        $output['text'] = 'No valid Bible text found for '. $p['damId'];
        return $output;
    }

    // Try extra chapters if provided
    if (isset($p['extraChapters'])) {
        foreach ($p['extraChapters'] as $chapter) {
            $url = 'https://4.dbt.io/api/bibles/filesets/';
            $url .=  $filesetUsed . '/' . $p['bookId'] . '/' . $chapter['chapterId'] . '?';
            $url .= 'verse_start=' . $chapter['verseStart'] . '&verse_end=' . $chapter['verseEnd'] . '&v=4&key=';
            $text .= '<sup class="chapternum">' .  $chapter['chapterId'] . ':</sup>';
            $text .= bibleGetPassageBibleBrainText($url);
        }
    }

    $output['link'] = 'https://live.bible.is/bible/' . $filesetUsed . '/' . $p['bookId'] . '/' . $p['chapterId'];
    $output['reference'] = $p['entry'];
    $output['text'] = $text;
    return $output;
}

// Simple validation to check for a meaningful text result
function isValidBibleText($text)
{
    return !empty($text) && strpos($text, 'error') === false && strlen(strip_tags($text)) > 20;
}

function bibleGetPassageBibleBrainText($url)
{
	$response = bibleBrainGet($url);

	// Check for error in the response
	if (isset($response->error)) {
		// Log the error or return a friendly message
		writeLogError('bibleGetPassageBibleBrainText', $response);
		return '<p class="bible_text error">Sorry, the passage could not be retrieved.</p>';
	}

	if (!isset($response->data) || !is_array($response->data)) {
		writeLogError('bibleGetPassageBibleBrainText', 'No data found in response.');
		return '<p class="bible_text error">No passage data available.</p>';
	}

	$verses = $response->data;
	$text = '<p class="bible_text">';
	foreach ($verses as $verse) {
		$text .= '<sup class="versenum">' . $verse->verse_start . '</sup>';
		$text .= $verse->verse_text . ' ';
	}
	return $text . '</p>';
}


function bibleBrainGetBibles($language_iso)
{
	$output = '';
	$count = 0;
	$url = 'https://4.dbt.io/api/bibles?language_code=' . $language_iso . '&';
	$response =  bibleBrainGet($url);
	$resources = $response->data;
	$dbp_prod = 'dbp-prod';
	$dbp_vid = 'dbp-vid';
	foreach ($resources as $resource) {
		$output .= $resource->abbr . ': ' . $resource->vname . '(' . $resource->name . ')<br>';
		if (isset($resource->filesets->$dbp_prod)) {
			$items = $resource->filesets->$dbp_prod;
			foreach ($items as $item) {
				$output .= '----------' . $item->id . '(' . $item->type . ')' . $item->size . '<br>';
			}
		}
		if (isset($resource->filesets->$dbp_vid)) {
			$items = $resource->filesets->$dbp_vid;
			foreach ($items as $item) {
				$output .= '----------' . $item->id . '(' . $item->type . ')' . $item->size . '<br>';
			}
		}
	}

	return $output;
}
