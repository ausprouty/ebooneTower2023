<?php

/*
/    // copies all files in directory
    //  Overwrites existing files
*/
myRequireOnce('writeLog.php');
myRequireOnce('createDirectory.php');

function copyDirectory($source, $destination)
{
	writeLogAppend('copyDirectory-12', "$source  to $destination");
	$source = str_replace('//', '/', $source);
	$destination = str_replace('//', '/', $destination);
	$debug = '';
	if (!file_exists($source)) {
		$message = "source directory $source does not exist";
		trigger_error($message, E_USER_ERROR);
		return;
	}
	if (!file_exists($destination)) {
		createDirectory($destination);
	}
	$handler = opendir($source);
	while ($mfile = readdir($handler)) {
		if ($mfile != '.' && $mfile != '..') {
			$setup_file = $source . $mfile;
			if (!is_dir($setup_file)) {
				$destination_file = $destination . $mfile;
				copy($setup_file, $destination_file);
				$debug .=  ' copied ' .  $setup_file . ' to ' . $destination_file . "\n\n";
			}
		}
	}
	writeLogAppend('copyDirectory-33', $debug);
	return;
}
