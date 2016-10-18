<?php   
class Si_Addevents_Block_Foursquare extends Mage_Core_Block_Template
{
    public function getIsHomePage()
    {
        return $this->getUrl('') == $this->getUrl('*/*/*', array('_current'=>true, '_use_rewrite'=>true));
    }
    public function isEnabled()
    {
    	if(Mage::getStoreConfig('foursquare/foursquare_settings/enable') == 1) {
    		return true;
    	} else {
    		return false;
    	}
    }
    public function isDisplayInHomePage()
    {
    	if(Mage::getStoreConfig('foursquare/foursquare_settings/display_home') == 1) {
    		return true;
    	} else {
    		return false;
    	}
    }
    public function isDisplayInPropertyPage()
    {
    	if(Mage::getStoreConfig('foursquare/foursquare_settings/display_property') == 1) {
    		return true;
    	} else {
    		return false;
    	}
    }

    public function getEventsListHome()
	{
		$lat =  '34.0928348';
		$lnt =  '-118.2439889';
		
		if(Mage::getSingleton('core/session')->getUserLat() && Mage::getSingleton('core/session')->getUserLnt()) {
			$lat =  Mage::getSingleton('core/session')->getUserLat();
			$lnt =  Mage::getSingleton('core/session')->getUserLnt();		
		}
		
		return $this->getDisplayEventsList($lat, $lnt);
	}
	public function getEventsList($pid)
	{
		$lat =  '34.0928348';
		$lnt =  '-118.2439889';
		
		$product = Mage::getModel('catalog/product')->load($pid);
		if($product->getLatitude() && $product->getLongitude()) {
			$lat =  $product->getLatitude();
			$lnt =  $product->getLongitude();
		}

		return $this->getDisplayEventsList($lat, $lnt);
	}
	
	public function getDisplayEventsList($lat, $lnt)
	{
		$client_id = Mage::getStoreConfig('foursquare/foursquare_settings/client_id');
		$client_secret = Mage::getStoreConfig('foursquare/foursquare_settings/client_secret');
		//$url = "https://api.foursquare.com/v2/venues/search?client_id=". $client_id ."&client_secret=". $client_secret ."&v=20130815&ll=". $lat .",". $lnt ."&radius=10000&categoryId=4bf58dd8d48988d181941735,52e81612bcbc57f1066b79ed,507c8c4091d498d9fc8c67a9,52e81612bcbc57f1066b7a22,50aaa49e4b90af0d42d5de11,52e81612bcbc57f1066b7a23,4eb1d4d54b900d56c88a45fc,52e81612bcbc57f1066b7a14,4bf58dd8d48988d165941735,5032848691d4c4b30a586d61&limit=3";
		$url = "https://api.foursquare.com/v2/venues/search?client_id=". $client_id ."&client_secret=". $client_secret ."&v=20130815&ll=". $lat .",". $lnt ."&radius=10000&categoryId=4d4b7105d754a06377d81259,4d4b7105d754a06378d81259&limit=3";
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		$data = curl_exec($ch);
		curl_close($ch);
		
		$response = json_decode($data);

		return $response;
	}
}