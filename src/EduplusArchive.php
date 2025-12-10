<?php

namespace Eduplus;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Psr7\MultipartStream;

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
     * Guzzle HTTP Client
     * 
     * @var Client
     */
    private $httpClient;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->httpClient = new Client([
            'timeout' => 360,
            'connect_timeout' => 10,
            'verify' => false, // For development, set to true in production
            'http_errors' => false, // Handle HTTP errors manually
        ]);
    }

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

        if ($this->pdfPath) {
            return $this->uploadWithFile($data);
        } elseif ($this->pdfUrl) {
            return $this->uploadWithUrl($data);
        } else {
            return 'Either pdfUrl() or pdfPath() must be called before upload().';
        }
    }

    /**
     * Search for results
     * 
     * @param array $filters
     * @return array Returns array of results on success, empty array if no results or on error
     */
    public function search($filters = [])
    {
        if (!self::$apiKey) {
            return [];
        }

        if (empty($filters)) {
            return [];
        }

        $url = self::$baseUrl . '/api/archive/search';

        $response = $this->makeRequest('GET', $url, [
            'query' => $filters
        ]);

        // If response is a string (error), return empty array for search
        if (is_string($response)) {
            return [];
        }

        if ($response && isset($response['status']) && $response['status'] === 'success') {
            return isset($response['data']['results']) ? $response['data']['results'] : [];
        }

        return [];
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
        $response = $this->makeRequest('POST', $url, [
            'json' => $data
        ]);

        // If response is a string (error), return it directly
        if (is_string($response)) {
            return $response;
        }

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

        // If response is a string (error), return it directly
        if (is_string($response)) {
            return $response;
        }

        if ($response && isset($response['status']) && $response['status'] === 'success') {
            return 'done';
        }

        return isset($response['message']) ? $response['message'] : 'Upload failed with unknown error.';
    }

    /**
     * Make HTTP request using Guzzle
     * 
     * @param string $method
     * @param string $url
     * @param array $options
     * @return array|string Returns array on success, error string on failure
     */
    private function makeRequest($method, $url, $options = [])
    {
        try {
            // Set default headers
            $defaultOptions = [
                'headers' => [
                    'x-api-key' => self::$apiKey,
                ]
            ];

            // Merge options
            $requestOptions = array_merge_recursive($defaultOptions, $options);

            $response = $this->httpClient->request($method, $url, $requestOptions);
            
            // Check for HTTP errors
            $statusCode = $response->getStatusCode();
            if ($statusCode >= 400) {
                return 'HTTP Error: ' . $statusCode;
            }
            
            $body = $response->getBody()->getContents();
            $decoded = json_decode($body, true);
            
            // Return decoded JSON or error message if JSON is invalid
            return $decoded !== null ? $decoded : 'Invalid JSON response from server';

        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $statusCode = $e->getResponse()->getStatusCode();
                return 'HTTP Error: ' . $statusCode;
            }
            return 'Request Error: ' . $e->getMessage();
        } catch (ConnectException $e) {
            return 'Connection Error: ' . $e->getMessage();
        } catch (\Exception $e) {
            return 'Request failed: ' . $e->getMessage();
        }
    }

    /**
     * Make multipart request for file upload using Guzzle
     * 
     * @param string $url
     * @param array $data
     * @param string $filePath
     * @return array|string Returns array on success, error string on failure
     */
    private function makeMultipartRequest($url, $data, $filePath)
    {
        try {
            // Check if file can be opened
            $fileHandle = fopen($filePath, 'r');
            if ($fileHandle === false) {
                return 'Cannot open file for reading: ' . $filePath;
            }

            // Prepare multipart data
            $multipart = [];
            
            // Add regular fields
            foreach ($data as $key => $value) {
                if ($key === 'students') {
                    // Handle students array
                    foreach ($value as $index => $student) {
                        foreach ($student as $studentKey => $studentValue) {
                            $multipart[] = [
                                'name' => "students[{$index}][{$studentKey}]",
                                'contents' => $studentValue
                            ];
                        }
                    }
                } else {
                    $multipart[] = [
                        'name' => $key,
                        'contents' => $value
                    ];
                }
            }

            // Add file
            $multipart[] = [
                'name' => 'result_file',
                'contents' => $fileHandle,
                'filename' => basename($filePath),
                'headers' => [
                    'Content-Type' => 'application/pdf'
                ]
            ];

            $response = $this->httpClient->request('POST', $url, [
                'multipart' => $multipart,
                'timeout' => 60, // Longer timeout for file uploads
                'headers' => [
                    'x-api-key' => self::$apiKey,
                    'User-Agent' => 'EduplusArchive-SDK/1.0.0'
                ]
            ]);
            
            // Check for HTTP errors
            $statusCode = $response->getStatusCode();
            if ($statusCode >= 400) {
                return 'HTTP Error: ' . $statusCode;
            }
            
            $body = $response->getBody()->getContents();
            $decoded = json_decode($body, true);
            
            // Return decoded JSON or error message if JSON is invalid
            return $decoded !== null ? $decoded : 'Invalid JSON response from server';

        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $statusCode = $e->getResponse()->getStatusCode();
                return 'HTTP Error: ' . $statusCode;
            }
            return 'Request Error: ' . $e->getMessage();
        } catch (ConnectException $e) {
            return 'Connection Error: ' . $e->getMessage();
        } catch (\Exception $e) {
            return 'File upload failed: ' . $e->getMessage();
        }
    }
}