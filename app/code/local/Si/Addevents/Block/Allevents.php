<?php   
class Si_Addevents_Block_Allevents extends Mage_Core_Block_Template
{
	public function setUserLocation()
    {
    	if(!Mage::getSingleton('core/session')->getUserLat() || !Mage::getSingleton('core/session')->getUserLnt()) {
	        $key = Mage::getStoreConfig('ipin/ipin_settings/ipin_api');
			$ip = $_SERVER['REMOTE_ADDR'];
			$url = "http://api.ipinfodb.com/v3/ip-city/?key=$key&ip=$ip&format=xml";
			// load xml file
			$xml = simplexml_load_file($url);
			$xmlData = get_object_vars($xml);
			Mage::getSingleton('core/session')->setUserLat($xmlData['latitude']);
			Mage::getSingleton('core/session')->setUserLnt($xmlData['longitude']);
    	}
    }
}