<?php
header('Content-Type: application/json');
include('config.php');
require('vendor/autoload.php'); // Include Composer autoload

use Firebase\JWT\JWT;

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['username']) && isset($data['password'])) {
    $username = $data['username'];
    $password = $data['password'];

    // Validate $username and $password (sanitize inputs in a real-world scenario)

    $query = "SELECT * FROM users WHERE username = '$username'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if(password_verify($password, $user['password'])){

            // Generate a JWT token
            $key = "koderahasia"; // Replace with your secret key
            $token = JWT::encode(['user_id' => $user['id']], $key, 'HS256');

            $response = array('status' => 'success', 'message' => 'Login successful', 'token' => $token);
        }
    } else {
        // Authentication failed
        $response = array('status' => 'error', 'message' => 'Invalid username or password');
    }
} else {
    // Invalid request
    $response = array('status' => 'error', 'message' => 'Invalid request');
}

echo json_encode($response);
$conn->close();
?>