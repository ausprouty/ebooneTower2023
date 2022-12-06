<?php
/* Do not change 
<a class="bible-readmore" href="https://biblegateway.com/passage/?search=Matthew%202:19-23&amp;version=NIV">

//to 

<a class="bible-readmore" href="https://biblegateway.com/passage/?search=Matthew%202:19-&amp;version=NIV">
*/
function modifyLinksReadmoreBible($text)
{
    return $text;
}
