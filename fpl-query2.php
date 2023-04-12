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


$total = $_GET['total'];

$service = new Google_Service_Sheets($client);
			
		$spreadsheetId = "15sAlPTHVMQKt9Sk3OO99Rbe4LZ1rBsC9FgzywhTS4Nw";
		clearSheet($service, $spreadsheetId);
		
		
		
		 $values = array(array("Account","Address","Due_date","Amount_due","Date"));		  
		  	
				$sql = "SELECT * FROM `fpl_accounts2` where active = '2' AND exist = '1'";
				$result = $conn->query($sql);	
				while($row = $result->fetch_assoc()) {
							//$date_due=date_create($row["due_date"]);
							array_push($values,array($row["account_no"],$row["address"],$row["due_date"],number_format((float)preg_replace("/[^0-9.]/", "", $row["amount_due"]), 2, '.', ''),$row["updatedate"]));
					}
				array_push($values,array("Total","","",$total,""));	
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
		
		updateFormat($service, $spreadsheetId);
		boldHeader($service, $spreadsheetId);

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

function updateFormat($service, $spreadsheetID){
	$format_requests = array();

	$format_requests[] =  
	 [
         [
            "repeatCell" => [
               "range" => [
                  "sheetId" => 0,
				  "startRowIndex" => 1,
				  //"endRowIndex" => 3,
				  "startColumnIndex" => 3,
				  "endColumnIndex" => 4				  
               ], 
               "cell" => [
                     "userEnteredFormat" => [
                        "numberFormat" => [
                           "type" => "NUMBER",
						   "pattern" => "#,##0.00"
                        ] 
                     ] 
                  ], 
               "fields" => "userEnteredFormat.numberFormat" 
            ] 
         ] 
      ]; 



$format = new Google_Service_Sheets_BatchUpdateSpreadsheetRequest(array('requests' => $format_requests));
$format_result = $service->spreadsheets->batchUpdate($spreadsheetID, $format);
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