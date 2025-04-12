<?php

/*
  Returns Json encoded array
	$out = successful content
	$out['login']  is set if login fails (login only)
	$out['token']  for authorization (login only)
	$out['debug']  for some debugging purposes

  Writes $debug to file if LOG_MODE == 'write_log'
*/
$debug = '';
$backend = '../' . $_GET['site'] . '/.env.api.' .  $_GET['location'] . '.php';
if (file_exists($backend)) {
	require_once($backend);
} else {
	trigger_error("No backend for AuthorApi. Looking for $backend", E_USER_ERROR);
}
require_once('php/myRequireOnce.php');
require_once('php/sql.php');
require_once('php/writeLog.php');
myHeaders(); // send cors headers

set_exception_handler('jsonExceptionHandler');


// assign variables
$p = setParameters($_POST);
$out = [];

// Optional my_uid setup
if (!empty($p['my_uid'])) {
	myRequireOnceSetup($p['my_uid']);
}

// Override from querystring
if (isset($_GET['page'])) {
	$p['page'] = $_GET['page'];
}
if (isset($_GET['action'])) {
	$p['action'] = $_GET['action'];
}

// Handle action
if (empty($p['action'])) {
	http_response_code(400);
	echo json_encode([
		'status' => 'error',
		'error' => "No action provided",
		'data' => null
	], JSON_UNESCAPED_UNICODE);
	exit;
}

if ($p['action'] === 'login') {
	$out = myApiLogin($p);
} else {
	if (empty($p['token'])) {
		http_response_code(401);
		echo json_encode([
			'status' => 'error',
			'error' => "Token is not set",
			'data' => null
		], JSON_UNESCAPED_UNICODE);
		exit;
	}

	$ok = myApiAuthorize($p['token']);
	unset($p['token']); // Remove token from output

	if ($ok || $p['action'] === 'bookmark') {
		myRequireOnce('dirMake.php');
		$subdirectory = $p['subdirectory'] ?? null;

		if (!empty($p['page'])) {
			myRequireOnce($p['page'], $subdirectory);
			$action = $p['action'];
			$out = $action($p);
		} else {
			http_response_code(400);
			echo json_encode([
				'status' => 'error',
				'error' => "Page is not set",
				'data' => null
			], JSON_UNESCAPED_UNICODE);
			exit;
		}
	} else {
		http_response_code(403);
		echo json_encode([
			'status' => 'error',
			'error' => "Not Authorized",
			'data' => null
		], JSON_UNESCAPED_UNICODE);
		exit;
	}
}

// Wrap output in consistent response format// If $out is already a structured response, just return it
if (is_array($out) && isset($out['status']) && array_key_exists('result', $out)) {
	$response = $out;
} else {
	$response = [
		'status' => isset($out->error) ? 'error' : 'ok',
		'error' => $out->error ?? null,
		'result' => $out
	];
}
writelogDebug('AuthorApi-105-' . $p['action'], $response);
header("Content-type: application/json");
echo json_encode($response, JSON_UNESCAPED_UNICODE);
exit;

// return response

die();



/*
//            FUNCTIONS
*/
function setParameters($post)
{

	foreach ($post as $param_name => $param_value) {
		$$param_name = $param_value;
		$p[$param_name] =  $param_value;
	}
	if (isset($p['route'])) {
		$route = json_decode($p['route']);
		$p['country_code'] = isset($route->country_code) ? $route->country_code : NULL;
		$p['language_iso'] = isset($route->language_iso) ? $route->language_iso : NULL;
		$p['library_code'] = isset($route->library_code) ? $route->library_code : NULL;
		$p['folder_name'] = isset($route->folder_name) ? $route->folder_name : NULL;
		$p['filename'] = isset($route->filename) ? $route->filename : NULL;
	}
	if (isset($p['sdcard'])) {
		$p['sdcard_settings'] = json_decode($p['sdcard']);
		$bad = ['/', '..'];
		if (isset($p['sdcard_settings']->subDirectory)) {
			$clean = str_replace($bad, '', $p['sdcard_settings']->subDirectory);
			$p['dir_sdcard'] = ROOT_SDCARD . $clean . '/';
		}
	}
	if (isset($p['apk_settings'])) {
		$p['apk_settings'] = json_decode($p['apk_settings']);
	}
	if (isset($p['capacitor_settings'])) {
		$p['capacitor_settings'] = json_decode($p['capacitor_settings']);
	}
	if (!isset($p['version'])) {
		$p['version'] = VERSION;
	}
	$p['site'] =  $_GET['site'];
	myDestination($p);  // set DESTINATION
	writeLogDebug('AuthorSetParameters-p', $p);
	return $p;
}

function jsonExceptionHandler($exception)
{
	http_response_code(500);
	$response = [
		'status' => 'error',
		'error' => $exception->getMessage(),
		'data' => null
	];
	header('Content-Type: application/json');
	echo json_encode($response, JSON_UNESCAPED_UNICODE);
	exit;
}
