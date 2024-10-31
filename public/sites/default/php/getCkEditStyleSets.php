<?php
myRequireOnce('writeLog.php');
// get styles that actually reside in the node_modules directory
function getCkEditStyleSets($p)
{
	$set = [];
	$site = ROOT_NODE_MODULES . 'ckeditor/styles.js';
	writeLog('getCKStyleSets-8-site', $site);
	if (file_exists($site)) {
		$text = file_get_contents($site);
		writeLog('getCKStyleSets-11-site', $text);
		$res = _getStyleSetName($text);
		if ($res) {
			foreach ($res as $value) {
				//$set[$value] = $value;
				$set[] = $value;
			}
		}
	} else {
		writeLogError('getCKStyleSets-20-site', $site);
		$message = 'in getCkEditStyleSets can not find ' . $site;
		trigger_error($message, E_USER_ERROR);
	}
	return $set;
}

function _getStyleSetName($text)
{
	$sets = [];
	$find = 'window.CKEDITOR.stylesSet.add';
	$bad = [' ', '(', '\''];
	$count = substr_count($text, $find);
	$pos_start = 0;
	for ($i = 0; $i < $count; $i++) {
		$pos_start = strpos($text, $find, $pos_start) + strlen($find);
		$pos_end = strpos($text, ',', $pos_start);
		$length = $pos_end - $pos_start;  // add 6 because last item is 6 long
		$old = substr($text, $pos_start, $length);
		$old = str_ireplace($bad, '', $old);
		$sets[] = $old;
		$pos_start = $pos_end;
	}
	$out = $sets;
	return $out;
}
