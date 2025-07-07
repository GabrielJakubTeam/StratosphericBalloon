<?php
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Allow-Headers: Content-Type, Authorization"); 
    header("Content-Type: application/json");

    include 'config.php';
	
    $SECRET_API_KEY = "API_KEY";
    $headers = getallheaders();

    if (!isset($headers["authorization"]) || $headers["authorization"] !== "Bearer " . $SECRET_API_KEY) {
        http_response_code(403);
        echo json_encode(["error" => "Unauthorized access api_php file"]);
        exit();
    }

    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data["longitude"]) && isset($data["latitude"])) {
        $longitude = $conn->real_escape_string($data["longitude"]);
        $latitude = $conn->real_escape_string($data["latitude"]);

        $sql = "INSERT INTO balloondata (longitude, latitude) VALUES ('$longitude', '$latitude')";

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
