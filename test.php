<?php
	$Fees = 0;
	 $Fees += str_replace("$-","",'$-0.69');
	 echo preg_replace("/[^0-9.]/", "", "$-0.69");
	 
	 echo str_replace("$","","$14.00");
?>