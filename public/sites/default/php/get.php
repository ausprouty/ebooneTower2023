<?php
myRequireOnce('writeLog.php');

function getFoldersContent($p)
{
	writeLogDebug('getFoldersContent', $p);

	if (empty($p['language_iso'])) {
		writeLogError('getFoldersContent', 'language_iso not set');
		return false;
	}

	$exclude = ['images', 'styles', 'templates'];
	$path = ROOT_EDIT_CONTENT . $p['country_code'] . '/' . $p['language_iso'] . '/';

	if (!file_exists($path)) {
		return [];
	}

	$folders = findFoldersInDirectory($path, $exclude);

	if (empty($folders)) {
		$folders = makeFoldersContent($path);
	}

	return $folders;
}

function findFoldersInDirectory($path, $exclude = [])
{
	$results = [];

	foreach (new DirectoryIterator($path) as $fileinfo) {
		if ($fileinfo->isDir() && !$fileinfo->isDot()) {
			$name = $fileinfo->getFilename();
			if (!in_array($name, $exclude)) {
				$results[] = $name;
			}
		}
	}

	return $results;
}

function makeFoldersContent($path)
{
	if (empty(STANDARD_SERIES)) {
		writeLogError('getFoldersContent', 'STANDARD_SERIES not set');
		return [];
	}

	$folders = array_map('trim', explode(',', STANDARD_SERIES));
	$results = [];

	foreach ($folders as $folder) {
		dirMake($path . $folder);
		$results[] = $folder;
	}

	if (empty($results)) {
		writeLogError('getFoldersContent', 'STANDARD_SERIES has no items');
	}

	return $results;
}


// use country and language
// this looks for template in the country/language/templates directory
// and then returns as content
function getTemplate($p)
{
	writeLogDebug('getTemplate-61', $p);
	if (!$p['language_iso']) {
		$message = "language_iso not set\n";
		trigger_error($message, E_USER_ERROR);
		return null;
	}
	if (!$p['template']) {
		$message = "template not set\n";
		trigger_error($message, E_USER_ERROR);
		return null;
	}

	$language_dir = ROOT_EDIT_CONTENT . $p['country_code'] . '/' . $p['language_iso'];
	$template = $language_dir . '/templates/' . $p['template'];
	$debug = ' template is ' . $p['template'] . "\n";
	if (file_exists($template)) {
		$debug .= "Template Found: $template" . "\n";
		$debug .= file_get_contents($template) . "\n";
		$out = file_get_contents($template);
	} else {
		$message = "NO Templates found ";
		trigger_error($message, E_USER_ERROR);
		return [];
	}

	return $out;
}

function getTemplates($p)
{
	writeLogDebug('getTemplates-102', $p);
	$templates = [];

	if (empty($p['language_iso'])) {
		trigger_error("language_iso not set\n", E_USER_ERROR);
		return null;
	}

	$template_directory = ROOT_EDIT_CONTENT . $p['country_code'] . '/' . $p['language_iso'] . '/templates/';
	writeLogDebug('getTemplates-110', $template_directory);

	if (!file_exists($template_directory)) {
		myRequireOnce('setup.php');
		setupTemplatesLanguage($p);
	}

	// Attempt to fetch templates after setup
	if (!file_exists($template_directory)) {
		return $templates;
	}

	$templates = findTemplatesInDirectory($template_directory);

	// Retry setup if no templates found
	if (empty($templates)) {
		myRequireOnce('setup.php');
		setupTemplatesLanguage($p);
		$templates = findTemplatesInDirectory($template_directory);
	}

	return $templates;
}

function findTemplatesInDirectory($directory)
{
	$results = [];

	foreach (scandir($directory) as $item) {
		if ($item === '.' || $item === '..') {
			continue;
		}
		$path = $directory . $item;
		if (is_dir($path)) {
			foreach (scandir($path) as $file) {
				if ($file !== '.' && $file !== '..') {
					$results[] = $item . '/' . $file;
				}
			}
		} else {
			$results[] = $item;
		}
	}

	return $results;
}
