<?php

require_once '../vendor/autoload.php';

use Eduplus\EduplusArchive;

// Configuration
$apiKey = 'your-institution-api-key';
$baseUrl = 'https://archive.yourdomain.com';

// Set base URL (optional, defaults to localhost:8000)
EduplusArchive::setBaseUrl($baseUrl);

echo "🚀 Eduplus Archive SDK - Search Results Example\n";
echo "===============================================\n\n";

// Example 1: Search by Student ID
echo "🔍 Example 1: Search by Student ID\n";
echo str_repeat("-", 40) . "\n";

$studentResults = EduplusArchive::setApiKey($apiKey)
    ->search([
        'student_id' => '2024001'
    ]);

if (count($studentResults)) {
    echo "✅ Found " . count($studentResults) . " result(s) for student ID: 2024001\n\n";
    
    foreach ($studentResults as $index => $result) {
        echo "📚 Result #" . ($index + 1) . ":\n";
        echo "   🏫 Institution: " . $result['institution'] . "\n";
        echo "   🏢 Branch: " . $result['branch'] . "\n";
        echo "   📖 Class: " . $result['class'] . " - " . $result['section'] . "\n";
        echo "   👥 Group: " . $result['group'] . " (" . $result['gender'] . ")\n";
        echo "   📝 Exam: " . $result['exam'] . "\n";
        echo "   📅 Date: " . $result['date_title'] . "\n";
        echo "   👨‍🎓 Students: " . $result['student_count'] . "\n";
        echo "   🔗 PDF: " . $result['pdf_url'] . "\n";
        echo "\n";
    }
} else {
    echo "❌ No results found for student ID: 2024001\n\n";
}

// Example 2: Search by Class and Section
echo "🔍 Example 2: Search by Class and Section\n";
echo str_repeat("-", 40) . "\n";

$classResults = EduplusArchive::setApiKey($apiKey)
    ->search([
        'class' => 'Class 10',
        'section' => 'A',
        'session' => '2024'
    ]);

if (count($classResults)) {
    echo "✅ Found " . count($classResults) . " result(s) for Class 10 - Section A (2024)\n\n";
    
    foreach ($classResults as $index => $result) {
        echo "📝 Exam #" . ($index + 1) . ": " . $result['exam'] . "\n";
        echo "   📅 " . $result['date_title'] . " (" . $result['student_count'] . " students)\n";
        echo "   🔗 " . $result['pdf_url'] . "\n\n";
    }
} else {
    echo "❌ No results found for Class 10 - Section A (2024)\n\n";
}

// Example 3: Search by Branch and Gender
echo "🔍 Example 3: Search by Branch and Gender\n";
echo str_repeat("-", 40) . "\n";

$branchResults = EduplusArchive::setApiKey($apiKey)
    ->search([
        'branch' => 'Main Campus',
        'gender' => 'Girls',
        'session' => '2024'
    ]);

if (count($branchResults)) {
    echo "✅ Found " . count($branchResults) . " result(s) for Main Campus - Girls (2024)\n\n";
    
    $totalStudents = 0;
    foreach ($branchResults as $result) {
        $totalStudents += $result['student_count'];
        echo "📚 " . $result['class'] . " - " . $result['section'] . " (" . $result['group'] . ")\n";
        echo "   📝 " . $result['exam'] . " - " . $result['student_count'] . " students\n";
        echo "   📅 " . $result['date_title'] . "\n\n";
    }
    
    echo "📊 Total students across all results: " . $totalStudents . "\n\n";
} else {
    echo "❌ No results found for Main Campus - Girls (2024)\n\n";
}

// Example 4: Search with Multiple Filters
echo "🔍 Example 4: Advanced Search with Multiple Filters\n";
echo str_repeat("-", 50) . "\n";

$advancedResults = EduplusArchive::setApiKey($apiKey)
    ->search([
        'branch' => 'Main Campus',
        'shift' => 'Morning',
        'version' => 'Bangla',
        'group' => 'Science',
        'session' => '2024',
        'exam' => 'Final Examination'
    ]);

if (count($advancedResults)) {
    echo "✅ Found " . count($advancedResults) . " result(s) matching advanced criteria:\n";
    echo "   🏢 Branch: Main Campus\n";
    echo "   🕐 Shift: Morning\n";
    echo "   📖 Version: Bangla\n";
    echo "   👥 Group: Science\n";
    echo "   📅 Session: 2024\n";
    echo "   📝 Exam: Final Examination\n\n";
    
    foreach ($advancedResults as $result) {
        echo "📚 " . $result['class'] . " - " . $result['section'] . " (" . $result['gender'] . ")\n";
        echo "   👨‍🎓 " . $result['student_count'] . " students\n";
        echo "   📅 " . $result['date_title'] . "\n";
        echo "   🔗 " . $result['pdf_url'] . "\n\n";
    }
} else {
    echo "❌ No results found matching the advanced criteria\n\n";
}

echo str_repeat("=", 50) . "\n";
echo "📝 Note: Make sure to:\n";
echo "   1. Replace 'your-institution-api-key' with your actual API key\n";
echo "   2. Update the base URL to point to your Eduplus Archive instance\n";
echo "   3. Modify the search parameters to match your actual data\n";
echo "   4. Ensure you have uploaded some results before searching\n";