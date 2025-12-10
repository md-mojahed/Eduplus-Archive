<?php

require_once '../vendor/autoload.php';

use Eduplus\EduplusArchive;

// Configuration
$apiKey = 'your-institution-api-key';
$baseUrl = 'https://archive.yourdomain.com';
$pdfFilePath = '/absolute/path/to/your/result.pdf'; // Update this path

// Set base URL (optional, defaults to localhost:8000)
EduplusArchive::setBaseUrl($baseUrl);

echo "🚀 Eduplus Archive SDK - Upload with File Example\n";
echo "=================================================\n\n";

// Check if file exists
if (!file_exists($pdfFilePath)) {
    echo "❌ PDF file not found: " . $pdfFilePath . "\n";
    echo "📝 Please update the \$pdfFilePath variable with the correct path to your PDF file.\n";
    exit(1);
}

echo "📁 Using PDF file: " . $pdfFilePath . "\n";
echo "📊 File size: " . number_format(filesize($pdfFilePath) / 1024, 2) . " KB\n\n";

// Upload result archive with PDF file
$result = EduplusArchive::setApiKey($apiKey)
    ->pdfPath($pdfFilePath)
    ->upload([
        'branch' => 'North Campus',
        'shift' => 'Day',
        'version' => 'English',
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
            ],
            [
                'id' => '2024102',
                'name' => 'Fatima Tuz Zahra',
                'mobile' => '01923456789',
                'father_name' => 'Mohammad Zakariya',
                'mother_name' => 'Rahima Begum'
            ],
            [
                'id' => '2024103',
                'name' => 'Hafsa Khatun Ahmed',
                'mobile' => '01634567890',
                'father_name' => 'Ahmed Hossain',
                'mother_name' => 'Kulsum Begum'
            ],
            [
                'id' => '2024104',
                'name' => 'Mariam Sultana',
                'mobile' => '01745678901',
                'father_name' => 'Sultan Ahmed',
                'mother_name' => 'Razia Sultana'
            ],
            [
                'id' => '2024105',
                'name' => 'Zainab Khatun',
                'mobile' => '01856789012',
                'father_name' => 'Abdul Karim',
                'mother_name' => 'Jahanara Begum'
            ],
            [
                'id' => '2024106',
                'name' => 'Ruqayyah Siddique',
                'mobile' => '01967890123',
                'father_name' => 'Siddique Rahman',
                'mother_name' => 'Mahmuda Khatun'
            ]
        ]
    ]);

// Check result
if ($result === "done") {
    echo "✅ Upload successful!\n";
    echo "📄 Result archive has been uploaded successfully.\n";
    echo "🎯 Class 9 Commerce Girls - Half Yearly Examination (6 students)\n";
    echo "📁 PDF file uploaded from local path\n";
} else {
    echo "❌ Upload failed!\n";
    echo "🚨 Error: " . $result . "\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "📝 Note: Make sure to:\n";
echo "   1. Replace 'your-institution-api-key' with your actual API key\n";
echo "   2. Update the base URL to point to your Eduplus Archive instance\n";
echo "   3. Set the correct path to your PDF file in \$pdfFilePath\n";
echo "   4. Ensure the PDF file is readable and less than 10MB\n";