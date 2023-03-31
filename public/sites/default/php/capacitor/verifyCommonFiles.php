<?php
/* Here we copy the files that all capacitors need, but are not sourced from content direcly
ROOT_EDIT/sites/SITE_CODE/capacitor-> ROOT_SDCARD/language_iso/
*/
myRequireOnce('writeLog.php');
myRequireOnce('createDirectory');
myRequireOnce('copyDirectory.php');


function verifyCommonFiles($p)
{
  $progress = new stdClass;
  $progress->common_files->progress = 'ready';
  $from = ROOT_EDIT . 'sites/' . SITE_CODE . '/capacitor/';
  $to = ROOT_CAPACITOR . $p['language_iso'] . '/';
  //copyDirectory($from, $to);
  verifyCommonFilesCopyFolder($from, $to);
  // writeLog('verifyCommonFiles-capacitor-19', "$from copied to  $to");
  return $progress;;
}

function verifyCommonFilesCopyFolder($from, $to)
{
  // writeLogAppend('verifyCommonFiles-capacitor-21', "$from to $to");

  // (A1) SOURCE FOLDER CHECK
  if (!is_dir($from)) {
    // writeLog('ERROR- verifyCommonFiles-capacitor-17', "$from does not exist");
    trigger_error("$from does not exist", E_USER_WARNING);
    exit;
  }
  $guard = ROOT_EDIT . 'sites/' . SITE_CODE . '/capacitor/';
  if (strpos($from, $guard) != 0) {
    // writeLog('ERROR- verifyCommonFiles-capacitor-17', "$from is not a guarded route");
    trigger_error("$from is not a guarded route", E_USER_WARNING);
    exit;
  }

  // (A2) CREATE DESTINATION FOLDER
  if (!is_dir($to)) {
    // writeLogAppend('verifyCommonFiles-capacitor-42', "creating folder $to");
    createDirectory($to);
  }

  // (A3) COPY FILES + RECURSIVE INTERNAL FOLDERS
  $dir = opendir($from);
  // writeLogAppend('verifyCommonFiles-capacitor-43', $from);
  while (($ff = readdir($dir)) !== false) {
    if ($ff != "." && $ff != "..") {
      if (is_dir("$from$ff")) {
        // writeLogAppend('verifyCommonFiles-capacitor-47', "$from$ff/  to $to$ff/");
        verifyCommonFilesCopyFolder("$from$ff/", "$to$ff/");
      } else {
        createDirectory("$to$ff");
        copy("$from$ff", "$to$ff");
        // writeLogAppend('verifyCommonFiles-capacitor-50', "$from$ff copied to $to$ff");
      }
    }
  }
  closedir($dir);
}
