<?php

function editTemplate($p)
{
  if (empty($p['language_iso'])) {
    return ['status' => 'error', 'error' => 'language_iso not set', 'result' => null];
  }
  if (empty($p['template'])) {
    return ['status' => 'error', 'error' => 'template not set', 'result' => null];
  }
  if (empty($p['text'])) {
    return ['status' => 'error', 'error' => 'text not set', 'result' => null];
  }
  if (empty($p['book_format'])) {
    return ['status' => 'error', 'error' => 'book_format not set', 'result' => null];
  }
  writeLogDebug("editTemplate-17", 'OK');
  // Prevent path traversal
  if (strpos($p['template'], '..') !== false) {
    return ['status' => 'error', 'error' => 'Invalid template path', 'result' => null];
  }

  // Ensure .html extension
  if (strpos($p['template'], '.html') === false) {
    $p['template'] .= '.html';
  }
  writeLogDebug("editTemplate-27", 'OK');
  // Base directory: e.g. ROOT/edit/country/lang/templates/
  $template_dir = dirStandard('language', 'edit', $p, 'templates/');

  // Final full path
  $full_path = $template_dir . $p['template'];
  writeLogDebug("editTemplate-33", $full_path);
  // Create any subdirectories if needed
  $subdir = dirname($full_path);
  writeLogDebug("editTemplate-36", $subdir);
  if (!is_dir($subdir)) {
    if (!mkdir($subdir, 0755, true)) {
      return ['status' => 'error', 'error' => "Failed to create subdirectory: $subdir", 'result' => null];
    }
  }
  writeLogDebug("editTemplate-42", $full_path);
  // Try writing file
  try {
    if (file_put_contents($full_path, $p['text']) === false) {
      return ['status' => 'error', 'error' => "Failed to write file: $full_path", 'result' => null];
    }
  } catch (Exception $e) {
    return ['status' => 'error', 'error' => $e->getMessage(), 'result' => null];
  }

  // All good
  writeLogDebug("editTemplate-53", 'OK');
  return ['status' => 'ok', 'error' => null, 'result' => ['written_to' => $full_path]];
}
