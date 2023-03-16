<?php
myRequireOnce('myGetPrototypeFile.php');
function getPrototypeFileLibrary($p)
{

    if ($p['country_code'] == 'AU') {
        $body = myGetPrototypeFile('library.html');
    } else {
        $body = myGetPrototypeFile('libraryNotAU.html');
    }
    return  $body;
}
