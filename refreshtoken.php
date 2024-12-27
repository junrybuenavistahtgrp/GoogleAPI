<?php
require __DIR__ . '/vendor/autoload.php'; // load library

session_start();

$client = new Google_Client();
// Get your credentials from the console
	$client->setApplicationName('Google Drive API PHP Quickstart');
    $client->setRedirectUri('http://localhost/query.php');

    $client->setScopes(Google_Service_Drive::DRIVE);
    $client->setAuthConfig('credentials.json');
    $client->setAccessType('offline');
    $client->setPrompt('select_account consent');



if (isset($_GET['code'])) {
    $client->authenticate($_GET['code']);
    $_SESSION['token'] = $client->getAccessToken();
    $client->getAccessToken(["refreshToken"]);
    $redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
    header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
    return;
}

if (isset($_SESSION['token'])) {
    $client->setAccessToken($_SESSION['token']);
}

if (isset($_REQUEST['logout'])) {
    unset($_SESSION['token']);
    $client->revokeToken();
}


?>
<!doctype html>
<html>
    <head><meta charset="utf-8"></head>
    <body>
        <header><h1>Get Token</h1></header>
        <?php
        if ($client->getAccessToken()) {
            $_SESSION['token'] = $client->getAccessToken();
            $token = json_decode($_SESSION['token']);
            echo "Access Token = " . $token->access_token . '<br/>';
            echo "Refresh Token = " . $token->refresh_token . '<br/>';
            echo "Token type = " . $token->token_type . '<br/>';
            echo "Expires in = " . $token->expires_in . '<br/>';
            echo "Created = " . $token->created . '<br/>';
            echo "<a class='logout' href='?logout'>Logout</a>";
            file_put_contents("token.txt",$token->refresh_token); // saving access token to file for future use
        } else {
            $authUrl = $client->createAuthUrl();
            print "<a id ='connect' class='login' href='$authUrl'>Connect Me!</a>";
        }
        ?>
    </body>
</html>