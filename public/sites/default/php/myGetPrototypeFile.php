<?php
myRequireOnce('writeLog.php');
// look in subdirectory first.  If not there look in site directory
//define("ROOT_EDIT", '/home/globa544/edit.mc2.online/');
// define("SITE_CODE", 'mc2');
function myGetPrototypeFile($filename, $subdirectory = null, $language_iso = null)
{
    $message = array(
        'filename' => $filename,
        'subdirectory' => $subdirectory,
        'language_iso' => $language_iso
    );
    writeLogAppend('myGetPrototypeFile-12', $message);
    $filename = _cleanMyGetPrototypeFile($filename);
    if ($language_iso) {
        $language_iso = _cleanMyGetPrototypeSubdirectory($language_iso);
        if (!$subdirectory) {
            $my_prototype = ROOT_EDIT . 'sites/' . SITE_CODE . '/prototype/' . $language_iso . '/' . $filename;
            if (file_exists($my_prototype) && !is_dir($my_prototype)) {
                return file_get_contents($my_prototype);
            }
        }
        if ($subdirectory) {
            $subdirectory = _cleanMyGetPrototypeSubdirectory($subdirectory);
            $my_prototype = ROOT_EDIT . 'sites/' . SITE_CODE . '/prototype/' . $subdirectory . '/' . $language_iso . '/' . $filename;
            if (file_exists($my_prototype) && !is_dir($my_prototype)) {
                return file_get_contents($my_prototype);
            }
        }
    }

    if ($subdirectory) {
        $subdirectory = _cleanMyGetPrototypeSubdirectory($subdirectory);
        $my_prototype = ROOT_EDIT . 'sites/' . SITE_CODE . '/prototype/' . $subdirectory . '/' . $filename;
        if (file_exists($my_prototype) && !is_dir($my_prototype)) {
            //_appendMyGetPrototypeFile('myGetPrototypeFile', 'Used ' . $my_prototype . "\n");
            return file_get_contents($my_prototype);
        }
        $my_prototype = ROOT_EDIT . 'sites/default/prototype/' . $subdirectory . '/' . $filename;
        if (file_exists($my_prototype) && !is_dir($my_prototype)) {
            //_appendMyGetPrototypeFile('myGetPrototypeFile', 'Used ' . $my_prototype . "\n");
            return file_get_contents($my_prototype);
        }
    }

    $my_prototype = ROOT_EDIT . 'sites/' . SITE_CODE . '/prototype/' . $filename;
    if (file_exists($my_prototype) && !is_dir($my_prototype)) {
        // _appendMyGetPrototypeFile('myGetPrototypeFile', 'Used ' . $my_prototype . "\n");
        return file_get_contents($my_prototype);
    }
    $prototype =  ROOT_EDIT . 'sites/default/prototype/' . $filename;
    if (file_exists($prototype) && !is_dir($my_prototype)) {
        //('myGetPrototypeFile', 'Used ' . $prototype . "\n");
        return file_get_contents($prototype);
    }
    return null;
}

function _cleanMyGetPrototypeFile($page)
{
    $bad = array('..', '$', '/');
    $page = str_replace($bad, '', $page);
    return $page;
}
function _cleanMyGetPrototypeSubdirectory($page)
{
    $bad = array('.', '$', '/');
    $page = str_replace($bad, '', $page);
    return $page;
}
function _appendMyGetPrototypeFile($filename, $content)
{

    $root_log = ROOT_LOG;
    if (!is_array($content)) {
        $text = $content;
    } else {
        $text = '';
        foreach ($content as $key => $value) {
            $text .= $key . ' => ' . $value . "\n";
        }
    }
    $fh = $root_log . $filename . '.txt';
    file_put_contents($fh, $text,  FILE_APPEND | LOCK_EX);
}
