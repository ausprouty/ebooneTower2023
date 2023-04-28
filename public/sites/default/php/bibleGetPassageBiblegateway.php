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

		BASED ON THE LOGIC OF JANUARY 2020
*/
myRequireOnce('getElementsByClass.php');
myRequireOnce('simple_html_dom.php', 'libraries/simplehtmldom_1_9_1');
myRequireOnce('writeLog.php');

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
//<p><strong><a href="http://mobile.biblegateway.com/versions/New-International-Version-NIV-Bible/">New International Version</a> (NIV)</strong> <p>Holy Bible, New International Version®, NIV® Copyright ©  1973, 1978, 1984, 2011 by <a href="http://www.biblica.com/">Biblica, Inc.®</a> Used by permission. All rights reserved worldwide.</p>',
//   'reference' => 'John 14:15-26',
// ),

function bibleGetPassageBiblegateway($p)
{
	$output = array();
	$output['debug'] = '';
	$parse = array();
	// it seems that Chinese does not always like the way we enter things.
	$reference_shaped = str_replace($p['bookLookup'], $p['bookId'], $p['entry']); // try this and see if it works/
	$reference_shaped = str_replace(' ', '%20', $reference_shaped);

	$agent = 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.1.4322; .NET CLR 2.0.50727; .NET CLR 3.0.04506.30)';
	$reffer = 'http://biblegateway.com//passage/?search=' . $reference_shaped . '&version=' . $p['version_code']; // URL
	$POSTFIELDS = null;
	$cookie_file_path = null;
	$ch = curl_init();	// Initialize a CURL conversation.
	// The URL to fetch. You can also set this when initializing a conversation with curl_init().
	curl_setopt($ch, CURLOPT_USERAGENT, $agent); // The contents of the "User-Agent: " header to be used in a HTTP request.
	curl_setopt($ch, CURLOPT_POST, 1); //TRUE to do a regular HTTP POST. This POST is the normal application/x-www-form-urlencoded kind, most commonly used by HTML forms.
	curl_setopt($ch, CURLOPT_POSTFIELDS, $POSTFIELDS); //The full data to post in a HTTP "POST" operation.
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  // TRUE to return the transfer as a string of the return value of curl_exec() instead of outputting it out directly.
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // TRUE to follow any "Location: " header that the server sends as part of the HTTP header (note this is recursive, PHP will follow as many "Location: " headers that it is sent, unless CURLOPT_MAXREDIRS is set).
	curl_setopt($ch, CURLOPT_REFERER, $reffer); //The contents of the "Referer: " header to be used in a HTTP request.
	curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file_path); // The name of the file containing the cookie data. The cookie file can be in Netscape format, or just plain HTTP-style headers dumped into a file.
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file_path); // The name of a file to save all internal cookies to when the connection closes.
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //FALSE to stop CURL from verifying the peer's certificate. Alternate certificates to verify against can be specified with the CURLOPT_CAINFO option or a certificate directory can be specified with the CURLOPT_CAPATH option. CURLOPT_SSL_VERIFYHOST may also need to be TRUE or FALSE if CURLOPT_SSL_VERIFYPEER is disabled (it defaults to 2). TRUE by default as of CURL 7.10. Default bundle installed as of CURL 7.10.
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); // 1 to check the existence of a common name in the SSL peer certificate. 2 to check the existence of a common name and also verify that it matches the hostname provided.
	curl_setopt($ch, CURLOPT_LOW_SPEED_LIMIT, 90); // Wait 30 seconds for download
	curl_setopt($ch, CURLOPT_LOW_SPEED_TIME, 90); // Wait 30 seconds for download
	curl_setopt($ch, CURLOPT_TIMEOUT, 90); // Wait 30 seconds for download
	$url = 'https://biblegateway.com/passage/?search=' . $reference_shaped . '&version=' . $p['version_code']; // URL
	$output['link'] = $url;
	curl_setopt($ch, CURLOPT_URL, $url);
	$string = curl_exec($ch);  // grab URL and pass it to the variable.
	// see https://code.tutsplus.com/tutorials/html-parsing-and-screen-scraping-with-the-simple-html-dom-library--net-11856
	//writeLogDebug('bibleGetPassageBiblegateway-79', $string);
	$html = str_get_html($string);
	$e = $html->find('.dropdown-display-text', 0);
	$reference = $e->innertext;
	writeLogAppend('bibleGetPassageBiblegateway-83', $reference);
	$passages = $html->find('.passage-text');
	$bible = '';
	foreach ($passages as $passage) {
		$bible .= $passage;
	}
	$html->clear();
	unset($html);
	if ($bible) {
		$bible = bibleGetPassageBiblegatewayClean($bible);
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
	//writeLogDebug('bibleGetPassageBiblegateway-110', $output);
	return $output;
}

function  bibleGetPassageBiblegatewayClean($bible)
{

	// now we are working just with Bible text
	//
	//writeLogDebug('bibleGetPassageBiblegateway-95', $bible);
	$html = str_get_html($bible);

	$ret = $html->find('span');
	foreach ($ret as $span) {
		$span->outertext = $span->innertext;
	}
	// remove all links
	$ret = $html->find('a');
	foreach ($ret as $href) {
		$href->outertext = '';
	}
	// remove footnotes
	$ret = $html->find('div[class=footnotes]');
	foreach ($ret as $footnote) {
		$footnote->outertext = '';
	}
	$bible = $html->outertext;

	$html = str_get_html($bible);
	$ret = $html->find('span[class=woj]');
	foreach ($ret as $span) {
		$span->outertext = $span->innertext;
	}
	$bible = $html->outertext;
	$html->clear();
	$html = str_get_html($bible);
	// remove links to footnotes
	$ret = $html->find('sup[class=footnote]');
	foreach ($ret as $footnote) {
		$footnote->outertext = '';
	}
	// remove crossreference div
	$ret = $html->find('div[class=crossrefs hidden]');
	foreach ($ret as $cross_reference) {
		$cross_reference->outertext = '';
	}
	$ret = $html->find('sup[class=crossreference]');
	foreach ($ret as $cross_reference) {
		$cross_reference->outertext = '';
	}
	$ret = $html->find('div[class=il-text]');
	foreach ($ret as $cross_reference) {
		$cross_reference->outertext = '';
	}
	// change chapter number to verse 1
	// <span class="chapternum">53&nbsp;</span>
	$ret = $html->find('span[class=chapternum]');
	foreach ($ret as $chapter) {
		$chapter->outertext = '<sup class="versenum">1&nbsp;</sup>';
	}
	$bible = $html->outertext;
	unset($html);
	$bad = array(
		'<!--end of crossrefs-->'
	);
	$good = '';
	$bible = str_replace($bad, $good, $bible);
	$pos_start = strpos($bible, '<p');
	if ($pos_start !== FALSE) {
		$bible = substr($bible, $pos_start);

		$bible = str_ireplace('</div>', '', $bible);
		$bible = str_ireplace('<div class="passage-other-trans">', '', $bible);
	}
	return $bible;
}
