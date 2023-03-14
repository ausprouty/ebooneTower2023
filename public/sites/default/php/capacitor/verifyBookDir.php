<?php

function verifyBookDir($p)
{
  if (!isset($p['capacitor_settings'])) {
    $message = 'No capacitor_settings in verifyBookDir';
    trigger_error($message, E_USER_ERROR);
  }
  $p['dir_capacitor'] = ROOT_CAPACITOR . _verifyBookClean($p['capacitor_settings']->subDirectory) . '/';
  $p['dir_video_list'] = ROOT_EDIT . 'sites/' . SITE_CODE . '/capacitor/' . $p['country_code'] . '/' . $p['language_iso'] . '/';
  $p['dir_series'] =  $p['country_code'] . '/' . $p['language_iso'] . '/' . $p['folder_name'];
  return $p;
}
function _verifyBookClean($dir_capacitor)
{
  $bad = ['/' . '..'];
  $clean = str_replace($bad, '', $dir_capacitor);
  return $clean;
}
