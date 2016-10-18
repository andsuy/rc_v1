<?php
class Si_Property_SearchController extends Mage_Core_Controller_Front_Action {
	public function indexAction() {
       $this->loadLayout();
       $this->renderLayout();
	}
	
	public function searchresultAction() {
	   $this->loadLayout();
	   $this->renderLayout();
	}
	
	public function regionAction() {
		$countryCode = $this->getRequest()->getParam('ccode');
	    $regionCollection = Mage::getModel('directory/region_api')->items($countryCode);
	    
	    $regionSelect = '';
	    if(count($regionCollection) > 0) {
			$regionSelect .= '<select name="state" id="state" class="validate-select required-entry" >';
			    $regionSelect .= '<option value="">Please select region, state or province</option>';
			        foreach($regionCollection as $region) {
			            $regionSelect .= '<option value="' . $region['name'] .'" >' . $region['name'] .'</option>';
			        }
			$regionSelect .= '</select>';
	    } else {
	    	$regionSelect .= '<input name="state" id="state" title="State / Province / Region" value="" class="input-text required-entry input-text_state" type="text"/>';
	    }
		echo $regionSelect;
	}
	   
	public function mapsearchresultAction() {

			//echo '<pre>'; print_r($positions); echo '</pre>';
			$this->loadLayout();
	   		$this->renderLayout();

	}

   public function searchresultmapAction() {
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
        
        $collection = Mage::getModel('property/property')->advanceSearch($data) ;
        $collectionData = $collection->getData();
        
        $productCollection = array();
        if(count($collectionData) > 0) {
        	foreach($collectionData as $_item) {
        		$product = Mage::getModel('catalog/product')->load($_item['entity_id']);
        		if(Mage::getStoreConfig('property_section/layout_settings/hide_address')) {
                	$addressArray = explode(' ', $product->getAddress());
                	array_shift($addressArray);
                	//echo '<pre>'; print_r($addressArray); echo '</pre>';               	
                	$displayAddress = implode(' ', $addressArray);
                } else {
                	$displayAddress = $product->getAddress();
                }
				                
        		$productCollection[] = array(
        									'title' => $product->getName(),
        									'room_lat' => $product->getLatitude(),
        									'room_lnt' => $product->getLongitude(),
        									'locality' => $displayAddress,
        									'city' => $product->getCity(),
        									'state' => $product->getState(),
        									'country' => $product->getCountry(),
        									'prod_url' => $product->getProductUrl(),
        								);
        	}
        }
//        /echo '<pre>'; print_r($productCollection); echo '</pre>';
        if(count($productCollection) > 0) {
	        echo json_encode($productCollection);
        } else {
        	echo null;
        }
   }
       
