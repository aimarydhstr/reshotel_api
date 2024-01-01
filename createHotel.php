<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include 'config.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_FILES["image"]) && isset($_POST["name"]) && isset($_POST["description"]) && isset($_POST["price"]) && isset($_POST["location"])) {
        $image = $_FILES["image"];
        $name = $_POST["name"];
        $description = $_POST["description"];
        $price = $_POST["price"];
        $location = $_POST["location"];

        $imageType = exif_imagetype($image["tmp_name"]);
        $allowedTypes = [IMAGETYPE_JPEG, IMAGETYPE_PNG];

        if (!in_array($imageType, $allowedTypes)) {
            http_response_code(400);
            echo json_encode(["message" => "Invalid file type"]);
            exit;
        }

        $uploadDir = "uploads/";
        $imageName = uniqid() . '_' . $image["name"];
        $uploadPath = $uploadDir . $imageName;

        if (move_uploaded_file($image["tmp_name"], $uploadPath)) {
            $conn = connectToDatabase();

            // Store the complete file path in the database
            $filePath = $_SERVER['DOCUMENT_ROOT'] . '/' . 'reshotel_api' . $uploadPath;

            $sql = "INSERT INTO hotels (file_name, name, description, price, location, file_path) VALUES ('$imageName', '$name', '$description', '$price', $location', '$filePath')";

            if ($conn->query($sql) === TRUE) {
                echo json_encode(["message" => "Image uploaded and details inserted into the database"]);
                http_response_code(200);
            } else {
                echo json_encode(["message" => "Error inserting data into the database"]);
                http_response_code(500);
            }

            $conn->close();
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Failed to upload image"]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["message" => "Invalid request data"]);
    }
} else {
    http_response_code(405);
    echo json_encode(["message" => "Method Not Allowed"]);
}
?>