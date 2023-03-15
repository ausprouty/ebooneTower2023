<?php
myRequireOnce(DESTINATION, 'verifyBookDir', 'sdcard');
function copySDCardCover($p)
{
     $p = verifyBookDir($p); // set $p['dir_sdcard']
     return;
}
