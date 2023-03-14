<?php
myRequireOnce('myGetPrototypeFile.php');
function getPrototypeFileLibrary($p)
{

    $body = myGetPrototypeFile('library.vue', $p['destination']);

    return  $body;
}
