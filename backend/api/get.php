<?php
require_once(__DIR__ . '/../config/database.php');
header("Access-Control-Allow-Origin: http://localhost:8080");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

// Handle API requests
function handleGet($currency = null)
{

    global $conn;
    if ($currency === null && isset($_GET['currency'])) {
        $currency = $_GET['currency'];
    }

    if ($currency !== null) {
        $startDate = $_GET['start_date'] ?? '2023-01-01';
        $endDate = $_GET['end_date'] ?? '2024-01-01';

        // Query database
        $tableName = "exchange_rates_" . $currency;
        $query = "SELECT * FROM $tableName WHERE date_time BETWEEN ? AND ?";
        $statement = $conn->prepare($query);

        // Bind parameters and execute query
        $statement->bind_param("ss", $startDate, $endDate);
        $statement->execute();

        // Get results
        $result = $statement->get_result();

        // Fetch data as associative array
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        $statement->close();

        // Set response headers and output data as JSON
        header('Content-Type: application/json');
        echo json_encode($data);
        return json_encode($data);
    } else {
        // Invalid request
        http_response_code(400);
        return json_encode(array("message" => "Invalid request"));
    }
}

handleGet();
