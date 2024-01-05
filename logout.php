<?php
header('Content-Type: application/json');
include('config.php');
require('vendor/autoload.php'); // Include Composer autoload

use Firebase\JWT\JWT;

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['token'])) {
    $token = $data['token'];

    try {
        // Decode token to get user_id
        $key = "koderahasia"; // Replace with your secret key
        $decoded = JWT::decode($token, $key, array('HS256'));

        // Perform any additional logout logic here if needed

        $response = array('status' => 'success', 'message' => 'Logout successful');
    } catch (Exception $e) {
        // Token is invalid or expired
        $response = array('status' => 'error', 'message' => 'Invalid token');
    }
} else {
    // Invalid request
    $response = array('status' => 'error', 'message' => 'Invalid request');
}

echo json_encode($response);