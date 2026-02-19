<?php

// Test QR Code Generation
require 'vendor/autoload.php';

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

try {
    echo "Testing QR Code Library...\n";
    echo "PHP Version: " . PHP_VERSION . "\n\n";
    
    // Test data
    $testData = 'TKT-1-test-' . time();
    echo "Test Data: $testData\n";
    
    // Generate QR
    $qrCode = QrCode::create($testData)
        ->setSize(300)
        ->setMargin(10);
    
    $writer = new PngWriter();
    $result = $writer->write($qrCode);
    
    // Save to file
    $filename = 'test_qr_' . time() . '.png';
    file_put_contents($filename, $result->getString());
    
    echo "\n✅ SUCCESS! QR Code saved to: $filename\n";
    echo "File size: " . filesize($filename) . " bytes\n";
    
} catch (Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
