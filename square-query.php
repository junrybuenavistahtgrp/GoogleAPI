<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/refresh.php';

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "square_db";
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
//echo $_GET['lastweek']."  ".$_GET['currentday'];
//$spreadsheetId = array("1AAsRSPtGJdAQWFXK0wGxUgZkqBINjUGyG-2GcE27Efo","1C4iLZmkbquOfCujmpBPrrTcIiyKHV67vn0EjjInf2YQ");
$msg.= "<center><h1>Square Report</h1></center>";
$Acc = $_GET['acc'];
	if ($Acc==1){
		$array = array("Plaza Bistro","@theMarket");
		$spreadsheetId = array("15MWKfKoJk6KVVdaEtDTcxRdiKiUfVVOXtRKtkoIXh-Y","1ywiygAudN1Sy9oahAXJq_uQDDR9LvssjNhdrf8aFGME");
		}
	if ($Acc==2){
		$array = array("Village Cafe","Royal Palms La Villa Tapas Bar");
		$spreadsheetId = array("1ptTlZ2xwomL3FHxhLyRTJAEsZAXuRStFlw80BzDCgFg","1oxrupM-NyfzOp3AL14nXrQ9eZ9rWXP6D65Y9MwTYG1w");
		}	
$ID = 0;
foreach($array as $location){
		
		clearSheet($service, $spreadsheetId[$ID]);
		
		$sql = "SELECT DISTINCT Category FROM `square_data` where Location = '".$location."' ORDER BY Category";
		
		$result = $conn->query($sql);
		
		$msg.= "<center><h3>Location: ".$location."</h3></center>";

		if ($result->num_rows > 0) {
		  $msg.= "<table>
		  <tr>
			<th>Categorysss</th>
			<th>Item Sold</th>
			<th>Gross Sales</th>
		  </tr>";
		  $values=array(array("Category","Item Sold","Gross Sales","Code","Date","Number","Memo/Description","Fees","Discounts","Taxes","Tip"));
		  
		  while($row = $result->fetch_assoc()) {
			
			$category = $row["Category"];
				
				$sql2 = "SELECT Qty, Gross_Sales FROM `square_data` where Location = '".$location."' AND Category = '".$category."'";
				$result2 = $conn->query($sql2);
				$Qty = 0;
				$Sales = 0;
				if ($result2->num_rows > 0) {
					while($row = $result2->fetch_assoc()){
					$Qty += $row["Qty"];
					$Sales += str_replace("$","",$row["Gross_Sales"]);
				
					}
				}		
			$msg.= " 
					<tr>
					<td>".$category."</td>
					<td>".$Qty."</td>
					<td>".number_format((float)$Sales, 2, '.', '')."</td>
				  </tr>
				 ";
			$yesterday=date('m/d/Y',strtotime("-1 days"));	 
			//$yesterday = date_format($date,"m/d/Y"); 
			array_push($values,array($category,$Qty,"$".number_format((float)$Sales, 2, '.', ''),get_code($category),$yesterday,$location." ".$yesterday,"To Post ".date('M Y')." Rev"));
		  }
		  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		  		   
			   $sql3 = "SELECT  Fees, Discounts, Tax, Tip FROM `square_transaction` where Location = '".$location."'";
			   $result3 = $conn->query($sql3);			   
			   $Fees = 0;
			   $Discounts = 0;
			   $Tax = 0;
			   $Tip = 0;
			   if ($result3->num_rows > 0) {
					while($row = $result3->fetch_assoc()){
					$Fees += preg_replace("/[^0-9.]/", "", $row["Fees"]);
					$Discounts += preg_replace("/[^0-9.]/", "", $row["Discounts"]);
					$Tax += preg_replace("/[^0-9.]/", "", $row["Tax"]);
					$Tip += preg_replace("/[^0-9.]/", "", $row["Tip"]);
					}
				}	   
			   $values[1][7]= "$".$Fees;
			   $values[1][8]= "$".$Discounts;
			   $values[1][9]= "$".$Tax;
			   $values[1][10]= "$".$Tip;
		   
		} else {
		  $msg.= "<center><h3>No result</h3></center>";
		}
		$msg.= "</table>";
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		

		
		$range = 'Sheet1!A1:K25';
		
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
		
		$result = $service->spreadsheets_values->batchUpdate($spreadsheetId[$ID], $body);
		printf("%d cells updated.", $result->getTotalUpdatedCells());
		$ID = $ID + 1;
		}
function get_code($rets){
	$code = '';
	if ($rets=="Beer"){
        $code="64200";
    } 
	else if($rets=="Beverages"){
        $code="63100";
    }
	else if($rets=="Coffee"){
        $code="63100";
    }
	else if($rets=="Food"){
        $code="63100";
    }
	else if($rets=="N/A Beverage"){
        $code="63200";
    }
	else if($rets=="Sparkl / Rose"){
        $code="64300";
    }
	else if($rets=="Wine Reserves"){
        $code="64300";
    }
    return $code; 
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

// Always set content-type when sending HTML email
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

// More headers
$headers .= 'From: <webmaster@example.com>' . "\r\n";


//mail($to,$subject,$message,$headers);

$conn->close();
?>