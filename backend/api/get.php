<?php
require_once('../config/database.php'); // Database configuration

header("Access-Control-Allow-Origin: http://localhost:8080");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

// Handle API requests
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['currency']) && isset($_GET['start_date']) && isset($_GET['end_date'])) {
    $currency = $_GET['currency'];
    $startDate = $_GET['start_date'] ?? '2023-01-01';
    $endDate = $_GET['end_date'] ?? '2024-01-01';


    // Query database
    $tableName = "rates_$currency";
    $query = "SELECT rates FROM $tableName";
    $statement = $conn->prepare($query);
    $statement->execute();
    $result = $statement->get_result();

    // Fetch the data
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $rates = unserialize($row['rates']);
        foreach ($rates as $rate) {
            // Assuming each rate object has 'timePeriod' property
            if ($rate->timePeriod >= $startDate && $rate->timePeriod <= $endDate) {
                $data[] = $rate;
            }
        }
    }

    // Close the statement
    $statement->close();

    // Return the data as JSON response
    header('Content-Type: application/json');
    echo json_encode($data);
} else {
    // Handle invalid requests
    http_response_code(400); // Bad request
    echo json_encode(array("message" => "Invalid request"));
}
