<?php
myRequireOnce('bookmark.php');
myRequireOnce('writeLog.php');
myRequireOnce('myGetPrototypeFile.php');

//content is an array of one record content data
// called by Publish Page
function createPage($p, $content)
{
    $debug = '';
    // get bookmark
    if (isset($p['recnum'])) {
        $b['recnum'] = $p['recnum'];
        $b['library_code'] = isset($p['library_code']) ? $p['library_code'] : 'library';
    } else {
        $b = $p;
    }
    $bookmark  = bookmark($b);
    //writeLogDebug('createPage-19', $bookmark);

    $p['selected_css'] = isset($bookmark['book']->style) ? $bookmark['book']->style : STANDARD_CSS;
    if (!isset($bookmark['book']->format)) {
        $debug = 'Bookmark[book]->format not set for  recnum ' . $b['recnum'] . ' in create page with library code ' . $b['library_code'] . "\n";
        $debug .= json_encode($out, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        writeLogError('createPage-20', $debug);
    }
    if ($bookmark['book']->format == 'series') {
        $this_template = myGetPrototypeFile('pageInSeries.html');
        // insert nav bar and set ribbon value and link value
        $nav = myGetPrototypeFile('navRibbon.html');
        $this_template = str_replace('[[nav]]', $nav, $this_template);
        $ribbon = isset($bookmark['library']->format->back_button) ? $bookmark['library']->format->back_button->image : DEFAULT_BACK_RIBBON;
        $navlink = 'index.html';
    }
    // values for page that is not part of a series
    if ($bookmark['book']->format == 'page') {
        $this_template = myGetPrototypeFile('page');
        // insert nav bar
        $nav = myGetPrototypeFile('navRibbon.html');
        $this_template = str_replace('[[nav]]', $nav, $this_template);
        $ribbon = isset($bookmark['library']->format->back_button->image) ? $bookmark['library']->format->back_button->image : DEFAULT_BACK_RIBBON;
        // this will work if there is no special library index.
        $index = 'index.html';
        if ($p['library_code'] != 'library') {

            $index = $p['library_code'] . '.html';
        }
        $navlink = '../' . $index;
        if ($p['destination'] == 'sdcard' || $p['destination'] == 'capacitor') {
            $navlink =   $bookmark['language']->iso .  '-' . $p['library_code'] . '-index';
        }

        $page_text_value = $content['text'];
    }
    //writeLog('createPage-103-debug', $debug);
    if (!isset($this_template)) {
        $debug .= 'FATAL ERROR. No Page Template for recnum' . $p['recnum'] . "\n";
        //writeLog('createPage-77-debug', $debug);
        writeLogError('createPage', $debug);
        return NULL;
    }

    $dir_value = $bookmark['language']->rldir;
    $card_style_value = '/sites/default/styles/cardGLOBAL.css';
    $book_style_value  =  $bookmark['book']->style;
    $page_title_and_image_value = createPageTitle($bookmark, $p);
    $page_text_value = $content['text'];
    $version_value = $p['version'];
    $footer = createLanguageFooter($p); // returns  $footer
    //writeLogDebug('createPage-73', $footer);

    $placeholders = array(
        '{{ dir }}',
        '{{ card.style }}',
        '{{ book.style }}',
        '{{ navlink }}',
        '{{ ribbon }}',
        '{{ page.title_and_image }}',
        '{{ page.text }}',
        '{{ version }}',
        '{{ footer }}'
    ); //
    $replace = array(
        $dir_value,
        $card_style_value,
        $book_style_value,
        $navlink,
        $ribbon,
        $page_title_and_image_value,
        $page_text_value,
        $version_value,
        $footer
    );

    $text = str_replace($placeholders, $replace, $this_template);
    $text = str_replace('{{ dir }}',  $dir_value, $text); // because dir is inside of page_title_and_image_valu
    //writeLogDebug('createPage-100', $text);
    return $text;
}

function createPageTitle($bookmark, $p)
{
    //writeLogDebug('createPageTitle-114', $bookmark);
    if (isset($bookmark['page']->image)) {
        if (isset($bookmark['page']->image->image)) {
            $img = $bookmark['page']->image->image;
            $page_title_and_image_value  = '<img  src ="' . $img  .  '"/>';
            return $page_title_and_image_value;
        }
    }
    $title = $bookmark['page']->title;
    if (isset($bookmark['page']->count)) {
        if ($bookmark['page']->count != '') {
            if ($p['destination'] != 'pdf') {
                $page_title_and_image_value =
                    '<div class="block {{ dir }}">
                        <div class="chapter_number {{ dir }}"><h1>' . $bookmark['page']->count . '.' . '</h1></div>
                        <div class="chapter_title {{ dir }}"><h1>'  . $title . '</h1></div>
                    </div>';
            } else {
                $page_title_and_image_value =
                    '<h1>' . $bookmark['page']->count . '. ' .  $title . '</h1>';
            }
        } else {
            $page_title_and_image_value  = '<h1>' . $title . '</h1>';
        }
    } else {
        $page_title_and_image_value  = '<h1>' . $title . '</h1>';
    }
    return $page_title_and_image_value;
}
/*
 // compute $page_title_and_image_value
     if (isset($bookmark['book']->image->image)){
        $page_image = $bookmark['book']->image->image;
        }
        else{ // legacy data
            $page_image = $bookmark['book']->image;
        }
        $img =  $page_image;

        $page_title_and_image_value .= '<h1>'. $bookmark['book']->title . '</h1>';

*/