	public function checkavailAction() {
		$_store = Mage::app()->getStore();
     	$currencySymbol = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
     	$from = $_GET["from"];
        $to = $_GET["to"];
        $productid = $_GET["productid"];
        $price = $_GET["price"];
        
        $start = strtotime($from);
        $end = strtotime($to);
        $daysBetween = ceil(abs($end - $start) / 86400);
        
        $block = Mage::helper('property')->checkavailAction($from, $to, $productid);
        //echo $block; exit;
        if($block == 0) {
        	echo 0;
        } else {
        	$response = '';
        	$processingFee = Mage::getStoreConfig('property_section/general/processing_fee');
        	$subtotal = 0.00;
	        for($i=0; $i<$daysBetween; $i++) {
				$day = date('d',strtotime($from . "+$i days"));
				$month = date('n',strtotime($from . "+$i days"));
				$year = date('Y',strtotime($from . "+$i days"));
				$checkSpCollection = Mage::getModel('property/propertyspecial')->getCollection()
					->addFieldToFilter('product_id', $productid)
					->addFieldToFilter('special_year', $year)
					->addFieldToFilter('special_month', $month)
					->addFieldToFilter('special_date', $day);
					
				if($checkSpCollection->count() > 0) {
					foreach ($checkSpCollection as $_sp) {
						$price = $_sp->getSpecialPrice();
						break;
					}
				} else {
					$price = $_GET["price"];
				}
		
				$subtotal += $price;
	        }
        
        	//$subtotal = $price * $daysBetween;
            $serviceFee = number_format(($subtotal / 100) * ($processingFee), 2);
            $subtotal = Mage::helper('directory')->currencyConvert($subtotal, Mage::app()->getStore()->getBaseCurrencyCode(), Mage::app()->getStore()->getCurrentCurrencyCode());
            $serviceFee = Mage::helper('directory')->currencyConvert($serviceFee, Mage::app()->getStore()->getBaseCurrencyCode(), Mage::app()->getStore()->getCurrentCurrencyCode());
            
        	$response .= "<p class='subtotal'>".$this->__('Subtotal')."
                    </p>
                    <h2 class='bigTotal'>" . $currencySymbol . $_store->roundPrice($subtotal) . "</h2> <input type='hidden' id='subtotal_days' value = '$daysBetween'> <input type='hidden' id='subtotal_amt' value = '$_store->roundPrice($subtotal)'>";
            $response .= '<p class="subtotal" style="color:#2C7ED1;font-size:10px;padding-bottom:10px;">(* '.$this->__('Exclude processing fee')." ".$currencySymbol . $_store->roundPrice($serviceFee) . ")
                        <input type='hidden' id='serviceFee' name='serviceFee' value='" . $_store->roundPrice($serviceFee) . "' />
                        </p>
                    <div class='clear'></div>
                    ";
        	echo $response;
        }
    }
    
