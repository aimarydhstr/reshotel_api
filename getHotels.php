<?php

include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $sql = "SELECT * FROM hotels";
    $result = mysqli_query($conn, $sql);

    if ($result->num_rows > 0) {
        $hotels = mysqli_fetch_all($result, MYSQLI_ASSOC);

        http_response_code(200);
        echo json_encode($hotels);
    } else {
        echo json_encode([]);
    }
    mysqli_close($conn);
} else {
    $response['message'] = 'Invalid request method';
    echo json_encode($response);
}

?>