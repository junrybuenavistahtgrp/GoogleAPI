<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/refresh.php';

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "clock_booking";
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

			
		$spreadsheetId = "1L_YqbS363xBhci7n908pqlK5o4Xq65smLjtvlgY2WIk";
		$hotels = array("Aqua Hotel","LaCasa","Royal Palms Resort & Spa","Tranquilo","Victoria Park Hotel","Beach Gardens","North Beach Hotel","Tara Hotel","Tropirock","Winterset");
		$totals = array();
	
		foreach($hotels as $value){
		
				$requestBody = new Google_Service_Sheets_ClearValuesRequest();
				$response = $service->spreadsheets_values->clear($spreadsheetId, $value.'!A1:D', $requestBody);		
				$values=array(array("Guest Name","Check In","Check Out","# of Nights"));
						$sql = "SELECT * FROM `bookings` where hotel_name='".$value."'";
						$result = $conn->query($sql);
						$check_in_total=0;
						$check_out_total=0;
						while($row = $result->fetch_assoc()) {
									
								if ($row["is_arrival"]) {
									$check_in_total++;
								} else {
									$check_out_total++;
								}
								array_push($values,array($row["guest_name"],$row["check_in"],$row["check_out"],$row["no_nights"]));
									
							}
						array_push($values,array("Total","#Arrival: ".$check_in_total,"#Departure: ".$check_out_total));	
						array_push($totals,array($check_in_total,$check_out_total));
						
				$range = $value.'!A1:D';
				
				//print_r($values);	
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
				//printf("%d cells updated.", $result->getTotalUpdatedCells());
		}
			print_r($totals);
			$requestBody = new Google_Service_Sheets_ClearValuesRequest();
				$response = $service->spreadsheets_values->clear($spreadsheetId, 'Summary!A1:D', $requestBody);		
				$values2=array(array("Hotel","#Arrivals","#Departures"));
				$index = 0;
				$total_arr = 0;
				$total_dep=0;
				foreach($hotels as $value){
					$total_arr+=$totals[$index][0];
					$total_dep+=$totals[$index][1];
					array_push($values2,array($value,$totals[$index][0],$totals[$index][1]));
					$index = $index + 1;
				}
				array_push($values2,array("Total",$total_arr,$total_dep));
				$range = 'Summary!A1:D';

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