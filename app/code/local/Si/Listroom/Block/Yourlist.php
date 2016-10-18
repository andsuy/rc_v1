<?php
class Si_Listroom_Block_Yourlist extends Mage_Catalog_Block_Product_Abstract
{
    protected function _prepareLayout()
    {
        return parent::_prepareLayout();
    }
    
    public function getAdvanceSearchResult(){
        $address = $this->getRequest()->getParam('searchAddress');
        $from = $this->getRequest()->getParam('from');
        $to = $this->getRequest()->getParam('to');
        $guest = $this->getRequest()->getParam('guest');
        $amount = $this->getRequest()->getParam('amount');
        $roomtypeVal = $this->getRequest()->getParam('roomtypeval');
        $amenityval = $this->getRequest()->getParam('amenityval');
        $keywordsval = $this->getRequest()->getParam('keywordsval');
        $pageno = $this->getRequest()->getParam('pageno');

        $data = array("address"=>$address, "from"=>$from, "to"=>$to, "guest"=>$guest, "amount"=>$amount,"pageno"=>$pageno,"roomtypeval"=>$roomtypeVal,"amenityVal"=>$amenityval,"keywordsVal"=>$keywordsval);
        
        if(trim($data["address"]) == "e.g. Berlin, Germany"){
            $data["address"] ="";
        }
        
        $amount = explode("-", $data["amount"]);
        $minval = $amount[0];
        $maxval = $amount[1];
        
        
        $collection = Mage::getModel('listroom/listroom')->getCollection()
        				->addFieldToFilter('status', 1);
        				
        if (trim($data["amount"]) != "") {
	        $collection->addFieldToFilter('budget_max', array('lteq' => $maxval));
	        $collection->addFieldToFilter('budget_min', array('gteq' => $minval));
        }
        
        
        if (trim($data["address"]) != "") {
        	$data["address"] = explode(",", $data["address"]);
            if (count($data["address"]) > 0) {
            	$filterOptions = array();
            	$filterKeys = array();
            	foreach($data["address"] as $_key){
            		$filterKeys[] = 'locality';
            		$filterOptions[] = array('like' => '%' . trim($_key) . '%');
            	}

            	$filterKeys[] = 'city';
        		$filterOptions[] = array('in' => $data["address"]);
        		$filterKeys[] = 'state';
        		$filterOptions[] = array('in' => $data["address"]);
        		$filterKeys[] = 'country';
        		$filterOptions[] = array('in' => $data["address"]);
        		
            	$collection->addFieldToFilter($filterKeys, $filterOptions);
            }
        }
        //echo $collection->getSelect(); exit;
        if (trim($data["roomtypeval"]) != "") {
            $data["roomtypeval"] = explode(",", $data["roomtypeval"]);
            if (count($data["roomtypeval"]) > 0) {
                $collection->addFieldToFilter('property_type', array('in' => array($data["roomtypeval"])));
            }
        }
        

        if (trim($data["amenityVal"]) != "") {
        	$data["amenityVal"] = explode(",", $data["amenityVal"]);
            if (count($data["amenityVal"]) > 0) {
            	$filterOptions = array();
            	$filterKeys = array();
            	foreach($data["amenityVal"] as $_key){
            		$filterKeys[] = 'amenity';
            		$filterOptions[] = array('like' => '%' . trim($_key) . '%');
            	}

            	$collection->addFieldToFilter($filterKeys, $filterOptions);
            }
        }
        
        if (trim($data["keywordsVal"]) != "") {
        	$data["keywordsVal"] = explode(",", $data["keywordsVal"]);
            if (count($data["keywordsVal"]) > 0) {
            	$filterOptions = array();
            	$filterKeys = array();
            	foreach($data["keywordsVal"] as $_key){
            		$filterKeys[] = 'keywords';
            		$filterOptions[] = array('like' => '%' . trim($_key) . '%');
            	}

            	$collection->addFieldToFilter($filterKeys, $filterOptions);
            }
        }
        
        
        if ($data["guest"] > 0) {
            $collection->addFieldToFilter('accommodates',array('lteq' => (int)$data["guest"]) );
        }
        
        if(!empty(trim($data["from"]))) {
	        $fromDate = date('Y-m-d', strtotime($data["from"]));
	        $collection->getSelect()->where("(`from` >= '". $fromDate ."')");
        }
		if(!empty(trim($data["from"]))) {
	        $toDate = date('Y-m-d', strtotime($data["to"]));
	        $collection->getSelect()->where("(`to` <= '". $toDate ."')");
		}
        //echo $collection->getSelect(); exit;
        
        $collection->setCurPage($data["pageno"])->setPageSize(10);
        //$collection->setPage($data["pageno"], 10);
        return $collection;
    }
    
}
