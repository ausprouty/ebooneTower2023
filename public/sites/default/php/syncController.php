<?php
// this program will figure out WHAT needs to be synced
myRequireOnce('syncDirectory.php');
myRequireOnce('writeLog.php');


function syncController($p){
    writeLog('syncController-8', $p);
    $site = $p['site'];
    $country = $p['country_code'];
    $country_directories= array();
    $language = $p['language_iso'];
    $language_directories= array();
    
    $site_directories= array(
        'sites/default/images'=> 'sites/default/images',
        "sites/$site/images" => "sites/$site/images",
        "sites/$site/content/images" => "sites/$site/content/images",
        'sites/default/styles' => 'default/styles',
        "sites/$site/styles" => "sites/$site/styles",
        "sites/$site/content/styles" => "sites/$site/content/styles",
        'sites/default/javascript' => 'default/javascript',
        "sites/$site/javascript" => "sites/$site/javascript",
        "sites/$site/content/javascript" => "sites/$site/content/javascript",
      
    );
    
    if ($p['country_code']){
        $country_directories= array(
            "sites/$site/content/$country/images" => "sites/$site/content/$country/images", 
            "sites/$site/content/$country/styles" => "sites/$site/content/$country/styles",
            "sites/$site/content/$country/javascript" => "sites/$site/content/$country/javascript",
        );
    }
    if ($p['language_iso']){
        $language_directories= array(
            "sites/$site/content/$country/$language/images" => "sites/$site/content/$country/$language/images",
            "sites/$site/content/$country/$language/styles" => "sites/$site/content/$country/$language/styles",
            "sites/$site/content/$country/$language/javascript" => "sites/$site/content/$country/$language/javascript",
        );
    }
    $all_directories = array_merge($site_directories, $country_directories, $language_directories);
    writeLogDebug('syncController-39', $all_directories);
    foreach ($all_directories as $source => $destination){
        $modified_source = ROOT_EDIT . $source;
        if ($p['destination'] == 'website'){
            $modified_destination = ROOT_WEBSITE . $destination;
        }
        else{
            $modified_destination = ROOT_STAGING . $destination;
        }
        writeLogAppend('syncController-52', "Going to: $modified_source --> $modified_destination");
        syncDirectory($modified_source, $modified_destination);
        writeLogAppend('syncController-52', "Returning From: $modified_source --> $modified_destination");
    }
    writeLogDebug('syncController-56', "About to return from syncController") ;
    return;
}