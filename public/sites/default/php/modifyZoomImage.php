<?php

/*
input:
<span class="zoom">
<img alt="Four Fields"
    src="https://launch-prototype.sent67.com/sites/launch/content/U1/eng/images/standard/FourFields.png"
     style="width:100%" />
</span>

output;
---------------once only --------------------
<script src="/sites/launch/javascript/pinch-zoom.js"></script>
<script src="/sites/launch/javascript/myZoom.js"></script>
<div class="page pinch-zoom-parent offscreen" id="pinch-zoom-parent">
<div class="pinch-zoom-close" onclick="zoomClose()"><img class="close" src="/sites/launch/images/standard/close.png" /></div>
<div class="pinch-zoom">
<div><img alt="Zoom" id="pinch-zoom-image"  src="/sites/launch/content/U1/eng/images/intro/intro2.png" /></div>
</div>
</div>
------------for each image---------------------
<div onclick="zoomShow('/sites/launch/content/U1/eng/images/intro/intro2.png')"><img src="/sites/launch/content/U1/eng/images/intro/intro2.png" /></div>

*/

function modifyZoomImage($text){
    $template_top ='
        <script src="/sites/default/javascript/pinch-zoom.js"></script>
        <script src="/sites/default/javascript/myZoom.js"></script>
        <div class="page pinch-zoom-parent offscreen" id="pinch-zoom-parent">
            <div class="pinch-zoom-close" onclick="zoomClose()">
                <input type="hidden" id="pinch-zoom-id" value="0">
                <img class="close" src="/sites/default/images/close.png" />
            </div>
            <div class="pinch-zoom">
                <div><img  id="pinch-zoom-image"  src="[image]" /></div>
            </div>
        </div>';
    $template_image = '
        <div id = "pinch-zoom[id]" onclick="zoomShow(\'[id]\', \'[image]\', )">
             <img  alt="[alt]" src="[image]" />
        </div>';
    $find_start = '<span class="zoom">';
    $find_end = '</span>';
    $count = substr_count($text, $find_start);
    for ($i = 0; $i < $count; $i++){
        $pos_start = strpos($text, $find_start);
        $pos_end = strpos($text, $find_end, $pos_start);
        $length = $pos_end - $pos_start + strlen($find_end);
        $old = substr($text, $pos_start, $length);
        $new ='';
        if ($i == 0){
            $new =$template_top;
        }
        $new .= $template_image;
        $new = str_replace('[id]',  $i , $new);
        $new = str_replace('[alt]',  modifyZoomImageGetAlt($old) , $new);
        $new = str_replace('[image]',  modifyZoomImageGetImage($old) , $new);
        $text = substr_replace($text, $new, $pos_start, $length);
    }
return $text;
}
/*
input:
<span class="zoom">
<img alt="Four Fields"
    src="https://launch-prototype.sent67.com/sites/launch/content/U1/eng/images/standard/FourFields.png"
     style="width:100%" />
</span>
*/
function modifyZoomImageGetAlt($text){
    $find_start = '<img alt="';
    $find_end = '"';
    $pos_start = strpos($text, $find_start );
    $pos_end = strpos($text, $find_end, $pos_start + strlen($find_start));
    $length = $pos_end - $pos_start;
    $alt = substr($text, $pos_start, $length);
    return $alt;

}

function modifyZoomImageGetImage($text){
    $find_start = 'src="';
    $find_end = '"';
    $pos_start = strpos($text, $find_start) + strlen($find_start);
    $pos_end = strpos($text, $find_end, $pos_start);
    $length = $pos_end - $pos_start ;
    $image= substr($text, $pos_start, $length);
    return $image;
}