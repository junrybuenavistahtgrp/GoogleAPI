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
$sql = "CREATE TABLE square_data (
Date date,
Time text, 
Time_Zone text,
Category text,
Item text, 
Qty int, 
Price_Point_Name text,
SKU text,
Modifiers_Applied text,
Gross_Sales double(20,2),
Discounts text, 
Net_Sales text,
Tax text,
Transaction_ID text,
Payment_ID text,
Device_Name text,
Notes text,
Details text,
Event_Type text,
Location text,
Dining_Option text,
Customer_ID text,
Customer_Name text,
Customer_Reference_ID text,
Unit text,
Count text,
GTIN text)";

if ($conn->query($sql) === TRUE) {
  echo "Table MyGuests created successfully";
} else {
  echo "Error creating table: " . $conn->error;
}

$conn->close();
?>