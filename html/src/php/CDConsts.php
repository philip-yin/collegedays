<?
	//For commonly executed code
	class CDConsts
	{
		public static function getAPIDomain()
		{
			return 'http://gocollegedays.com';
		}

		public static function getConst($identifier = '', $value, $PDOconn = NULL)
		{
			$sql = "SELECT charValue, intValue FROM const WHERE identifier=:identifier";
			$stmtA = $PDOconn->prepare($sql);
			$paramsA[':identifier'] = $identifier;
			$stmtA->execute($paramsA);
			
			if($stmtA->rowCount() == 0) return false;
			
			$row = $stmtA->fetch();
			return $row[$value];
		}
	}
?>