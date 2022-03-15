<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/refresh.php';

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "clock_report";
$msg = "";
$msg.= "<style>
table, td, th {
  border: 1px solid black;
}

table {
  border-collapse: collapse;
  width: 100%;
}

td {
  text-align: center;
}
</style>";
//echo str_replace("$","","$14.00");
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
$service = new Google_Service_Sheets($client);

			
		$spreadsheetId = "1WdWRkSTRVB0f7kq_95LE8SP8hRq-9VSHhZpUJIomy8w";
		$hotels = array("Aqua Hotel","LaCasa","Royal Palms Resort & Spa","Tranquilo","Victoria Park Hotel","Beach Gardens","North Beach Hotel","Tara Hotel","Tropirock","Winterset");
		
		foreach($hotels as $value){
		
				$requestBody = new Google_Service_Sheets_ClearValuesRequest();
				$response = $service->spreadsheets_values->clear($spreadsheetId, $value.'!A1:D', $requestBody);		
				$values=array(array("Folio Number","Stay Date","Balance"));
						$sql = "SELECT * FROM `arrival` where Hotel='".$value."'";
						$result = $conn->query($sql);
						
						while($row = $result->fetch_assoc()) {
									
									array_push($values,array($row["folio_number"],$row["stay_date"],$row["balance"]));
							}			
				boldHeader($service, $spreadsheetId);
				$range = $value.'!A1:D';
				
				print_r($values);	
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
		}
	

function boldHeader($service, $spreadsheetID){
	$format_requests = array();

	$format_requests[] =  
	 [
         [
            "repeatCell" => [
               "range" => [
                  "sheetId" => 0, 
                  "startRowIndex" => 0, 
                  "endRowIndex" => 1 
               ], 
               "cell" => [
                     "userEnteredFormat" => [
                        "textFormat" => [
                           "bold" => true 
                        ] 
                     ] 
                  ], 
               "fields" => "userEnteredFormat.textFormat.bold" 
            ] 
         ] 
      ]; 



$format = new Google_Service_Sheets_BatchUpdateSpreadsheetRequest(array('requests' => $format_requests));
$format_result = $service->spreadsheets->batchUpdate($spreadsheetID, $format);
}

function clearSheet($service, $spreadsheetID = 0){
$request = new \Google_Service_Sheets_UpdateCellsRequest([
    'updateCells' => [ 
        'range' => [
            'sheetId' => 0 
        ],
        'fields' => "*" //clears everything
    ]
  ]);
$requests[] = $request;

$requestBody = new Google_Service_Sheets_BatchUpdateSpreadsheetRequest();
$requestBody->setRequests($requests);
$response = $service->spreadsheets->batchUpdate($spreadsheetID, $requestBody);
return $response;
}
		
		//////////////////////////////////////////////////////////////////////////////////////////////////////////
echo $msg;

//$to = "michaelvinocur@htgrp.net";
//$to = "junrybuenavista@htgrp.net";
//$to = "junrybuenavista@yahoo.com";
$subject = "Amazon CSV Reports";

$message = $msg;
//INSERT INTO occupancy (Hotel) VALUES('');
// Always set content-type when sending HTML email
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

// More headers
$headers .= 'From: <webmaster@example.com>' . "\r\n";


//mail($to,$subject,$message,$headers);

$conn->close();
?>