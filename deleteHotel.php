<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include 'config.php';

if ($_SERVER["REQUEST_METHOD"] === "DELETE") {
    $data = json_decode(file_get_contents("php://input"));

    if (isset($data->id)) {
        $id = $data->id;

        $conn = connectToDatabase();

        $sql = "SELECT file_name FROM hotels WHERE id = '$id'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $fileName = $row["file_name"];
            $filePath = "uploads/" . $fileName;

            if (unlink($filePath)) {
                $sqlDelete = "DELETE FROM hotels WHERE id = '$id'";
                if ($conn->query($sqlDelete) === TRUE) {
                    echo json_encode(["message" => "Hotel deleted successfully"]);
                    http_response_code(200);
                } else {
                    echo json_encode(["message" => "Error deleting hotel from the database"]);
                    http_response_code(500);
                }
            } else {
                echo json_encode(["message" => "Error deleting image from the server"]);
                http_response_code(500);
            }
        } else {
            echo json_encode(["message" => "Hotel not found"]);
            http_response_code(404);
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
