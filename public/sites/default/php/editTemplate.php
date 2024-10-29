<?php
myRequireOnce('dirStandard.php');
myRequireOnce('fileWrite.php');
myRequireOnce('writeLog.php');

// use country and language
// this looks for template in the language/templates directory

function editTemplate($p)
{
  $debug = 'getTemplate' . "\n";
  if (!$p['language_iso']) {
    $message = "language_iso not set";
    writeLogError('editTemplate-14', $message);
    return false;
  }
  if (!$p['template']) {
    $message = "template not set";
    writeLogError('editTemplate-19', $message);
    return false;
  }
  if (!$p['text']) {
    $message = "text not set";
    writeLogError('editTemplate-24', $message);
    return false;
  }
  if (!$p['book_format']) {
    $message = "book_format not set";
    writeLogError('editTemplate-29', $message);
    return false;
  }
  // $template_dir = ROOT_EDIT_CONTENT . $p['country_code'] .'/'. $p['language_iso'] .'/templates/';
  $template_dir = dirStandard('language', 'edit',  $p,  'templates/');

  // make sure this is an html file
  if (strpos($p['template'], '.html') === FALSE) {
    $p['template'] .= '.html';
  }
  writeLogDebug ('editTemplate-38', $template_dir);
  writeLogDebug ('editTemplate-39', is_dir($template_dir));
  $filename = $template_dir . $p['template'];
  writeLogDebug('editTemplate-40', $filename);
  writeLogDebug('editTemplate-41', $p['text']);
  // Open the file for writing or appending
  $mode = 'w';
  $file = fopen($filename, $mode);
  if ($file === false) {
      throw new Exception("Unable to open file: $filename");
  }

  // Write the text to the file
  fwrite($file, $p['text']);

  // Close the file
  fclose($file);
  return 'success';
}
