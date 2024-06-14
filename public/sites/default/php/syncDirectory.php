<?php
myRequireOnce('writeLog.php');
function syncDirectory($source, $destination){ 
    // Function to get all files in a directory with their modification times
    // Get files from directories A and B
    $filesA = syncDirectoryGetFiles($source);
    writeLogDebug('syncDirectory-8', $filesA);
    $filesB = syncDirectoryGetFiles($destination);
    writeLogDebug('syncDirectory-10', $filesB);
    // Update directory B based on directory A
    foreach ($filesA as $relativePath => $mtimeA) {
        $sourceFile = $source . '/' . $relativePath;
        $destFile = $destination . '/' . $relativePath;

        // Check if the file exists in directory B
        if (!isset($filesB[$relativePath]) || $filesB[$relativePath] < $mtimeA) {
            // Create the destination directory if it doesn't exist
            $destDir = dirname($destFile);
            if (!is_dir($destDir)) {
                mkdir($destDir, 0777, true);
            }
            // Copy or update the file
            if (copy($sourceFile, $destFile)) {
                writeLogAppend('syncDirectory-22', "File copied: $relativePath") ;
            } else {
                writeLogAppend('syncDirectory-25', "Failed to copy: $destFile") ;
            }
        }
        else{
            writeLogAppend('syncDirectory-31', "File copy not needed: $relativePath") ;
        }
    }
    writeLogAppend('syncDirectory-33', "About to return from syncDirectory") ;
    return;
}
function syncDirectoryGetFiles($dir) {
    $files = [];
    // Check if the directory exists
    if (!is_dir($dir)) {
        return $files;
    }
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($iterator as $file) {
        if ($file->isFile()) {
            $relativePath = substr($file->getPathname(), strlen($dir) + 1);
            $files[$relativePath] = $file->getMTime();
        }
    }
    return $files;
}
?>
