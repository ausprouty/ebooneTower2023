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
// assign variables
$out = array();
$p = setParameters($_POST);
if (isset($p['my_uid'])) {
	myRequireOnceSetup($p['my_uid']);
}
// get is used in prototype and publish and overrides $p
if (isset($_GET['page'])) {
	$p['page'] = $_GET['page'];
}
if (isset($_GET['action'])) {
	$p['action'] = $_GET['action'];
}
if (isset($p['action'])) {
	// login routine
	if ($p['action'] == 'login') {
		$out = myApiLogin($p);
	} else {
		// take action if authorized user
		if (!isset($p['token'])) {
			$message = "Token is not set";
			trigger_error($message, E_USER_ERROR);
			die;
		}
		$ok = myApiAuthorize($p['token']);
		unset($p['token']);  // so it will not be sent back
		if ($ok || $p['action'] == 'bookmark') {
			myRequireOnce('dirMake.php');
			if (isset($p['page'])) {
				$subdirectory = null;
				if (isset($p['subdirectory'])) {
					$subdirectory  = $p['subdirectory'];
				}
				writeLogDebug('AuthorApi-62-p', $p);
				myRequireOnce($p['page'], $subdirectory);
				$action = $p['action'];
				$out = $action($p);
			} else {
				$message = $p['page']  . "is not set";
				trigger_error($message, E_USER_ERROR);
			}
		} else {
			$message = "Not Authorized";
			$debug .= $message;
			trigger_error($message, E_USER_ERROR);
		}
	}
} else {
	$message = "No Action";
	$debug .= $message;
	trigger_error($message, E_USER_ERROR);
}


$debug .= "\n\nHERE IS JSON_ENCODE OF DATA THAT IS NOT ESCAPED\n";
$debug .= json_encode($out) . "\n";
writeLog($p['action'],   $debug);
header("Content-type: application/json");
echo json_encode($out, JSON_UNESCAPED_UNICODE);

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
	myDestination($p);  // set destination
	writeLogDebug('AuthorSetParameters-p', $p);
	return $p;
}
