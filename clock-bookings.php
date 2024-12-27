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
		$hotels = array("Aqua","La Casa","Royal Palms","Tranquillo","Victoria Park","Beach Gardens","North Beach Hotel","Tara Hotel","Tropirock","Winterset");
		$totals = array();
		$balanceTotal2=array();
		foreach($hotels as $value){
		
				$requestBody = new Google_Service_Sheets_ClearValuesRequest();
				$response = $service->spreadsheets_values->clear($spreadsheetId, $value.'!A1:D', $requestBody);		
				$values=array(array("Guest Name","Check In","Check Out","# of Nights"));
						$sql = "SELECT * FROM `bookings` where hotel_name='".$value."'";
						$result = $conn->query($sql);
						$countTotal=0;
						$balanceTotal=0;
						while($row = $result->fetch_assoc()) {
									
									//$balanceTotal += preg_replace("/[^0-9.]/", "", $row["balance"]);
									array_push($values,array($row["guest_name"],$row["check_in"],$row["check_out"],$row["no_nights"]));
									//$countTotal = $countTotal + 1;
							}
						//array_push($totals,$countTotal);		
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