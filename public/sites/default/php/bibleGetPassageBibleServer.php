<?php
/* requires $p as array:
         'entry' => 'Zephaniah 1:2-3'
          'bookId' => 'Zeph',
          'chapterId' => 1,
          'verseStart' => 2,
          'verseEnd' => 3,
         'collection_code' => 'OT' ,
         'version_ot' => '123', // this is bid
         'version_nt' => '134'
     )
    returns an array:
    $output['content']= [
		'reference' =>  $output['passage_name'],
		'text' => $output['bible'],
		'link' => $output['link']
	];

		BASED ON THE LOGIC OF October 2023
*/
myRequireOnce('getElementsByClass.php');
myRequireOnce('simple_html_dom.php', 'libraries/simplehtmldom_1_9_1');

// returns array (and I have no idea why both verse and reference; why k.
//1 =>
//array (
//  'verse' =>
//  array (
//    1 => 'John 14:15-26',
//  ),
//  'k' =>
//  array (
//    1 => '<h3>Jesus Promises the Holy Spirit</h3><p><sup>15 </sup>&#8220;If you love'
//  'bible' => '<h3>Jesus Promises the Holy Spirit</h3><p><sup>15 </sup>&#8220;If you love me, keep my commands.  you of everything I have said to you.</p>
//
//<p><strong><a href="http://mobile.BibleServer.com/versions/New-International-Version-NIV-Bible/">New International Version</a> (NIV)</strong> <p>Holy Bible, New International Version®, NIV® Copyright ©  1973, 1978, 1984, 2011 by <a href="http://www.biblica.com/">Biblica, Inc.®</a> Used by permission. All rights reserved worldwide.</p>',
//   'reference' => 'John 14:15-26',
// ),

function bibleGetPassageBibleServer($p)
{
	$output = array();
	$output['debug'] = '';
	$parse = array();
	$reference_shaped = $p['entry']; // try this and see if it works/
	$reference_shaped = str_replace(' ', '%20', $reference_shaped);
	$agent = 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.1.4322; .NET CLR 2.0.50727; .NET CLR 3.0.04506.30)';
	$reffer = 'https://bibleserver.com/' . $p['version_code'] . '/' . $reference_shaped; // URL
	$cookie_file_path = null;
	$ch = curl_init();	// Initialize a CURL conversation.
	// The URL to fetch. You can also set this when initializing a conversation with curl_init().
	curl_setopt($ch, CURLOPT_USERAGENT, $agent); // The contents of the "User-Agent: " header to be used in a HTTP request.
	curl_setopt($ch, CURLOPT_POST, 1); //TRUE to do a regular HTTP POST. This POST is the normal application/x-www-form-urlencoded kind, most commonly used by HTML forms.
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  // TRUE to return the transfer as a string of the return value of curl_exec() instead of outputting it out directly.
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // TRUE to follow any "Location: " header that the server sends as part of the HTTP header (note this is recursive, PHP will follow as many "Location: " headers that it is sent, unless CURLOPT_MAXREDIRS is set).
	curl_setopt($ch, CURLOPT_REFERER, $reffer); //The contents of the "Referer: " header to be used in a HTTP request.
	curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file_path); // The name of the file containing the cookie data. The cookie file can be in Netscape format, or just plain HTTP-style headers dumped into a file.
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file_path); // The name of a file to save all internal cookies to when the connection closes.
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //FALSE to stop CURL from verifying the peers certificate. Alternate certificates to verify against can be specified with the CURLOPT_CAINFO option or a certificate directory can be specified with the CURLOPT_CAPATH option. CURLOPT_SSL_VERIFYHOST may also need to be TRUE or FALSE if CURLOPT_SSL_VERIFYPEER is disabled (it defaults to 2). TRUE by default as of CURL 7.10. Default bundle installed as of CURL 7.10.
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); // 1 to check the existence of a common name in the SSL peer certificate. 2 to check the existence of a common name and also verify that it matches the hostname provided.
	curl_setopt($ch, CURLOPT_LOW_SPEED_LIMIT, 90); // Wait 30 seconds for download
	curl_setopt($ch, CURLOPT_LOW_SPEED_TIME, 90); // Wait 30 seconds for download
	curl_setopt($ch, CURLOPT_TIMEOUT, 90); // Wait 30 seconds for download
	$url = $reffer;
	$output['link'] = $url;
	//writeLogDebug('bibleGetPassageBibleServer-70', $url);
	curl_setopt($ch, CURLOPT_URL, $url);
	$string = curl_exec($ch);  // grab URL and pass it to the variable.
	// see https://code.tutsplus.com/tutorials/html-parsing-and-screen-scraping-with-the-simple-html-dom-library--net-11856
	writeLogDebug('bibleGetPassageBibleServer-73', $string);
	$html = str_get_html($string);
	$begin = strpos($html, '<h2 class="bible-name">');
	$html = substr($html, $begin);
	$end = strpos ($html, '<footer style');
	$bible = substr($html, 0, $end);
	if ($bible) {
		$bible = bibleGetPassageBibleServerClean($bible);
		$bible = bibleGetPassageBibleServerTrim($bible, $p['verseStart'], $p['verseEnd']);
		$output['bible'] =   "\n" . '<!-- begin bible -->' . $bible;
		$output['bible'] .=  "\n" . '<!-- end bible -->' . "\n";
	} else {
		$output['bible'] = null;
	}
	$output['content'] = [
		'reference' =>  $reference,
		'text' => $output['bible'],
		'link' => $output['link']
	];
	return $output;
}

