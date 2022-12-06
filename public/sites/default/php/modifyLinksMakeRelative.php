<?php
// '/sites/mc2/content/M2/eng/tc/tc01.html'
//      to
//    '../tc/tc01.html'

function modifyLinksMakeRelative($link)
{
    $parts = explode('/', $link);
    $filename = array_pop($parts);
    $directory = array_pop($parts);
    $new = '../' . $directory . '/' . $filename;
    return $new;
}