    public function calenderAction() {
    	$_store = Mage::app()->getStore();
        $productId = $_GET["productid"]; //To
        $dateSplit = explode("__", $_GET["date"]);
        $getDateDeatils = Mage::helper('property')->getDateDeatils($productId, $_GET["date"]);
        
        $blocked = $getDateDeatils[0];
        $not_avail = $getDateDeatils[1];
        $selectedArray = array();
        $spe_avail = $getDateDeatils[2];
                
        // calender price start
        $_sp = array();
        foreach($spe_avail as $key=>$value)
        {
            $avail = explode(",",$key);
            foreach($avail as $_val){
                $spDay = (int) $_val;
                $_sp[$spDay] = $value;
            }
         }
        //end of calender price

        $x = $dateSplit[0];
        if ($x == "")
            $x = date("n");
        $year = $dateSplit[1];
        $date = strtotime("$year/$x/1");
        $day = date("D", $date);
        $m = date("F", $date);
        $prev_year = $year;
        $next_year = $year;
        $prev_month = intval($x) - 1;
        $next_month = intval($x) + 1;

// if current month is Decembe or January month navigation links have to be updated to point to next / prev years
        if ($x == 12) {
            $next_month = 1;
            $next_year = $year + 1;
        } elseif ($x == 1) {
            $prev_month = 12;
            $prev_year = $year - 1;
        }
        $totaldays = date("t", $date);
        echo "<table border = '1' cellspacing = '0'  bordercolor='blue' cellpadding ='2' class='calend'>
                        <tr class='weekDays'>
	                        <th><font size = '2' face = 'tahoma'>Sun</font></th>
	                        <th><font size = '2' face = 'tahoma'>Mon</font></th>
	                        <th><font size = '2' face = 'tahoma'>Tue</font></th>
	                        <th><font size = '2' face = 'tahoma'>Wed</font></th>
	                        <th><font size = '2' face = 'tahoma'>Thu</font></th>
	                        <th><font size = '2' face = 'tahoma'>Fri</font></th>
	                        <th><font size = '2' face = 'tahoma'>Sat</font></th>
                        </tr> ";

        if ($day == "Sun")
            $st = 1;
        if ($day == "Mon")
            $st = 2;
        if ($day == "Tue")
            $st = 3;
        if ($day == "Wed")
            $st = 4;
        if ($day == "Thu")
            $st = 5;
        if ($day == "Fri")
            $st = 6;
        if ($day == "Sat")
            $st = 7;
        if ($st >= 6 && $totaldays == 31) {
            $tl = 42;
        } elseif ($st == 7 && $totaldays == 30) {
            $tl = 42;
        } else {
            $tl = 35;
        }
        $ctr = 1;
        $d = 1;

        for ($i = 1; $i <= $tl; $i++) {
            if ($ctr == 1)
                echo "<tr class='blockcal'>";
            if ($i >= $st && $d <= $totaldays) {
                if (strtotime("$year-$x-$d") < strtotime(date("Y-n-j", strtotime('+3 days')))) {
                    echo "<td align='center' class='previous days '><font size = '2' face = 'tahoma'>$d</font></td>";
                } else {
                    if (in_array("$year-$x-$d", $selectedArray)) {
                        echo "<td class='selected days' align='center'><font size = '2' face = 'tahoma'>$d</font></td>";
                    } else {
                        $date = strtotime("$year/$x/$d");
                        $tdDate = 'tdId' . '_' . date("m/d/Y", $date);
                        if (in_array("$d", $blocked)) {
                            echo "<td id=" . $tdDate . " class='normal days " . $d . " ' align='center' style='background-color:#E07272;'><font size = '2' face = 'tahoma'>$d</font></td>";
                        } else if (in_array("$d", $not_avail)) {
                            echo "<td id=" . $tdDate . " class='normal days " . $d . " ' align='center'style='background-color:#F18200;color: black !important;' ><font size = '2' face = 'tahoma'>$d</font></td>";
                        } else if(array_key_exists($d,$_sp)){
                                echo "<td style='padding: 11px 23px;' id=" . $tdDate . " class='normal days " . $d . " ' align='center' ><font size = '2' face = 'tahoma'>$d</font><br><div style='width: 25px;font-size: 1.0em;text-align: right;'>". 
                                        Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol().$_store->roundPrice(Mage::helper('directory')->currencyConvert($_sp[$d],Mage::app()->getStore()->getBaseCurrencyCode(), Mage::app()->getStore()->getCurrentCurrencyCode()))."</div></td>";
                            }else{
                                echo "<td id=" . $tdDate . " class='normal days " . $d . " ' align='center' ><font size = '2' face = 'tahoma'>$d</font></td>";
                          }
                        }
                }
                $d++;
            } else {
                echo "<td>&nbsp</td>";
            }
            $ctr++;
            if ($ctr > 7) {
                $ctr = 1;
                echo "</tr>";
            }
        }
        echo '</table>';
    }
    
    public function averagecheckAction() {
		if($zipcode = $this->getRequest()->getParam('zipcode')) {
			$type = $this->getRequest()->getParam('roomtype');
			$collection = Mage::getModel('catalog/product')->getCollection()
    				->addAttributeToFilter('zipcode', $zipcode);
    		if($type == 1) {
    			$collection->addAttributeToFilter('room_type', array('in'=>array(11,12)));
    		} elseif($type == 2) {
    			$collection->addAttributeToFilter('room_type', 10);
    		}
	    	if($collection->count()) {
	    		$price = 0.00;
	    		foreach($collection as $_product) {
	    			$product = Mage::getModel('catalog/product')->load($_product->getId());
	    			$price += $product->getPrice();
	    		}
	    		$averagePrice = (float)($price / $collection->count());
	    	} else {
	    		$averagePrice = null;
	    	}
    	
			if($averagePrice) {
				$return = '<p class="empty" style="font-weight:bold; font-size:14px;">The average price for this area is ' . Mage::helper('core')->currency($averagePrice, true, false) .'.</p>';
			} else {
				$return = '<p class="empty" style="font-weight:bold; font-size:14px;">We have no suggesstion for you.</p>';
			}
		} else {
				$return = '<p class="empty" style="font-weight:bold; font-size:14px;">We have no suggesstion for you.</p>';
			}
		echo $return;
    }
}