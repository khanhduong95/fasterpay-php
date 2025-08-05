<?php

namespace FasterPay;

class HttpClient
{
    protected $header;

    public function __construct(array $header = [])
    {
        $this->header = $header;
    }

    public function init()
    {
        $ch = curl_init();

        $defaultOptions = [
            CURLOPT_USERAGENT => 'FasterPay-PHP-SDK',
            CURLOPT_HEADER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 60,
            CURLOPT_TIMEOUT => 60,
        ];

        curl_setopt_array($ch, $defaultOptions);

        return $ch;
    }

    public function get($endpoint, array $params = [], array $headers = [])
    {
        return $this->call($endpoint, $params, 'GET', $headers);
    }

    public function post($endpoint, array $params = [], array $headers = [])
    {
        return $this->call($endpoint, $params, 'POST', $headers);
    }

    public function postMultipart($endpoint, array $params = [], array $headers = [])
    {
        return $this->call($endpoint, $params, 'POST_MULTIPART', $headers);
    }

    public function put($endpoint, array $params = [], array $headers = [])
    {
        return $this->call($endpoint, $params, 'PUT', $headers);
    }

    public function delete($endpoint, array $params = [], array $headers = [])
    {
        return $this->call($endpoint, $params, 'DELETE', $headers);
    }

    private function call($endpoint, array $params = [], $method, array $headers = [])
    {
        $ch = $this->init();

        $header = array_merge($this->header, $headers);

        switch (strtoupper($method)) {
            case 'POST':
                curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
                if (!empty($params)) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
                }
                curl_setopt($ch, CURLOPT_POST, true);
                break;

            case 'POST_MULTIPART':
                $multipartHeaders = [];
                foreach ($header as $h) {
                    if (stripos($h, 'Content-Type:') !== 0) {
                        $multipartHeaders[] = $h;
                    }
                }
                curl_setopt($ch, CURLOPT_HTTPHEADER, $multipartHeaders);

                if (!empty($params)) {
                    $multipartData = $this->buildMultipartData($params);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $multipartData);
                }

                curl_setopt($ch, CURLOPT_POST, true);
                break;

            case 'PUT':
            case 'DELETE':
                curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
                curl_setopt($ch, CURLOPT_HTTPGET, false);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
                if (!empty($params)) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
                }
                break;

            case 'GET':
                curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
                if (!empty($params)) {
                    $endpoint .= '?' . http_build_query($params);
                }
                curl_setopt($ch, CURLOPT_POST, false);
                curl_setopt($ch, CURLOPT_HTTPGET, true);
                break;
        }

        curl_setopt($ch, CURLOPT_URL, $endpoint);

        $response = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);

        return [
            'response' => $response,
            'httpCode' => $info['http_code']
        ];
    }

    private function buildMultipartData(array $data, $prefix = '')
    {
        $result = [];

        foreach ($data as $key => $value) {
            $fieldName = $prefix ? $prefix . '[' . $key . ']' : $key;

            if (is_array($value)) {
                $nestedData = $this->buildMultipartData($value, $fieldName);
                $result = array_merge($result, $nestedData);
            } elseif (is_string($value) && substr($value, 0, 1) === '@') {
                if (class_exists('CURLFile')) {
                    $filePath = substr($value, 1);
                    if (file_exists($filePath)) {
                        $result[$fieldName] = new CURLFile($filePath);
                    } else {
                        $result[$fieldName] = $value;
                    }
                } else {
                    $result[$fieldName] = $value;
                }
            } else {
                $result[$fieldName] = (string) $value;
            }
        }

        return $result;
    }
}
