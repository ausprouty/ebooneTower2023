<?php
// given multiply102  will return 102

function videoFindForCapacitorNewName($filename)
{
  $length = strlen($filename);
  $numeric = FALSE;
  $count = 0;
  while (!$numeric && $count < $length) {
    $count++;
    $character = substr($filename, $count, 1);
    if (is_numeric($character)) {
      $numeric = TRUE;
    }
  }
  $new_name = substr($filename, $count);
  return $new_name;
}
