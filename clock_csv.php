<?php
require __DIR__ . '/vendor/autoload.php';
//test google api3
function getClient()
{
    $client = new Google_Client();
    $client->setApplicationName('Google Drive API PHP Upload');
    $client->setScopes(Google_Service_Drive::DRIVE_FILE);
    $client->setAuthConfig('credentials.json');
    $client->setAccessType('offline');
    $client->setPrompt('select_account consent');

    $tokenPath = 'token.json';
    if (file_exists($tokenPath)) {
        $accessToken = json_decode(file_get_contents($tokenPath), true);
        $client->setAccessToken($accessToken);
    }

    if ($client->isAccessTokenExpired()) {
        if ($client->getRefreshToken()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
        } else {
            $authUrl = $client->createAuthUrl();
            printf("Open the following link in your browser:\n%s\n", $authUrl);
            print 'Enter verification code: ';
            $authCode = trim(fgets(STDIN));
            $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
            $client->setAccessToken($accessToken);

            if (!file_exists(dirname($tokenPath))) {
                mkdir(dirname($tokenPath), 0700, true);
            }
            file_put_contents($tokenPath, json_encode($client->getAccessToken()));
        }
    }
    return $client;
}

/**
 * Finds or creates a folder on Google Drive.
 * @param Google_Service_Drive $service Google Drive API service instance.
 * @param string $folderName The name of the folder to find or create.
 * @param string|null $parentFolderId Optional parent folder ID for nested folders.
 * @return string The Google Drive folder ID.
 */
function getOrCreateFolder($service, $folderName, $parentFolderId = null)
{
    $query = "name='$folderName' and mimeType='application/vnd.google-apps.folder' and trashed=false";
    if ($parentFolderId) {
        $query .= " and '$parentFolderId' in parents";
    }

    $response = $service->files->listFiles([
        'q' => $query,
        'spaces' => 'drive'
    ]);

    if (count($response->files) > 0) {
        return $response->files[0]->id;
    } else {
        $folderMetadata = new Google_Service_Drive_DriveFile([
            'name' => $folderName,
            'mimeType' => 'application/vnd.google-apps.folder',
            'parents' => $parentFolderId ? [$parentFolderId] : []
        ]);
        $folder = $service->files->create($folderMetadata);
        return $folder->id;
    }
}

/**
 * Uploads a file to a specific Google Drive folder.
 * @param Google_Service_Drive $service Google Drive API service instance.
 * @param string $filePath Path to the file to upload.
 * @param string $mimeType MIME type of the file to upload.
 * @param string $folderId ID of the destination folder.
 */
function uploadFileToFolder($service, $filePath, $mimeType, $folderId)
{
    $fileName = basename($filePath);

    $response = $service->files->listFiles([
        'q' => "'$folderId' in parents and name='$fileName' and trashed=false",
        'spaces' => 'drive'
    ]);

    if (count($response->files) > 0) {
        $existingFileId = $response->files[0]->id;
        $service->files->delete($existingFileId);
    }

    $fileMetadata = new Google_Service_Drive_DriveFile([
        'name' => $fileName,
        'parents' => [$folderId]
    ]);

    $content = file_get_contents($filePath);
    $service->files->create($fileMetadata, [
        'data' => $content,
        'mimeType' => $mimeType,
        'uploadType' => 'multipart'
    ]);

    echo "Uploaded $fileName to Google Drive folder.\n";
}

/**
 * Recursively uploads files and folders to Google Drive.
 * @param Google_Service_Drive $service Google Drive API service instance.
 * @param string $sourceDir Path to the source directory.
 * @param string $parentFolderId ID of the Google Drive folder to upload to.
 */
function uploadDirectoryToDrive($service, $sourceDir, $parentFolderId)
{
    $items = scandir($sourceDir);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;

        $filePath = $sourceDir . '/' . $item;
        if (is_dir($filePath)) {
            // Create or get the subfolder on Google Drive
            $subFolderId = getOrCreateFolder($service, $item, $parentFolderId);
            // Recursively upload the contents of the subfolder
            uploadDirectoryToDrive($service, $filePath, $subFolderId);
        } else {
            // Upload the file to the current Google Drive folder
            $mimeType = mime_content_type($filePath);
            uploadFileToFolder($service, $filePath, $mimeType, $parentFolderId);
        }
    }
}

// Get the API client and construct the service object
$client = getClient();
$service = new Google_Service_Drive($client);

// Create or find the main folder 'square_csv' on Google Drive
$mainFolderId = getOrCreateFolder($service, 'square_csv');

// Upload the entire contents of C:/clock_csv to the 'square_csv' folder in Google Drive
uploadDirectoryToDrive($service, 'C:/clock_csv', $mainFolderId);
