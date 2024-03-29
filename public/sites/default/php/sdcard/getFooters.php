<?php

myRequireOnce('writeLog.php');
//define("ROOT_EDIT", ROOT . 'edit.mc2.online/');
function getFooters($p)
{
	$output = [];
	$directory = ROOT_EDIT . '/sites/' . SITE_CODE . '/prototype/sdcard/';
	if (file_exists($directory)) {
		$handler = opendir($directory);
		while ($mfile = readdir($handler)) {
			if ($mfile != '.' && $mfile != '..') {
				$needle = 'languageFooter';
				if (strpos($mfile, $needle) !== FALSE) {
					$output[] = $mfile;
				}
			}
		}
		closedir($handler);
	}
	return $output;
}
