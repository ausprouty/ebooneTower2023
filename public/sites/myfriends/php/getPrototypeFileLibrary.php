<?php
myRequireOnce('myGetPrototypeFile.php');
// Australia and Austria have stacked libraries that will need navigation
function getPrototypeFileLibrary($p)
{
    if ($p['country_code'] == 'AU' || $p['country_code'] == 'AT') {
        $body = myGetPrototypeFile('library.html');
    } else {
        $body = myGetPrototypeFile('libraryNotAU.html');
    }
    return  $body;
}
