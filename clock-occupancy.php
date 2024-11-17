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
	
	$booked_percent = floatval(str_replace('%', '', $row["Booked_percent"]));
	$total_booked_percent += $booked_percent;
	
	$occupancy_percent = floatval(str_replace('%', '', $row["Occupancy_percent"]));
	$total_occupancy_percent += $occupancy_percent;
	
	$charges = floatval(str_replace(['USD', ','], '', $row["Charges"]));
	$total_charges += $charges;
	
	$adr = floatval(str_replace('', '', $row["ADR"]));
	$total_adr += $adr;
	
	$revpar = floatval(str_replace('', '', $row["RevPAR"]));
	$total_revpar += $revpar;
	
	
	$count++;
    $values[] = [
        $row["Hotel"], $row["Date"], $row["Capacity"], $row["OOS"], $row["Booked_rooms"], $row["Booked_percent"],
        $row["Occupancy"], $row["Occupancy_percent"], $row["Charges"], $row["ADR"], $row["RevPAR"], $row["Bednights"]
    ];
	$capacity += $row["Capacity"];
	$oos += $row["OOS"];
	$booked_rooms += $row["Booked_rooms"];
	$average_booked_percent = $count != 0 ? number_format($total_booked_percent / $count, 2) . " %" : "N/A";
	$occupancy += $row["Occupancy"];
	$average_occupancy_percent = $count != 0 ? number_format($total_occupancy_percent / $count, 2) . " %" : "N/A";
	$bednights += $row["Bednights"];
}

$values[] = ["Total", "", $capacity, $oos, $booked_rooms, $average_booked_percent , $occupancy, $average_occupancy_percent, number_format($total_charges, 2, '.', ',') . " USD", $total_adr, $total_revpar, $bednights];



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
