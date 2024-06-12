<?php
// this program will figure out WHAT needs to be synced
myRequireOnce('writeLog.php');
myRequireOnce('syncDirectory.php');

function syncController($p){
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
    writeLog('syncController-39', $all_directories);
    foreach ($all_directories as $source => $destination){
        $modified_source = ROOT_EDIT . $source;
        if ($p['destination'] == 'website'){
            $modified_destination = ROOT_PUBLISH . $destination;
        }
        else{
            $modified_destination = ROOT_STAGING . $destination;
        }
        syncDirectory($modified_source, $modified_destination);
    }
}