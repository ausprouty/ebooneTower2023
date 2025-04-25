<?php
myRequireOnce('sql.php');
/*requires $p as array:
        'entry' => 'Zephaniah 1:2-3'
        'bookId' => 'Zeph',
        'chapterId' => 1,
        'verseStart' => 2,
        'verseEnd' => 3,
        'bid' => '123' // this is bid being used.
        'collection_code' => 'OT' ,
        'version_ot' => '123', // this is bid
        'version_nt' => '134'
        )


    return aut as an array:
        debug:
       content: as an array with
		'passage_name' => 'Zephaniah 1:2-3'
		'bible' => 'bible verse text',
        'link' => 'https://biblegateway.com/passage/?search=John%201:1-14&version=LSG',
        'publisher' => 'Louis Segond (LSG) by Public Domain'

*/

function bibleGetPassage($p)
{
    $out = [];
    // make sure bid is set
    writeLogDebug('bibleGetPassage-30', $p);
    if (!isset($p['bid'])) {
        writeLogDebug('bibleGetPassage-32', $p['collection_code']);
        if (isset($p['collection_code'])) {
            if ($p['collection_code'] == 'OT') {
                if (isset($p['version_ot'])) {
                    $p['bid'] = $p['version_ot'];
                }
            }
            if ($p['collection_code'] == 'NT') {
                if (isset($p['version_nt'])) {
                    $p['bid'] = $p['version_nt'];
                }
            }
        }
        if (!isset($p['bid'])) {
            $message = 'p[bid] is not set';
            trigger_error($message, E_USER_ERROR);
            return null;
        }
    }
    $sql = "SELECT * FROM dbm_bible WHERE bid = " . $p['bid'];
    //$debug = $sql . "\n";
    $data = sqlBibleArray($sql);
    if ($data['right_to_left'] != 't') {
        $p['rldir'] = 'ltr';
    } else {
        $p['rldir'] = 'rtl';
    }

    //writeLogDebug('bibleGetPassage-59',$data['source']);
    if ($data['source'] == 'bible_gateway') {
        myRequireOnce('bibleGetPassageBiblegateway.php');
        $p['version_code'] = $data['version_code'];
        $out = bibleGetPassageBiblegateway($p);
        return $out;
    }
    if ($data['source']  == 'dbt') {
        myRequireOnce('bibleGetPassageBibleBrain.php');
        $p['damId'] = $data['dam_id'];
        $out = bibleGetPassageBibleBrain($p);
        writeLogDebug('bibleGetPassage-66', $out);
        return $out;
    }
    if ($data['source']  == 'bible_server') {
        myRequireOnce('bibleGetPassageBibleServer.php');
        $p['version_code'] = $data['version_code'];
        $out = bibleGetPassageBibleServer($p);
        return $out;
    }
    return $out;
}
