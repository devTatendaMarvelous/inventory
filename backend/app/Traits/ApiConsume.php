<?php

namespace App\Traits;

use CURLFile;

define('MAX_EXECUTION_TIME', '600000000');
ini_set('max_execution_time', MAX_EXECUTION_TIME);

trait ApiConsume
{
    private function executeRequest($method, $url, $data = [], $use_token = true, $is_file = false)
    {

        $token = session('api_token'); // Retrieve the token from session

        $curl = curl_init();

        $headers = ['Content-Type: application/json'];

        // Conditionally append the Authorization header if $use_token is true and token exists
        if ($use_token && !empty($token)) {
            $headers[] = 'Authorization: Bearer ' . $token;
        }

        if ($is_file) {
            $file = $data['file'];
            $data['file'] = new CURLFile($file->getPathname(), $file->getClientMimeType(), $file->getClientOriginalName());
            $headers = []; // multipart/form-data does not need Content-Type header
            if ($use_token && !empty($token)) {
                $headers[] = 'Authorization: Bearer ' . $token;
            }
        }

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => strtoupper($method),
            CURLOPT_HTTPHEADER => $headers,
        ]);

        if (!empty($data)) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $is_file ? $data : json_encode($data));
        }
        $response = curl_exec($curl);

        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        $responseData = [
            'status' => $httpCode,
            'content' => json_decode($response, true),
        ];
        return json_decode(json_encode($responseData), false);
    }

    function getRequest($url, $service = null)
    {
        return $this->executeRequest('GET', $this->resolveUrl($url, false, $service));
    }

    function postRequest($url, $data = [], $is_auth = false, $service = null)
    {

        return $this->executeRequest('POST', $this->resolveUrl($url, $is_auth, $service), $data, !$is_auth);
    }

    function filePostRequest($url, $data)
    {
        return $this->executeRequest('POST', $this->resolveUrl($url), $data);
    }

    function filePutRequest($url, $data)
    {
        return $this->executeRequest('PUT', $this->resolveUrl($url), $data);
    }

    function putRequest($url, $data, $service = null)
    {
        return $this->executeRequest('PUT', $this->resolveUrl($url, false, $service), $data);
    }

    function deleteRequest($url, $is_download = false)
    {
        return $this->executeRequest('DELETE', $this->resolveUrl($url, false, $is_download));
    }

    function patchRequest($url, $data)
    {
        return $this->executeRequest('PATCH', $this->resolveUrl($url), $data);
    }

    function resolveUrl($url, $is_auth = false, $service = null)
    {
        if ($service == 'contabo') {
            $url = env('CONTABO_URL') . $url;
        } elseif ($service == 'sms') {
            $url = env('SMS_SERVICE') . $url;
        } elseif ($service == 'claims') {
            $url = env('CLAIMS_SERVICE') . $url;
        } elseif ($service == 'services') {
            $url = env('SERVICES_URL') . $url;
        } elseif ($service == 'notifications') {
            $url = env('NOTIFICATION_SERVICE') . $url;
        } elseif ($is_auth) {
            $url = env('AUTH_URL') . $url;
        } else {
            $url = env('API_URL') . $url;
        }
        return $url;
    }
}
