<?php
require_once(__DIR__ . '/../config/database.php');
require_once(__DIR__ . '/../api/get.php');
require_once(__DIR__ . '/data_process.php');
require_once(__DIR__ . '/../database/table_process.php');
require_once(__DIR__ . '/../database/exchange_rates.php');

function fetchData($currencyCode)  // Fetching data from BOI to MySQL
{
    checkIfTableExists($currencyCode);
    $url = "https://edge.boi.gov.il/FusionEdgeServer/sdmx/v2/data/dataflow/BOI.STATISTICS/EXR/1.0/RER_{$currencyCode}_ILS?startperiod=2023-01-01&endperiod=2025-01-01";
    $xmlData = file_get_contents($url);


    $xml = simplexml_load_string($xmlData);

    $dataSet = $xml->xpath('//message:DataSet');
    $json = json_encode($dataSet, JSON_PRETTY_PRINT);
    $obj = json_decode($json)[0];

    $data = $obj->Series->Obs;
    updateData($currencyCode, $data);
}

// Making the fetch for each currency
$currencies = ['USD', 'EUR', 'GBP'];
foreach ($currencies as $currency) {
    echo "Currency: $currency\n";
    echo fetchData($currency) . "\n\n";
}
?>
