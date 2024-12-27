
<?php
	$accountData = json_decode(file_get_contents("tokentest.json"), true);
	$newLoginHistory['location'] = "example-city-3";
	array_push($accountData['refresh_tokens'] = "yes");
	file_put_contents("tokentest.json", json_encode($accountData));

?>