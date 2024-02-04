<?php
require_once(__DIR__ . '/../database/table_process.php');
require_once(__DIR__ . '/../config/database.php');
require_once(__DIR__ . '/../data/data_process.php');

function updateData($currency, $data)
{
    global $conn;

    $tableName = "exchange_rates_" . $currency;

    try {
        $currentData = json_decode(handleGet($currency));

        $formattedDatas = setDataAsArrays($data);

        if (empty($currentData)) {
            $formattedValues = [];
            for ($i = 0; $i < count($formattedDatas['values']); $i++) {
                $rate = $formattedDatas['values'][$i];
                $date = $formattedDatas['dates'][$i];

                // Escape values to prevent SQL injection
                $rate = $conn->real_escape_string($rate);
                $date = $conn->real_escape_string($date);

                // Ensure date format is correct
                if (!DateTime::createFromFormat('Y-m-d', $date)) {
                    die("Error: Invalid date format.");
                }

                // Format values for insertion
                $formattedValues[] = "('" . $currency . "', '" . $rate . "', '" . $date . "')";
            }

            $sql = "INSERT IGNORE INTO $tableName (currency, exchange_rate, date_time) VALUES " . implode(", ", $formattedValues);

            if ($conn->query($sql) !== TRUE) {
                die("Error: " . $conn->error);
            }
        } elseif ((count($currentData) < count($formattedDatas['values']))) { //checks if current data needs update
            updateExchangeRates($conn, $formattedDatas['values'],  $formattedDatas['dates'], $currency);
        }
    } catch (Exception $error) {
        return $error;
    }
}

function updateExchangeRates($conn, $rates, $dates, $currency)
{
    $tableName = "exchange_rates_" . $currency;
    $existingRecords = [];

    // Get existing records for dates
    foreach ($dates as $date) {
        $existingRecord = getExchangeRateByDate($conn, $tableName, $date);
        $existingRecords[$date] = $existingRecord;
    }

    // Process each rate and date
    foreach ($rates as $key => $rate) {
        $sqlTimestamp = date("Y-m-d", strtotime($dates[$key]));

        // Check if the record already exists
        if (isset($existingRecords[$sqlTimestamp])) {
            // Update existing record
            updateExchangeRate($conn, $tableName, $existingRecords[$sqlTimestamp]['id'], $rate);
        } else {
            // Insert new record
            insertExchangeRates($conn, $tableName, $rate, $sqlTimestamp, $currency);
        }
    }
}

function updateExchangeRate($conn, $tableName, $recordId, $rate)
{
    $sql = "UPDATE $tableName SET exchange_rate = $rate WHERE id = $recordId";

    if ($conn->query($sql) !== TRUE) {
        die("Error updating record: " . $conn->error);
    }
}

function insertExchangeRates($conn, $tableName, $rate, $date, $currency)
{
    $sql = "INSERT INTO $tableName (currency, exchange_rate, date_time) VALUES ('$currency', $rate, '$date')";

    if ($conn->query($sql) !== TRUE) {
        die("Error inserting record: " . $conn->error);
    }
}

function getExchangeRateByDate($conn, $tableName, $date)
{
    $sql = "SELECT * FROM $tableName WHERE date_time = '$date'";
    $result = $conn->query($sql);

    if (!$result) {
        die("Error: " . $conn->error);
    }

    return $result->fetch_assoc();
}

?>
