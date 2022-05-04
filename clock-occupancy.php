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

			
		$spreadsheetId = "1unWKFXF0BO8ZxwuOa8qG46Chd2REp5nKkI1wbjGWNC4";
		clearSheet($service, $spreadsheetId);	
		$values=array(array("Hotel","Date","Capacity","OOS","Booked rooms","Booked %","Occupancy","Occupancy %","Charges","ADR","RevPAR","Bednights"));
		  	  
		  	
				$sql = "SELECT * FROM `occupancy`";
				$result = $conn->query($sql);
				
				$capacity = 0;
				$oos = 0;
				$booked_rooms = 0;
				$occupancy = 0;
				$charges = 0;
				$adr = 0;
				$revpar = 0;
				$bednights = 0;
				$dates;
				while($row = $result->fetch_assoc()) {
							$capacity += $row["Capacity"];
							$oos += $row["OOS"];
							$booked_rooms += $row["Booked_rooms"];
							$occupancy += $row["Occupancy"];
							$charges += preg_replace("/[^0-9.]/", "", $row["Charges"]);
							$adr += $row["ADR"];
							$revpar += $row["RevPAR"];
							$bednights += $row["Bednights"];
							$dates = $row["Date"];
							array_push($values,array($row["Hotel"],$row["Date"],$row["Capacity"],$row["OOS"],$row["Booked_rooms"],$row["Booked_percent"],$row["Occupancy"],$row["Occupancy_percent"],$row["Charges"],$row["ADR"],$row["RevPAR"],$row["Bednights"]));
							//array_push($values,array($row["	Hotel"],$row["Date"],$row["Capacity"],$row["OOS"],$row["Booked_rooms"],$row["Booked_percent"],$row["Occupancy"],$row["Occupancy_percent"],$row["Charges"],$row["ADR"],$row["RevPAR"],$row["Bednights"]));			
					}
				array_push($values,array("Total",$dates,$capacity,$oos,$booked_rooms,"",$occupancy,number_format((float)($occupancy/$capacity)*100, 1, '.', '')." %",number_format($charges,2)." USD",$adr,$revpar,$bednights));
		boldHeader($service, $spreadsheetId);
		$range = 'Sheet1!A1:L';
		
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

		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
		$var = preg_replace("/\([^)]+\)/","",$dates);
		$datetotal = str_replace('/', '-', $var);
		$datetotal2 = date('Y-m-d', strtotime($datetotal));
		
		$datecheck = date('d', strtotime($datetotal));
		if((int)$datecheck == 1){
			$conn->query("DELETE FROM `total_report`");
		}
		
		$conn->query("DELETE FROM total_report
					  WHERE Dates = '".$datetotal2."'");

		$conn->query("INSERT INTO total_report (Dates, Capacity, OOS,Booked_rooms,Booked_percent,Occupancy,Occupancy_percent,Charges,ADR,RevPAR,Bednights) 
			    VALUES ('".$datetotal2."', '".$capacity."', '".$oos."', '".$booked_rooms."', '', '".$occupancy."', '".number_format((float)($occupancy/$capacity)*100, 1, '.', '')." %', '".number_format($charges,2)." USD', '".$adr."', '".$revpar."', '".$bednights."')");
		
		
		$requestBody = new Google_Service_Sheets_ClearValuesRequest();
				$response = $service->spreadsheets_values->clear($spreadsheetId, 'Sheet2!A1:L', $requestBody);	
		
		$values=array(array("Date","Capacity","OOS","Booked rooms","Booked %","Occupancy","Occupancy %","Charges","ADR","RevPAR","Bednights"));
		$sql = "SELECT * FROM `total_report`";
		$result = $conn->query($sql);
		
				while($row = $result->fetch_assoc()) {					
							array_push($values,array($row["Dates"],$row["Capacity"],$row["OOS"],$row["Booked_rooms"],$row["Booked_percent"],$row["Occupancy"],$row["Occupancy_percent"],$row["Charges"],$row["ADR"],$row["RevPAR"],$row["Bednights"]));					
					}
		
		boldHeader($service, $spreadsheetId);
		$range = 'Sheet2!A1:L';
		
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
		//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
		
		$spreadsheetId = "1R83VGKq-255Ku6YLetYwnHitxMZDUpOTV5YinUxEMpE";
		$requestBody = new Google_Service_Sheets_ClearValuesRequest();
				$response = $service->spreadsheets_values->clear($spreadsheetId, 'Sheet1!A1:L', $requestBody);
		
		clearSheet($service, $spreadsheetId);
		$values=array(array("Hotel","Date","Capacity","OOS","Booked rooms","Booked %","Occupancy","Occupancy %","Charges","ADR","RevPAR","Bednights"));
		$sql = "SELECT * FROM `occupancy_5days`";
		$result = $conn->query($sql);
		
				while($row = $result->fetch_assoc()) {					
							array_push($values,array($row["Hotel"],$row["Date"],$row["Capacity"],$row["OOS"],$row["Booked_rooms"],$row["Booked_percent"],$row["Occupancy"],$row["Occupancy_percent"],$row["Charges"],$row["ADR"],$row["RevPAR"],$row["Bednights"]));					
					}	
		boldHeader($service, $spreadsheetId);
		$range = 'Sheet1!A1:L';
		
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
		//----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
		
		
		$spreadsheetId = "1FzEctwqj9YTpENHTaFSb5dTgZ4TtCSh2XVkzNMPsxLQ";
		$requestBody = new Google_Service_Sheets_ClearValuesRequest();
				$response = $service->spreadsheets_values->clear($spreadsheetId, 'Sheet1!A1:L', $requestBody);
		
		clearSheet($service, $spreadsheetId);
		$values=array(array("Hotel","Date","Capacity","OOS","Booked rooms","Booked %","Occupancy","Occupancy %","Charges","ADR","RevPAR","Bednights"));
		$sql = "SELECT * FROM `occupancy_1year`";
		$result = $conn->query($sql);
		
				while($row = $result->fetch_assoc()) {					
							array_push($values,array($row["Hotel"],$row["Date"],$row["Capacity"],$row["OOS"],$row["Booked_rooms"],$row["Booked_percent"],$row["Occupancy"],$row["Occupancy_percent"],$row["Charges"],$row["ADR"],$row["RevPAR"],$row["Bednights"]));					
					}	
		boldHeader($service, $spreadsheetId);
		$range = 'Sheet1!A1:L';
		
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
		
		
		
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	

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