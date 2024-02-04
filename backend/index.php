<?php
require_once('config/database.php'); // Database configuration
require_once('./api/get.php');

function fetchData($currencyCode)  // Fetching data from BOI to MySQL
{
    dataExists($currencyCode);
    $url = "https://edge.boi.gov.il/FusionEdgeServer/sdmx/v2/data/dataflow/BOI.STATISTICS/EXR/1.0/RER_{$currencyCode}_ILS?startperiod=2023-01-01&endperiod=2024-01-01";
    $xmlData = file_get_contents($url);


    $xml = simplexml_load_string($xmlData);

    $dataSet = $xml->xpath('//message:DataSet');
    $json = json_encode($dataSet, JSON_PRETTY_PRINT);
    $obj = json_decode($json)[0];

    $data = $obj->Series->Obs;
    updateData($currencyCode, $data);
}

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
        } elseif ((count($currentData) < count($formattedDatas['values']))) {
            updateExchangeRates($conn, $formattedDatas['values'],  $formattedDatas['dates'], $currency);
        }
    } catch (Exception $error) {
        return $error;
    }
}

function setDataAsArrays($dataSet)
{
    $values = [];
    $dates = [];

    foreach ($dataSet as $data) {
        $attribute = ($data->{'@attributes'});

        array_push($values, $attribute->{'OBS_VALUE'});
        array_push($dates, $attribute->{'TIME_PERIOD'});
    }

    return ['values' => $values, 'dates' => $dates];
}

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

function dataExists($currencyCode) // Checks if there is data already
{
    global $conn;
    $tableName = "exchange_rates_$currencyCode";

    // Check if the table exists
    $checkTableQuery = "SHOW TABLES LIKE '$tableName'";
    $result = $conn->query($checkTableQuery);
    if ($result->num_rows == 0) {
        createTable($currencyCode);
        return false;
    }

    // Checks if the table has data
    $checkDataQuery = "SELECT COUNT(*) AS count FROM $tableName";
    $result = $conn->query($checkDataQuery);
    $row = $result->fetch_assoc();
    $count = $row['count'];

    return $count > 0;
}

// Making the fetch for each currency
$currencies = ['USD', 'EUR', 'GBP'];
foreach ($currencies as $currency) {
    echo "Currency: $currency\n";
    echo fetchData($currency) . "\n\n";
}

function updateExchangeRates($conn, $rates, $dates, $currency)
{
    $tableName = "exchange_rates_" . $currency;
    $existingRecords = [];

    // Retrieve existing records for provided dates
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
