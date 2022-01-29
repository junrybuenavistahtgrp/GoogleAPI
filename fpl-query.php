<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/refresh.php';

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fpl_db";
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
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$date = $_GET['date'];

$service = new Google_Service_Sheets($client);
			
		$spreadsheetId = array("11MmGBoyfAz7Ot3sHPvomoQy8aowoxzZuA0-M041RGHw");
		clearSheet($service, $spreadsheetId);	
		
		 $values = array(array("Account_Group","Account","Address","Amount_due","Due_date","Date"));		  
		  	
				$sql = "SELECT * FROM `fpl_accounts`";
				$result = $conn->query($sql);	
				while($row = $result->fetch_assoc()) {
					
							array_push($values,array($row["Account_Group"],$row["Account_no"],$row["address"],preg_replace("/[^0-9.]/", "", $row["amount_due"]),date_format($row["Due_Date"],"m/d/Y"),$date));
					}
					
		$range = 'Sheet1!A1:F';

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

// Always set content-type when sending HTML email
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

// More headers
$headers .= 'From: <webmaster@example.com>' . "\r\n";


//mail($to,$subject,$message,$headers);

$conn->close();
?>