<?php
/* Here we copy the files that all capacitors need, but are not sourced from content direcly
ROOT_EDIT/sites/SITE_CODE/capacitor-> ROOT_SDCARD/language_iso/
*/
myRequireOnce('writeLog.php');
myRequireOnce('createDirectory.php');


function verifyCommonFiles($p)
{
  writeLog('verifyCommonFiles-capacitor-10', $p);
  $progress = new stdClass;
  $progress->common_files->progress = 'ready';
  $from = ROOT_EDIT . 'sites/' . SITE_CODE . '/capacitor/';
  $to = ROOT_SDCARD . $p['language_iso'] . '/';
  writeLog('verifyCommonFiles-capacitor-12', "$from goes $to");
  $new_progress = verifyCommonFilesCopyFolder($from, $to);
  return $progress;;
}

function verifyCommonFilesCopyFolder($from, $to)
{
  writeLogAppend('verifyCommonFiles-capacitor-21', "$from to $to");

  // (A1) SOURCE FOLDER CHECK
  if (!is_dir($from)) {
    trigger_error("$from does not exist", E_USER_WARNING);
    exit;
  }
  $guard = ROOT_EDIT . 'sites/' . SITE_CODE . '/capacitor/';
  if (strpos($from, $guard) != 0) {
    trigger_error("$from is not a guarded route", E_USER_WARNING);
    exit;
  }

  // (A2) CREATE DESTINATION FOLDER
  if (!is_dir($to)) {
    createDirectory($to);
    writeLogAppend('verifyCommonFiles-capacitor-42', $to);
  }

  // (A3) COPY FILES + RECURSIVE INTERNAL FOLDERS
  $dir = opendir($from);
  writeLogAppend('verifyCommonFiles-capacitor-43', $from);
  while (($ff = readdir($dir)) !== false) {
    if ($ff != "." && $ff != "..") {
      if (is_dir("$from$ff")) {
        writeLogAppend('verifyCommonFiles-capacitor-47', "$from$ff/  to $to$ff/");
        verifyCommonFilesCopyFolder("$from$ff/", "$to$ff/");
      } else {
        copy("$from$ff", "$to$ff");
        writeLogAppend('verifyCommonFiles-capacitor-50', "$from$ff copied to $to$ff");
      }
    }
  }
  closedir($dir);
}
