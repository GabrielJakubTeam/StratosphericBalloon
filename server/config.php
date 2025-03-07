<?php
    // connection to the database
    $servername = "servername";
    $username = "username";  
    $password = "password";  
    $dbname = "dbname";

    // database connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die(json_encode(["error" => "Connection error with database: " . $conn->connect_error]));
    }
?>