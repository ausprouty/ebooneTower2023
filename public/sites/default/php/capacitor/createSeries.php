<?php
myRequireOnce('bookmark.php');
myRequireOnce('writeLog.php');
myRequireOnce('myGetPrototypeFile.php');
myRequireOnce('createSeriesNavlink.php');
myRequireOnce('modifyTextForVue.php');
myRequireOnce('progressMerge.php');



function createSeries($p, $data)
{
    $progress = new stdClass;
    //writeLogDebug('capacitor-createSeries-12', $p);
    $text = json_decode($data['text']);
    // get language footer in prototypeOEpublish.php
    $footer = createLanguageFooter($p); // returns $footer
    if (isset($p['recnum'])) {
        $b['recnum'] = $p['recnum'];
        $b['library_code'] = $p['library_code'];
    } else {
        $b = $p;
    }
    $bookmark  = bookmark($b);
    //writeLogDebug('createSeries21', $bookmark);
    $selected_css = isset($bookmark['book']->style) ? $bookmark['book']->style : STANDARD_CSS;
    //writeLogDebug('createSeries-23',  $p['destination']);
    // replace placeholders in template
    // Note: Current  is unique in that only has one book in the series.
    if ($data['folder_name'] == 'current' || $data['folder_name'] == 'youth-basics') {
        $this_template = myGetPrototypeFile('seriesCurrent.html');
    } else {
        $this_template = myGetPrototypeFile('series');
        $nav = myGetPrototypeFile('navRibbon.html');
        $this_template = str_replace('[[nav]]', $nav, $this_template);
    }

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

    $download_ready = isset($bookmark['language']->download_ready) ? $bookmark['language']->download_ready : 'Ready for Offline Use';
    $download_now = isset($bookmark['language']->download) ? $bookmark['language']->download : 'Download for Offline Use';
    $description = isset($text->description) ? $text->description : NULL;
    $ribbon = isset($bookmark['library']->format->back_button) ? $bookmark['library']->format->back_button->image : DEFAULT_BACK_RIBBON;
    $language_dir = '/content/' . $data['country_code'] . '/' . $data['language_iso'] . '/' . $data['folder_name'] . '/';
    $files_json = '[{"url":"' .  $language_dir . 'files.json"},' . "\n"; // rest to be filled in with chapters
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
            $status = $chapter->publish;
            //_write_series_log($p, $chapter);
            if ($status  == true) { // we only want to process those with this as true
                $filename = $language_dir . $chapter->filename . '.html';
                //writeLogAppend('capacitor-createSeries-113', $filename);
                /* we need to replace
                /sites/sent67/content/U1/eng/hope/hope01.html
                with
                /content/U1/eng/hope/hope01.html
                */
                $filename = str_ireplace('/sites/' . SITE_CODE, '', $filename);
                //writeLogAppend('capacitor-createSeries-120', $filename);
                $files_json .= '{"url":"' . $filename . '"},' . "\n";
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

                $link =  $p['language_iso'] . '-' . $chapter->filename;


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
    $out = new stdClass;
    $text = str_replace('[[chapters]]', $chapters_text, $this_template);
    $progress =  modifyTextForVue($text, $bookmark, $p);
    $progress->files_json = $files_json;
    //writeLogDebug('capacitor-createSeries-161', $out);
    return $progress;
}
