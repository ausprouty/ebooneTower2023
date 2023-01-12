<?php
function version2Text($text)
{
   $text = str_ireplace('Notes: (click outside box to save)<br />', '', $text);
   $text = str_ireplace('“I will by when”statements', '“I will by when” statements', $text);
   $text = str_ireplace('#f1c40f', '#f9b625', $text);
   return $text;
}
