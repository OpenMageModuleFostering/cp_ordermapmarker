<?php
require_once(Mage::getBaseDir('lib') . '/Googlefusiontables/contrib/Google_FusiontablesService.php');
require_once(Mage::getBaseDir('lib') . '/Googlefusiontables/contrib/Google_Oauth2Service.php');
require_once(Mage::getBaseDir('lib') . '/Googlefusiontables/Google_Client.php');

class CP_Ordermapmarker_Helper_Ordermapmarker
{
	private $helper;
	
	public function __construct()
	{		
		$this->helper = Mage::helper('ordermapmarker');
	}
	public function syncCoordinatesToFusionTable($location)
	{		
		try{
			$client = new Google_Client();
			$client->setApplicationName($this->helper->getApplicationName());
			session_start();
			if (isset($_SESSION['token'])){
				$client->setAccessToken($_SESSION['token']);
			}
			
			$tableid = $this->helper->getTableId();
			$key = file_get_contents(Mage::getBaseDir('lib'). DS .'Googlefusiontables'. DS . 'key'. DS .'fusionmaps-7915addd10af.p12');
		
			$client->setClientId($this->helper->getClientId());
			
			$client->setAssertionCredentials(new Google_AssertionCredentials($this->helper->getClientId(),  array('https://www.googleapis.com/auth/fusiontables'), $key,'notasecret','http://oauth.net/grant_type/jwt/1.0/bearer'));
		
			$service = new Google_FusiontablesService($client);
			$insert_statement = "Insert into $tableid (`order_id`,`order_address`,`latitude`,`longitude`) VALUES('".$location['order_id']."','".$location['order_address']."','".$location['latitude']."','".$location['longitude']."');";					
			$result = $service->query->sql($insert_statement);
			
			if($result['columns'][0] && $result['rows'][0][0]){
				return true;
			}			
			return false;
			
		}catch(Exception $e){			
			Mage::log($e->getMessage(), null, 'googleFusion.log');
			return false;
		}		
	}	
}
