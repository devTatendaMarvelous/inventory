<?php

define('MAX_EXECUTION_TIME', '6000');

ini_set('max_execution_time', MAX_EXECUTION_TIME);

function getRequest($url){
    $url=env('BUCKET_API').$url;

    $curl = curl_init();
    curl_setopt_array(
        $curl, array(
        CURLOPT_URL                 => $url,
        CURLOPT_RETURNTRANSFER      => true,
        CURLOPT_ENCODING            => '',
        CURLOPT_MAXREDIRS           => 10,
        CURLOPT_TIMEOUT             => 0,
        CURLOPT_FOLLOWLOCATION      => true,
        CURLOPT_HTTP_VERSION        => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST       => 'GET',
        CURLOPT_HTTPHEADER          => array(
            'Content-Type: application/json'
        ),
    ));
    $response = curl_exec($curl);
    curl_close($curl);

    return @json_decode(json_encode(json_decode($response, false)));
}

function postRequest($url=null,$data = []){
    $url=env('INFOBIP_BASE_URL').'/sms/2/text/advanced';
    $key=env('INFOBIP_API_KEY');
    $curl = curl_init();
    curl_setopt_array(
        $curl, array(
        CURLOPT_URL                 => $url,
        CURLOPT_RETURNTRANSFER      => true,
        CURLOPT_ENCODING            => '',
        CURLOPT_MAXREDIRS           => 10,
        CURLOPT_TIMEOUT             => 0,
        CURLOPT_FOLLOWLOCATION      => true,
        CURLOPT_HTTP_VERSION        => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST       => 'POST',
        CURLOPT_POSTFIELDS          =>  '{"messages":[{"destinations":[{"to":"+263716291396"}],"from":"InfoSMS","text":"This is a sample message"}]}',
        CURLOPT_HTTPHEADER          => array(
            'Content-Type: application/json',
            'Authorization: App 41a661925d50d626a44c3488982e10af-4c5be773-6c36-440b-ac60-637d39ccb221',
        'Accept: application/json'
        ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);
    return @json_decode(json_encode(json_decode($response, true)));

}
function filePostRequest($url,$data){

    $file = $data['file'];
    $data['file'] = new CURLFile($file->getPathname(), $file->getClientMimeType(), $data['fileName']);
    $url=env('BUCKET_API').$url;
    $curl = curl_init();
    curl_setopt_array(
        $curl, array(
        CURLOPT_URL                 => $url,
        CURLOPT_RETURNTRANSFER      => true,
        CURLOPT_ENCODING            => '',
        CURLOPT_MAXREDIRS           => 10,
        CURLOPT_TIMEOUT             => 0,
        CURLOPT_FOLLOWLOCATION      => true,
        CURLOPT_HTTP_VERSION        => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST       => 'POST',
        CURLOPT_POSTFIELDS          =>  $data,
        CURLOPT_HTTPHEADER          => array(
            'Content-Type: multipart/form-data'
        ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);
    return @json_decode(json_encode(json_decode($response, true)));

}

function filePutRequest($url,$data){

    $file = $data['file'];
    $data['file'] = new CURLFile($file->getPathname(), $file->getClientMimeType(), $file->getClientOriginalName());

    $url=env('BUCKET_API').$url;
    $curl = curl_init();
    curl_setopt_array(
        $curl, array(
        CURLOPT_URL                 => $url,
        CURLOPT_RETURNTRANSFER      => true,
        CURLOPT_ENCODING            => '',
        CURLOPT_MAXREDIRS           => 10,
        CURLOPT_TIMEOUT             => 0,
        CURLOPT_FOLLOWLOCATION      => true,
        CURLOPT_HTTP_VERSION        => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST       => 'PUT',
        CURLOPT_POSTFIELDS          =>  $data,
        CURLOPT_HTTPHEADER          => array(
            'Content-Type: multipart/form-data'
        ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);
    return @json_decode(json_encode(json_decode($response, true)));

}

function putRequest($url,$data){
    $url=env('BUCKET_API').$url;
    $curl = curl_init();
    curl_setopt_array(
        $curl, array(
        CURLOPT_URL                 => $url,
        CURLOPT_RETURNTRANSFER      => true,
        CURLOPT_ENCODING            => '',
        CURLOPT_MAXREDIRS           => 10,
        CURLOPT_TIMEOUT             => 0,
        CURLOPT_FOLLOWLOCATION      => true,
        CURLOPT_HTTP_VERSION        => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST       => 'PUT',
        CURLOPT_POSTFIELDS          =>  json_encode($data),
        CURLOPT_HTTPHEADER          => array(
            'Content-Type: application/json'
        ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    return @json_decode(json_encode(json_decode($response, true)));
}

function deleteRequest($url){
    $url=env('BUCKET_API').$url;

    $curl = curl_init();
    curl_setopt_array(
        $curl, array(
        CURLOPT_URL                 => $url,
        CURLOPT_RETURNTRANSFER      => true,
        CURLOPT_ENCODING            => '',
        CURLOPT_MAXREDIRS           => 10,
        CURLOPT_TIMEOUT             => 0,
        CURLOPT_FOLLOWLOCATION      => true,
        CURLOPT_HTTP_VERSION        => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST       => 'DELETE',
        CURLOPT_HTTPHEADER          => array(
            'Content-Type: application/json'
        ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    return @json_decode(json_encode(json_decode($response, true)));
}

function patchRequest($url,$data){
    $url=env('BUCKET_API').$url;
    $curl = curl_init();
    curl_setopt_array(
        $curl, array(
        CURLOPT_URL                 => $url,
        CURLOPT_RETURNTRANSFER      => true,
        CURLOPT_ENCODING            => '',
        CURLOPT_MAXREDIRS           => 10,
        CURLOPT_TIMEOUT             => 0,
        CURLOPT_FOLLOWLOCATION      => true,
        CURLOPT_HTTP_VERSION        => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST       => 'PATCH',
        CURLOPT_POSTFIELDS          =>  json_encode($data),
        CURLOPT_HTTPHEADER          => array(
            'Content-Type: application/json'
        ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    return @json_decode(json_encode(json_decode($response, true)));
}


