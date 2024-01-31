<?php
require_once('config/database.php'); // Database configuration

function fetchData($currencyCode)  // Fetching data from BOI to MySQL
{
    global $conn; 
    header("Access-Control-Allow-Origin: http://localhost:8080");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

    if (dataExists($currencyCode)) {
        return "Data already exists for $currencyCode. Skipping fetch and insert.";
    }

    $url = "https://edge.boi.gov.il/FusionEdgeServer/sdmx/v2/data/dataflow/BOI.STATISTICS/EXR/1.0/RER_{$currencyCode}_ILS?startperiod=2023-01-01&endperiod=2024-01-01";
    $xmlData = file_get_contents($url);

    if ($xmlData) {
        $xml = simplexml_load_string($xmlData);

        if ($xml) {
            $dataSet = $xml->xpath('//message:DataSet');
            $json = json_encode($dataSet, JSON_PRETTY_PRINT);
            $obj = json_decode($json)[0];

            $baseCurrency = $obj->Series->{'@attributes'}->BASE_CURRENCY;
            $unitMeasure = $obj->Series->{'@attributes'}->UNIT_MEASURE;

            // Checks if table for this currency exists, creates if not
            $tableName = "rates_$currencyCode";
            $createTableQuery = "CREATE TABLE IF NOT EXISTS $tableName (
                                    id INT AUTO_INCREMENT PRIMARY KEY,
                                    base_currency VARCHAR(255),
                                    unit_measure VARCHAR(255),
                                    rates TEXT
                                )";
            $conn->query($createTableQuery);

            // bind SQL for inserting data
            $statement = $conn->prepare("INSERT INTO $tableName (base_currency, unit_measure, rates) VALUES (?, ?, ?)");

            $rates = array();
            foreach ($obj->Series->Obs as $observation) {  // Creates array of objects as rates
                $timePeriod = $observation->{'@attributes'}->TIME_PERIOD;
                $obsValue = $observation->{'@attributes'}->OBS_VALUE;

                $rate = new stdClass();
                $rate->timePeriod = $timePeriod;
                $rate->obsValue = $obsValue;

                $rates[] = $rate;
            }

            // Serialize rates array before inserting into the database
            $serializedRates = serialize($rates);

            $statement->bind_param("sss", $baseCurrency, $unitMeasure, $serializedRates);
            $statement->execute();

            // Close the statement
            $statement->close();

            return "Data inserted for $currencyCode.";
        } else {
            return "Failed to parse XML for $currencyCode.";
        }
    } else {
        return "Failed to fetch XML data for $currencyCode.";
    }
}

function dataExists($currencyCode) // Checks if there is data already
{
    global $conn;
    $tableName = "rates_$currencyCode";

    // Check if the table exists
    $checkTableQuery = "SHOW TABLES LIKE '$tableName'";
    $result = $conn->query($checkTableQuery);
    if ($result->num_rows == 0) {
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
