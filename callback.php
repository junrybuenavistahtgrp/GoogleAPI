<?php
require __DIR__ . '/vendor/autoload.php';
		
function url_origin( $s, $use_forwarded_host = false )
{
	$ssl      = ( ! empty( $s['HTTPS'] ) && $s['HTTPS'] == 'on' );
	$sp       = strtolower( $s['SERVER_PROTOCOL'] );
	$protocol = substr( $sp, 0, strpos( $sp, '/' ) ) . ( ( $ssl ) ? 's' : '' );
	$port     = $s['SERVER_PORT'];
	$port     = ( ( ! $ssl && $port=='80' ) || ( $ssl && $port=='443' ) ) ? '' : ':'.$port;
	$host     = ( $use_forwarded_host && isset( $s['HTTP_X_FORWARDED_HOST'] ) ) ? $s['HTTP_X_FORWARDED_HOST'] : ( isset( $s['HTTP_HOST'] ) ? $s['HTTP_HOST'] : null );
	$host     = isset( $host ) ? $host : $s['SERVER_NAME'] . $port;
	return $protocol . '://' . $host;
}
function full_url( $s, $use_forwarded_host = false )
{
	return url_origin( $s, $use_forwarded_host ) . $s['REQUEST_URI'];
}
function GetBetween($content,$start,$end)
{
	$r = explode($start, $content);
	if (isset($r[1])){
	$r = explode($end, $r[1]);
	return $r[0];
	}
	return '';
}
		
$absolute_url = full_url( $_SERVER );
$code=GetBetween($absolute_url,'code=','&');
echo "Authentication code: ".$code;
	
$client = new Google_Client();
$client->setApplicationName('Google Drive API PHP Quickstart');
$client->setRedirectUri('http://localhost/query.php');

$client->setScopes(Google_Service_Drive::DRIVE);
$client->setAuthConfig('credentials.json');
$client->setAccessType('offline');
$client->setPrompt('select_account consent');
		
$tokenPath = 'token.json';
		
$authCode = $code;

// Exchange authorization code for an access token.
$accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
$client->setAccessToken($accessToken);
			
if (!file_exists(dirname($tokenPath))) {
        mkdir(dirname($tokenPath), 0700, true);
        }
    file_put_contents($tokenPath, json_encode($client->getAccessToken()));
$refreshToken = $client->getAccessToken()["refreshToken"];
echo $refreshToken."fffff";	

?>