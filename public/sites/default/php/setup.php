<?php
myRequireOnce('create.php');
myRequireOnce('copyGlobal.php');
myRequireOnce('dirStandard.php');
myRequireOnce('writeLog.php');


function setupImageFolder($p)
{
	$debug = 'setupImageFolder' . "\n";
	if (!isset($p['language_iso'])) {
		$message = 'language_iso not set in setupImageFolder' . "\n";
		trigger_error($message, E_USER_ERROR);
		return null;
	}
	copyGlobal(
		dirStandard('country', 'edit',  $p, $folders = 'images/standard/'),
		dirStandard('language', 'edit',  $p, $folders = 'images/standard/')
	);
	$out = 'success';
	return $out;
}

// only set up directory for styles;
// do NOT copy styles into here
// encouarage people to use the ZZ folder

function setupTemplatesLanguage($p)
{
	$count = 0;
	if (!isset($p['language_iso'])) {
		$message = 'language_iso not set' . "\n";
		trigger_error($message, E_USER_ERROR);
		return null;
	}
	$template_dir = dirStandard('country', 'edit',  $p, $folders = 'templates');
	$p['folder_name'] = array();
	$dir = new DirectoryIterator($template_dir);
	foreach ($dir as $fileinfo) {
		if ($fileinfo->isDir() && !$fileinfo->isDot()) {
			$folders = 'templates/' .  $fileinfo->getFilename() . '/';
			writeLogError('setupTemplatesLanguage-' . $count, $folders);
			$count++;
			copyGlobal(
				dirStandard('country', 'edit',  $p, $folders),
				dirStandard('language', 'edit',  $p, $folders)
			);
			$out = 'success';
		}
	}
	$out = 'success';
	return $out;
}
