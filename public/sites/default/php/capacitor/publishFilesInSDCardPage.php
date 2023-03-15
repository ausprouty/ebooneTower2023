<?php
myRequireOnce('copyFilesForSDApp.php', 'capacitor');

function publishFilesInCapacitorPage($filename, $p, $destination)
{
    writeLogDebug('capacitor-publishFilesInCapacitorPage-5', $p);
    if (strpos($filename,  myString('/assets/images')) !== false) {
        /*
            if this file is not found:
            /assets/images/eng/tc/transferable-concepts-image-22.png 
            look here:
            /sites/mc2/content/M2/eng/tc/transferable-concepts-image-22.png (images stored in series folders)
            and copy to 
            ROOT_CAPACITOR . assets/images/eng/tc/transferable-concepts-image-22.png 
        */
        $old_dir = '/assets/images/' . $p['language_iso'];
        $new_dir = 'sites/' . SITE_CODE . '/content/' . $p['country_code'] . '/' . $p['language_iso']; // mc2/content/M2/eng/images
        $to = $destination . substr($filename, 1); // getting rid of intital '/'
        writeLogDebug('capacitor-publishFilesInCapacitorPage-18', ROOT_EDIT);
        $from =  ROOT_EDIT  . str_replace($old_dir, $new_dir, $filename);
        $from = str_replace('//', '/', $from);
        writeLogAppend('publishFilesInCapacitorPage-20', $from);
        if (file_exists($from)) {
            copyFilesForSDApp($from, $to, 23);
        } else {

            /*
             @/assets/images/standard/Stories-of-the-Prophets.png        
            /assets/images/standard/look-back.png -- not found
                    /sites/mc2/images/standard/look-back.png -- desired file
                */
            if (strpos($from, myString('/assets/images/standard/')) !== FALSE) {
                $new_dir = '/sites/' . SITE_CODE . '/';
                $from = str_replace('/assets/', $new_dir, $from);
                $from = str_replace('@/', '', $from);
            }
            /*  This area is for files in standard and custom directory
                "/home/globa544/edit.mc2.online/sites/mc2/content/M2/eng/standard/TransferableConcepts.png -- not found  ($from)
                "/home/globa544/edit.mc2.online/sites/mc2/content/M2/eng/images/standard/TransferableConcepts.png -- desired
                /assets/images/eng/standard/TransferableConcepts.png -- original file ($filename)
            */ elseif (strpos($from, myString('/standard/')) !== FALSE) {
                $from = str_replace('/standard/', '/images/standard/', $from);
            } elseif (strpos($from, myString('/custom/')) !== FALSE) {
                $from = str_replace('/custom/', '/images/custom/', $from);
            } elseif (strpos($from, myString('/ribbons/')) !== FALSE) {
                ///home/globa544/edit.mc2.online/assets/images/ribbons/back-ribbon-mc2.png
                $new = '/sites/' . SITE_CODE . '/';
                $from = str_replace('/assets/', $new, $from);
                // /home/globa544/edit.mc2.online/sites/mc2/images/ribbons/back-ribbon-mc2.png
                // @/assets/images/standard/Stories-of-Hope.png
            }
            if (file_exists($from)) {
                copyFilesForSDApp($from, $to, 52);
            }
            if (!file_exists($from)) {
                /* check country directory
                @/assets/images/standard/Stories-of-Hope.png to 
                 /sites/mc2/content/M2/images/standard/Stories-of-Hope.png
                */
                $old = 'sites/' . SITE_CODE;
                $cc = '/content/' . $p['country_code'];
                $new = $old . $cc;
                $from = str_ireplace($old, $new, $from);
                $from = str_ireplace('@', '', $from);
                //de-duplicate
                $from = str_ireplace($cc . $cc, $cc, $from);
                $from = str_ireplace('//', '/', $from);
                if (file_exists($from)) {
                    copyFilesForSDApp($from, $to, 69);
                } else {
                    $message = "$from -- modified not found\n$filename -- original file\n\n";
                    writeLogAppend('ERRORS-publishFilesInCapacitorPage-72', $message);
                }
            }
        }
        return;
    }
    if (strpos($filename, myString('sites/')) !== false) {
        /*
        sites/mc2/content/M2/eng/tc/transferable-concepts-image-11.png 
        we know a file exists but it may be missing 'images' after 'eng'
        /home/globa544/edit.mc2.online/sites/mc2/content/M2/eng/tc/transferable-concepts-image-11.png 
        but we do not want to copy it to
        /home/globa544/mc2.capacitor/sites/mc2/content/M2/eng/tc/transferable-concepts-image-11.png
        instead copy to 
        /home/globa544/mc2.capacitor/assets/images/eng/tc/transferable-concepts-image-11.png
        */
        $new_dir = '/assets/images';
        $old_dir = 'sites/' . SITE_CODE . '/content/' . $p['country_code']; // mc2/content/M2
        //writeLogAppend('publishFilesInCapacitorPage-133', "$filename\n$old_dir\n\n");
        $to = $destination . str_replace($old_dir, $new_dir, $filename);
        $to = str_replace('//', '/', $to);
        //writeLogAppend('WARNING- publishFilesInCapacitorPage-93', $to);
        $from = $filename;
        $necessary =  '/' . $p['language_iso'] . '/images/';
        if (strpos($from, $necessary) === FALSE) {
            $old = '/' . $p['language_iso'] . '/';
            $from = str_replace($old, $necessary, $from);
        }
        $from =  ROOT_EDIT  . $from;
        if (file_exists($from)) {
            copyFilesForSDApp($from, $to, 107);
        }
        if (!file_exists($from)) {
            /*
            string(161) "sites/mc2/content/M2/eng/multiply2/Period1.png -- original file
            /home/globa544/edit.mc2.online/sites/mc2/content/M2/eng/images/multiply2/Period1.png -- not found"
            */
            $try = str_replace('images/', '', $from);
            if (file_exists($try)) {
                copyFilesForSDApp($try, $to, 111);
            } else {
                $message = "$filename -- original file\n$from -- not found\n$try -- not found\n\n";
                writeLogAppend('ERRORS-publishFilesInCapacitorPage-115', $message);
            }
        }
    } else {
        writeLogAppend('ERRORS-publishFilesInCapacitorPage-86', "$filename -- original file");
    }
}


function myString($string)
{
    return strval($string);
}
