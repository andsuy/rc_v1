<?php
// Enter the path that the oauth library is in relation to the php file
require_once (Mage::getBaseDir() . '/yelp/OAuth.php');
   
class Si_Addevents_Block_Yelp extends Mage_Core_Block_Template
{
    public function getIsHomePage()
    {
        return $this->getUrl('') == $this->getUrl('*/*/*', array('_current'=>true, '_use_rewrite'=>true));
    }
    public function isEnabled($type)
    {
    	if(Mage::getStoreConfig('yelp/'.$type.'/enable') == 1) {
    		return true;
    	} else {
    		return false;
    	}
    }
    public function isDisplayInHomePage()
    {
    	if(Mage::getStoreConfig('yelp/yelp_settings/display_home') == 1) {
    		return true;
    	} else {
    		return false;
    	}
    }
    public function isDisplayInPropertyPage()
    {
    	if(Mage::getStoreConfig('yelp/yelp_settings/display_property') == 1) {
    		return true;
    	} else {
    		return false;
    	}
    }

    public function getEventsListHome($term = '', $category = '')
	{
		$lat =  '34.0928348';
		$lnt =  '-118.2439889';
		
		if(Mage::getSingleton('core/session')->getUserLat() && Mage::getSingleton('core/session')->getUserLnt()) {
			$lat =  Mage::getSingleton('core/session')->getUserLat();
			$lnt =  Mage::getSingleton('core/session')->getUserLnt();		
		}
		
		return $this->getDisplayEventsList($lat, $lnt, $term, $category);
	}
	public function getEventsList($pid, $term = '', $category = '')
	{
		$lat =  '34.0928348';
		$lnt =  '-118.2439889';
		
		$product = Mage::getModel('catalog/product')->load($pid);
		if($product->getLatitude() && $product->getLongitude()) {
			$lat =  $product->getLatitude();
			$lnt =  $product->getLongitude();
		}

		return $this->getDisplayEventsList($lat, $lnt, $term, $category);
	}
	
	public function getDisplayEventsList($lat, $lnt, $term = '', $category = '')
	{
		//$unsigned_url = "http://api.yelp.com/v2/search?term=restaurants&sort=2&limit=10&". $lat .",". $lnt;
		$unsigned_url = "http://api.yelp.com/v2/search?term=". $term ."&category_filter=". $category ."&ll=".$lat.",".$lnt."&sort=2&limit=4";
		
		// Set your keys here
		$consumer_key = Mage::getStoreConfig('yelp/yelp_settings/consumer_key');
		$consumer_secret = Mage::getStoreConfig('yelp/yelp_settings/consumer_secret');
		$token = Mage::getStoreConfig('yelp/yelp_settings/token');
		$token_secret = Mage::getStoreConfig('yelp/yelp_settings/token_secret');
		
		// Token object built using the OAuth library
		$token = new OAuthToken($token, $token_secret);
		
		// Consumer object built using the OAuth library
		$consumer = new OAuthConsumer($consumer_key, $consumer_secret);
		
		// Yelp uses HMAC SHA1 encoding
		$signature_method = new OAuthSignatureMethod_HMAC_SHA1();
		
		// Build OAuth Request using the OAuth PHP library. Uses the consumer and token object created above.
		$oauthrequest = OAuthRequest::from_consumer_and_token($consumer, $token, 'GET', $unsigned_url);
		
		// Sign the request
		$oauthrequest->sign_request($signature_method, $consumer, $token);
		
		// Get the signed URL
		$signed_url = $oauthrequest->to_url();
				
		// Send Yelp API Call
		$ch = curl_init($signed_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		$data = curl_exec($ch); // Yelp response
		curl_close($ch);
		
		// Handle Yelp response data
		$response = json_decode($data);

		return $response;
	}
}