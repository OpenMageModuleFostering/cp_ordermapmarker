<?php
class CP_Ordermapmarker_Helper_Data extends Mage_Core_Helper_Abstract
{	
	public function getOrderDataWithCoordinates($order)
	{		
		$country = $order->getShippingAddress()->getCountry() == 'US' ? "United States" : $order->getShippingAddress()->getCountry();
		
		$address = $order->getShippingAddress()->getStreet1(). " ". $order->getShippingAddress()->getStreet2()." ". $order->getShippingAddress()->getCity()." ".$order->getShippingAddress()->getRegion()." ".$order->getShippingAddress()->getPostcode() ." ". $country; 
		
		if($address)
		{			
			if($result = $this->getCoordinatesFromAddress($address)){				
				$response = array('order_id'	  => $order->getIncrementId(),
								  'order_address' => $address,
								  'latitude'	  => $result['latitude'],
								  'longitude'	  => $result['longitude'],
							); 
				return $response;
			}
			return false;
		}		
		return false;
	}
	
	public function getCoordinatesFromAddress($address)
	{
		$address = urlencode($address);
		$url = $this->getgeoCodeApiUrl() . "&address=" . $address;
		$response = file_get_contents($url);
		$json = json_decode($response,true);
 
		$lat = $json['results'][0]['geometry']['location']['lat'];
		$lng = $json['results'][0]['geometry']['location']['lng'];
		
		if($lat && $lng){
			return array("latitude" => $lat,"longitude" => $lng);
		}
		return false;		
	}
	
	public function getgeoCodeApiUrl()
	{
		return "http://maps.google.com/maps/api/geocode/json?sensor=false";
	}
	
	public function getApplicationName()
	{
		return Mage::getStoreConfig('ordermapmarker/config/googleapp', Mage::app()->getStore());		
	}
	
	public function getTableId()	
	{
		return Mage::getStoreConfig('ordermapmarker/config/fusiontable', Mage::app()->getStore());		
	}
	
	public function getClientId()
	{
		return Mage::getStoreConfig('ordermapmarker/config/googleclient', Mage::app()->getStore());		
	}
	
	public function getIsEnabled()
	{
		return Mage::getStoreConfig('ordermapmarker/config/status', Mage::app()->getStore());
	}
} 