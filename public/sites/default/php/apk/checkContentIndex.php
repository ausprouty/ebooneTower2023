<?php


myRequireOnce('getBuild.php');
myRequireOnce('writeLog.php');
myRequireOnce('findLibraries.php');

function checkContentIndex($p)
{
  $build = getBuild($p);
  $p['dir_apk'] = ROOT_APK .  $build . '/';

  $file = $p['dir_apk'] . 'index.html';
  if (!file_exists($file)) {
    //writeLogDebug('apk-('checkContentIndex-15', $file);
    return 'undone';
  }
  $libraries = findLibraries($p);
  foreach ($libraries as $library) {
    if ($library == 'library') {
      $library = 'index';
    }
    $file = $p['dir_apk'] . 'folder/content/' . $p['country_code'] . '/' .  $p['language_iso'] . '/' . $library . '.html';

    if (!file_exists($file)) {
      //writeLogDebug('apk-('checkContentIndex-25', $file);
      return 'undone';
    }
  }

  return 'done';
}
