<?php
header("Access-Control-Allow-Origin: http://localhost:8080");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

define('DB_HOST', 'localhost');
define('DB_USER', 'dor');
define('DB_PASS', '123456');

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS);

// Check connection
if ($conn->connect_error) {
    die('Connection failed ' . $conn->connect_error);
}

$result = $conn->query("SHOW DATABASES LIKE 'bank_db'"); // Check for bank_db DB

if ($result->num_rows === 0) {
    if ($conn->query("CREATE DATABASE bank_db")) {
        echo "Database 'bank_db' created successfully.\n";
    } else {
        echo "Error creating database 'bank_db': " . $conn->error;
    }
}

$conn->select_db('bank_db');