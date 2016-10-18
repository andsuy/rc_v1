<?php   
class Si_Addevents_Block_Eventbrite extends Mage_Core_Block_Template
{
    public function getIsHomePage()
    {
        return $this->getUrl('') == $this->getUrl('*/*/*', array('_current'=>true, '_use_rewrite'=>true));
    }
    public function isEnabled()
    {
    	if(Mage::getStoreConfig('eventbrite/eventbrite_settings/enable') == 1) {
    		return true;
    	} else {
    		return false;
    	}
    }
    public function isDisplayInHomePage()
    {
    	if(Mage::getStoreConfig('eventbrite/eventbrite_settings/display_home') == 1) {
    		return true;
    	} else {
    		return false;
    	}
    }
    public function isDisplayInPropertyPage()
    {
    	if(Mage::getStoreConfig('eventbrite/eventbrite_settings/display_property') == 1) {
    		return true;
    	} else {
    		return false;
    	}
    }
    public function getDay($day)
    {
    	$days = array(
    				0 => 'Monday',
    				1 => 'Tuesday',
    				2 => 'Wednesday',
    				3 => 'Thursday',
    				4 => 'Friday',
    				5 => 'Saturday',
    				6 => 'Sunday'
    			);
    	return $days[$day];
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
		$authentication_tokens = array('app_key'  => Mage::getStoreConfig('eventbrite/eventbrite_settings/api_key'),
		                       			'user_key' => Mage::getStoreConfig('eventbrite/eventbrite_settings/user_key'));
		$eb_client = new Si_Addevents_Helper_Eventbrite( $authentication_tokens );
		$search_params = array(
		  'max' => 3,
		  //'city' => 'San Francisco',
		  //'region' => 'CA',
		  //'country' => 'US'
		  'category'	=> 'conferences,conventions,performances,social,fairs,travel,food,music,recreation',
		  'latitude'	=>	$lat,
		  'longitude'	=>	$lnt,
		  'sort_by'		=>	'date',
		  'date_created' => date('Y-m-d', strtotime('-1 year')) . ' ' . date('Y-m-d'),
		  'date'		=>	'Future',
		  'within'		=>	10,
		  'within_unit'	=>	'M'
		);
		$resp = $eb_client->event_search($search_params);
		$respArray = get_object_vars($resp);
		return $respArray;
	}
}