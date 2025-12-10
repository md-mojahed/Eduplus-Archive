# Eduplus Archive SDK

A PHP SDK for interacting with the Eduplus Archive API - Student Result Archive System. Built with Guzzle HTTP for reliable and modern HTTP communication.

## Requirements

- PHP 7.2 or higher
- Guzzle HTTP 6.5+ or 7.0+ (automatically installed via Composer)

## Installation

Install via Composer:

```bash
composer require eduplus/archive
```

## Features

- **Modern HTTP Client**: Built with Guzzle HTTP for reliable communication
- **Fluent Interface**: Chainable method calls for clean, readable code
- **Comprehensive Error Handling**: Detailed error messages for debugging
- **File Upload Support**: Both URL-based and direct file uploads
- **Search Functionality**: Flexible filtering with multiple parameters
- **PHP 7.2+ Compatible**: Works with both Guzzle 6.5+ and 7.0+

## Quick Start

```php
<?php

require_once 'vendor/autoload.php';

use Eduplus\EduplusArchive;

// Set your API key and base URL
EduplusArchive::setApiKey('your-api-key-here');
EduplusArchive::setBaseUrl('https://your-domain.com'); // Optional, defaults to localhost:8000
```

## Usage

### Upload with PDF URL

```php
use Eduplus\EduplusArchive;

$result = EduplusArchive::setApiKey($apiKey)
    ->pdfUrl('https://example.com/result.pdf')
    ->upload([
        'branch' => 'Main Campus',
        'shift' => 'Morning',
        'version' => 'Bangla',
        'class' => 'Class 10',
        'group' => 'Science',
        'section' => 'A',
        'gender' => 'Boys',
        'session' => '2024',
        'exam' => 'Final Examination',
        'students' => [
            [
                'id' => '2024001',
                'name' => 'Mohammad Abdullah Al Mamun',
                'mobile' => '01712345678',
                'father_name' => 'Abdul Rahman Khan',
                'mother_name' => 'Fatima Begum'
            ],
            [
                'id' => '2024002',
                'name' => 'Ahmed Hassan Siddique',
                'mobile' => '01823456789',
                'father_name' => 'Hassan Ali Siddique',
                'mother_name' => 'Khadija Khatun'
            ]
        ]
    ]);

if ($result === "done") {
    echo "Upload successful!";
} else {
    echo "Error: " . $result;
}
```

### Upload with PDF File

```php
use Eduplus\EduplusArchive;

$result = EduplusArchive::setApiKey($apiKey)
    ->pdfPath('/absolute/path/to/result.pdf')
    ->upload([
        'branch' => 'Main Campus',
        'shift' => 'Morning',
        'version' => 'Bangla',
        'class' => 'Class 9',
        'group' => 'Commerce',
        'section' => 'B',
        'gender' => 'Girls',
        'session' => '2024',
        'exam' => 'Half Yearly Examination',
        'students' => [
            [
                'id' => '2024101',
                'name' => 'Aisha Rahman Chowdhury',
                'mobile' => '01812345678',
                'father_name' => 'Rahman Chowdhury',
                'mother_name' => 'Nasreen Akter'
            ]
        ]
    ]);

if ($result === "done") {
    echo "Upload successful!";
} else {
    echo "Error: " . $result;
}
```

### Search Results

```php
use Eduplus\EduplusArchive;

// Search by student ID
$results = EduplusArchive::setApiKey($apiKey)
    ->search([
        'student_id' => '2024001'
    ]);

if (count($results)) {
    foreach ($results as $data) {
        echo "Institution: " . $data['institution'] . "\n";
        echo "Class: " . $data['class'] . "\n";
        echo "Section: " . $data['section'] . "\n";
        echo "Exam: " . $data['exam'] . "\n";
        echo "PDF URL: " . $data['pdf_url'] . "\n";
        echo "Date: " . $data['date_title'] . "\n";
        echo "---\n";
    }
} else {
    echo "No data found!";
}

// Search with multiple filters
$results = EduplusArchive::setApiKey($apiKey)
    ->search([
        'branch' => 'Main Campus',
        'class' => 'Class 10',
        'section' => 'A',
        'session' => '2024'
    ]);

if (count($results)) {
    foreach ($results as $data) {
        // Process each result
        echo "Found result for: " . $data['class'] . " - " . $data['section'] . "\n";
    }
} else {
    echo "No data found!";
}
```

## API Reference

### Configuration Methods

#### `setApiKey(string $apiKey): EduplusArchive`
Set the API key for authentication.

#### `setBaseUrl(string $baseUrl): void`
Set the base URL for the API (optional, defaults to localhost:8000).

### Upload Methods

#### `pdfUrl(string $url): EduplusArchive`
Set a PDF URL for upload. Use this when you have a publicly accessible PDF URL.

#### `pdfPath(string $path): EduplusArchive`
Set a local PDF file path for upload. Use this when you have a local PDF file.

#### `upload(array $data): string`
Upload the result archive. Returns "done" on success, error message on failure.

**Required fields in `$data`:**
- `branch` (string): Branch name
- `shift` (string): Shift name
- `version` (string): Version name
- `class` (string): Class name
- `group` (string): Group name
- `section` (string): Section name
- `gender` (string): Gender
- `session` (string): Academic session
- `exam` (string): Exam name
- `students` (array): Array of student objects

