<?php

myRequireOnce('writeLog.php');
/*filenamestring(80) "/home/globa544/test_staging.mc2.online/content/M2/eng/multiply1/multiply105.html"

We assume filename is inside of content
 /content/M2/eng/multiply1/multiply105.html

 and all refered files are in absolute paths
         /sites/mc2/styles/mc2GLOBAL.css


 And ends up with   ../../../../sites/mc2/styles/mc2GLOBAL.css

 with the exception of the Zoom Routine: 
           regular="/images/zoom/
           zoom="/images/zoom/
*/

function makePathsRelative($text, $filename)
{
   //writeLogDebug('makePathsRelative-17-ZOOM', $text);

   // make sure all paths are absolute first
   $text = str_ireplace('href="sites', 'href="/sites', $text);
   $up = '../';
   $replace = '"';
   $parts = explode('/', $filename);
   $count = substr_count($filename, '/');

   $start = false;
   for ($i = 0; $i < $count; $i++) {
      if ($parts[$i] == 'content' || $parts[$i] == 'nojs') {
         $start = true;
      }
      if ($start) {
         $replace .= $up;
      }
   }
   $text = str_ireplace('"/', $replace, $text);
   // we need to make sure that Zoom images are absolute. Sorry.
   $text = str_ireplace('regular="images/zoom/', 'regular="/images/zoom/', $text);
   $text = str_ireplace('zoom="images/zoom/', 'zoom="/images/zoom/', $text);
   //writeLogDebug('makePathsRelative-36-ZOOM', $text);
   return $text;
}
