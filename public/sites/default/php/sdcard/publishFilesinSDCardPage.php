<?php

function publishFilesInSDCardPage($filename, $p, $destination)
{
    writeLogDebug('publishFilesInSDCardPage-5', $p);
    if (strpos($filename, '/assets/images') !== false) {
        /*
            if this file is not found:
            /assets/images/eng/tc/transferable-concepts-image-22.png 
            look here:
            /sites/mc2/content/M2/eng/tc/transferable-concepts-image-22.png (images stored in series folders)
            and copy to 
            ROOT_SDCARD . assets/images/eng/tc/transferable-concepts-image-22.png 
        */
        $old_dir = '/assets/images/' . $p['language_iso'];
        $new_dir = 'sites/' . SITE_CODE . '/content/' . $p['country_code'] . '/' . $p['language_iso']; // mc2/content/M2/eng/images
        $to = $destination . substr($filename, 1); // getting rid of intital '/'
        writeLogDebug('publishFilesInSDCardPage-18', ROOT_EDIT);
        $from =  ROOT_EDIT  . str_replace($old_dir, $new_dir, $filename);
        $from = str_replace('//', '/', $from);
        writeLogAppend('publishFilesInSDCardPage-20', $from);
        if (file_exists($from)) {
            createDirectory($to);
            copy($from, $to);
            //$message = "$from \n  $to \n\n";
            //  writeLogAppend('publishFilesInSDCardPage-116', $message);
        } else {

            /*
                    /assets/images/standard/look-back.png -- not found
                    /sites/mc2/images/standard/look-back.png -- desired file
                */
            if (strpos($from, '/assets/images/standard/') !== FALSE) {
                $new_dir = '/sites/' . SITE_CODE . '/';
                $from = str_replace('/assets/', $new_dir, $from);
            }
            /*  This area is for files in standard and custom directory
                "/home/globa544/edit.mc2.online/sites/mc2/content/M2/eng/standard/TransferableConcepts.png -- not found  ($from)
                "/home/globa544/edit.mc2.online/sites/mc2/content/M2/eng/images/standard/TransferableConcepts.png -- desired
                /assets/images/eng/standard/TransferableConcepts.png -- original file ($filename)
            */ elseif (strpos($from, '/standard/') !== FALSE) {
                $from = str_replace('/standard/', '/images/standard/', $from);
            } elseif (strpos($from, '/custom/') !== FALSE) {
                $from = str_replace('/custom/', '/images/custom/', $from);
            } elseif (strpos($from, '/ribbons/') !== FALSE) {
                ///home/globa544/edit.mc2.online/assets/images/ribbons/back-ribbon-mc2.png
                $new = '/sites/' . SITE_CODE . '/';
                $from = str_replace('/assets/', $new, $from);
                // /home/globa544/edit.mc2.online/sites/mc2/images/ribbons/back-ribbon-mc2.png
            }
            if (file_exists($from)) {
                createDirectory($to);
                copy($from, $to);
            }
            if (!file_exists($from)) {
                $message = "$from -- modified not found\n$filename -- original file\n\n";
                writeLogAppend('ERRORS-publishFilesInSDCardPage-51', $message);
            }
        }
    } elseif (strpos($filename, 'sites/') !== false) {
        /*
        sites/mc2/content/M2/eng/tc/transferable-concepts-image-11.png 
        we know a file exists but it may be missing 'images' after 'eng'
        /home/globa544/edit.mc2.online/sites/mc2/content/M2/eng/tc/transferable-concepts-image-11.png 
        but we do not want to copy it to
        /home/globa544/sdcard.mc2/sites/mc2/content/M2/eng/tc/transferable-concepts-image-11.png
        instead copy to 
        /home/globa544/sdcard.mc2/assets/images/eng/tc/transferable-concepts-image-11.png
        */
        $new_dir = '/assets/images';
        $old_dir = 'sites/' . SITE_CODE . '/content/' . $p['country_code']; // mc2/content/M2
        //writeLogAppend('publishFilesInSDCardPage-133', "$filename\n$old_dir\n\n");
        $to = $destination . str_replace($old_dir, $new_dir, $filename);
        //writeLogAppend('WARNING- publishFilesInSDCardPage-66', $to);
        $from = $filename;
        $necessary = '/' . $p['language_iso'] . '/images/';
        if (strpos($from, $necessary === FALSE)) {
            $old = '/' . $p['language_iso'] . '/';
            $from = str_replace($old, $necessary, $from);
        }
        $from =  ROOT_EDIT  . $from;
        if (file_exists($from)) {
            $message = "$from \n  $to \n\n";
            // writeLogAppend('publishFilesInSDCardPage-78', $message);
            copy($from, $to);
        }
        if (!file_exists($from)) {
            $message = "$filename -- original file\n$from -- not found";
            writeLogAppend('ERRORS-publishFilesInSDCardPage-83', $message);
        }
    } else {
        writeLogAppend('ERRORS-publishFilesInSDCardPage-86', "$filename -- original file");
    }
}
