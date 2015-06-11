<?
require_once('/var/www/html/api/obj/CDObject.php');
require_once('/var/www/vendor/autoload.php');

use Aws\S3\S3Client;
class Image extends CDObject
{
	//Class constructor
	function Image($identifier = NULL, $PDOconn = NULL)
	{
		//Call super constructor
		parent::CDObject('image', $PDOconn);

		//Try to identify the user
		if($identifier == NULL)
			return;
			
			//First try to search by ID
			$sql = "SELECT objectID FROM image WHERE objectID=:objectID";
			$stmtA = $this->PDOconn->prepare($sql);
			$paramsA[':objectID'] = $identifier;
			$stmtA->execute($paramsA);
			
			//Any convos by id?
			if($stmtA->rowCount() > 0)
			{
				$row = $stmtA->fetch();
				$this->ID = $row['objectID'];
				$this->exists = true;
				$this->refresh();
				return; //Done
			}

			//Try by row
			$sql = "SELECT objectID FROM image WHERE row=:row";
			$stmtC = $this->PDOconn->prepare($sql);
			$paramsC[':row'] = $identifier;
			$stmtC->execute($paramsC);
			
			if($stmtC->rowCount() > 0)
			{
				$row = $stmtC->fetch();
				$this->ID = $row['objectID'];
				$this->exists = true;
				$this->refresh();
				return;
			}
	}

	public function create($fileName)
	{
		if($this->exists || $this->ID == '' || $this->ID == NULL)
			return false;
		
		//Create image
		$sql = "INSERT INTO image (objectID, filename) VALUES (:objectID, :filename)";
		$stmtA = $this->PDOconn->prepare($sql);
		$paramsA[':objectID'] = $this->ID;
		$paramsA[':filename'] = $fileName;
		$stmtA->execute($paramsA);

			$this->ID = $this->ID;
			$this->refresh();
			$this->exists = true;

		return true;
	}
	
	public function getURL()
	{
		if(!$this->exists) return false;
		
		$this->refresh();
		$url = $this->row['url'];
		
		if(time() > ($this->row['expTime'] - 60))
		{
			$accessKey = CDConsts::getConst("AWS_ACCESS_KEY", 'charValue', $this->PDOconn);
			$secretKey = CDConsts::getConst("AWS_SECRET_KEY", 'charValue', $this->PDOconn);
	 
			$client = S3Client::factory(array(
			   'key' => $accessKey,
			   'secret' => $secretKey
			));
			
			//Get a url for the image
			$expireInSeconds = 10 * (60 * 60);
			$expireInMinutes = $expireInSeconds / 60;
			$signedURL = $client->getObjectUrl('greatdays', "/img/".$this->row['filename'], '+'.$expireInMinutes.' minutes');
	 
			//Update url & time
			$sql = "UPDATE image SET exptime=:exptime, url=:url WHERE objectID=:objectID";
			$stmtA = $this->PDOconn->prepare($sql);
			$paramsA[':exptime'] = time() + $expireInSeconds;
			$paramsA[':url'] = $signedURL;
			$paramsA[':objectID'] = $this->ID;
			$stmtA->execute($paramsA);

			$url = $signedURL;
		}

		return $url;
	}
	
	public function delete()
	{
		if(!$this->exists) return false;
	
		$accessKey = CDConsts::getConst("AWS_ACCESS_KEY", 'charValue', $this->PDOconn);
		$secretKey = CDConsts::getConst("AWS_SECRET_KEY", 'charValue', $this->PDOconn);
	 
		$client = S3Client::factory(array(
		   'key' => $accessKey,
		   'secret' => $secretKey
		));
	
		$result = $client->deleteObject(array(
		  'Bucket' => 'greatdays',
		  'Key'    => "/img/".$this->row['filename']
		));

		$sql = "DELETE FROM image WHERE objectID=:objectID";
		$stmtB = $this->PDOconn->prepare($sql);
		$paramsB[':objectID'] = $this->ID;
		$stmtB->execute($paramsB);
		
		return true;
	}
}

?>
