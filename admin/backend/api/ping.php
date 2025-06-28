<?php
/**
 * Ping API Endpoint
 * Simple endpoint to check server connectivity for Virunga Homestay Admin Dashboard
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, HEAD, POST');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Include utility functions
require_once '../utils/response_utils.php';

try {
    // Simple ping response
    $response = [
        'success' => true,
        'message' => 'Server is online',
        'timestamp' => date('Y-m-d H:i:s'),
        'server_time' => time(),
        'status' => 'healthy'
    ];
    
    // For HEAD requests, just return headers
    if ($_SERVER['REQUEST_METHOD'] === 'HEAD') {
        http_response_code(200);
        exit();
    }
    
    // Return JSON response for GET/POST
    echo json_encode($response);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error',
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}
?>
