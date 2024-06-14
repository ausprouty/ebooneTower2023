<?php
myRequireOnce('publishDestination.php');
myRequireOnce('fileWritePDF.php', 'pdf');

function fileWrite($filename, $text, $p)
{
    //writeLogAppend('fileWrite-6', $filename);

    //make sure publishDestination is in $filename exactly once.
    $p['filename'] = $filename;
    $publishDestination =  publishDestination($p);
    writeLogAppend('fileWrite-11', $filename . '   '. $publishDestination);
    if (strpos($filename,  $publishDestination) === false) {
        //writeLogAppend('fileWrite-13', $filename. '  '.  $publishDestination );
        $filename = $publishDestination . $filename;
    }
    $count = substr_count($filename, $publishDestination);
    for ($i = 1; $i < $count; $i++) {
        $filename = str_ireplace($publishDestination, '', $filename);
    }
    // make sure we have a file to write
    $root_filename = str_ireplace($publishDestination, '', $filename);
    if (strlen($root_filename) < 3) {
        writeLogAppend('ERROR-fileWrite-20', $filename);
        writeLogAppend('ERROR-fileWrite-20', $p);
        return;
    }
    $destination = NULL;
    if (isset($p['destination'])) {
        $destination = $p['destination'];
    }
    if ($destination == 'nojs' ||  $destination == 'pdf') {
        $bad =  $publishDestination . 'content/';
        $filename = str_ireplace($bad, $publishDestination, $filename);
    } else {
        $message = "filename was $filename and destination is "  .  $destination;
    }
    //writeLogAppend('fileWrite-38', $filename);
    $filename = dirMake($filename);
    if (is_dir($filename)) {
        return;
    }
    if ($destination == 'pdf') {
        $output = fileWritePDF($filename, $text);
        return $output;
    }
    if (strpos($filename, 'index')){
        writeLogAppend('WATCH-fileWrite-43', $filename);
    }
    $fh = fopen($filename, 'w');
    if ($fh) {
        fwrite($fh, $text);
        fclose($fh);
    } else {
        $message = 'NOT able to write' .  $filename . ' with destination of ' . $destination;
        writeLogAppend('ERROR-fileWrite-56', $message);
        writeLogAppend('ERROR-fileWrite-56', "$text\n\n");
    }
}
