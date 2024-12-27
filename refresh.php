<?php
	require __DIR__ . '/vendor/autoload.php';
	
	function getClient(){
			$client = new Google_Client();

			echo "initializing class\n";
			$client->setApplicationName('Google Drive API PHP Quickstart');
			$client->setRedirectUri('http://localhost/query.php');
			$client->setScopes(Google_Service_Drive::DRIVE);
			$client->setAuthConfig('credentials.json');
			$client->setAccessType('offline');
			$client->setPrompt('select_account consent');
			
			$tokenPath = 'token.json';
			if (file_exists($tokenPath)) {
				$accessToken = json_decode(file_get_contents($tokenPath), true);
				$client->setAccessToken($accessToken);
			}
			
			if ($client->isAccessTokenExpired()) {
				$refreshToken = file_get_contents(__DIR__ . "/token.txt"); // load previously saved token
				echo $refreshToken;
				$client->refreshToken($refreshToken);
				$tokens = $client->getAccessToken();
				$client->setAccessToken($tokens);

				if (!file_exists(dirname($tokenPath))) {
				mkdir(dirname($tokenPath), 0700, true);
				}
				file_put_contents($tokenPath, json_encode($client->getAccessToken()));
			}
			return $client;
	}
	$client = getClient();
?>