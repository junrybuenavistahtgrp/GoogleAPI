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

			
		$spreadsheetId = "1411DbG7TFk6-zIalztVG67idvJY9y2owNb6uxyJnBgc";
		//$spreadsheetId = "1FdwdSg6a5MafX_ltDdTHH6DQk8_mYN5yRp_0zDbyQbM";//testbot
		$hotels = array("Aqua Hotel","LaCasa","Royal Palms Resort & Spa","Tranquilo","Victoria Park Hotel","Beach Gardens","North Beach Hotel","Tara Hotel","Tropirock","Winterset");
		//$hotels = array("Aqua Hotel");
		$totals = array();
		$balanceTotal2=array();
		foreach($hotels as $value){
		
		
		
				
				

				$values=array(array("Number","Reference Number","Arrival","Departure","Stay","Guest name","Room charges","Other charges","Total charges","Balance","Marketing channel"));
						$sql = "SELECT * FROM `advance_search` where hotel_name='".$value."'";
						$result = $conn->query($sql);					
						while($row = $result->fetch_assoc()) {													
									array_push($values,array($row["num"],$row["ref_num"],$row["arr"],$row["def"],$row["stay"],$row["guest"],$row["room_char"],$row["other_char"],$row["total_char"],$row["balance"],$row["marketing"]));									
							}											
				$range = $value.'!A1:K';
				
				$requestBody = new Google_Service_Sheets_ClearValuesRequest();

				$response = $service->spreadsheets_values->clear($spreadsheetId, $range, $requestBody);
				$response = $service->spreadsheets_values->clear($spreadsheetId, $value.'!A1:D', $requestBody);	
				
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