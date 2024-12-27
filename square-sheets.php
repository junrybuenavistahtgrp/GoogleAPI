<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/refresh.php';

$service = new Google_Service_Sheets($client);

$spreadsheetId = '1ogDFBdZV8omFKT6pLXrsgA066-cSPTHV7hi84eu_CqE';
$range = 'Sheet1!A1:E4';

$values = [
    ["Item", "Cost", "Stockeds", "Ship Date"],
    ["Wheel", "$20.50", "4555", "3/1/2017"],
    ["Door", "$1500", "211", "3/15/2016"],
    ["Engine", "$100", "11", "3/20/2016"],
];
$data = [];
$data[] = new Google_Service_Sheets_ValueRange([
    'range' => $range,
    'values' => $values
]);
// Additional ranges to update ...
$body = new Google_Service_Sheets_BatchUpdateValuesRequest([
    'valueInputOption' => 'USER_ENTERED',
    'data' => $data
]);
$result = $service->spreadsheets_values->batchUpdate($spreadsheetId, $body);
printf("%d cells updated.", $result->getTotalUpdatedCells());