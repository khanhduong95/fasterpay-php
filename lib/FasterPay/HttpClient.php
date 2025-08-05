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

    /**
     * Send POST request with automatic multipart detection
     *
     * @param string $endpoint API endpoint
     * @param array $params Form parameters
     * @param array $headers Additional headers
     * @return array
     */
    public function post($endpoint, array $params = [], array $headers = [])
    {
        // Auto-detect files and use multipart if needed
        $files = $this->extractFiles($params);
        
        if (!empty($files)) {
            return $this->postMultipart($endpoint, $params, $files, $headers);
        }
        
        return $this->call($endpoint, $params, 'POST', $headers);
    }

    /**
     * Send PUT request with automatic multipart detection
     * Note: If files detected, uses POST with _method=PUT for multipart compatibility
     *
     * @param string $endpoint API endpoint
     * @param array $params Form parameters
     * @param array $headers Additional headers
     * @return array
     */
    public function put($endpoint, array $params = [], array $headers = [])
    {
        // Auto-detect files and use multipart if needed
        $files = $this->extractFiles($params);
        
        if (!empty($files)) {
            $params['_method'] = 'PUT';
            return $this->postMultipart($endpoint, $params, $files, $headers);
        }
        
        return $this->call($endpoint, $params, 'PUT', $headers);
    }

    public function delete($endpoint, array $params = [], array $headers = [])
    {
        return $this->call($endpoint, $params, 'DELETE', $headers);
    }

    /**
     * Send multipart POST request with file uploads
     *
     * @param string $endpoint API endpoint
     * @param array $params Form parameters
     * @param array $files Array of files where key is field name and value is file path
     * @param array $headers Additional headers
     * @return array
     */
    public function postMultipart($endpoint, array $params = [], array $files = [], array $headers = [])
    {
        $ch = $this->init();

        $header = array_merge($this->header, $headers);
        
        // Remove Content-Type header to let cURL set it automatically for multipart
        $header = array_filter($header, function($h) {
            return stripos($h, 'Content-Type:') !== 0;
        });
        
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_URL, $endpoint);

        // Prepare multipart data
        $postData = [];
        
        // Add regular form fields
        foreach ($params as $key => $value) {
            if (is_array($value)) {
                // Handle nested arrays (like colors, localized_address)
                foreach ($value as $subKey => $subValue) {
                    $postData[$key . '[' . $subKey . ']'] = $subValue;
                }
            } else {
                $postData[$key] = $value;
            }
        }

        // Add files
        foreach ($files as $fieldName => $filePath) {
            if (file_exists($filePath)) {
                if (class_exists('CURLFile')) {
                    // PHP 5.5+
                    $postData[$fieldName] = new \CURLFile($filePath);
                } else {
                    // PHP 5.4 compatibility
                    $postData[$fieldName] = '@' . $filePath;
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

    /**
     * Extract file fields from params for multipart upload
     *
     * @param array $params Reference to params array
     * @return array Files array for multipart upload
     */
    private function extractFiles(array &$params)
    {
        $files = [];
        
        foreach ($params as $field => $value) {
            if (is_string($value) && !empty($value)) {
                // Check if it looks like a file path (not a URL and file exists)
                if (!filter_var($value, FILTER_VALIDATE_URL) && file_exists($value)) {
                    $files[$field] = $value;
                    unset($params[$field]); // Remove from params as it will be sent as file
                }
            }
        }
        
        return $files;
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