<?php
// Simulate GET request
$_GET['action'] = 'get_provinces';

// Capture output
ob_start();
include 'ongkir.php';
$output = ob_get_clean();

// Check for JSON validity
$json = json_decode($output, true);

// Log result
$log = "Timestamp: " . date('Y-m-d H:i:s') . "\n";
if (json_last_error() === JSON_ERROR_NONE) {
    $log .= "Status: VALID JSON\n";
    $log .= "Output: " . substr($output, 0, 500) . "...\n";
} else {
    $log .= "Status: INVALID JSON (Error: " . json_last_error_msg() . ")\n";
    $log .= "Raw Output:\n" . $output . "\n";
}

file_put_contents('debug_test_result.txt', $log);

echo "Test complete. Check debug_test_result.txt";
