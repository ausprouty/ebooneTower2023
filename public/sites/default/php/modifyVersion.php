<?php

myRequireOnce('write.Log');
// find all .css and .js  and create version based on time created
// called by publishFiles

function modifyVersion($text, $fname)
{
    $text = modifyVersionStyles($text);
    $text = modifyVersionJavascripts($text);
    $text = modifyVersionImages($text);
    return $text;
}

function modifyVersionStyles($text)
{
    if (strpos($text, '<link') == false) {
        return $text;
    }
    $styles = [];
    $root = substr(ROOT_EDIT, 0, -1);  // need to take off / at end
    $doc = new DOMDocument();
    libxml_use_internal_errors(true);
    $doc->loadHTML($text);
    $domcss = $doc->getElementsByTagName('link');
    foreach ($domcss as $links) {
        if (strtolower($links->getAttribute('rel')) == "stylesheet") {
            // this gets rid of duplicates
            // produces /sites/default/styles/appGLOBAL.css
            //define("ROOT_EDIT", '/home2/citylead/public_html/launch-edit/')
            $link = $links->getAttribute('href');
            $styles[$link] =  $root . $link;
        }
    }
    libxml_clear_errors();
    $text = modifyVersionUpdate($text, $styles);
    return $text;
}

function modifyVersionJavascripts($text)
{
    if (strpos($text, '<script') == false) {
        return $text;
    }
    $javascripts = [];
    $root = substr(ROOT_EDIT, 0, -1);  // need to take off / at end
    $doc = new DOMDocument();
    libxml_use_internal_errors(true);
    $doc->loadHTML($text);
    $domjava = $doc->getElementsByTagName('script');
    foreach ($domjava as $java) {
        $script = $java->getAttribute('src');
        if (strpos($script, '?') == false && $script != '' && strpos($script, 'http') === false) {
            $javascripts[$script] =  $root . $script;
        }
    }
    libxml_clear_errors();
    $text = modifyVersionUpdate($text, $javascripts);
    return $text;
}

function modifyVersionImages($text)
{
    if (strpos($text, '<img') == false) {
        return $text;
    }
    $images = [];
    $root = substr(ROOT_EDIT, 0, -1);  // need to take off / at end
    $doc = new DOMDocument();
    libxml_use_internal_errors(true);
    $doc->loadHTML($text);
    $domimage = $doc->getElementsByTagName('img');
    foreach ($domimage as $image) {
        $img = $image->getAttribute('src');
        if (strpos($img, '?') == false && $img != '' && strpos($img, 'http') === false) {
            $images[$img] =  $root . $img;
        }
    }
    libxml_clear_errors();
    $text = modifyVersionUpdate($text, $images);
    return $text;
}

function modifyVersionUpdate($text, $files)
{
    foreach ($files as $key => $file_name) {
        if (file_exists($file_name)) {
            $version = filemtime($file_name);
            if (!$version) {
                $version = time();
            }
            $versioned = $key . '?v=' . $version;
            $text = str_replace($key, $versioned, $text);
        }
    }
    return $text;
}
