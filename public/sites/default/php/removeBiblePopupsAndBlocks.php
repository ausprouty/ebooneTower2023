<?php

/*
Change 

    <a href="javascript:popUp('pop2')">John 10:10</a>)&nbsp;
    <div class="popup" id="pop2"><!-- begin bible -->
    <p><sup class="versenum">10&nbsp;</sup>The thief comes only to steal and kill and destroy; I have come that they may have life, and have it to the full.</p>
    <!-- end bible --></div>

    to
    <span class="bible-link">John 10:10</span>

    */
myRequireOnce('removeBiblePopups.php');
myRequireOnce('create.php');66]\
myRequireOnce('getLatestContent.php');

function removeBiblePopupsAndBlocks($p)
{
    $p['text'] = removeBiblePopups($p['text']);
    createContent($p);
    $p['scope'] = 'page';
    unset($p['recnum']);
    $out = getLatestContent($p);
    return $out;
}
