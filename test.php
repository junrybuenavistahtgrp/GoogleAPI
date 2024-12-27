<?php
	$Fees = 0;
	 $Fees += str_replace("$-","",'$-0.69');
	 echo preg_replace("/[^0-9.]/", "", "$$$(14.003)")."<br>";
	 
	 echo str_replace("$","","$$$(14.00)")."<br>";
	 
	 $date=date_create("2022-01-2");
echo date_format($date,"m/d/Y")."<br>";

echo number_format((float)'1703', 2, '.', '');

$totals = array();
array_push($totals,4);
array_push($totals,5);
print_r($totals);
echo $totals[0];
echo "<br><br>";

$var = preg_replace("/\([^)]+\)/","","15 Mar 2022 (Fri)");
$date = str_replace('/', '-', $var);
echo date('Y-m-d', strtotime($date));
echo "<br><br>";
echo $datecheck = date('d', strtotime($date));

if((int)$datecheck == 15){echo "yes";}

?>