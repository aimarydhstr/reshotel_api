<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include 'config.php';

if ($_SERVER["REQUEST_METHOD"] === "DELETE") {
    if (isset($_POST["id"])) {
        $id = $_POST["id"];

        $conn = connectToDatabase();

        $uploadDir = "uploads/";
        $filePath = $uploadDir . $fileName;

        if (unlink($filePath)) {
            $sql = "DELETE FROM hotels WHERE id = '$id'";

            if ($conn->query($sql) === TRUE) {
                echo json_encode(["message" => "Image deleted successfully"]);
                http_response_code(200);
            } else {
                echo json_encode(["message" => "Error deleting image from the database"]);
                http_response_code(500);
            }
        } else {
            echo json_encode(["message" => "Error deleting image from the server"]);
            http_response_code(500);
        }

        $conn->close();
    } else {
        http_response_code(400);
        echo json_encode(["message" => "Invalid request data"]);
    }
} else {
    http_response_code(405);
    echo json_encode(["message" => "Method Not Allowed"]);
}
?>