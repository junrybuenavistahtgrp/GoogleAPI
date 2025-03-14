<?php
error_reporting(E_ERROR | E_PARSE);
require __DIR__ . '/google-drive.php';

if( isset( $_POST['submit'] ) ){
    
    if( empty( $_FILES["file"]['tmp_name'] ) ){
        echo "Go back and Select file to upload.";
        exit;
    }
    
    $file_tmp  = $_FILES["file"]["tmp_name"];
    $file_type = $_FILES["file"]["type"];
	
    //$file_name = basename($_FILES["file"]["name"]);
	$file_name = "2021-09-28_348868589.pdf";
    $path = "C:/clock_csv".$file_name;
	
	

    move_uploaded_file($file_tmp, $path);

    $folder_id = create_folder( "google-drive-test-folder" );
	echo "Folder ID:".$folder_id;
    $success = insert_file_to_drive( $path , $file_name, $folder_id);
	
    if( $success ){
        echo "file uploaded successfully";
    } else { 
        echo "Something went wrong.";
    }
}

// This will create a folder and also sub folder when $parent_folder_id is given
function create_folder( $folder_name, $parent_folder_id=null ){

    $folder_list = check_folder_exists( $folder_name );

    // if folder does not exists
    if( count( $folder_list ) == 0 ){
        $service = new Google_Service_Drive( $GLOBALS['client'] );
        $folder = new Google_Service_Drive_DriveFile();
    
        $folder->setName( $folder_name );
        $folder->setMimeType('application/vnd.google-apps.folder');
        if( !empty( $parent_folder_id ) ){
            $folder->setParents( [ $parent_folder_id ] );        
        }

        $result = $service->files->create( $folder );
    
        $folder_id = null;
        
        if( isset( $result['id'] ) && !empty( $result['id'] ) ){
            $folder_id = $result['id'];
        }
    
        return $folder_id;
    }

    return $folder_list[0]['id'];
    
}

// This will check folders and sub folders by name
function check_folder_exists( $folder_name ){
    
    $service = new Google_Service_Drive($GLOBALS['client']);

    $parameters['q'] = "mimeType='application/vnd.google-apps.folder' and name='$folder_name' and trashed=false";
    $files = $service->files->listFiles($parameters);

    $op = [];
    foreach( $files as $k => $file ){
        $op[] = $file;
    }
	
    return $op;
}

// This will display list of folders and direct child folders and files.
function get_files_and_folders(){
    $service = new Google_Service_Drive($GLOBALS['client']);

    $parameters['q'] = "mimeType='application/vnd.google-apps.folder' and 'root' in parents and trashed=false";
    $files = $service->files->listFiles($parameters);
    
    echo "<ul>";
    foreach( $files as $k => $file ){
        echo "<li> 
        
            {$file['name']} - {$file['id']} ---- ".$file['mimeType'];

            try {
                // subfiles
                $sub_files = $service->files->listFiles(array('q' => "'{$file['id']}' in parents"));
                echo "<ul>";
                foreach( $sub_files as $kk => $sub_file ) {
                    echo "<li&gt {$sub_file['name']} - {$sub_file['id']}  ---- ". $sub_file['mimeType'] ." </li>";
                }
                echo "</ul>";
            } catch (\Throwable $th) {
                // dd($th);
            }
        
        echo "</li>";
    }
    echo "</ul>";
}

// This will insert file into drive and returns boolean values.
function insert_file_to_drive( $file_path, $file_name, $parent_file_id = null ){
    $service = new Google_Service_Drive( $GLOBALS['client'] );
    $file = new Google_Service_Drive_DriveFile();

    $file->setName( $file_name );

    if( !empty( $parent_file_id ) ){
        $file->setParents( [ $parent_file_id ] );        
    }

    $result = $service->files->create(
        $file,
        array(
            'data' => file_get_contents($file_path),
            'mimeType' => 'application/octet-stream',
        )
    );

    $is_success = false;
    
    if( isset( $result['name'] ) && !empty( $result['name'] ) ){
        $is_success = true;
    }

    return $is_success;
}

if( isset( $_GET['list_files_and_folders'] ) ){
    echo "<h1>Retriving List all files and folders from Google Drive</h1>";
    get_files_and_folders();
}
if( isset( $_GET['insert_file'] ) ){
    echo "<h1>Insert File</h1>";
	$month=array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");
	
	
	
	if ($handle = opendir('C:/FPL_Downloads/')) {

		while (false !== ($entry = readdir($handle))) {

			if ($entry != "." && $entry != "..") {

				$FileName = $entry;
				$Date_Acc = explode('_', $FileName);
				//print_r($Date_Acc);
			
				$Date = explode('-', $Date_Acc[0]);
				//print_r($Date);

				$Acc = explode('.', $Date_Acc[1]);
				//print_r($Acc[0]);
				
				$folder_idY = create_folder( "FPL -".date("Y") );
					$folder_idM = create_folder( "".date("M"),$folder_idY );
						$folder_idAcc = create_folder( $Acc,$folder_idM );
				
				$success = insert_file_to_drive( "C:/FPL_Downloads/2021-09-28_348868589.pdf" , "2021-09-28_348868589.pdf", $folder_idAcc);
	
				if( $success ){
					echo "File ".$Acc."uploaded successfully<br>";
				} else { 
					echo "Something went wrong.";
				}
			}
		}

		closedir($handle);
	}

	echo $file_tmp."<br>";
	echo $file_type."<br>";
	echo $file_name."<br>";
	echo $path."<br>";    
}

if( isset( $_GET['insert_onefile'] ) ){
    echo "<h1>Insert File</h1>";
	$month=array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");
	
				$FileName = $_GET['Ac_no'];
				$Date_Acc = explode('_', $FileName);
				//print_r($Date_Acc);
				$Date = explode('-', $Date_Acc[0]);
				//print_r($Date);
				$Acc = explode('.', $Date_Acc[1]);
				echo $Acc;
				//print_r($Acc[0]);				
				$folder_root = create_folder( "FPL");
					$folder_year = create_folder( "".date("Y"),$folder_root );
						$folder_month = create_folder( "".date("M"),$folder_year );
							$folder_group = create_folder( $_GET['accountgroup'],$folder_month );
								$folder_idAcc = create_folder( $Acc,$folder_group );
				
				$success = insert_file_to_drive( "C:/FPL_Downloads/".$FileName , $FileName, $folder_idAcc);
	
				if( $success ){
					echo "File ".$Acc."uploaded successfully<br>";
				} else { 
					echo "Something went wrong.";
				}
			
		closedir($handle);
	

	echo $file_tmp."<br>";
	echo $file_type."<br>";
	echo $file_name."<br>";
	echo $path."<br>";    
}

// Function just for easier debugging
function dd( ...$d ){
    echo "<pre style='background-color:#000;color:#fff;' >";
    print_r($d);
exit;
}
		