<?php
myRequireOnce('writeLog.php');
myRequireOnce('dirCreate.php');


function createLibrary($p, $text)
{

    myRequireOnce('decidePublishBook.php', $p['destination']);
    myRequireOnce('getLibraryImage.php', $p['destination']);
    myRequireOnce('getPrototypeFileLibrary.php', $p['destination']);
    /* Return a container for the books in this library.
    This will be used to prototype these books by prototypeLibraryandBooks.

    */

    $out = [];
    $out['books'] = []; // used by publishLibraryAndBooks
    $debug = "createLibrary\n";
    $filename =  $p['library_code'];
    //
    // get bookmark
    //
    if (isset($p['recnum'])) {
        $b['recnum'] = $p['recnum'];
        $b['library_code'] = isset($p['library_code']) ? $p['library_code'] : 'library';
    } else {
        $b = $p;
    }
    $bookmark  = bookmark($b);
    //writeLogDebug('createLibrary-31', $bookmark);
    //
    // get template for library and fill in library details
    //
    $body = getPrototypeFileLibrary($p);
    //
    //  Format Navigation area;
    //

    $no_ribbon = isset($text->format->no_ribbon) ? isset($text->format->no_ribbon) : false;
    if ($no_ribbon) {
        $debug .= 'prototypeLibrary was asked not to set ribbon at top ' . "\n";
        $nav = '';
        $ribbon = '';
    } else {
        $debug .= 'Ribbon In prototypeLibrary ' . "\n";
        $nav = myGetPrototypeFile('navRibbon.html', $p['destination']);
        $ribbon = isset($text->format->back_button) ? $text->format->back_button->image : DEFAULT_BACK_RIBBON;
    }
    $body = str_replace('[[nav]]', $nav, $body);
    //
    //  Replace other variables for Library
    //
    $library_image =  getLibraryImage($p, $text);
    $body = str_replace('{{ library.image }}', $library_image, $body);

    $library_text = isset($text->text) ? $text->text : null;
    // check to see if image above has text;
    $body = str_replace('{{ library.text }}', $library_text, $body);
    $root_index = '/content/index.html';
    $navlink = '../index.html';


    // get language footer in prototypeOEpublish.php
    $footer = createLanguageFooter($p);
    $language_iso = isset($p['language_iso']) ? $p['language_iso'] : DEFAULT_LANGUAGE_ISO;
    $placeholders = array(
        '{{ navlink }}',
        '{{ ribbon }}',
        '{{ version }}',
        '{{ footer }}',
        '{{ language.rldir }}',
        '{{ country_code }}',
        '{{ language_iso }}',

    );
    $replace = array(
        $navlink,
        $ribbon,
        $p['version'],
        $footer,
        $bookmark['language']->rldir,
        $p['country_code'],
        $language_iso
    );
    $body = str_replace($placeholders, $replace, $body);
    //
    // select appropriate book template
    //
    $temp = 'bookTitled.html';
    if ($bookmark['language']->titles) {
        $temp = 'bookImage.html';
        $debug .= 'Using template for bookImage ' . "\n";
    }
    $book_template = myGetPrototypeFile('' . $temp, $p['destination']);
    //
    //  replace for values in book templage for each book
    //
    $books = '';
    $placeholders = array(
        '{{ link }}',
        '{{ book.image }}',
        '{{ book.title }}',
        '{{ language.rldir }}'
    );
    // you do not add book cards if this is a custom libary
    $custom_library = false;
    if (isset($text->format->custom)) {
        $custom_library  = $text->format->custom;
    }
    if (isset($text->books) && !$custom_library) {
        foreach ($text->books as $book) {
            if (decidePublishBook($p, $book)  == true) {
                if (!isset($book->hide)) {
                    $book->hide = false;
                }
                if (!$book->hide) {
                    // deal with legacy data
                    $code = '';
                    if (isset($book->code)) {
                        $code = $book->code;
                    } else if (isset($book->name)) {
                        $code = $book->name;
                        $book->code = $code;
                    }
                    // you will need library code in bookmark
                    $book->library_code =  $b['library_code'];
                    // deal with any duplicates
                    $out['books'][$code] = $book;
                    // create link for series, library or page
                    if ($p['destination'] !== 'sdcard') {
                        if ($book->format == 'series') {
                            $this_link =  $code . '/index.html';
                        } elseif ($book->format == 'library') {
                            $this_link =  $code . '.html';
                        } else {
                            $this_link = 'pages/' . $code . '.html';
                        }
                    }
                    if ($p['destination'] === 'sdcard') {
                        if ($book->format == 'series') {
                            $this_link = $p['language_iso'] . '-' .  $code . '-index';
                        } elseif ($book->format == 'library') {
                            $this_link = $p['language_iso'] . '-' .  $code . '-index';
                        } else {
                            // todo: fix this next line
                            $this_link = $p['language_iso'] . '-' .  $code;
                        }
                    }

                    // dealing with legacy data
                    if (isset($book->image->image)) {
                        $book_image =  $book->image->image;
                    } else {
                        $country_index =  dirCreate('country', $p['destination'], $p);
                        $book_image =   $country_index .  $bookmark['language']->image_dir . '/' . $book->image;
                    }
                    if ($p['destination'] == 'sdcard') {
                        /* change 
                          /sites/mc2/content/M2/eng/images
                          /sites/mc2/content/M2/images/
                           to    
                           @/assets/images/eng
                           @/assets/images/
                        */
                        $old = 'sites/' . SITE_CODE . '/content/' . $p['country_code'] . '/' . $p['language_iso'] . '/images';
                        $new = '@/assets/images/' . $p['language_iso'];
                        $book_image = str_replace($old, $new, $book_image);
                        $old = 'sites/' . SITE_CODE . '/content/' . $p['country_code'] . '/images';
                        $new = '@/assets/images/';
                        $book_image = str_replace($old, $new, $book_image);
                    }
                    writeLogAppend('createLibrary-163', $book_image);
                    $replace = array(
                        $this_link,
                        $book_image,
                        $book->title,
                        $bookmark['language']->rldir
                    );
                    $books .= str_replace($placeholders, $replace, $book_template);
                }
            }
        }
    }

    $out['body'] = str_replace('[[books]]', $books, $body);
    //writeLog('createLibrary', $debug);
    return $out;
}
