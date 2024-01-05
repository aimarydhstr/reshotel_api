<?php
header('Content-Type: application/json');
require_once 'config.php';

$data = json_decode(file_get_contents('php://input'), true);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $data['name'];
    $username = $data['username'];
    $password = password_hash($data['password'], PASSWORD_DEFAULT);
    $role = 'User';

    $sql = "INSERT INTO users (name, username, password, role) VALUES ('$name', '$username', '$password', '$role')";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(['message' => 'Registrasi berhasil']);
    } else {
        echo json_encode(['error' => 'Registrasi gagal']);
    }
} else {
    echo json_encode(['error' => 'Invalid request method']);
}
?>