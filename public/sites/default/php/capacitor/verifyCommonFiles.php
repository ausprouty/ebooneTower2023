<?php
/* Here we copy the files that all capacitors need, but are not sourced from content direcly
ROOT_EDIT/sites/SITE_CODE/capacitor-> ROOT_SDCARD/language_iso/
*/
myRequireOnce('writeLog.php');
myRequireOnce('createDirectory');
myRequireOnce('copyDirectory.php');


function verifyCommonFiles($p)
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
  $progress->common_files = verifyCommonFilesCopyFolder($from, $to);
  return $progress;
}

function verifyCommonFilesCopyFolder($from, $to)
{
  $progress = new Progress();
  // //writeLogAppend('verifyCommonFiles-capacitor-21', "$from to $to");

  // (A1) SOURCE FOLDER CHECK
  if (!is_dir($from)) {
    // writeLog('ERROR- verifyCommonFiles-capacitor-17', "$from does not exist");
    //trigger_error("$from does not exist", E_USER_WARNING);
    $progress->common_files->progress = 'error';
    $progress->common_files->message = "$from does not exist";
    return $progress;
  }
  $guard = ROOT_EDIT . 'sites/' . SITE_CODE . '/capacitor/';
  if (strpos($from, $guard) != 0) {
    // writeLog('ERROR- verifyCommonFiles-capacitor-17', "$from is not a guarded route");
    //trigger_error("$from is not a guarded route", E_USER_WARNING);
    $progress->common_files->progress = 'error';
    $progress->common_files->message = "$from is not a guarded route";
    return $progress;
  }

  // (A2) CREATE DESTINATION FOLDER
  if (!is_dir($to)) {
    $progress->common_files->progress = 'undone';
    $progress->common_files->message = "$to is not created";
    return $progress;
  }

  // (A3) COPY FILES + RECURSIVE INTERNAL FOLDERS
  $dir = opendir($from);
  // //writeLogAppend('verifyCommonFiles-capacitor-43', $from);
  while (($ff = readdir($dir)) !== false) {
    if ($ff != "." && $ff != "..") {
      if (is_dir("$from$ff")) {
        // //writeLogAppend('verifyCommonFiles-capacitor-47', "$from$ff/  to $to$ff/");
        //verifyCommonFilesCopyFolder("$from$ff/", "$to$ff/");
      } else {
        //createDirectory("$to$ff");
        //copy("$from$ff", "$to$ff");
        // //writeLogAppend('verifyCommonFiles-capacitor-50', "$from$ff copied to $to$ff");
        $progress->common_files->progress = 'undone';
        $progress->common_files->message = "$from$ff is not created";
        return $progress;
      }
    }
  }
  closedir($dir);
  $progress->common_files->progress = 'done';
  $progress->common_files->message = "No errors";
  return $progress;
}
