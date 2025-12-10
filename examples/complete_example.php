<?php

require_once '../vendor/autoload.php';

use Eduplus\EduplusArchive;

// Configuration
$apiKey = 'your-institution-api-key';
$baseUrl = 'https://archive.yourdomain.com';

// Set base URL (optional, defaults to localhost:8000)
EduplusArchive::setBaseUrl($baseUrl);

echo "🚀 Eduplus Archive SDK - Complete Example\n";
echo "=========================================\n\n";

// Example usage as described in the requirements
echo "📤 Testing Upload with URL...\n";
echo str_repeat("-", 30) . "\n";

$result = EduplusArchive::setApiKey($apiKey)
    ->pdfUrl('https://iscm.edu.bd/storage/notice/attachments/0VP91mQ7kVKc7YWp5l8hXL10Il4HZJ4k1AsbOAmS.pdf')
    ->upload([
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
        ],
        'branch' => 'Main Campus',
        'shift' => 'Morning',
        'version' => 'Bangla',
        'class' => 'Class 10',
        'group' => 'Science',
        'section' => 'A',
        'gender' => 'Boys',
        'session' => '2024',
        'exam' => 'Final Examination'
    ]);

if ($result == "done") {
    echo "✅ Upload successful!\n";
} else {
    echo "❌ Error: " . $result . "\n";
}

echo "\n";

// Example with file upload (if you have a local PDF file)
echo "📤 Testing Upload with File...\n";
echo str_repeat("-", 30) . "\n";

$pdfPath = '/path/to/your/result.pdf'; // Update this path

if (file_exists($pdfPath)) {
    $result = EduplusArchive::setApiKey($apiKey)
        ->pdfPath($pdfPath)
        ->upload([
            'students' => [
                [
                    'id' => '2024101',
                    'name' => 'Aisha Rahman Chowdhury',
                    'mobile' => '01812345678',
                    'father_name' => 'Rahman Chowdhury',
                    'mother_name' => 'Nasreen Akter'
                ],
                [
                    'id' => '2024102',
                    'name' => 'Fatima Tuz Zahra',
                    'mobile' => '01923456789',
                    'father_name' => 'Mohammad Zakariya',
                    'mother_name' => 'Rahima Begum'
                ]
            ],
            'branch' => 'North Campus',
            'shift' => 'Day',
            'version' => 'English',
            'class' => 'Class 9',
            'group' => 'Commerce',
            'section' => 'B',
            'gender' => 'Girls',
            'session' => '2024',
            'exam' => 'Half Yearly Examination'
        ]);

    if ($result == "done") {
        echo "✅ File upload successful!\n";
    } else {
        echo "❌ Error: " . $result . "\n";
    }
} else {
    echo "⚠️  Skipping file upload (PDF file not found: " . $pdfPath . ")\n";
    echo "📝 Update \$pdfPath variable with correct file path to test file upload\n";
}

echo "\n";

// Search examples
echo "🔍 Testing Search...\n";
echo str_repeat("-", 20) . "\n";

// Search by student ID
$result = EduplusArchive::setApiKey($apiKey)
    ->search([
        'student_id' => '2024001'
    ]);

if (count($result)) {
    echo "✅ Found " . count($result) . " result(s) for student 2024001:\n";
    foreach ($result as $data) {
        echo "   📚 " . $data['class'] . " - " . $data['section'] . "\n";
        echo "   🏫 " . $data['institution'] . "\n";
        echo "   📝 " . $data['exam'] . "\n";
        echo "   📅 " . $data['date_title'] . "\n";
        echo "   🔗 " . $data['pdf_url'] . "\n";
        echo "\n";
    }
} else {
    echo "❌ No data found for student 2024001!\n\n";
}

// Search by class and branch
$result = EduplusArchive::setApiKey($apiKey)
    ->search([
        'branch' => 'Main Campus',
        'class' => 'Class 10',
        'section' => 'A'
    ]);

if (count($result)) {
    echo "✅ Found " . count($result) . " result(s) for Class 10-A at Main Campus:\n";
    foreach ($result as $data) {
        echo "   📝 " . $data['exam'] . " (" . $data['student_count'] . " students)\n";
        echo "   📅 " . $data['date_title'] . "\n";
    }
} else {
    echo "❌ No data found for Class 10-A at Main Campus!\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "🎯 SDK Usage Summary:\n";
echo "   • Use setApiKey() to authenticate\n";
echo "   • Use pdfUrl() for URL-based uploads\n";
echo "   • Use pdfPath() for file-based uploads\n";
echo "   • Use upload() with student and institutional data\n";
echo "   • Use search() with various filter options\n";
echo "   • Returns 'done' on successful upload\n";
echo "   • Returns array of results for search\n\n";

echo "📝 Remember to:\n";
echo "   1. Replace 'your-institution-api-key' with actual API key\n";
echo "   2. Update base URL to your Eduplus Archive instance\n";
echo "   3. Ensure PDF files are accessible and under 10MB\n";
echo "   4. Use proper student data structure as shown\n";