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
		$hotels = array("Aqua Hotel","LaCasa","Royal Palms Resort & Spa","Tranquilo","Victoria Park Hotel","Beach Gardens","North Beach Hotel","Tropirock","Winterset");
		$totals = array();
		$balanceTotal2=array();
		foreach($hotels as $value){
		
				$requestBody = new Google_Service_Sheets_ClearValuesRequest();
				$response = $service->spreadsheets_values->clear($spreadsheetId, $value.'!A1:D', $requestBody);		
				$values=array(array("Folio Number","Stay Date","Balance"));
						$sql = "SELECT * FROM `arrival` where Hotel='".$value."'";
						$result = $conn->query($sql);
						$countTotal=0;
						$balanceTotal=0;
						while($row = $result->fetch_assoc()) {
									
									$balanceTotal += preg_replace("/[^0-9.]/", "", $row["balance"]);
									array_push($values,array($row["folio_number"],$row["stay_date"],$row["balance"]));
									$countTotal = $countTotal + 1;
							}
						array_push($totals,$countTotal);		
						array_push($values,array("","",number_format($balanceTotal,2)." USD"));
						array_push($balanceTotal2,number_format($balanceTotal,2)." USD");
						
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
			print_r($totals);
			echo "ggggggggggggggggggggggggggg";
			$requestBody = new Google_Service_Sheets_ClearValuesRequest();
				$response = $service->spreadsheets_values->clear($spreadsheetId, 'Total!A1:D', $requestBody);		
				$values2=array(array("Hotel","Folio Number","Stay Date","Balance"));
				$index = 0;
				$totalfol = 0;
				$balanceTotal3=0;
				foreach($hotels as $value){
					$balanceTotal3 += preg_replace("/[^0-9.]/", "", $balanceTotal2[$index]);
					array_push($values2,array($value,$totals[$index],$totals[$index],$balanceTotal2[$index]));
					$totalfol = $totalfol + $totals[$index];
					$index = $index + 1;
				}
				array_push($values2,array("Total",$totalfol,$totalfol,number_format($balanceTotal3,2)." USD"));
				$range = 'Total!A1:D';
				
				print_r($values);	
				$data = [];
				$data[] = new Google_Service_Sheets_ValueRange([
					'range' => $range,
					'values' => $values2
				]);	
				// Additional ranges to update ...
				$body = new Google_Service_Sheets_BatchUpdateValuesRequest([
					'valueInputOption' => 'USER_ENTERED',
					'data' => $data
				]);		
				$result = $service->spreadsheets_values->batchUpdate($spreadsheetId, $body);
				printf("%d cells updated.", $result->getTotalUpdatedCells());	



	
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