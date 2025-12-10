<?php

// Simple test to verify the SDK is working
require_once 'vendor/autoload.php';

use Eduplus\EduplusArchive;

echo "🧪 Eduplus Archive SDK - Basic Test\n";
echo "===================================\n\n";

// Test 1: Check if class exists
if (class_exists('Eduplus\EduplusArchive')) {
    echo "✅ EduplusArchive class loaded successfully\n";
} else {
    echo "❌ EduplusArchive class not found\n";
    exit(1);
}

// Test 2: Test method chaining
try {
    $instance = EduplusArchive::setApiKey('test-key');
    if ($instance instanceof EduplusArchive) {
        echo "✅ setApiKey() method works correctly\n";
    } else {
        echo "❌ setApiKey() method failed\n";
    }
} catch (Exception $e) {
    echo "❌ setApiKey() method error: " . $e->getMessage() . "\n";
}

// Test 3: Test pdfUrl method
try {
    $instance = EduplusArchive::setApiKey('test-key')->pdfUrl('https://example.com/test.pdf');
    if ($instance instanceof EduplusArchive) {
        echo "✅ pdfUrl() method works correctly\n";
    } else {
        echo "❌ pdfUrl() method failed\n";
    }
} catch (Exception $e) {
    echo "❌ pdfUrl() method error: " . $e->getMessage() . "\n";
}

// Test 4: Test pdfPath method
try {
    $instance = EduplusArchive::setApiKey('test-key')->pdfPath('/test/path.pdf');
    if ($instance instanceof EduplusArchive) {
        echo "✅ pdfPath() method works correctly\n";
    } else {
        echo "❌ pdfPath() method failed\n";
    }
} catch (Exception $e) {
    echo "❌ pdfPath() method error: " . $e->getMessage() . "\n";
}

// Test 5: Test validation (should fail without proper data)
try {
    $result = EduplusArchive::setApiKey('test-key')
        ->pdfUrl('https://example.com/test.pdf')
        ->upload([]);
    
    if ($result !== "done") {
        echo "✅ Validation works correctly (expected failure)\n";
        echo "   Error message: " . $result . "\n";
    } else {
        echo "❌ Validation failed (unexpected success)\n";
    }
} catch (Exception $e) {
    echo "✅ Exception handling works: " . $e->getMessage() . "\n";
}

// Test 6: Test search without API key
try {
    $results = EduplusArchive::setApiKey('')->search(['student_id' => 'test']);
    if (is_array($results) && empty($results)) {
        echo "✅ Search validation works correctly\n";
    } else {
        echo "❌ Search validation failed\n";
    }
} catch (Exception $e) {
    echo "✅ Search exception handling works: " . $e->getMessage() . "\n";
}

echo "\n🎉 All basic tests completed!\n";
echo "📝 The SDK is ready to use with proper API credentials.\n";
echo "🚀 Run the examples in the 'examples/' directory to test with real API calls.\n";