<?php


$values = [
    ["Wine","Service Charge","N/A Beverage","Liquor","Food","Cold plates","Beverages","Beer","Uncategorized"],
    ["Wine","Special Cocktails","Seltzer","N/A Beverage","Liquor","Food","Beverages","Beer","Uncategorized"],
	["Xmas Pre Orders","Savory","Pre-orders","Pies","Pastry","PM","Merchandise","Merch","Lunch","Loaves","Dogs","Cake - Classic","Breakfast","Bread","Beverages","AM","Uncategorized"]
];

print_r($values[1]);




echo "<br><br>".count($values[1]);

for ($x = 0; $x <= count($values[1]); $x++) {
  if ($values[1][$x]=="Wine"){
        unset($values[1][$x]);		
    } 
}

for ($x = 0; $x <= count($values[1]); $x++) {
  if ($values[1][$x]=="Seltzer"){
        unset($values[1][$x]);		
    } 
}

	
$arr2 = array_values($values[1]);

echo "<br><br>".count($values[1])."<br>";

print_r($arr2);	