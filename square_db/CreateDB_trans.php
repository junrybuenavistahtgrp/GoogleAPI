<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "square_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// sql to create table
$sql = "CREATE TABLE square_transaction (
Date date,
Time text, 
Time_Zone text,
Gross_Sales text,
Discounts text, 
Service_Charges text, 
Net_Sales text,
Gift_Card_Sales text,
Tax text,
Tip text,
Partial_Refunds text, 
Total_Collected text,
Source text,
Card text,
Card_Entry_Methods text,
Cash text,
Square_Gift_Card text,
Other_Tender text,
Other_Tender_Type text,
Other_Tender_Note text,
Fees text,
Net_Total text,
Transaction_ID text,
Payment_ID text,
Card_Brand text,
PAN_Suffix text,
Device_Name text,
Staff_Name text,
Staff_ID text,
Details text,
Description text,
Event_Type text,
Location text,
Dining_Option text,
Customer_ID text,
Customer_Name text,
Customer_Reference_ID text,
Device_Nickname text,
Deposit_ID text,
Deposit_Date text,
Deposit_Details text,
Fee_Percentage_Rate text,
Fee_Fixed_Rate text,
Refund_Reason text,
Discount_Name text,
Transaction_Status text,
Order_Reference_ID text)";

if ($conn->query($sql) === TRUE) {
  echo "Table MyGuests created successfully";
} else {
  echo "Error creating table: " . $conn->error;
}

$conn->close();
?>