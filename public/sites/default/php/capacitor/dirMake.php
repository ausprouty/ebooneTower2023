<?php
// make directory if not found. No directory can have words '.bat','.html', '.json', '.mp3', '.mp4', '.wav', '.vue)
myRequireOnce('writeLog.php');
function dirMake($filename)
{
	$filename = rtrim($filename);
	if (strpos($filename, '//') !== FALSE) {
		$filename = str_ireplace('//', '/', $filename);
	}
	if (strpos($filename, '..') !== FALSE) {
		$filename = str_ireplace('..', '', $filename);
	}
	if (
		strpos($filename, ROOT_EDIT) === FALSE &&
		strpos($filename, ROOT_STAGING) === FALSE &&
		strpos($filename, ROOT_CAPACITOR) === FALSE &&
		strpos($filename, ROOT_WEBSITE) === FALSE
	) {
		$filename = ROOT_EDIT .  $filename;
	}
	//writeLogAppend('capacitor-dirMake-23', $filename);
	$file_types = array('.bat', '.html', '.jpg', '.js', '.json', '.mp3', '.mp4', '.png', '.wav', '.vue');
	$parts = explode('/', $filename);
	$dir = '';
	foreach ($parts as $part) {
		$ok = true;
		foreach ($file_types as $type) {
			if (strpos($part, $type) !== false) {
				$ok = false;
			}
		}
		if ($ok) {
			$dir .= $part . '/';
			if (!file_exists($dir)) {
				if (!is_dir($dir)) {
					if (file_exists($dir)) {
						writeLogAppend('ERROR -capacitor-dirMake-37', $dir);
					} else {
						mkdir($dir);
						writeLogAppend('capacitor-dirMake-42', $dir);
					}
				}
			}
		}
	}
	return  $filename;
}
