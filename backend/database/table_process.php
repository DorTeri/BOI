<?php

function createTable($currencyCode)
{
    global $conn;
    $tableName = "exchange_rates_$currencyCode";
    $createTableQuery = "CREATE TABLE IF NOT EXISTS $tableName (
                            id INT AUTO_INCREMENT PRIMARY KEY,
                            currency VARCHAR(255),
                            exchange_rate DECIMAL(10,2),
                            date_time DATE
                        )";
    $conn->query($createTableQuery);
}

function checkIfTableExists($currencyCode)
{
    global $conn;
    $tableName = "exchange_rates_$currencyCode";

    // Check if the table exists
    $checkTableQuery = "SHOW TABLES LIKE '$tableName'";
    $result = $conn->query($checkTableQuery);
    if ($result->num_rows == 0) { // Creates if not
        createTable($currencyCode);
        return false;
    }
}
