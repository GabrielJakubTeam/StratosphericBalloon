<?php
    // api validation
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: POST");
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

    // save data into database
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data["longitude"]) && isset($data["latitude"])) {
        $longitude = $conn->real_escape_string($data["longitude"]);
        $latitude = $conn->real_escape_string($data["latitude"]);

        $sql = "INSERT INTO balloontable (longitude, latitude) VALUES ('$longitude', '$latitude')";

        if ($conn->query($sql) === TRUE) {
            echo json_encode(["message" => "Data saved"]);
        } else {
            echo json_encode(["error" => "Erro: " . $conn->error]);
        }
    } else {
        echo json_encode(["error" => "No enought data"]);
    }

    $conn->close();
?>

