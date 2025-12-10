<?php

namespace Eduplus;

/**
 * Eduplus Archive SDK
 * 
 * A PHP SDK for interacting with the Eduplus Archive API
 * 
 * @package Eduplus\Archive
 * @author Light Technologies
 * @version 1.0.0
 */
class EduplusArchive
{
    /**
     * API Key for authentication
     * 
     * @var string
     */
    private static $apiKey;

    /**
     * Base URL for the API
     * 
     * @var string
     */
    private static $baseUrl = 'http://localhost:8000';

    /**
     * PDF URL for upload
     * 
     * @var string
     */
    private $pdfUrl;

    /**
     * PDF file path for upload
     * 
     * @var string
     */
    private $pdfPath;

    /**
     * Set the API key
     * 
     * @param string $apiKey
     * @return EduplusArchive
     */
    public static function setApiKey($apiKey)
    {
        self::$apiKey = $apiKey;
        return new self();
    }

    /**
     * Set the base URL for the API
     * 
     * @param string $baseUrl
     * @return void
     */
    public static function setBaseUrl($baseUrl)
    {
        self::$baseUrl = rtrim($baseUrl, '/');
    }

    /**
     * Set PDF URL for upload
     * 
     * @param string $url
     * @return EduplusArchive
     */
    public function pdfUrl($url)
    {
        $this->pdfUrl = $url;
        return $this;
    }

    /**
     * Set PDF file path for upload
     * 
     * @param string $path
     * @return EduplusArchive
     */
    public function pdfPath($path)
    {
        $this->pdfPath = $path;
        return $this;
    }

    /**
     * Upload result archive
     * 
     * @param array $data
     * @return string Returns "done" on success, error message on failure
     */
    public function upload($data)
    {
        if (!self::$apiKey) {
            return 'API key is required. Use setApiKey() first.';
        }

        // Validate required fields
        $requiredFields = ['branch', 'shift', 'version', 'class', 'group', 'section', 'gender', 'session', 'exam', 'students'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                return "Field '{$field}' is required.";
            }
        }

        // Validate students array
        if (!is_array($data['students']) || empty($data['students'])) {
            return 'Students must be a non-empty array.';
        }

        // Validate each student
        foreach ($data['students'] as $index => $student) {
            $requiredStudentFields = ['id', 'name', 'father_name', 'mother_name'];
            foreach ($requiredStudentFields as $field) {
                if (!isset($student[$field]) || empty($student[$field])) {
                    return "Student at index {$index} is missing required field '{$field}'.";
                }
            }
        }

        try {
            if ($this->pdfPath) {
                return $this->uploadWithFile($data);
            } elseif ($this->pdfUrl) {
                return $this->uploadWithUrl($data);
            } else {
                return 'Either pdfUrl() or pdfPath() must be called before upload().';
            }
        } catch (\Exception $e) {
            return 'Upload failed: ' . $e->getMessage();
        }
    }

    /**
     * Search for results
     * 
     * @param array $filters
     * @return array Returns array of results on success, empty array if no results
     */
    public function search($filters = [])
    {
        if (!self::$apiKey) {
            return [];
        }

        if (empty($filters)) {
            return [];
        }

        try {
            $queryString = http_build_query($filters);
            $url = self::$baseUrl . '/api/archive/search?' . $queryString;

            $response = $this->makeRequest('GET', $url);

            if ($response && isset($response['status']) && $response['status'] === 'success') {
                return isset($response['data']['results']) ? $response['data']['results'] : [];
            }

            return [];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Upload with URL
     * 
     * @param array $data
     * @return string
     */
    private function uploadWithUrl($data)
    {
        $data['result_file'] = $this->pdfUrl;
        
        $url = self::$baseUrl . '/api/archive/upload';
        $response = $this->makeRequest('POST', $url, $data, ['Content-Type: application/json']);

        if ($response && isset($response['status']) && $response['status'] === 'success') {
            return 'done';
        }

        return isset($response['message']) ? $response['message'] : 'Upload failed with unknown error.';
    }

    /**
     * Upload with file
     * 
     * @param array $data
     * @return string
     */
    private function uploadWithFile($data)
    {
        if (!file_exists($this->pdfPath)) {
            return 'PDF file not found: ' . $this->pdfPath;
        }

        if (!is_readable($this->pdfPath)) {
            return 'PDF file is not readable: ' . $this->pdfPath;
        }

        // Validate file type
        $fileInfo = pathinfo($this->pdfPath);
        if (!isset($fileInfo['extension']) || strtolower($fileInfo['extension']) !== 'pdf') {
            return 'File must be a PDF: ' . $this->pdfPath;
        }

        // Check file size (10MB limit)
        $fileSize = filesize($this->pdfPath);
        if ($fileSize > 10 * 1024 * 1024) {
            return 'PDF file size must be less than 10MB.';
        }

        $url = self::$baseUrl . '/api/archive/upload-file';
        $response = $this->makeMultipartRequest($url, $data, $this->pdfPath);

        if ($response && isset($response['status']) && $response['status'] === 'success') {
            return 'done';
        }

        return isset($response['message']) ? $response['message'] : 'Upload failed with unknown error.';
    }

    /**
     * Make HTTP request
     * 
     * @param string $method
     * @param string $url
     * @param array $data
     * @param array $headers
     * @return array|null
     */
    private function makeRequest($method, $url, $data = null, $headers = [])
    {
        $ch = curl_init();

        // Default headers
        $defaultHeaders = [
            'x-api-key: ' . self::$apiKey,
            'User-Agent: EduplusArchive-SDK/1.0.0'
        ];

        $allHeaders = array_merge($defaultHeaders, $headers);

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_HTTPHEADER => $allHeaders,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);

        if ($method === 'POST' && $data) {
            curl_setopt($ch, CURLOPT_POST, true);
            if (in_array('Content-Type: application/json', $headers)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            } else {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            }
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            throw new \Exception('cURL Error: ' . $error);
        }

        if ($httpCode >= 400) {
            throw new \Exception('HTTP Error: ' . $httpCode);
        }

        return json_decode($response, true);
    }

    /**
     * Make multipart request for file upload
     * 
     * @param string $url
     * @param array $data
     * @param string $filePath
     * @return array|null
     */
    private function makeMultipartRequest($url, $data, $filePath)
    {
        $ch = curl_init();

        // Prepare multipart data
        $postData = [];
        
        // Add regular fields
        foreach ($data as $key => $value) {
            if ($key === 'students') {
                // Handle students array
                foreach ($value as $index => $student) {
                    foreach ($student as $studentKey => $studentValue) {
                        $postData["students[{$index}][{$studentKey}]"] = $studentValue;
                    }
                }
            } else {
                $postData[$key] = $value;
            }
        }

        // Add file
        if (class_exists('CURLFile')) {
            $postData['result_file'] = new \CURLFile($filePath, 'application/pdf', basename($filePath));
        } else {
            // Fallback for older PHP versions
            $postData['result_file'] = '@' . $filePath . ';type=application/pdf';
        }

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 60, // Longer timeout for file uploads
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $postData,
            CURLOPT_HTTPHEADER => [
                'x-api-key: ' . self::$apiKey,
                'User-Agent: EduplusArchive-SDK/1.0.0'
            ],
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            throw new \Exception('cURL Error: ' . $error);
        }

        if ($httpCode >= 400) {
            throw new \Exception('HTTP Error: ' . $httpCode);
        }

        return json_decode($response, true);
    }
}