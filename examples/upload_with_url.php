<?php

require_once '../vendor/autoload.php';

use Eduplus\EduplusArchive;

// Configuration
$apiKey = 'your-institution-api-key';

echo "🚀 Eduplus Archive SDK - Upload with URL Example\n";
echo "================================================\n\n";

// Upload result archive with PDF URL
$result = EduplusArchive::setApiKey($apiKey)
    ->pdfUrl('https://iscm.edu.bd/storage/notice/attachments/0VP91mQ7kVKc7YWp5l8hXL10Il4HZJ4k1AsbOAmS.pdf')
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
            ],
            [
                'id' => '2024003',
                'name' => 'Omar Faruk Rahman',
                'mobile' => '01934567890',
                'father_name' => 'Abdur Rahman Sheikh',
                'mother_name' => 'Rashida Begum'
            ],
            [
                'id' => '2024004',
                'name' => 'Ibrahim Khalil Ahmed',
                'mobile' => '01645678901',
                'father_name' => 'Khalil Ahmed Miah',
                'mother_name' => 'Salma Khatun'
            ],
            [
                'id' => '2024005',
                'name' => 'Yusuf Ali Khan',
                'mobile' => '01756789012',
                'father_name' => 'Ali Khan Chowdhury',
                'mother_name' => 'Amina Begum'
            ]
        ]
    ]);

// Check result
if ($result === "done") {
    echo "✅ Upload successful!\n";
    echo "📄 Result archive has been uploaded successfully.\n";
    echo "🎯 Class 10 Science Boys - Final Examination (5 students)\n";
} else {
    echo "❌ Upload failed!\n";
    echo "🚨 Error: " . $result . "\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "📝 Note: Make sure to replace 'your-institution-api-key' with your actual API key.\n";