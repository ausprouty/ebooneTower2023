<?php
myRequireOnce('bookmark.php');
myRequireOnce('publishFiles.php');
myRequireOnce('writeLog.php');
myRequireOnce('myGetPrototypeFile.php');
myRequireOnce('createSeriesNavlink.php');



function createSeries($p, $data)
{

    //writeLogDebug('createSeries-13', $p);
    $text = json_decode($data['text']);
    writeLogDebug('createSeries-15', $text);
    // get language footer in prototypeOEpublish.php
    $footer = createLanguageFooter($p); // returns $footer
    if (isset($p['recnum'])) {
        $b['recnum'] = $p['recnum'];
        $b['library_code'] = $p['library_code'];
    } else {
        $b = $p;
    }
    $bookmark  = bookmark($b);
    writeLogDebug('createSeries-25', $bookmark);
    $selected_css = isset($bookmark['book']->style) ? $bookmark['book']->style : STANDARD_CSS;
    writeLogDebug('createSeries-26',  $p);
    // replace placeholders in template

    $this_template = myGetPrototypeFile('series');
    $nav = myGetPrototypeFile('navRibbon.html');
    $this_template = str_replace('[[nav]]', $nav, $this_template);
    

    //set placeholders
    $placeholders = array(
        '{{ language.rldir }}',
        '{{ book.style }}',
        '{{ navlink }}',
        '{{ ribbon }}',
        '{{ book.image }}',
        '{{ download_ready }}',
        '{{ book.title }}',
        '{{ book.description }}',
        '{{ json }}',
        '{{ download_now }}',
        '{{ version }}',
        '{{ footer }}'
    );
    $navlink = createSeriesNavlink($p);
    writeLogDebug('createSeries-50',  $navlink);

    $download_ready = isset($bookmark['language']->download_ready) ? $bookmark['language']->download_ready : 'Ready for Offline Use';
    $download_now = isset($bookmark['language']->download) ? $bookmark['language']->download : 'Download for Offline Use';
    $description = isset($text->description) ? $text->description : NULL;
    $ribbon = isset($bookmark['library']->format->back_button) ? $bookmark['library']->format->back_button->image : DEFAULT_BACK_RIBBON;
    $language_dir = '/content/' . $data['country_code'] . '/' . $data['language_iso'] . '/' . $data['folder_name'] . '/';
    // dealing with legacy data
    if (isset($bookmark['book']->image->image)) {
        $book_image = $bookmark['book']->image->image;
    } else {
        $book_image =  '/content/' . $bookmark['language']->image_dir . '/' . $bookmark['book']->image;
    }
    $book_title = '';
    if ($bookmark['language']->titles == false) {
        $book_title = $bookmark['book']->title;
    }
    // get language footer in prototypeOEpublish.php
    $footer = createLanguageFooter($p); // returns  $footer
    //
    //writeLog('createSeries-71-navlink', $navlink);
    $replace = array(
        $bookmark['language']->rldir,
        $bookmark['book']->style,
        $navlink,
        $ribbon,
        $book_image,
        $download_ready,
        $book_title,
        $description,
        $language_dir . 'files.json',  // but his will be morphed to /sites/mc2/content
        $download_now,
        $p['version'],
        $footer
    );
    // //writeLogDebug('createSeries-94', $this_template);
    $this_template = str_replace($placeholders, $replace, $this_template);

    //
    // get chapter template
    //
    $chapterText_template = myGetPrototypeFile('chapterText.html');
    $chapterImage_template = myGetPrototypeFile('chapterImage.html');
    $placeholders = array(
        '{{ link }}',
        '{{ language.rldir }}',
        '{{ chapter.title }}',
        '{{ chapter.description }}',
        '{{ chapter.image }}',
        '{{ language.rldir }}'
    );
    //
    // replace for each chapter
    //
    $chapters_text = '';
    if (isset($text->chapters)) {
        foreach ($text->chapters as $chapter) {
            $status = false;
            if ($p['destination'] == 'staging') {
                if (isset($chapter->prototype)) {
                    $status = $chapter->prototype;
                }
            } else {
                $status = $chapter->publish;
            }
            //_write_series_log($p, $chapter);
            if ($status  == true) { // we only want to process those with this as true
                $image = null;
                if (isset($chapter->image)) {
                    if ($chapter->image != '') {
                        $image = '/content/' . $bookmark['language']->folder
                            . '/' . $bookmark['book']->code . '/' . $chapter->image;
                    }
                }
                $description = isset($chapter->description) ? $chapter->description : null;
                $title = $chapter->title;
                if ($chapter->count) {
                    // $title = $chapter->count . '. '. $chapter->title;
                    $title = '
                    <!-- chapter title begin -->
                        <div class="block {{ language.rldir }}">
                            <div class="chapter_number series {{ language.rldir }}">' .  $chapter->count . '.' . '</div>
                            <div class="chapter_title series {{ language.rldir }}">'  . $chapter->title . '</div>
                        </div>
                    <!-- chapter title end -->' . "\n";
                }

                $link =  $chapter->filename . '.html';


                $replace = array(
                    $link,
                    $bookmark['language']->rldir,
                    $title,
                    $description,
                    $image,
                    $bookmark['language']->rldir,
                );
                if ($image) {
                    $chapters_text .= str_replace($placeholders, $replace, $chapterImage_template);
                } else {
                    $chapters_text .= str_replace($placeholders, $replace, $chapterText_template);
                }
            }
        }
    }
    $out['text'] = str_replace('[[chapters]]', $chapters_text, $this_template);
    $out['p'] = $p;
    //writeLogDebug('createSeries-161', $out);
    return $out;
}
