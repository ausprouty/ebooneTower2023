<?php
/* Here we copy the files that all capacitors need, but are not sourced from content direcly
ROOT_EDIT/sites/SITE_CODE/capacitor-> ROOT_SDCARD/language_iso/
*/
myRequireOnce('writeLog.php');
myRequireOnce('createDirectory');
myRequireOnce('copyDirectory.php');


function createCommonFiles($p)
{

  class Progress
  {
    public $common_files;
  }
  class CommonFiles
  {
    public $progress;
    public $message;
  }
  $progress = new Progress();
  $common_files = new CommonFiles();
  $progress->common_files = $common_files;
  $from = ROOT_EDIT . 'sites/' . SITE_CODE . '/capacitor/';
  $to = ROOT_CAPACITOR . $p['language_iso'] . '/';
  createCommonFilesCopyFolder($from, $to);
  $progress->common_files->progress = 'done';
  $progress->common_files->message = 'Finished routine, but did not check to see if files are there.';
  return $progress;
}

function createCommonFilesCopyFolder($from, $to)
{
  // //writeLogAppend('createCommonFiles-capacitor-21', "$from to $to");

  // (A1) SOURCE FOLDER CHECK
  if (!is_dir($from)) {
    // writeLog('ERROR- createCommonFiles-capacitor-17', "$from does not exist");
    trigger_error("$from does not exist", E_USER_WARNING);
    exit;
  }
  $guard = ROOT_EDIT . 'sites/' . SITE_CODE . '/capacitor/';
  if (strpos($from, $guard) != 0) {
    // writeLog('ERROR- createCommonFiles-capacitor-17', "$from is not a guarded route");
    trigger_error("$from is not a guarded route", E_USER_WARNING);
    exit;
  }

  // (A2) CREATE DESTINATION FOLDER
  if (!is_dir($to)) {
    // //writeLogAppend('createCommonFiles-capacitor-42', "creating folder $to");
    createDirectory($to);
  }

  // (A3) COPY FILES + RECURSIVE INTERNAL FOLDERS
  $dir = opendir($from);
  // //writeLogAppend('createCommonFiles-capacitor-43', $from);
  while (($ff = readdir($dir)) !== false) {
    if ($ff != "." && $ff != "..") {
      if (is_dir("$from$ff")) {
        // //writeLogAppend('createCommonFiles-capacitor-47', "$from$ff/  to $to$ff/");
        createCommonFilesCopyFolder("$from$ff/", "$to$ff/");
      } else {
        createDirectory("$to$ff");
        copy("$from$ff", "$to$ff");
        // //writeLogAppend('createCommonFiles-capacitor-50', "$from$ff copied to $to$ff");
      }
    }
  }
  closedir($dir);
}
