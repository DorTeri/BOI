<?php
require_once('config/database.php'); // Include database configuration

function fetchData($currencyCode)
{
    global $conn; // Access the database connection object
    header("Access-Control-Allow-Origin: http://localhost:8080");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

    if (dataExists($currencyCode)) {
        return "Data already exists for $currencyCode. Skipping fetch and insert.";
    }

    $url = "https://edge.boi.gov.il/FusionEdgeServer/sdmx/v2/data/dataflow/BOI.STATISTICS/EXR/1.0/RER_{$currencyCode}_ILS?startperiod=2023-01-01&endperiod=2024-01-01";
    $xmlData = file_get_contents($url);

    if ($xmlData !== false) {
        $xml = simplexml_load_string($xmlData);

        if ($xml !== false) {
            $dataSet = $xml->xpath('//message:DataSet');
            $json = json_encode($dataSet, JSON_PRETTY_PRINT);
            $obj = json_decode($json)[0];

            $baseCurrency = $obj->Series->{'@attributes'}->BASE_CURRENCY;
            $unitMeasure = $obj->Series->{'@attributes'}->UNIT_MEASURE;

            // Check if table for this currency exists, create if not
            $tableName = "rates_$currencyCode";
            $createTableQuery = "CREATE TABLE IF NOT EXISTS $tableName (
                                    id INT AUTO_INCREMENT PRIMARY KEY,
                                    base_currency VARCHAR(255),
                                    unit_measure VARCHAR(255),
                                    rates TEXT
                                )";
            $conn->query($createTableQuery);

            // Prepare and bind SQL statement for inserting data
            $stmt = $conn->prepare("INSERT INTO $tableName (base_currency, unit_measure, rates) VALUES (?, ?, ?)");

            $rates = array();
            foreach ($obj->Series->Obs as $observation) {
                $timePeriod = $observation->{'@attributes'}->TIME_PERIOD;
                $obsValue = $observation->{'@attributes'}->OBS_VALUE;

                $rate = new stdClass();
                $rate->timePeriod = $timePeriod;
                $rate->obsValue = $obsValue;

                $rates[] = $rate;
            }

            // Serialize rates array before inserting into the database
            $serializedRates = serialize($rates);

            // Bind parameters and execute the statement
            $stmt->bind_param("sss", $baseCurrency, $unitMeasure, $serializedRates);
            $stmt->execute();

            // Close the statement
            $stmt->close();

            return "Data inserted for $currencyCode.";
        } else {
            return "Failed to parse XML for $currencyCode.";
        }
    } else {
        return "Failed to fetch XML data for $currencyCode.";
    }
}

function dataExists($currencyCode) {
    global $conn;
    $tableName = "rates_$currencyCode";
    $checkQuery = "SELECT COUNT(*) AS count FROM $tableName";
    $result = $conn->query($checkQuery);
    $row = $result->fetch_assoc();
    $count = $row['count'];
    return $count > 0;
}

$currencies = ['USD', 'EUR', 'GBP'];
foreach ($currencies as $currency) {
    echo "Currency: $currency\n";
    echo fetchData($currency) . "\n\n";
}
