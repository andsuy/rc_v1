<?php
class Si_Property_Block_Propertylist extends Mage_Catalog_Block_Product_Abstract
{
    protected function _prepareLayout() {
        return parent::_prepareLayout();
    }

    public function getAdvanceSearchResult(){
        $address = $this->getRequest()->getParam('searchAddress');
        $checkin = $this->getRequest()->getParam('checkin');
        $checkout = $this->getRequest()->getParam('checkout');
        $searchguest = $this->getRequest()->getParam('searchguest');
        $amount = $this->getRequest()->getParam('amount');
        $roomtypeVal = $this->getRequest()->getParam('roomtypeval');
        $amenityval = $this->getRequest()->getParam('amenityval');
        $ratingval = $this->getRequest()->getParam('ratingval');
        $keywordsval = $this->getRequest()->getParam('keywordsval');
        $pageno = $this->getRequest()->getParam('pageno');
        $upperLimitPrice = $this->getRequest()->getParam('upperLimitPrice');
        
        $nelat = $this->getRequest()->getParam('nelat');
        $nelng = $this->getRequest()->getParam('nelng');
        $swlat = $this->getRequest()->getParam('swlat');
        $swlng = $this->getRequest()->getParam('swlng');
        
        //$data = array("address"=>$address,"checkin"=>$checkin,"checkout"=>$checkout,"searchguest"=>$searchguest,"amount"=>$amount,"pageno"=>$pageno,"roomtypeval"=>$roomtypeVal,"amenityVal"=>$amenityval,"ratingVal"=>$ratingval,"keywordsVal"=>$keywordsval,"upperLimitPrice"=>$upperLimitPrice);
        $data = array("address"=>$address,"checkin"=>$checkin,"checkout"=>$checkout,"searchguest"=>$searchguest,"amount"=>$amount,"pageno"=>$pageno,"roomtypeval"=>$roomtypeVal,"amenityVal"=>$amenityval,"ratingVal"=>$ratingval,"keywordsVal"=>$keywordsval,"upperLimitPrice"=>$upperLimitPrice, "nelat"=>$nelat, "nelng"=>$nelng, "swlat"=>$swlat, "swlng"=>$swlng);
        
        if($data["checkin"] == "mm/dd/yyyy"){
            $data["checkin"] ="";
        }
        if($data["checkout"] == "mm/dd/yyyy"){
            $data["checkout"] ="";
        }
        if(trim($data["address"]) == "e.g. Berlin, Germany"){
            $data["address"] ="";
        }
        
        $collection = Mage::getModel('property/property')->advanceSearch($data);
        return $collection;
    }
    
    public function getBookingHistory() {
        $result = array();
        return $result;
    }
}
