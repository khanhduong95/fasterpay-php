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
        $testParams = $params;
        $files = $this->extractFiles($testParams);

        if (!empty($files)) {
            return $this->postMultipart($endpoint, $params, $headers);
        }

        return $this->call($endpoint, $params, 'POST', $headers);
    }

    public function put($endpoint, array $params = [], array $headers = [])
    {
        $testParams = $params;
        $files = $this->extractFiles($testParams);

        if (!empty($files)) {
            $params['_method'] = 'PUT';
            return $this->postMultipart($endpoint, $params, $headers);
        }

        return $this->call($endpoint, $params, 'PUT', $headers);
    }

    public function delete($endpoint, array $params = [], array $headers = [])
    {
        return $this->call($endpoint, $params, 'DELETE', $headers);
    }

    public function postMultipart($endpoint, array $params = [], array $headers = [])
    {
        $ch = $this->init();

        $header = array_merge($this->header, $headers);

        $header = array_filter($header, function($h) {
            return stripos($h, 'Content-Type:') !== 0;
        });

        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_URL, $endpoint);

        $files = $this->extractFiles($params);
        $postData = [];

        $this->buildPostData($params, $postData, '');

        foreach ($files as $fieldName => $fileValue) {
            if (is_object($fileValue)) {
                if ($fileValue instanceof \SplFileInfo) {
                    // Convert SplFileInfo to file path for HTTP compatibility
                    $filePath = $fileValue->getPathname();
                    if (class_exists('CURLFile')) {
                        $postData[$fieldName] = new \CURLFile($filePath);
                    } else {
                        $postData[$fieldName] = '@' . $filePath;
                    }
                } else {
                    // Handle other file objects (CURLFile, etc.)
                    $postData[$fieldName] = $fileValue;
                }
            } elseif (is_string($fileValue) && file_exists($fileValue)) {
                if (class_exists('CURLFile')) {
                    $postData[$fieldName] = new \CURLFile($fileValue);
                } else {
                    $postData[$fieldName] = '@' . $fileValue;
                }
            }
        }

        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

        $response = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);

        return [
            'response' => $response,
            'httpCode' => $info['http_code']
        ];
    }

    private function buildPostData(array $data, array &$postData, $prefix)
    {
        foreach ($data as $key => $value) {
            $currentKey = $prefix ? $prefix . '[' . $key . ']' : $key;

            if (is_array($value)) {
                $this->buildPostData($value, $postData, $currentKey);
            } else {
                $postData[$currentKey] = $value;
            }
        }
    }

    private function extractFiles(array &$params)
    {
        $files = [];
        $this->extractFilesRecursive($params, $files, '');
        return $files;
    }

    private function extractFilesRecursive(array &$params, array &$files, $prefix)
    {
        foreach ($params as $key => $value) {
            $currentKey = $prefix ? $prefix . '[' . $key . ']' : $key;

            if (is_array($value)) {
                $this->extractFilesRecursive($params[$key], $files, $currentKey);
            } elseif ($this->isFileObject($value)) {
                $files[$currentKey] = $value;
                unset($params[$key]);
            }
        }
    }

    private function isFileObject($value)
    {
        if (is_object($value)) {
            return $value instanceof \CURLFile ||
                (class_exists('SplFileInfo') && $value instanceof \SplFileInfo) ||
                (class_exists('finfo') && method_exists($value, 'getPathname')) ||
                method_exists($value, '__toString');
        }

        if (is_string($value) && !empty($value)) {
            if (filter_var($value, FILTER_VALIDATE_URL)) {
                return false;
            }
            return file_exists($value);
        }

        return false;
    }

    private function call($endpoint, array $params = [], $method, array $headers = [])
    {
        $ch = $this->init();

        $header = array_merge($this->header, $headers);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        switch (strtoupper($method)) {
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, true);
                if (!empty($params)) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
                }
                break;
            case 'PUT':
            case 'DELETE':
                curl_setopt($ch, CURLOPT_HTTPGET, false);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
                if (!empty($params)) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
                }
                break;
            case 'GET':
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
}