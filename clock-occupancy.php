<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/refresh.php';

// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "clock_report";

// Create connection to MySQL
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize Google Sheets API service
$service = new Google_Service_Sheets($client);

// Function to format header rows in Google Sheets
function boldHeader($service, $spreadsheetId) {
    // Format header row (if needed)
    // Add your implementation here if specific formatting is required
}

// Clear specified sheet in Google Sheets
function clearSheet($service, $spreadsheetId) {
    $requestBody = new Google_Service_Sheets_ClearValuesRequest();
    $service->spreadsheets_values->clear($spreadsheetId, 'Sheet1!A1:L', $requestBody);
}

// Function to update Google Sheets with data
function updateSheet($service, $spreadsheetId, $range, $values) {
    $data = [];
    $data[] = new Google_Service_Sheets_ValueRange([
        'range' => $range,
        'values' => $values
    ]);
    $body = new Google_Service_Sheets_BatchUpdateValuesRequest([
        'valueInputOption' => 'USER_ENTERED',
        'data' => $data
    ]);
    $result = $service->spreadsheets_values->batchUpdate($spreadsheetId, $body);
    printf("%d cells updated.\n", $result->getTotalUpdatedCells());
}

// Prepare data for each spreadsheet

// Update occupancy report for daily data
$spreadsheetId = "1unWKFXF0BO8ZxwuOa8qG46Chd2REp5nKkI1wbjGWNC4";
clearSheet($service, $spreadsheetId);
$values = [["Hotel", "Date", "Capacity", "OOS", "Booked rooms", "Booked %", "Occupancy", "Occupancy %", "Charges", "ADR", "RevPAR", "Bednights"]];
$sql = "SELECT * FROM `occupancy`";
$result = $conn->query($sql);
$capacity = $oos = $booked_rooms = $occupancy = $charges = $adr = $revpar = $bednights = 0;
while ($row = $result->fetch_assoc()) {
    $values[] = [
        $row["Hotel"], $row["Date"], $row["Capacity"], $row["OOS"], $row["Booked_rooms"], $row["Booked_percent"],
        $row["Occupancy"], $row["Occupancy_percent"], $row["Charges"], $row["ADR"], $row["RevPAR"], $row["Bednights"]
    ];
}
$occupancy_percentage = $capacity != 0 ? number_format((float)($occupancy / $capacity) * 100, 1, '.', '') . " %" : "N/A";
$values[] = ["Total", "", $capacity, $oos, $booked_rooms, "", $occupancy, $occupancy_percentage, number_format($charges, 2) . " USD", $adr, $revpar, $bednights];
updateSheet($service, $spreadsheetId, 'Sheet1!A1:L', $values);

// Update occupancy report for 5 days
$spreadsheetId = "1R83VGKq-255Ku6YLetYwnHitxMZDUpOTV5YinUxEMpE";
clearSheet($service, $spreadsheetId);
$values = [["Hotel", "Date", "Capacity", "OOS", "Booked rooms", "Booked %", "Occupancy", "Occupancy %", "Charges", "ADR", "RevPAR", "Bednights"]];
$hotels = ["Aqua Hotel", "LaCasa", "Royal Palms Resort & Spa", "Tranquilo", "Victoria Park Hotel", "Beach Gardens", "North Beach Hotel", "Tara Hotel", "Tropirock", "Winterset"];
foreach ($hotels as $hotel) {
    $sql = "SELECT * FROM `occupancy_5days` WHERE Hotel='$hotel'";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        $values[] = [
            $row["Hotel"], $row["Date"], $row["Capacity"], $row["OOS"], $row["Booked_rooms"], $row["Booked_percent"],
            $row["Occupancy"], $row["Occupancy_percent"], $row["Charges"], $row["ADR"], $row["RevPAR"], $row["Bednights"]
        ];
    }
    $values[] = ["", "", "", "", "", "", "", "", "", "", "", ""];
}
updateSheet($service, $spreadsheetId, 'Sheet1!A1:L', $values);

// Update occupancy report for 10 days
$spreadsheetId = "11yZR3IRN4mPK-F94mBzXGNr7O79jojAK_ZJ6l4EUsNM";
clearSheet($service, $spreadsheetId);
$values = [["Hotel", "Date", "Capacity", "OOS", "Booked rooms", "Booked %", "Occupancy", "Occupancy %", "Charges", "ADR", "RevPAR", "Bednights"]];
foreach ($hotels as $hotel) {
    $sql = "SELECT * FROM `occupancy_10days` WHERE Hotel='$hotel'";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        $values[] = [
            $row["Hotel"], $row["Date"], $row["Capacity"], $row["OOS"], $row["Booked_rooms"], $row["Booked_percent"],
            $row["Occupancy"], $row["Occupancy_percent"], $row["Charges"], $row["ADR"], $row["RevPAR"], $row["Bednights"]
        ];
    }
    $values[] = ["", "", "", "", "", "", "", "", "", "", "", ""];
}
updateSheet($service, $spreadsheetId, 'Sheet1!A1:L', $values);

// Update occupancy report for 1 year
$spreadsheetId = "1FzEctwqj9YTpENHTaFSb5dTgZ4TtCSh2XVkzNMPsxLQ";
clearSheet($service, $spreadsheetId);
$values = [["Hotel", "Date", "Capacity", "OOS", "Booked rooms", "Booked %", "Occupancy", "Occupancy %", "Charges", "ADR", "RevPAR", "Bednights"]];
$sql = "SELECT * FROM `occupancy_1year`";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $values[] = [
        $row["Hotel"], $row["Date"], $row["Capacity"], $row["OOS"], $row["Booked_rooms"], $row["Booked_percent"],
        $row["Occupancy"], $row["Occupancy_percent"], $row["Charges"], $row["ADR"], $row["RevPAR"], $row["Bednights"]
    ];
}
updateSheet($service, $spreadsheetId, 'Sheet1!A1:L', $values);

$conn->close();
?>
