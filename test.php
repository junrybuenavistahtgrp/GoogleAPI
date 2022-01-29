<?php
	$Fees = 0;
	 $Fees += str_replace("$-","",'$-0.69');
	 echo preg_replace("/[^0-9.]/", "", "$$$(14.003)")."<br>";
	 
	 echo str_replace("$","","$$$(14.00)")."<br>";
	 
	 $date=date_create("2022-01-2");
echo date_format($date,"m/d/Y");
?>