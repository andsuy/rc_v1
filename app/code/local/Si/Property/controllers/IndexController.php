<?php
class Si_Property_IndexController extends Mage_Core_Controller_Front_Action{
	public function preDispatch() {
	    parent::preDispatch();
	    if (!Mage::getSingleton('customer/session')->authenticate($this)) {
	    	Mage::getSingleton('core/session')->addNotice($this->__('Login to access this section.'));
	        $this->setFlag('', 'no-dispatch', true);
	    }
    }
    
    /*public function indexAction() {
	  $this->loadLayout();   
	  $this->getLayout()->getBlock("head")->setTitle($this->__("Titlename"));
      $this->renderLayout();   
    }*/
    
    public function formAction() {
	  $this->loadLayout();   
	  $this->getLayout()->getBlock("head")->setTitle($this->__("List your Space"));
      $this->renderLayout();
    }
    
    public function postAction() {
        $this->loadLayout();
        $this->renderLayout();
        $customer = Mage::getSingleton('customer/session')->getCustomer();

        $CusId = $customer->getId();
        $CusEmail = $customer->getEmail();
        
        // check 1st property for the user
        $fisrtPost = 0;
        $prevProducts = Mage::getModel('property/property')->getPropertyCollection()
        	->addFieldToFilter(array(array('attribute' => 'user_id', 'eq' => $CusId)));
        if(count($prevProducts) == 0) {
        	$fisrtPost = 1;
        }
        
        
        $post = $this->getRequest()->getPost();

        $amenity = array();
        $amenity = implode(",", $post['amenities']);
        $amenity = str_replace(" ", "", $amenity);
        
        $restriction = array();
        $restriction = implode(",", $post['restrictions']);
        $restriction = str_replace(" ", "", $restriction);
        
        $random = rand(1, 100000000000);
        $sku = rand(1, $random);
        $websiteId = Mage::app()->getWebsite()->getId(); //Website Id
        $store_id = Mage::app()->getStore()->getId(); //store Id
        
        if ($post && $_FILES['propertyimage']['size'] <='1048576') {
			
			if(isset($_FILES['propertyimage']['name']) && $_FILES['propertyimage']['name'] != '') {
				try {	
					/* Starting upload */	
					$uploader = new Varien_File_Uploader('propertyimage');
					
					// Any extention would work
	           		$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
					$uploader->setAllowRenameFiles(false);
					
					// Set the file upload mode 
					// false -> get the file directly in the specified folder
					// true -> get the file in the product like folders 
					//	(file.jpg will go in something like /media/f/i/file.jpg)
					$uploader->setFilesDispersion(true);
							
					// We set media as the upload dir
					$path = Mage::getBaseDir('media') . DS . 'catalog' . DS . 'product' . DS;
					$imagePath = $uploader->save($path, $_FILES['propertyimage']['name'] );
					//echo '<pre>'; print_r($imagePath); echo '</pre>'; exit;
				} catch (Exception $e) {
		      		Mage::getSingleton('core/session')->addError('Image upload error.');
		        }
		        //this way the name is saved in DB
	  			$filePath = Mage::getBaseDir('media') . DS . 'catalog' . DS . 'product' . $imagePath['file'];
			}
				$lat = '';
				$lnt = '';
				try {
					$q = urlencode($post['address'].','.$post['city'].",".$post['state'].",".$post['propcountry']);
					$q = str_replace(" ", "+", $q);
					$geocodeURL = "http://maps.googleapis.com/maps/api/geocode/json?address=$q&sensor=false";
					$ch = curl_init($geocodeURL);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					$result = curl_exec($ch);
					$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
					curl_close($ch);
					if ($httpCode == 200) {
					  $geocode = json_decode($result);
					  $lat = $geocode->results[0]->geometry->location->lat;
					  $lnt = $geocode->results[0]->geometry->location->lng;
					  //echo 'Lat=' . $lat . ' || Lnt='. $lnt; exit;
					  $formatted_address = $geocode->results[0]->formatted_address;
					  $geo_status = $geocode->status;
					  $location_type = $geocode->results[0]->geometry->location_type;
					} else {
					 $geo_status = "HTTP_FAIL_$httpCode";
					}
				} catch (Exception $ex) {
				}
            $product = Mage::getModel('catalog/product');
            // Build the product
            $country_name = Mage::app()->getLocale()->getCountryTranslation($post['country']);
            $search_keywords = $post['address'] . ' ' . $post['city'] . ' ' . $post['state'] . ' ' . $country_name;
            $product->setStoreID($store_id);//Store Id
               $product ->setRoomsAvailable($post['rooms_available'])//No of rooms avaliable
                    ->setSku($sku)//product Sku
                    ->setUserId($CusId)//Customer id
                    ->setAttributeSetId(9)
                    ->setTypeId('virtual')//product typereviewPage
                    ->setName($post['name'])//propertyName
                    ->setDescription($post['desc'])//Description
                    ->setShortDescription($post['desc'])//shortdescription
                    ->setPrice($post['price']) // Set some price
                    ->setCleaningFee($post['cleaning_fee'])
                    ->setExtraFees($post['extra_fees'])
                    ->setDepositAmount($post['deposit_amount'])
                    ->setAccomodates($post['accomodates'])//Custom created and assigned attributes
                    ->setPropertyEmail($CusEmail)//host email id
                    ->setAddress($post['address'])// property address
                    ->setAmenities($amenity)//amenity like room service,e.t.c
                    ->setRestrictions($restriction)
                    ->setState($post['state'])//property state name
                    ->setCity($post['city'])// property city name
                    ->setCountry($country_name)//country
                    ->setZipcode($post['zipcode'])
                    ->setSearchKeywords($search_keywords)
                    ->setLatitude($lat)//Latitude
                    ->setLongitude($lnt)//Longitude
                    ->setPropertySize($post['property_size'])
                    ->setHostContactNumber($post['host_contact_number'])
                    ->setCancellationPolicy($post['cancellation_policy'])//regarding to cancelation policy
                    ->setBedType($post['bed_type'])//bedtype
                    ->setPropertyType($post['property_type'])//property type
                    ->setRoomType($post['room_type'])//room type
                    ->setCategoryIds(array(3))//Property Category
                    ->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)//Visibility in both catalog and search
                    ->setKeywords(trim($post['keywords']))//property keywords<br>
					->setHouseRule(trim($post['house_rule']))//house rules
                    ->setStatus(1) //enable the Status
                    ->setTaxClassId(0) # My default tax class
                    ->setStockData(array(
                        'is_in_stock' => 1,
                        'qty' => 100000
                    ))//Inventory
                    //->setBanner($post['banner']) //banner
                    ->setUrlKey($sku)
                    ->setPropertyApproved(1) //approved
                    ->setCreatedAt(strtotime('now'))
                    ->setWebsiteIDs(array($websiteId)); //Website id, my is 1 (default frontend)
                try {
            		$product->save();
            		
            		$propertyLocationData = array(
            									'product_id' => $product->getId(),
            									'latitude' => $lat,
            									'longitude' => $lnt,
            									'created_at' => now(),
            									'update_at' => now(),
            								);
            		$propertyLoaction = Mage::getModel('property/propertylocation');
            		$propertyLoaction->setData($propertyLocationData)->setId();
            		$propertyLoaction->save();
            		
            		// if first post send mail and set booking credit
            		//if($fisrtPost = 1) {
            			
						// Database access                
				$dbhost = '54.186.179.68';
				$dbuser = 'amazonmagento';
				$dbpass = 'trAv4WjBrHn';
				$conn = mysql_connect($dbhost, $dbuser, $dbpass);
				if(! $conn ) {
				  //die('Could not connect: ' . mysql_error());
				  Mage::log('EC2 Database log: '. mysql_error());
				}
				$db_selected = mysql_select_db('amazonmagento', $conn);
				if (!$db_selected) {
					//die ("Can\'t use test_db : " . mysql_error());
					Mage::log('EC2 Database log: '. mysql_error());
				}

					$mailTemplate = Mage::getModel('core/email_template');
					
					$senderName = Mage::getSingleton('customer/session')->getCustomer()->getName();
					$senderEmail = Mage::getSingleton('customer/session')->getCustomer()->getEmail();
			        //**** contact mails to hodt starts ****/
			        //get guest details
			        //$host = Mage::getModel('customer/customer')->load($model->getRecieverId());
			        $storeId = Mage::app()->getStore()->getStoreId();
			        //Mage::log('Check: ' . $host . ' -- ' . $storeId);
			        //$product = Mage::getModel('catalog/product')->load($model->getProductId());
			            	        
			            ////**** mail function to guest starts ****/
				        // Retrieve corresponding email template id
						
			            $templateId = Mage::getStoreConfig('property_section/custom_email/seller_first_post_template', $storeId);
						//echo $templateId;
						
			            $emailTemplate = $mailTemplate->loadDefault($templateId);
			            $emailTemplateVariables = array(
						            'host_name' => $senderName
				        );
				        $processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
			            $generalcontact=Mage::getStoreConfig('trans_email/ident_general/email');
						
						$receiverName = 'Admin';
						$receiverEmail = $generalcontact;
			            $mailSubject = "New Post";
						$mailBody = $processedTemplate;
						$status = 0;
						$createdAt = now();
						$updateAt = now();
		                
						$sql = "INSERT INTO mails_data ".
						       "(sender_name, sender_email, receiver_name, receiver_email, mail_subject, mail_body, status, created_at, update_at) ".
						       "VALUES ".
						       "('$senderName','$senderEmail','$receiverName','$receiverEmail','$mailSubject','$mailBody','$status','$createdAt','$updateAt')";
						//echo $sql;
						
						$retval = mysql_query( $sql, $conn );
						if(! $retval ) {
							//die('Could not insert data: ' . mysql_error());
							Mage::log('EC2 Database log: '. mysql_error());
						}	
				       Mage::getSingleton('core/session')->addSuccess($this->__('Your message sent successfully.'));
            		//}
                }
                catch (Exception $ex) {
                    //Handle the error
                    Mage::getSingleton('core/session')->addError('Error');
                }
				if (file_exists($filePath)) {
					try {
						$productObj = Mage::getModel('catalog/product')->load($product->getId());
						$productObj->addImageToMediaGallery($filePath, array('image', 'small_image', 'thumbnail'), true, false);
						$productObj->save();
					} catch (Exception $ex) {
						Mage::getSingleton('core/session')->addError('Product image save error.');
					}
				}
            Mage::getModel('core/cookie')->set('product_approveid', $product->getId());
            Mage::getSingleton('core/session')->addSuccess($this->__('Space created Successfully'));

            $this->_redirect('property/index/show', array('trigger' => 'p'));
        } else {
            Mage::getSingleton('core/session')->addError($this->__('Space cannot be created currently. Please contact customer care.'));
            $this->_redirect('*/*/form');
        }
    }

    public function editAction() {
        $this->loadLayout();
        $customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
        $this->getLayout()->getBlock('head')->setTitle($this->__('Edit your Place'));
        $this->renderLayout();

        //owner permission 
        $entity_id = (int)$this->getRequest()->getParam('id');
        $collection = Mage::getModel('catalog/product')->load($entity_id);
        $customer_id = $collection->getUserId();

        if ($customerId != $customer_id) {
            Mage::getSingleton('core/session')->addError($this->__("Access denied"));
            $this->_redirect('*/*/show/');
            return;
        }
    }
    
    public function deleteAction() {
        $customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
        
        //owner permission 
        $entity_id = (int)$this->getRequest()->getParam('id');
        $collection = Mage::getModel('catalog/product')->load($entity_id);
        $customer_id = $collection->getUserId();

        if ($customerId != $customer_id) {
            Mage::getSingleton('core/session')->addError($this->__("Access denied"));
            $this->_redirect('*/*/show/');
            return;
        }
        Mage::register('isSecureArea', true);
        Mage::getModel('catalog/product')->setId($entity_id)->delete();
        Mage::unregister('isSecureArea');
        Mage::getSingleton('core/session')->addSuccess($this->__("Property has been deleted successfully"));
        $this->_redirect('*/*/show');
        return;
    }
    
    public function updateAction() {
        if($post = $this->getRequest()->getParams()) {
	        $amenity = array();
	        $amenity = implode(",", $post['amenity']);
	        $amenity = str_replace(" ", "", $amenity);
	        
	        $restriction = array();
	        $restriction = implode(",", $post['restriction']);
	        $restriction = str_replace(" ", "", $restriction);
	        
	        $lat = '';
			$lnt = '';
			try {
				$q = urlencode($post['address'].','.$post['city'].",".$post['state'].",".$post['country']);
				$q = str_replace(" ", "+", $q);
				$geocodeURL = "http://maps.googleapis.com/maps/api/geocode/json?address=$q&sensor=false";
				$ch = curl_init($geocodeURL);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				$result = curl_exec($ch);
				$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
				curl_close($ch);
				if ($httpCode == 200) {
				  $geocode = json_decode($result);
				  $lat = $geocode->results[0]->geometry->location->lat;
				  $lnt = $geocode->results[0]->geometry->location->lng;
				  //echo 'Lat=' . $lat . ' || Lnt='. $lnt; exit;
				  $formatted_address = $geocode->results[0]->formatted_address;
				  $geo_status = $geocode->status;
				  $location_type = $geocode->results[0]->geometry->location_type;
				} else {
				 $geo_status = "HTTP_FAIL_$httpCode";
				}
			} catch (Exception $ex) {
			}
					
	        //Update product details 
	        $product = Mage::getModel('catalog/product')->load($post['id']);
	        $search_keywords = $post['address'] . ' ' . $post['city'] . ' ' . $post['state'] . ' ' . $post['country'];
	        if ($product->getId()) {
	        	try {
		            $product->setPropertyType($post['property_type'])
		                    ->setRoomType($post['room_type'])
		                    ->setName($post['name'])
		                    ->setDescription($post['description'])
		                    ->setKeywords($post['keywords'])
		                    ->setShortDescription($post['description'])
		                    ->setHouseRule($post['house_rule'])
		                    ->setAmenities($amenity)
		                    ->setRestrictions($restriction)
		                    ->setAccomodates($post['accomodates'])
		                    ->setRoomsAvailable($post['rooms_available'])
		                    ->setBedtype($post['bed_type'])
		                    ->setPrice($post['price'])
		                    ->setCleaningFee($post['cleaning_fee'])
		                    ->setExtraFees($post['extra_fees'])
		                    ->setDepositAmount($post['deposit_amount'])
		                    ->setAddress($post['address'])
		                    ->setCity($post['city'])
		                    ->setState($post['state'])
		                    ->setCountry($post['country'])
		                    ->setZipcode($post['zipcode'])
		                    ->setSearchKeywords($search_keywords)
		                    ->setPropertySize($post['property_size'])
		                    ->setHostContactNumber($post['host_contact_number'])
		                    ->setLatitude($lat)
		                    ->setLongitude($lnt)
		                    ->setMetaTitle($post['meta_title'])//Meta title
		                    ->setMetaKeyword($post['meta_keyword'])//Meta keywords
		                    ->setMetaDescription($post['meta_description'])//Meta description
		                    ->setCancellationPolicy($post['cancellation_policy'])
		                    ->setBanner($post['banner']); //banner
		            $product->save();
		            
		            $propertyLoactionObj = Mage::getModel('property/propertylocation')->load($product->getId(), 'product_id');
            		$propertyLocationData = array(
							'product_id' => $product->getId(),
							'latitude' => $lat,
							'longitude' => $lnt,
							'created_at' => now(),
							'update_at' => now(),
						);
            		$propertyLoaction = Mage::getModel('property/propertylocation');
            		$propertyLoaction->setData($propertyLocationData);
            		if(!empty($propertyLoactionObj)) {
            			$propertyLoaction->setId($propertyLoactionObj->getLocationId());
            		}
            		$propertyLoaction->save();

            		
		            Mage::getSingleton('core/session')->addSuccess($this->__('Your place updated successfully'));
		            $this->_redirect('*/*/show');
		            return;
	        	} catch (Exception $ex) {
	        		Mage::getSingleton('core/session')->addError($this->__('Your place not updated.'));
	            	$this->_redirect('*/*/edit', array('id' => $post["id"]));
	        	}
	        } else {
	            Mage::getSingleton('core/session')->addError($this->__('Item not found.'));
	            $this->_redirect('*/*/edit', array('id' => $post["id"]));
	            return;
	        }
        } else {
    		Mage::getSingleton('core/session')->addError($this->__('Your place not updated.'));
        	$this->_redirect('*/*/edit', array('id' => $post["id"]));
        }
    }
    
	public function contactAction() {
		$this->loadLayout();
		$this->renderLayout();
	}
	
	public function saveinboxAction() {
		if ($data = $this->getRequest()->getPost()) {
			$_product = Mage::getModel('catalog/product')->load($this->getRequest()->getParam('product_id'));
			
			$model = Mage::getModel('customerextend/cuspropmsg');		
			$model->setData($data);
			
			try {
				$model->setCreatedAt(now());
				$model->setUpdateAt(now());
				$model->setSenderId(Mage::getSingleton('customer/session')->getCustomer()->getId());
				$model->setMessageType(1);
				$model->save();
				// Database access                
				$dbhost = '54.186.179.68';
				$dbuser = 'amazonmagento';
				$dbpass = 'trAv4WjBrHn';
				$conn = mysql_connect($dbhost, $dbuser, $dbpass);
				if(! $conn ) {
				  //die('Could not connect: ' . mysql_error());
				  Mage::log('EC2 Database log: '. mysql_error());
				}
				$db_selected = mysql_select_db('amazonmagento', $conn);
				if (!$db_selected) {
					//die ("Can\'t use test_db : " . mysql_error());
					Mage::log('EC2 Database log: '. mysql_error());
				}

					$mailTemplate = Mage::getModel('core/email_template');
					
					$senderName = Mage::getSingleton('customer/session')->getCustomer()->getName();
					$senderEmail = Mage::getSingleton('customer/session')->getCustomer()->getEmail();
			        //**** contact mails to hodt starts ****/
			        //get guest details
			        $host = Mage::getModel('customer/customer')->load($model->getRecieverId());
			        $storeId = Mage::app()->getStore()->getStoreId();
			        //Mage::log('Check: ' . $host . ' -- ' . $storeId);
			        $product = Mage::getModel('catalog/product')->load($model->getProductId());
			            	        
			            ////**** mail function to guest starts ****/
				        // Retrieve corresponding email template id
			            $templateId = Mage::getStoreConfig('property_section/custom_email/hostcontact_template', $storeId);
						$callcofirm=false;
			            if($model->getCanCall()==1){
						 $callcofirm=true;
						}
			            $emailTemplate = $mailTemplate->loadDefault($templateId);
			            $emailTemplateVariables = array(
						            'host_name' => $host->getName(),
									'guest_name' => $senderName,
					                'room_name'	   => $product->getName(),
					                'checkin' => date('l, F d, Y', strtotime($model->getCheckin())),
					                'checkout'  => date('l, F d, Y', strtotime($model->getCheckout())),
									'message'   => $model->getMessage(),
									'contact_number' => $model->getContactNumber(),
									'timezone' => $model->getTimezone(),
									'can_call' => $callcofirm
				        );
				        $processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
			        
						$receiverName = $host->getName();
						$receiverEmail = $host->getEmail();
			            $mailSubject = "New Enquiry ";
						$mailBody = $processedTemplate;
						$status = 0;
						$createdAt = now();
						$updateAt = now();
		
						$sql = "INSERT INTO mails_data ".
						       "(sender_name, sender_email, receiver_name, receiver_email, mail_subject, mail_body, status, created_at, update_at) ".
						       "VALUES ".
						       "('$senderName','$senderEmail','$receiverName','$receiverEmail','$mailSubject','$mailBody','$status','$createdAt','$updateAt')";
						//echo $sql;
						$retval = mysql_query( $sql, $conn );
						if(! $retval ) {
							//die('Could not insert data: ' . mysql_error());
							Mage::log('EC2 Database log: '. mysql_error());
						}	
				Mage::getSingleton('core/session')->addSuccess($this->__('Your message sent successfully.'));

				$this->_redirectUrl($_product->getProductUrl());
				return;
            } catch (Exception $e) {
                Mage::getSingleton('core/session')->addError($e->getMessage());
                $this->_redirectUrl($_product->getProductUrl());
                return;
            }
		}
	}
	
	public function showAction() {
		$this->loadLayout();
		$this->renderLayout();
	}
	
	public function statusAction() {
        $status = $this->getRequest()->getParam('status');
        $productId = $this->getRequest()->getParam('productid');
        $product = Mage::getModel('catalog/product')->load($productId);
        $storeId = Mage::app()->getStore()->getId();

        $product->setStoreID($storeId)
                ->setStatus($status)//1 = Enabled 2 = Disabled
                ->save();
        
        echo 1;
    }
    
    public function blockcalendarAction() {
	    //owner permission
        $customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
        $customerObj = Mage::getSingleton('customer/customer')->load($customerId);
		$groupId = $customerObj->getGroupId();
        $entity_id = (int)$this->getRequest()->getParam('id');
        $collection = Mage::getModel('catalog/product')->load($entity_id);
        $customer_id = $collection->getUserId();
        
        if ($groupId != 4 && $customerId != $customer_id) {
            Mage::getSingleton('core/session')->addError($this->__("Access denied"));
            $this->_redirect('property/index/show/');
            return;
        }
        
        $this->loadLayout();
        $this->renderLayout();
    }
    
    public function calendarviewAction() {
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

        echo '<a class="pre_grid" href="javascript:void(0);" onclick="ajaxLoadCalendar(\'' . Mage::getBaseUrl() . 'property/index/calendarview/?date=' . $prev_month . '__' . $prev_year . '&productid=' . $productId . '\')" >previous</a>';
        echo '<div class="date_grid">' . date("F, Y", $date) . '</div>';
        echo '<a class="next_grid" href="javascript:void(0);" onclick="ajaxLoadCalendar(\'' . Mage::getBaseUrl() . 'property/index/calendarview/?date=' . $next_month . '__' . $next_year . '&productid=' . $productId . '\')" >next</a>';
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
                if (strtotime("$year-$x-$d") < strtotime(date("Y-n-j"))) {
                    echo "<td align='center' class='previous days '><font size = '2' face = 'tahoma'>$d</font></td>";
                } else {
                    if (in_array("$year-$x-$d", $selectedArray)) {
                        echo "<td class='selected days' align='center'><font size = '2' face = 'tahoma'>$d</font></td>";
                    } else {
                        $date = strtotime("$year/$x/$d");
                        $tdDate = 'tdId' . '_' . date("m/d/Y", $date);
                        if (in_array("$d", $blocked)) {
                            echo "<td id=" . $tdDate . " class='normal days " . $d . " ' align='center' style='background-color:#E07272; cursor:context-menu;'><font size = '2' face = 'tahoma'>$d</font></td>";
                        } else if (in_array("$d", $not_avail)) {
                            echo "<td id=" . $tdDate . " class='normal blockday days " . $d . " ' align='center'style='background-color:#F18200;color: black !important;' onclick='return removeBlockDate(this)' ><font size = '2' face = 'tahoma'>$d</font></td>";
                        } else if(array_key_exists($d,$_sp)){
                                echo "<td style='padding: 11px 23px;' id=" . $tdDate . " class='normal days " . $d . " ' align='center' onclick='return modifyPrice(this, ". $_sp[$d] .")' ><font size = '2' face = 'tahoma'>$d</font><br><div style='width: 25px;font-size: 1.0em;text-align: right;'>".
                                   Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol().$_store->roundPrice(Mage::helper('directory')->currencyConvert($_sp[$d],Mage::app()->getStore()->getBaseCurrencyCode(), Mage::app()->getStore()->getCurrentCurrencyCode()))."</div></td>";
                            }else{
                            echo "<td id=" . $tdDate . " class='normal availableDay days " . $d . " ' align='center' onclick='displayForm1(this)' ><font size = '2' face = 'tahoma'>$d</font></td>";
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
        echo '<input type="hidden" value="' . $x . '" id="currentMonth" />';
        echo '<input type="hidden" value="' . $year . '" id="currentYear" />';
    }
    
    
    public function checkavailAction() {
        $resource = Mage::getSingleton('core/resource');
		$write = $resource->getConnection('core_write');
     	$table = $resource->getTableName('property_availability');

     	$from = $_GET["from"];
        $to = $_GET["to"];
        $productid = $_GET["productid"];
        
        $start = strtotime($from);
        $end = strtotime($to);
        $daysBetween = ceil(abs($end - $start) / 86400);
        $condition = '';
        for($i=0; $i<=$daysBetween; $i++) {
			$day = date('d',strtotime($from . "+$i days"));
			$month = date('n',strtotime($from . "+$i days"));
			$year = date('Y',strtotime($from . "+$i days"));
			$condition .= "(booking_year = '". $year ."' AND booking_month = '". $month ."' AND block_date LIKE '%". $day ."%') OR ";
        }
        $conditionClause = rtrim($condition, ' OR ');
        $checkSql = "SELECT COUNT(available_id) as block FROM ". $table ." WHERE product_id = ". $productid ." AND (" . $conditionClause . ")";
        $block = $write->fetchOne($checkSql);
        //echo $checkSql;
        if($block != 0) {
        	echo 0;
        } else {
        	echo 1;
        }
    }
    
    public function updateavailAction() {
    	$checkin = $this->getRequest()->getParam('check_in');
    	$start = strtotime($checkin);
        $end = strtotime($this->getRequest()->getParam('check_out'));
        $productId = $this->getRequest()->getParam('productid');
        $bookAvail = $this->getRequest()->getParam('book_avail');
        $pricePer = $this->getRequest()->getParam('price_per');
        
        $daysBetween = ceil(abs($end - $start) / 86400);
        
        /***** add/update block/availability date starts *****/
        for($i=0; $i<=$daysBetween; $i++) {
			$day = date('d',strtotime($checkin . "+$i days"));
			$month = date('n',strtotime($checkin . "+$i days"));
			$year = date('Y',strtotime($checkin . "+$i days"));
			
			if($bookAvail == 1) {
				$data = array(
							'product_id' => $productId,
							'booking_type' => 1,
							'booking_year' => $year,
							'booking_month' => $month,
							'created_at' => now(),
							'update_at' => now(),
						);
				$checkCollection = Mage::getModel('property/propertyavailablity')->getCollection()
									->addFieldToFilter('product_id', $productId)
									->addFieldToFilter('booking_type', 1)
									->addFieldToFilter('booking_year', $year)
									->addFieldToFilter('booking_month', $month);
				if($checkCollection->count() > 0) {
					foreach ($checkCollection as $_check) {
						//Mage::log('Check: ' . $_check->getAvailableId());
						$availableId = $_check->getAvailableId();
						break;
					}
					
					$available = Mage::getModel('property/propertyavailablity')->load($availableId);
					$preBlockDate = $available->getBlockDate() . ',' . $day;
					//Mage::log('Block date: ' . $preBlockDate);
					$model = Mage::getModel('property/propertyavailablity');		
					$model->setData($data)
						->setId($availableId);
					$model->setBlockDate($preBlockDate);
					$model->save();
					
				} else {
					$available = Mage::getModel('property/propertyavailablity');
					$available->setData($data);
					$available->setBlockDate($day);
					$available->save();
				}
			} elseif($bookAvail == 3) {
				$data = array(
					'product_id' => $productId,
					'special_year' => $year,
					'special_month' => $month,
					'special_date' => $day,
					'special_price' => $pricePer,
					'created_at' => now(),
					'update_at' => now(),
				);
				
				$special = Mage::getModel('property/propertyspecial');
				$special->setData($data);
				$special->save();
			}
        }
        /***** add/update block/availability date ends *****/
        echo 1;
    }
    
    public function removeblockAction() {
    	$blockDate = $this->getRequest()->getParam('block_date');
        $productId = $this->getRequest()->getParam('productid');
        $dateArray = explode('/', $blockDate);
        
			$checkCollection = Mage::getModel('property/propertyavailablity')->getCollection()
						->addFieldToFilter('product_id', $productId)
						->addFieldToFilter('booking_type', 1)
						->addFieldToFilter('booking_year', $dateArray[2])
						->addFieldToFilter('booking_month', $dateArray[0]);
			if($checkCollection->count() > 0) {
				foreach ($checkCollection as $_check) {
					//Mage::log('Check: ' . $_check->getAvailableId());
					$availableId = $_check->getAvailableId();
					break;
				}
				
				$available = Mage::getModel('property/propertyavailablity')->load($availableId);
				$preBlockDate = trim(str_replace($dateArray[1], '', $available->getBlockDate()), ',');

				//Mage::log('Block date: ' . $preBlockDate);
				$model = Mage::getModel('property/propertyavailablity');		
				$model->setId($availableId);
				$model->setBlockDate($preBlockDate);
				$model->save();
				
			}
        echo 1;
    }
    
    public function updatespecialAction() {
    	$spdate = $this->getRequest()->getParam('sp_date');
        $productId = $this->getRequest()->getParam('productid');
        $pricePer = $this->getRequest()->getParam('price_per');
        $delinfo = $this->getRequest()->getParam('delinfo');

    	$day = date('d',strtotime($spdate));
		$month = date('n',strtotime($spdate));
		$year = date('Y',strtotime($spdate));
			
		$checkCollection = Mage::getModel('property/propertyspecial')->getCollection()
					->addFieldToFilter('product_id', $productId)
					->addFieldToFilter('special_year', $year)
					->addFieldToFilter('special_month', $month)
					->addFieldToFilter('special_date', $day);
		if($checkCollection->count() > 0) {
				foreach ($checkCollection as $_check) {
					//Mage::log('Check: ' . $_check->getAvailableId());
					$specialId = $_check->getSpecialId();
					break;
				}
				
				if($delinfo == 1) {
					$special = Mage::getModel('property/propertyspecial')->load($specialId);
					$special->delete()->save();
				} elseif($delinfo == 0) {
					$special = Mage::getModel('property/propertyspecial')->load($specialId);
					$special->setSpecialPrice($pricePer);
					$special->setUpdateAt(now());
					$special->save();
				}
		}
        echo 1;
    }
    
    public function galleryAction() {
        $this->loadLayout();
        $customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
        $this->getLayout()->getBlock('head')->setTitle($this->__('Update gallery'));

        //owner permission 
        $entity_id = (int)$this->getRequest()->getParam('id');
        $collection = Mage::getModel('catalog/product')->load($entity_id);
        $customer_id = $collection->getUserId();

        if ($customerId != $customer_id) {
            Mage::getSingleton('core/session')->addError($this->__("Access denied"));
            $this->_redirect('*/*/show/');
            return;
        }
        $this->renderLayout();
    }
    
    public function imageuploadAction() {
        $entity_id = $this->getRequest()->getParam('id');  //property id
        Mage::getModel('property/property')->imageupload($_FILES, $entity_id);
		return $this->_redirect('*/*/gallery', array('id' => $entity_id));
    }
    public function albumupdateAction() {
        $post = $this->getRequest()->getPost();
        $entityId = $this->getRequest()->getParam('entity_id');
        $imageCollection = $this->getRequest()->getParam('imageCollection');
        if ($this->getRequest()->getParam('remove') != "0") {         
            for ($i = 0; $i < count($imageCollection); $i++) {
                if ($imageCollection[$i]) {
                    Mage::getModel('property/property')->removeImage($imageCollection[$i], $entityId);
                }
            }          
        }
        Mage::getModel('property/property')->albumupdate($post);
        if(count($imageCollection)) {
            Mage::getSingleton('core/session')->addSuccess($this->__('Image Removed successfully'));
            return $this->_redirect('*/*/gallery', array('id'=>$entityId));
        }   

        return $this->_redirect('*/*/show');
    }
    
    public function reviewAction() {
        $this->loadLayout();
        $customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
        $this->getLayout()->getBlock('head')->setTitle($this->__('Review'));

        //owner permission 
        $entity_id = (int)$this->getRequest()->getParam('id');
        $collection = Mage::getModel('catalog/product')->load($entity_id);
        $customer_id = $collection->getUserId();

        if ($customerId != $customer_id) {
            Mage::getSingleton('core/session')->addError($this->__("Access denied"));
            $this->_redirect('*/*/show/');
            return;
        }
        $this->renderLayout();
    }
    
    public function reviewstatusAction() {
        $status = $this->getRequest()->getParam('status');
        $reviewid = Mage::app()->getRequest()->getParam('reviewid');
        $status = Mage::getModel('property/property')->review($status, $reviewid);
        if ($status == "2") {
            echo "Available";
        } else {
            echo "NotAvailable";
        }
    }
    
     public function recommendedpriceAction() {
     	$rtype = $this->getRequest()->getParam('rtype');
     	if($rtype == 12) { $dtype = 'Shared room'; }
     	elseif($rtype == 11) { $dtype = 'Private room'; }
     	elseif($rtype == 10) { $dtype = 'Entire home/apt'; }
     	$rcity = $this->getRequest()->getParam('rcity');
		$rstate = $this->getRequest()->getParam('rstate');
		$rstateCode = $this->sgetStateCode($rstate);	
     	$zip = $this->getRequest()->getParam('zip');
     	$currencySymbol = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getBaseCurrencyCode())->getSymbol();
     	$resource = Mage::getSingleton('core/resource');
    	$readConnection = $resource->getConnection('core_read');
    	$query = "SELECT AvgDailyPrice FROM RcAvgRoomPrice where zipcode = '" . $zip . "' AND roomtype LIKE '%". $dtype ."%'";
    	//echo $query . '<br>';
    	$result = $readConnection->fetchOne($query);
    	if(!empty($result)) {
    		$resultPrice = $result;
    	} else {
    		$query = "SELECT AvgDailyPrice FROM RcAvgRoomPrice where city LIKE '%" . $rcity . "%' AND state = '". $rstateCode ."' AND roomtype LIKE '%". $dtype ."%'";    		
    		$result = $readConnection->fetchOne($query);
    		if(!empty($result)) {
	    		$resultPrice = $result;
	    	}
    	}
    	$returnText = '';
    	if($resultPrice) {
    		$returnText = 'Recommended price: ' . $currencySymbol . number_format($resultPrice, 2);
    	} else {
     		$propertyCollection = Mage::getModel('property/property')->getPropertyCollection()
							->addFieldToFilter('zipcode', array('eq' => trim($zip)));
    		if($propertyCollection->count()) {
				$price = 0.00;
				$count = $propertyCollection->count();
				foreach ($propertyCollection as $_property) {
					$property = Mage::getModel('catalog/product')->load($_property->getId());
					$price += $property->getPrice();
				}
				$returnText = 'Recommended price: ' . $currencySymbol . number_format((floor($price/$count)), 2);
			}
    	}
		echo $returnText;
     }

     public function sgetStateCode($statename) {
		$countryCode = 'US';
	    $regionCollection = Mage::getModel('directory/region_api')->items($countryCode);
	    
	    if(count($regionCollection) > 0) {
	        foreach($regionCollection as $region) {
	        	if($region['name'] == $statename) {
	        		$regionCode = $region['code'];
	        		break;
	        	}
	        }
	    }
		return $regionCode;
     }
     
     public function statenameAction() {
     	$statecode = $this->getRequest()->getParam('statecode');
		$countryCode = 'US';
	    $regionCollection = Mage::getModel('directory/region_api')->items($countryCode);
	    
	    $regionSelect = '';
	    if(count($regionCollection) > 0) {
			$regionSelect .= '<select name="state" id="state" class="validate-select required-entry" >';
			    $regionSelect .= '<option value="">Please select region, state or province</option>';
			        foreach($regionCollection as $region) {
			        	if($region['code'] == $statecode) {
			        		$selected = 'selected="selected"';
			        	} else {
			        		$selected = '';
			        	}
			            $regionSelect .= '<option '. $selected .' value="' . $region['name'] .'" >' . $region['name'] .'</option>';
			        }
			$regionSelect .= '</select>';
	    }
		echo $regionSelect;
     }
     
    public function averageAction() {
	  $this->loadLayout();   
	  $this->getLayout()->getBlock("head")->setTitle($this->__("Get Average Price"));
      $this->renderLayout();
    }

}