<?php
    // downloading data from the database
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
    header("Content-Type: application/json");

    include 'config.php';
    $SECRET_API_KEY = "Api_key";

    // api authorization
    $headers = getallheaders();
    if (!isset($headers["Authorization"]) || $headers["Authorization"] !== "Bearer " . $SECRET_API_KEY) {
        http_response_code(403);
        echo json_encode(["error" => "Unauthorized access"]);
        exit();
    }

    // load data from database to website
    $sql = "SELECT * FROM balloontable ORDER BY id DESC LIMIT 5";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        echo json_encode($data, JSON_PRETTY_PRINT);
    } else {
        echo json_encode(["error" => "No data"]);
    }

    $conn->close();
?>
