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
		strpos($filename, ROOT_APK) === FALSE &&
		strpos($filename, ROOT_SDCARD) === FALSE &&
		strpos($filename, ROOT_CAPACITOR) === FALSE &&
		strpos($filename, ROOT_WEBSITE) === FALSE
	) {
		$filename = ROOT_EDIT .  $filename;
	}
	writeLogAppend('dirMake-23', $filename);
	$file_types = array('.bat', '.html', '.js', '.json', '.mp3', '.mp4', '.wav', '.vue');
	$parts = explode('/', $filename);
	if (strpos($filename, '/') === 0){
		$dir = '/';
	}
	else{
		$dir = ''
	}
	foreach ($parts as $part) {
		$ok = true;
		foreach ($file_types as $type) {
			if (strpos($part, $type) !== false) {
				$ok = false;
			}
		}
		if ($ok) {
			$dir .= $part . '/';
			writeLogAppend('dirMake-41', $dir);
			if (!file_exists($dir)) {
				mkdir($dir);
			}
		}
	}
	return  $filename;
}
