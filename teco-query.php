<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/refresh.php';

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "teco_db";
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
$day = $_GET['day'];
			
		$spreadsheetId = "1iSVSMilWr2HtjozHU4tYgfslDgmrEmgAkiq3PceR1Xo";
		//clearSheet($service, $spreadsheetId);	
		  $values=array(array(date("Y/m/d"),"","",""));
		  array_push( $values,array("Account","Address","Amount_due","Due_date"));		  
		  	
				$sql = "SELECT * FROM `teco_data`";
				$result = $conn->query($sql);	
				while($row = $result->fetch_assoc()) {
						if($day==5){
							if($row["acc"] != '221000071805')
							array_push($values,array($row["acc"],$row["address"],$row["amount_due"],$row["due_date"],));
						}
						if($day==15){
							if($row["acc"] == '221000071805')
							array_push($values,array($row["acc"],$row["address"],$row["amount_due"],$row["due_date"],));
						}
					}
					
		if($day==5){
		$range = 'Sheet1!A1:D';}
		if($day==15){
		$range = 'Sheet1!F1:I';}
	
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