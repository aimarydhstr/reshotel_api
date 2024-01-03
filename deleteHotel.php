<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include('config.php');

// Get input data from Flutter
$input_data = json_decode(file_get_contents('php://input'), true);

// Check if the 'id' parameter is provided
if (isset($input_data['id'])) {
    $hotel_id = $input_data['id'];

    // Select file path from the database before deleting the hotel
    $select_query = "SELECT file_path FROM hotels WHERE id = $hotel_id";
    $result = mysqli_query($conn, $select_query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $file_path = $row['file_path'];

        // Delete hotel from the database
        $delete_query = "DELETE FROM hotels WHERE id = $hotel_id";

        if (mysqli_query($conn, $delete_query)) {
            // Hotel deleted successfully

            // Remove the image file from the system
            if (file_exists($file_path)) {
                unlink($file_path);
            }

            $response = array(
                'status' => 'success',
                'message' => 'Hotel deleted successfully.'
            );
            echo json_encode($response);
        } else {
            // Error in deleting hotel
            $response = array(
                'status' => 'error',
                'message' => 'Failed to delete hotel. ' . mysqli_error($conn)
            );
            echo json_encode($response);
        }
    } else {
        // No matching record found
        $response = array(
            'status' => 'error',
            'message' => 'Hotel not found.'
        );
        echo json_encode($response);
    }
} else {
    // 'id' parameter not provided
    $response = array(
        'status' => 'error',
        'message' => 'Hotel ID not provided.'
    );
    echo json_encode($response);
}

// Close database connection
mysqli_close($conn);
?>
