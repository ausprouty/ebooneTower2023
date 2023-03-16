<?php
/* Here we copy the files that all capacitors need, but are not sourced from content direcly
/sites/mc2/images/css ->mc2.capacitor.something/folder/sites/mc2/images/css
/sites/mc2/images/standard -> mc2.capacitor.something/folder/sites/mc2/images/standard
/sites/default/capacitor/Cx File Explorer.apk-> mc2.capacitor.something/Cx File Explorer.apk
/langugage javascript folders
*/
myRequireOnce('copyDirectory.php');
myRequireOnce('verifyBookDir.php');

function verifyCommonFiles($p)
{
  $subdirectory =  _verifyBookClean($p['capacitor_settings']->subDirectory);
  $p['dir_capacitor'] = ROOT_CAPACITOR . $subdirectory . '/';;
  // css
  $source = ROOT_EDIT . 'sites/default/images/css/';
  $destination = $p['dir_capacitor'] . 'folder/sites/default/images/css/';
  copyDirectory($source, $destination);

  $source = ROOT_EDIT . 'sites/' . SITE_CODE . '/images/css/';
  $destination = $p['dir_capacitor'] . 'folder/sites/' . SITE_CODE . '/images/css/';
  copyDirectory($source, $destination);

  // standrd images
  $source = ROOT_EDIT . 'sites/' . SITE_CODE . '/images/standard/';
  $destination = $p['dir_capacitor'] . 'folder/sites/' . SITE_CODE . '/images/standard/';
  copyDirectory($source, $destination);

  $source = ROOT_EDIT . 'sites/' . SITE_CODE . '/images/menu/';
  $destination = $p['dir_capacitor'] . 'folder/sites/' . SITE_CODE . '/images/menu/';
  copyDirectory($source, $destination);

  $source = ROOT_EDIT . 'sites/' . SITE_CODE . '/images/standard/';
  $destination = $p['dir_capacitor'] . 'folder/sites/' . SITE_CODE . '/images/standard/';
  copyDirectory($source, $destination);

  $source = ROOT_EDIT . 'sites/' . SITE_CODE . '/content/' . $p['country_code'] . '/images/standard/';
  $destination = $p['dir_capacitor'] . 'folder/sites/' . SITE_CODE . '/' . $p['country_code'] . '/images/standard/';
  copyDirectory($source, $destination);


  // javascript

  $languages = explode('.',  $subdirectory);
  foreach ($languages as $language_code) {
    if ($language_code != '') {
      $source = ROOT_EDIT . 'sites/' . SITE_CODE . '/content/' . $p['country_code'] . '/' . $language_code . '/javascript/';
      $destination = $p['dir_capacitor'] . 'folder/content/' . $p['country_code'] . '/' . $language_code . '/javascript/';
      writeLogAppend('verifyCommonFiles-50', "$source\n$destination \n\n");
      copyDirectory($source, $destination);
    }
  }
  // apk
  $source = ROOT_EDIT . 'sites/default/capacitor/Cx File Explorer.apk';
  $destination = $p['dir_capacitor'] . 'Cx File Explorer.apk';
  copy($source, $destination);

  $source = ROOT_EDIT . 'sites/' . SITE_CODE . '/prototype/capacitor/' .  SITE_CODE . '.html';
  $destination = $p['dir_capacitor'] . SITE_CODE . '.html';
  copy($source, $destination);

  return true;
}
