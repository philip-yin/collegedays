<?
	include_once('/var/www/html/src/php/setup.php');
	include_once('/var/www/html/api/user/obj/User.php');
	
	$response = array();
	$response['data'] = array();
	
		//Create a meta object
		$meta = array();
		$meta['time'] = time();
		$meta['type'] = '/image/upload/';
		$meta['status'] = 0;

	//Add the meta
	$response['meta'] = $meta;
	
		//Logged in?
		session_start();
		if(!isset($_SESSION['userID']))
		{
			$response['data']['reason'] = "Login required.";
			sendResponse(400, json_encode($response)); return false;
		}
	
	//Connect
	$PDOconn = newPDOconn();
	$User = new User($_SESSION['userID'], $PDOconn);
	
	require_once('/var/www/vendor/autoload.php');
	use Aws\S3\S3Client;
  

    $maxNumberOfMegabytes = 5;	 	 
	$MB = 1000000; //Megabyte
	   
	if(!isset($_FILES['file']['name']))
	{
	   $response['data']['reason'] = "No image data.";
	   sendResponse(400, json_encode($response));
	   return false; 		 
	}
	
	if (($_FILES['file']["type"] == "image/gif")
        || ($_FILES['file']["type"] == "image/jpeg")
		|| ($_FILES['file']["type"] == "image/jpg")
		|| ($_FILES['file']["type"] == "image/pjpeg")
		|| ($_FILES['file']["type"] == "image/x-png")
		|| ($_FILES['file']["type"] == "image/png"))
	 {
	  //File is valid
	 }
	 else
	 {
	   $response['data']['reason'] = "Invalid File.";
	   sendResponse(400, json_encode($response));
	   return false; 	 
	 }
	
	if ($_FILES['file']['error'] > 0)
	{
	   $response['data']['reason'] = "An error occured during upload.";
	   sendResponse(400, json_encode($response));
	   return false; 	 
	}
	 
	if($_FILES['file']['size'] > ($MB * $maxNumberOfMegabytes))
	{
	   $response['data']['reason'] = "File is too large.";
	   sendResponse(400, json_encode($response));
	   return false; 		 
	}

	 //The image passed all the tests! 
	 $fileName = $_FILES['file']['name'];

		//Get file ext
		for($i = (strlen($fileName) - 1); $i >= 0; $i--)
		{
			if($fileName[$i] == '.')
				break;
				
			$fileExt .= $fileName[$i];
		}
	 
	 $fileExt = ".".strrev($fileExt);

	 //Create the image
	 require_once('/var/www/html/api/image/obj/Image.php');
	 $Image = new Image(NULL, $PDOconn);
	 
	 //Generate new filename
	 $Image->ID = $Image->generateUniqueID();
	 $newFileName = $Image->ID;
	 $newFileNameWithExt = $newFileName.$fileExt;

	 $accessKey = CDConsts::getConst("AWS_ACCESS_KEY", 'charValue', $PDOconn);
	 $secretKey = CDConsts::getConst("AWS_SECRET_KEY", 'charValue', $PDOconn);
	 
	 $client = S3Client::factory(array(
	   'key' => $accessKey,
	   'secret' => $secretKey
	 ));
	 
    //Get the bucket
	$cdimageBucket;
	$bucketName = "greatdays";
	$result = $client->listBuckets();
      
	foreach ($result['Buckets'] as $bucket) {
	// Each Bucket value will contain a Name and CreationDate
		
		if($bucket['Name'] == $bucketName)
		{
		   $cdimageBucket = $bucket;
		   break;
		}
	}	 
	 
	if($cdimageBucket == NULL)
	{ 
		$response['reason'] = "No image bucket.";
		sendResponse(400, json_encode($response));
		return false; 	 
	}

	//Insert image into bucket
	$result = $client->putObject(array(
		'Bucket'     => $cdimageBucket['Name'],
		'Key'        => "/img/".$newFileNameWithExt,
		'SourceFile' => $_FILES['file']['tmp_name']
	));
 
	// We can poll the object until it is accessible
	$client->waitUntil('ObjectExists', array(
		'Bucket' => $cdimageBucket['Name'],
		'Key'    => "/img/".$newFileNameWithExt
	));

	$Image->create($newFileNameWithExt);
	 
		//Update the user's image
		$User->deleteImage();
		$User->setImage($Image->ID);

	//Set the status to 1 (success)
	$response['meta']['status'] = 1;
	$response['data']['imageURL'] = $signedURL;

	//Send the response
	sendResponse(200, json_encode($response));
	return true;
?>