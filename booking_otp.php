<?php

const servicePlanId = "39587f8de63e4bb89bff3ac0b9975200";
const region = "us";
$curl = curl_init();

curl_setopt_array($curl, [
  CURLOPT_HTTPHEADER => [
    "Authorization: Bearer 11d89bd7b8c74708bfc135b244206fc8"
  ],
  CURLOPT_URL => "https://" . region . ".sms.api.sinch.com/xms/v1/" . servicePlanId . "/inbounds",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_CUSTOMREQUEST => "GET",
]);

$response = curl_exec($curl);
$error = curl_error($curl);

curl_close($curl);

if ($error) {
  echo "cURL Error #:" . $error;
} else {
  echo $response;
  echo "<br><br>";
  $data = json_decode($response, TRUE);
  print_r($data);
  echo "<br><br>";
  print_r($data['inbounds'][0]['body']);
  echo "<br><br>";
  $myarray = explode(' ', $data['inbounds'][0]['body']);
  echo "<h3 id='otp'>".preg_replace('/\D/', '', $myarray[17])."</h3>";
}
?>