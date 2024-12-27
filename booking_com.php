<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/refresh.php';

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Booking_com";
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

$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
$service = new Google_Service_Sheets($client);

			
		$spreadsheetId = "1WfQeK_WhPWXJe9Gt3NzmBPX3Y2ZSQBuEL24hpTeWhsQ";
		$hotels = array("Aqua Hotel","LaCasa","Royal Palms Resort & Spa","Tranquilo","Victoria Park Hotel","Beach Gardens","North Beach Hotel","Tara Hotel","Tropirock","Winterset");
		foreach($hotels as $value){
		echo "ff1";
				$requestBody = new Google_Service_Sheets_ClearValuesRequest();
				
				$response = $service->spreadsheets_values->clear($spreadsheetId, $value.'!A1:N', $requestBody);		
				
				$values=array(array("Book No.","Guest Name","Check-in","Check-out","Room Nights","Comm","Result","Original Amount","Final Amount","Commission Amount","Dispute Commission Amount","Remarks"));
						$sql = "SELECT * FROM `hotel_data` where Hotel_name='".$value."'";
						$result = $conn->query($sql);
						echo "ff";
						while($row = $result->fetch_assoc()){
							array_push($values,array($row["Book_Number"],$row["Guest_Name"],$row["Check_in"],$row["Check_out"],$row["Room_Nights"],$row["Comm"],$row["Result"],$row["Original_Amount"],$row["Final_Amount"],$row["Commission_Amount"],$row["Dispute_commission_amount"],$row["Remarks"]));
							
						}
						
				
				$range = $value.'!A1:N';
				
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