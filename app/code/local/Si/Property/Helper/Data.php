<?php
class Si_Property_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getPropertyTypeAttrId() {
		return Mage::getResourceModel('eav/entity_attribute')->getIdByCode('catalog_product', 'property_type');
	}
	public  function getRoomTypeAttrId() {
		return Mage::getResourceModel('eav/entity_attribute')->getIdByCode('catalog_product', 'room_type');
	}
	public function getAmenitiesAttrId() {
		return Mage::getResourceModel('eav/entity_attribute')->getIdByCode('catalog_product', 'amenities');
	}	
	public function getCancellationPolicyAttrId() {
		return Mage::getResourceModel('eav/entity_attribute')->getIdByCode('catalog_product', 'cancellation_policy');	
	}
	public function getRestrictionsAttrId() {
		return Mage::getResourceModel('eav/entity_attribute')->getIdByCode('catalog_product', 'restrictions');	
	}
	public function getBedTypeAttrId() {
		return Mage::getResourceModel('eav/entity_attribute')->getIdByCode('catalog_product', 'bed_type');
	}
	
	public function getAccomodatesType() {
		return Mage::getResourceModel('eav/entity_attribute')->getIdByCode('catalog_product','accomodates');
    }
	public  function getRooms() {
		return Mage::getResourceModel('eav/entity_attribute')->getIdByCode('catalog_product','rooms_available');
	}
        
	public  function getbaseimage() {
		return Mage::getResourceModel('eav/entity_attribute')->getIdByCode('catalog_product', 'image');
	}
	public  function getsmallimage() {
		return Mage::getResourceModel('eav/entity_attribute')->getIdByCode('catalog_product', 'small_image');
	}
	public  function getthumbimage() {
		return Mage::getResourceModel('eav/entity_attribute')->getIdByCode('catalog_product', 'thumbnail');
	}
	public  function getmediagallery() {
		return Mage::getResourceModel('eav/entity_attribute')->getIdByCode('catalog_product', 'media_gallery');
	}
	
	public function getCustomerRatings($productId) {
        $resource = Mage::getSingleton('core/resource');
        $cn = $resource->getConnection('core_read');
        $sql = "select rt.rating_code,avg(vote.percent) as percent from " . $resource->getTableName('rating_option_vote') . " as vote inner join " . $resource->getTableName('rating') . " as rt on(vote.rating_id=rt.rating_id) inner join " . $resource->getTableName('review') . " as rr on(vote.entity_pk_value=rr.entity_pk_value) where rt.entity_id=1 and vote.entity_pk_value='$productId'  and rr.status_id=1 group by rt.rating_code";
        $rating = $cn->fetchAll($sql);
        return $rating;
    }
    public function showRatingCode($count=0) {
        for ($x = 1; $x <= $count; $x++) {
            echo "<img style='float:left'  src='" .  Mage::getDesign()->getSkinUrl('images/red.png') . "' width='16' height='16' alt='' />";
        }
        for ($i = $x; $i <= 5; $i++) {
            echo "<img style='float:left'  src='" .  Mage::getDesign()->getSkinUrl('images/grey.png') . "' width='16' height='16' alt=''/>";
        }
    }
    
    public function getPropertyName($propertyName) {
        $property_name = substr($propertyName, 0, 20);
        if(strlen($propertyName) >20) { $property_name.='..'; }
        return $property_name;
    }
    
    public function checkavailAction($from, $to, $productid) {
        $resource = Mage::getSingleton('core/resource');
		$write = $resource->getConnection('core_write');
     	$table = $resource->getTableName('property_availability');

        
        $start = strtotime($from);
        $end = strtotime($to);
        $daysBetween = ceil(abs($end - $start) / 86400);
        $condition = '';
        for($i=0; $i<$daysBetween; $i++) {
			$day = date('d',strtotime($from . "+$i days"));
			$month = date('n',strtotime($from . "+$i days"));
			$year = date('Y',strtotime($from . "+$i days"));
			$condition .= "(booking_year = '". $year ."' AND booking_month = '". $month ."' AND block_date LIKE '%". $day ."%') OR ";
        }
        $conditionClause = rtrim($condition, ' OR ');
        $checkSql = "SELECT COUNT(available_id) as block FROM ". $table ." WHERE product_id = ". $productid ." AND (" . $conditionClause . ")";
        $block = $write->fetchOne($checkSql);
        //echo $checkSql; exit;
        if($block != 0) {
        	return 0;
        } else {
        	return 1;
        }
    }
    
    public function getDateDeatils($productId, $date) {
    	$dateSplit = explode("__", $date);
    	
    	$bookedDates = array();
    	$checkBookedCollection = Mage::getModel('property/propertyavailablity')->getCollection()
    			->addFieldToFilter('product_id', $productId)
    			->addFieldToFilter('booking_type', 2)
				->addFieldToFilter('booking_year', $dateSplit[1])
				->addFieldToFilter('booking_month', $dateSplit[0]);
		//echo $checkBookedCollection->getSelect(); exit;
		if($checkBookedCollection->count() > 0) {
			foreach ($checkBookedCollection as $_booked) {
				$bookDateArr = explode(',', $_booked->getBlockDate());
				foreach ($bookDateArr as $book) {
					$bookedDates[] = $book;
				}
			}
		}
		$uniqueBookedDates = array_unique($bookedDates);
		
		$notAvailDates = array();
		$checkNotavailCollection = Mage::getModel('property/propertyavailablity')->getCollection()
				->addFieldToFilter('product_id', $productId)
    			->addFieldToFilter('booking_type', 1)
				->addFieldToFilter('booking_year', $dateSplit[1])
				->addFieldToFilter('booking_month', $dateSplit[0]);
		if($checkNotavailCollection->count() > 0) {
			foreach ($checkNotavailCollection as $_notAvail) {
				$_notAvailArr = explode(',', $_notAvail->getBlockDate());
				foreach ($_notAvailArr as $notAvail) {
					$notAvailDates[] = $notAvail;
				}
			}
		}
		$uniqueNotAvailDates = array_unique($notAvailDates);
		
		$spDates = array();
		$checkSpCollection = Mage::getModel('property/propertyspecial')->getCollection()
				->addFieldToFilter('product_id', $productId)
				->addFieldToFilter('special_year', $dateSplit[1])
				->addFieldToFilter('special_month', $dateSplit[0]);
		if($checkSpCollection->count() > 0) {
			foreach ($checkSpCollection as $_sp) {
				$spDates[$_sp->getSpecialDate()] = $_sp->getSpecialPrice();
			}
		}
				
		return array($uniqueBookedDates, $uniqueNotAvailDates, $spDates);
    }
    
    public function getSimilarLocation($city, $productId) {
        $propertyCollection = Mage::getModel('property/property')->getPropertyCollection()
                        ->addAttributeToSelect('*')
                        ->addAttributeToFilter('entity_id', array('neq' => $productId))
                        ->addAttributeToFilter('city', array('like' => $city . "%"))
                        ->addFieldToFilter(array(array('attribute' => 'status', 'eq' => '1')))
                        ->setPageSize(10)->setOrder('created_at', 'desc');
		//echo $propertyCollection->getSelect(); exit;
        return $propertyCollection;
    }
    
    public function getSimilarCustomer($customerid, $productId) {
        $propertyCollection = Mage::getModel('property/property')->getPropertyCollection()
                        ->addAttributeToSelect('*')
                        ->addAttributeToFilter('entity_id', array('neq' => $productId))
                        ->addAttributeToFilter('user_id', array('eq' => $customerid))
                        ->addFieldToFilter(array(array('attribute' => 'status', 'eq' => '1')))
                        ->setPageSize(10)->setOrder('created_at', 'desc');
        return $propertyCollection;
    }
}
	 