**Required fields in each student object:**
- `id` (string): Student ID
- `name` (string): Student name
- `father_name` (string): Father's name
- `mother_name` (string): Mother's name
- `mobile` (string, optional): Mobile number

### Search Methods

#### `search(array $filters): array`
Search for results. Returns array of results on success, empty array if no results found.

**Available filters:**
- `student_id` (string): Search by student ID
- `branch` (string): Filter by branch
- `shift` (string): Filter by shift
- `version` (string): Filter by version
- `class` (string): Filter by class
- `group` (string): Filter by group
- `section` (string): Filter by section
- `gender` (string): Filter by gender
- `session` (string): Filter by session
- `exam` (string): Filter by exam

## Response Format

### Upload Response
- Success: Returns `"done"`
- Error: Returns error message string

### Search Response
Returns array of result objects with the following structure:

```php
[
    [
        'institution' => 'MSD School',
        'branch' => 'Main Campus',
        'shift' => 'Morning',
        'version' => 'Bangla',
        'class' => 'Class 10',
        'group' => 'Science',
        'section' => 'A',
        'gender' => 'Boys',
        'session' => '2024',
        'exam' => 'Final Examination',
        'student_count' => 5,
        'pdf_url' => 'https://domain.com/storage/result-cards/...',
        'updated_at' => '2024-12-10T17:45:17.000Z',
        'date_title' => '10 December 2024 5:45 PM'
    ]
]
```

## Error Handling

The SDK never throws exceptions and handles all errors gracefully:

### Upload Method
- **Success**: Returns `"done"`
- **Failure**: Returns descriptive error message string

### Search Method  
- **Success**: Returns array of results
- **No Results**: Returns empty array `[]`
- **Error**: Returns empty array `[]` (never returns error strings for search)

### Common Error Messages
- **API Key Missing**: "API key is required. Use setApiKey() first."
- **Missing Required Fields**: "Field 'branch' is required."
- **Invalid Students Data**: "Students must be a non-empty array."
- **File Not Found**: "PDF file not found: /path/to/file.pdf"
- **File Too Large**: "PDF file size must be less than 10MB."
- **Network Errors**: "Connection Error: ..." or "HTTP Error: 404"

## Examples

### Complete Upload Example

```php
<?php

require_once 'vendor/autoload.php';

use Eduplus\EduplusArchive;

// Configuration
$apiKey = 'your-institution-api-key';
$baseUrl = 'https://archive.yourdomain.com';

EduplusArchive::setBaseUrl($baseUrl);

// Upload with URL
$result = EduplusArchive::setApiKey($apiKey)
    ->pdfUrl('https://example.com/results/class10-final.pdf')
    ->upload([
        'branch' => 'Main Campus',
        'shift' => 'Morning',
        'version' => 'Bangla',
        'class' => 'Class 10',
        'group' => 'Science',
        'section' => 'A',
        'gender' => 'Boys',
        'session' => '2024',
        'exam' => 'Final Examination',
        'students' => [
            [
                'id' => '2024001',
                'name' => 'Mohammad Abdullah Al Mamun',
                'mobile' => '01712345678',
                'father_name' => 'Abdul Rahman Khan',
                'mother_name' => 'Fatima Begum'
            ],
            [
                'id' => '2024002',
                'name' => 'Ahmed Hassan Siddique',
                'mobile' => '01823456789',
                'father_name' => 'Hassan Ali Siddique',
                'mother_name' => 'Khadija Khatun'
            ]
        ]
    ]);

if ($result === "done") {
    echo "✅ Upload successful!\n";
} else {
    echo "❌ Upload failed: " . $result . "\n";
}
```

### Complete Search Example

```php
<?php

require_once 'vendor/autoload.php';

use Eduplus\EduplusArchive;

// Configuration
$apiKey = 'your-institution-api-key';
EduplusArchive::setBaseUrl('https://archive.yourdomain.com');

// Search for a specific student
$studentResults = EduplusArchive::setApiKey($apiKey)
    ->search(['student_id' => '2024001']);

if (count($studentResults)) {
    echo "Found " . count($studentResults) . " results for student 2024001:\n\n";
    
    foreach ($studentResults as $result) {
        echo "📚 {$result['class']} - {$result['section']}\n";
        echo "🏫 {$result['institution']}\n";
        echo "📝 {$result['exam']}\n";
        echo "📅 {$result['date_title']}\n";
        echo "🔗 {$result['pdf_url']}\n";
        echo "---\n";
    }
} else {
    echo "No results found for student 2024001\n";
}

// Search by class and section
$classResults = EduplusArchive::setApiKey($apiKey)
    ->search([
        'class' => 'Class 10',
        'section' => 'A',
        'session' => '2024'
    ]);

if (count($classResults)) {
    echo "\nFound " . count($classResults) . " results for Class 10 - Section A:\n";
    
    foreach ($classResults as $result) {
        echo "📝 {$result['exam']} ({$result['student_count']} students)\n";
    }
} else {
    echo "\nNo results found for Class 10 - Section A\n";
}
```

## License

This SDK is open-sourced software licensed under the [MIT license](LICENSE).

## Support

For support and questions:
- Email: info@lighttechnologies.com.bd
- Website: https://lighttechnologies.com.bd

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.