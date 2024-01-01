<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include 'config.php';

if ($_SERVER["REQUEST_METHOD"] === "PUT") {
    if (isset($_FILES["image"]) && isset($_POST["id"]) && isset($_POST["name"]) && isset($_POST["description"]) && isset($_POST["price"]) && isset($_POST["location"])) {
        $image = $_FILES["image"];
        $id = $_POST["id"];
        $name = $_POST["name"];
        $description = $_POST["description"];
        $price = $_POST["price"];
        $location = $_POST["location"];

        if (!empty($image["tmp_name"])) {
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

            if (!move_uploaded_file($image["tmp_name"], $uploadPath)) {
                http_response_code(500);
                echo json_encode(["message" => "Failed to upload image"]);
                exit;
            }

            // Delete the existing image file if it exists
            $conn = connectToDatabase();
            $sqlSelect = "SELECT file_path FROM hotels WHERE id = '$id'";
            $result = $conn->query($sqlSelect);

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $existingFilePath = $row["file_path"];
                if (file_exists($existingFilePath)) {
                    unlink($existingFilePath);
                }
            }
        }

        $conn = connectToDatabase();

        $updateImagePart = (!empty($image["tmp_name"])) ? ", file_name = '$imageName', file_path = '$uploadPath'" : "";
        $sql = "UPDATE hotels SET name = '$name', description = '$description', price = '$price', location = '$location' $updateImagePart WHERE id = '$id'";

        if ($conn->query($sql) === TRUE) {
            http_response_code(200);
            echo json_encode(["message" => "Record updated successfully"]);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Error updating data in the database"]);
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
