<?php

function bibleBrainGet($url)
{
    $key = BIBLE_BRAIN_KEY;
    $url .= (strpos($url, '?') === false ? '?' : '&') . 'v=4&key=' . $key;
    
    writeLogDebug('bibleBrainGet-url', $url);

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
    ));

    $rawResponse = curl_exec($curl);

    if ($rawResponse === false) {
        $curlError = curl_error($curl);
        writeLogError('bibleBrainGet-curl_error', $curlError);
        curl_close($curl);
        return null;
    }

    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    $response = json_decode($rawResponse);

    if ($httpCode >= 400 || (is_object($response) && isset($response->error))) {
        writeLogError('bibleBrainGet-api_error', [
            'http_code' => $httpCode,
            'response' => $response
        ]);
    }

    writeLogDebug('bibleBrainGet-response', $response);
    return $response;
}
