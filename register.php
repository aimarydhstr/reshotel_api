<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = 'User';

    $sql = "INSERT INTO users (username, password, role) VALUES ('$username', '$password', '$role')";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(['message' => 'Registrasi berhasil']);
    } else {
        echo json_encode(['error' => 'Registrasi gagal']);
    }
} else {
    echo json_encode(['error' => 'Invalid request method']);
}
?>