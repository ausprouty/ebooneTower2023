<?php
/*
Input: 
<div class="reveal bible">&nbsp;
<hr />
<p>John 10:22-30</p>

<p><sup>22&nbsp;</sup>Then came the Festival of Dedication at Jerusalem. It was winter,<sup class="versenum">23&nbsp;</sup>and Jesus was in the temple courts walking in Solomon&rsquo;s Colonnade.<sup class="versenum">24&nbsp;</sup>The Jews who were there gathered around him, saying, &ldquo;How long will you keep us in suspense? If you are the Messiah, tell us plainly.&rdquo;</p>

<p><sup class="versenum">25&nbsp;</sup>Jesus answered, &ldquo;I did tell you, but you do not believe. The works I do in my Father&rsquo;s name testify about me,<sup class="versenum">26&nbsp;</sup>but you do not believe because you are not my sheep.<sup class="versenum">27&nbsp;</sup>My sheep listen to my voice; I know them, and they follow me.<sup class="versenum">28&nbsp;</sup>I give them eternal life, and they shall never perish; no one will snatch them out of my hand.<sup class="versenum">29&nbsp;</sup>My Father, who has given them to me, is greater than all; no one can snatch them out of my Father&rsquo;s hand.<sup class="versenum">30&nbsp;</sup>I and the Father are one.&rdquo;</p>

<p><!-- end bible --><a class="readmore" href="https://biblegateway.com/passage/?search=John%2010:22-30&amp;version=NIV" target="_blank">Read More </a></p>

<hr /></div>

Output:

<p>[BibleBlock]</p>

*/
function removeBiblePassage($text){
    $find_begin = '<div class="reveal bible">';
    $find_end = '<hr /></div>';
    $count = substr_count($text, $find_begin);
    $new ='[BibleBlock]';
    for ($i = 0; $i < $count; $i++){
        $pos_start = strpos($text,$find_begin) ;
        $pos_end = strpos($text, $find_end, $pos_start) + strlen($find_end);
        $length = $pos_end- $pos_start;
        $text = substr_replace($text, $new, $pos_start, $length);
    }
    return $text;
}

/*
Input:

<div id="bible">
<div class="bible">
<p class="myfriends-reference">Matth&auml;us 7, 7-11</p>

<p><sup>7</sup>&raquo;Bittet, und es wird euch gegeben; sucht, und ihr werdet finden; klopft an, und es wird euch ge&ouml;ffnet.<sup>8</sup>Denn jeder, der bittet, empf&auml;ngt, und wer sucht, findet, und wer anklopft, dem wird ge&ouml;ffnet.<sup>9</sup>Oder w&uuml;rde jemand unter euch seinem Kind einen Stein geben, wenn es ihn um Brot bittet?<sup>10</sup>W&uuml;rde er ihm eine Schlange geben, wenn es ihn um einen Fisch bittet?<sup>11</sup>Wenn also ihr, die ihr doch b&ouml;se seid, das n&ouml;tige Verst&auml;ndnis habt, um euren Kindern gute Dinge zu geben, wie viel mehr wird dann euer Vater im Himmel denen Gutes geben, die ihn darum bitten.&laquo;</p>
<a class="readmore" href="https://biblegateway.com/passage/?search=Matthew%207:7-11&amp;version=NGU-DE">Link zur Textquelle</a></div>
</div>

*/
function removeBibleDiv($text){
    $find_begin = '<div id="bible">';
    $find_end = '</div>';
    $count = substr_count($text, $find_begin);
    $new ='[BibleBlock]';
    for ($i = 0; $i < $count; $i++){
        $pos_start = strpos($text,$find_begin) ;
        $pos_mid =  strpos($text, $find_end, $pos_start);
        $pos_end = strpos($text, $find_end, $pos_mid) + strlen($find_end);
        $length = $pos_end- $pos_start;
        $text = substr_replace($text, $new, $pos_start, $length);
    }
    return $text;


}