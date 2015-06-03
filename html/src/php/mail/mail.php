<?
//Get amazon sdk
require('/var/www/vendor/autoload.php');
use Aws\Ses\SesClient;
  
function sendEmail($sourceAddress, $toAddress, $subjectHeader, $titleText, $bodyText, $bodyHTML, $PDOconn)
{
  //Get aws access credentials
  require_once('/var/www/html/src/php/setup.php');
  $accessKey = CDConsts::getConst("AWS_ACCESS_KEY", 'charValue', $PDOconn); 
  $secretKey = CDConsts::getConst("AWS_SECRET_KEY", 'charValue', $PDOconn);

  $client = SesClient::factory( array(
     'region'  => 'us-west-2',
	 'key' => $accessKey,
	 'secret' => $secretKey
	));

  $emailHTML =
	'<div style="font-family:\'helvetica neue\', \'Arial\'">
        <div style="margin:0 auto;max-width:320px;font-size:13px;border:2px solid #E8EBEF">
		
				<div style="padding:10px 0;text-align:center;background-color:#a54242;color:#f4f4f4;">
					CollegeDays
				</div>
				
                <div style="padding:20px;background-color:#fff">
					<div width="100%">
						<div>
							<div style="font-size:20px;color:black;margin-bottom:5px">'.$titleText.'</div>
						</div>
						<div style="margin-top:0px;padding-top:2px">
						'.$bodyHTML.'
						</div>
					</div>
				</div>
                    
				<div style="padding:20px 20px;font-size:11px;color:#888;background-color:#E8EBEF">
						<div><span class="il">CollegeDays</span> <a style="color:#5291cb;text-decoration:none" href="http://gocollegedays.com" target="_blank">http://<span class="il">gocollegedays</span>.com</a></div>
						<div>
							9500 Gilman Drive, La Jolla, CA 92093
						</div>
						<div style="margin-top:5px"></div>
				</div>
		</div>
    </div>';
	
  try{


  $result = $client->sendEmail(array(
     // Source is required
     'Source' => $sourceAddress,
     // Destination is required
     'Destination' => array(
        'ToAddresses' => array($toAddress),
        'CcAddresses' => array(),
        'BccAddresses' => array(),
     ),
     // Message is required
     'Message' => array(
        // Subject is required
        'Subject' => array(
            // Data is required
            'Data' => $subjectHeader,
            'Charset' => 'UTF-8',
        ),
        // Body is required
        'Body' => array(
            'Text' => array(
                // Data is required
                'Data' => $bodyText,
                'Charset' => 'UTF-8',
            ),
            'Html' => array(
                // Data is required
                'Data' => $emailHTML,
                'Charset' => 'UTF-8',
            ),
        ),
    ),
		'ReplyToAddresses' => array('no-reply@gocollegedays.com'),
		'ReturnPath' => 'no-reply@gocollegedays.com',
	));
	//echo "Result: ".$result;
  }
  catch(Exception $e)
  {
	//echo "e: ".$e;
  }

}
?>