<?php
class CP_Ordermapmarker_Model_Observer
{
	public function createOrderAddressPin(Varien_Event_Observer $observer)
	{		
		$order = $observer->getOrder();		
		
		// Check if Google Map Maker is Available.
		if(Mage::helper('ordermapmarker')->getIsEnabled()){
			
			// Get an Array of Order with Id, Address and Location Coordinates.	
			$orderData = Mage::helper('ordermapmarker')->getOrderDataWithCoordinates($order);				
			
			// Insert the Order Array In Google-Fusion Table.
			if($orderData){
				$SyncProcessResult = Mage::helper('ordermapmarker/ordermapmarker')->syncCoordinatesToFusionTable($orderData);
				if($SyncProcessResult){
					Mage::log("Google Maps Sync Successful for Order: ".$order->getIncrementId(), null, 'googleFusion.log');
				}else{
					Mage::log("Google Maps Sync Failed for Order: ".$order->getIncrementId(), null, 'googleFusion.log');
				}
			}
			return;
		}
	}	
}
