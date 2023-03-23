<?php
myRequireOnce('writeLog.php');
myRequireOnce('createDirectory.php');
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
		strpos($filename, ROOT_MEDIA) === FALSE &&
		strpos($filename, ROOT_WEBSITE) === FALSE
	) {
		$filename = ROOT_EDIT .  $filename;
	}
	createDirectory($filename);
	return  $filename;
}
