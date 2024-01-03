<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include 'config.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["id"], $_POST["name"], $_POST["description"], $_POST["price"], $_POST["location"])) {
        $hotelId = $_POST["id"];
        $name = $_POST["name"];
        $description = $_POST["description"];
        $price = $_POST["price"];
        $location = $_POST["location"];
        $existingImage = null;

        // Select existing image data
        $selectSql = "SELECT file_name FROM hotels WHERE id=?";
        $selectStmt = $conn->prepare($selectSql);
        $selectStmt->bind_param("i", $hotelId);
        $selectStmt->execute();
        $selectStmt->bind_result($existingImage);
        $selectStmt->fetch();
        $selectStmt->close();

        if (isset($_FILES["image"])) {
            $image = $_FILES["image"];
            $imageType = exif_imagetype($image["tmp_name"]);
            $allowedTypes = [IMAGETYPE_JPEG, IMAGETYPE_PNG];

            if (!in_array($imageType, $allowedTypes)) {
                respondError("Invalid file type", 400);
            }

            $uploadDir = "uploads/";
            $imageName = uniqid() . '_' . $image["name"];
            $uploadPath = $uploadDir . $imageName;

            if (move_uploaded_file($image["tmp_name"], $uploadPath)) {
                if ($existingImage !== null) {
                    $oldImagePath = $_SERVER['DOCUMENT_ROOT'] . '/reshotel_api/uploads/' . $existingImage;
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }
                $existingImage = $imageName;
            } else {
                respondError("Failed to upload image", 500);
            }
        }

        $sql = "UPDATE hotels SET name=?, description=?, price=?, location=?";
        $params = [$name, $description, $price, $location];

        if ($existingImage !== null) {
            $sql .= ", file_name=?, file_path=?";
            $params[] = $existingImage;
            $params[] = $_SERVER['DOCUMENT_ROOT'] . '/reshotel_api/' . $uploadPath;
        }

        $sql .= " WHERE id=?";
        $params[] = $hotelId;

        $stmt = $conn->prepare($sql);
        $types = str_repeat('s', count($params));
        $stmt->bind_param($types, ...$params);

        if ($stmt->execute()) {
            echo json_encode(["message" => "Hotel details updated" . ($existingImage !== null ? " with new image" : "")]);
            http_response_code(200);
            exit;
        } else {
            echo json_encode(["message" => "Error updating data in the database"]);
            http_response_code(500);
            exit;
        }

        $stmt->close();
    } else {
        echo json_encode(["message" => "Invalid request data"]);
        http_response_code(400);
        exit;
    }
} else {
    echo json_encode(["message" => "Method Not Allowed"]);
    http_response_code(405);
    exit;
}
?>
