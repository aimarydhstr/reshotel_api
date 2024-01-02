<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include 'config.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_FILES["image"], $_POST["name"], $_POST["description"], $_POST["price"], $_POST["location"])) {
        $image = $_FILES["image"];
        $name = $_POST["name"];
        $description = $_POST["description"];
        $price = $_POST["price"];
        $location = $_POST["location"];

        $imageType = exif_imagetype($image["tmp_name"]);
        $allowedTypes = [IMAGETYPE_JPEG, IMAGETYPE_PNG];

        if (!in_array($imageType, $allowedTypes)) {
            respondError("Invalid file type", 400);
        }

        $uploadDir = "uploads/";
        $imageName = uniqid() . '_' . $image["name"];
        $uploadPath = $uploadDir . $imageName;

        if (move_uploaded_file($image["tmp_name"], $uploadPath)) {
            $filePath = $_SERVER['DOCUMENT_ROOT'] . '/reshotel_api/' . $uploadPath;

            $sql = "INSERT INTO hotels (file_name, name, description, price, location, file_path) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssss", $imageName, $name, $description, $price, $location, $filePath);

            if ($stmt->execute()) {
                respondSuccess("Image uploaded and details inserted into the database");
            } else {
                respondError("Error inserting data into the database", 500);
            }

            $stmt->close();
        } else {
            respondError("Failed to upload image", 500);
        }
    } else {
        respondError("Invalid request data", 400);
    }
} else {
    respondError("Method Not Allowed", 405);
}

function respondSuccess($message) {
    echo json_encode(["message" => $message]);
    http_response_code(200);
    exit;
}

function respondError($message, $statusCode) {
    echo json_encode(["message" => $message]);
    http_response_code($statusCode);
    exit;
}
?>
