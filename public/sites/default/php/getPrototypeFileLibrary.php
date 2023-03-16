<?php
myRequireOnce('myGetPrototypeFile.php');
function getPrototypeFileLibrary($p)
{

    $body = myGetPrototypeFile('library');

    return  $body;
}