function bibleGetPassageBibleServerTrim($bible, $verseStart, $verseEnd){
	$findBegin = '<sup>'. $verseStart .'</sup>';
	$findBeyond = $verseEnd + 1;
	$findEnd = '<sup>'. $findBeyond.'</sup>';
	writeLog('trim-106', $findBegin . ' --- '. $findEnd);
	$posBegin = strpos($bible, $findBegin);
	$bible = substr($bible, $posBegin);
	writeLog('trim-109', $bible);
	$posEnd = strpos($bible, $findEnd);
	writeLog('trim-111', $posEnd);
	if ($posEnd){
		$bible = substr($bible, 0, $posEnd);
	}
	return $bible;
}

function  bibleGetPassageBibleServerClean($bible)
{
	// now we are working just with Bible text
	writeLogDebug('bibleGetPassageBibleServer-95', $bible);
	$html = str_get_html($bible);
	// remove all links
	$items = $html->find('a');
	foreach ($items as $href) {
		$href->outertext = '';
	}
	$bible = $html->outertext;
	$html->clear();
	$html = str_get_html($bible);
	$items = $html->find('span[class=d-sr-only]');
	foreach ($items as $item) {
		$item->outertext = '';
	}
	$items = $html->find('span.verse-references');
	foreach ($items as $item) {
		$item->outertext = '';
	}
	$items = $html->find('noscript');
	foreach ($items as $item) {
		$item->outertext = '';
	}
	$items = $html->find('span.verse');
	$count = 0;
	foreach ($items as $item) {
		$count++;
		if ($count == 1){
			$debug = $item->outertext;
			writeLog('span-verse-140', $debug);
		}
		$verseNumberElement = $item->find('span.verse-number', 0);
		if ($verseNumberElement) {
			// Extract the number inside the span element
			$verseNumber = trim($verseNumberElement->plaintext);
			// Create a new <sup> element with the extracted number
			$supElement = '<sup>' . $verseNumber . '</sup>';
		}
		$verseContentElement = $item->find('span.verse-content', 0);
		if ($verseContentElement) {
			$verseContent = trim($verseContentElement->plaintext);
		}
		$item->outertext = $supElement . $verseContent;
		if ($count == 1){
			$debug = $item->outertext;
			writeLog('span-verse-158', $debug);
		}
	}
	$bible = $html->outertext;
	$bad = array('()', '(  )', '(;)', '(;;)', '(;;;)',
		'(  ;   )', '(  ;   ;   )', '(; ; )', '(; )','<!---->', '   ');
	$bible = str_ireplace($bad, '', $bible);
	$bible = preg_replace('/\[\d+\]/', '', $bible);
	return $bible;
}
