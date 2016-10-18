<?php
class Si_Listroom_IndexController extends Mage_Core_Controller_Front_Action{
	public function preDispatch() {
	    parent::preDispatch();
	    if (!Mage::getSingleton('customer/session')->authenticate($this)) {
	    	Mage::getSingleton('core/session')->addNotice($this->__('Login to access this section.'));
	        $this->setFlag('', 'no-dispatch', true);
	    }
    }
    
    public function formAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setTitle($this->__('List your Requirement'));
        $this->renderLayout();
    }

	public function postAction() {
		if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
			$this->_redirect('*/*/form');
			return;
		}
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        $CusId = $customer->getId();

        $post = $this->getRequest()->getPost();

        if($post) {
	        $amenity = implode(",", $post['amenity']);
	        //$amenity = str_replace(" ", "", $amenity);
        }

        if ($post) {
        	$pos = strpos($_SERVER['HTTP_REFERER'], 'edit');
        	if($pos === false) {
	        	$countryModel = Mage::getModel('directory/country')->loadByCode($post['country']);
				$post['country'] = $countryModel->getName();
        	}
        	try {
				$room = Mage::getModel('listroom/listroom');
				$data = $post;
				$lat = '';
				$lnt = '';
				try {
					$q = urlencode($data['locality'].','.$data['city'].",".$data['state'].",".$data['country']);
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
				$data['user_id'] = $CusId;
				$data['amenity'] = $amenity;
				$data['status'] = 1;
				$data['room_lat'] = $lat;
				$data['room_lnt'] = $lnt;
				$data['created_at'] = now();
				$data['update_at'] = now();
				
				$room->setData($data)->setId($this->getRequest()->getParam('room_id'));
				$room->save();
				if($this->getRequest()->getParam('room_id')) {
		            Mage::getSingleton('core/session')->addSuccess($this->__('Your requirement updated successfully'));
				} else {
					Mage::getSingleton('core/session')->addSuccess($this->__('Your requirement submitted successfully'));
				}
	            $this->_redirect('property/index/show', array('trigger' => 'r'));
	            return;
            } catch (Exception $ex) {
                Mage::getSingleton('core/session')->addError($ex->getMessage());
                $this->_redirect('*/*/form');
				return;
            }
        } else {
            Mage::getSingleton('core/session')->addError($this->__('Item not found.'));
            $this->_redirect('*/*/form');
			return;
        }
    }
    
    public function editAction() {
        $this->loadLayout();
        $this->_initLayoutMessages('catalog/session');
        $customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
        
        if ($customerId) {
        	//owner permission 
	        $entity_id = (int)$this->getRequest()->getParam('id');
	        $room = Mage::getModel('listroom/listroom')->load($entity_id);
	        $Customer_id = $room->getUserId();
	
	        if ($customerId != $Customer_id) {
	            Mage::getSingleton('core/session')->addError($this->__("Access denied"));
	            $this->_redirect('*/property/show', array('trigger' => 'r'));
	            return;
	        }
        
            $this->getLayout()->getBlock('head')->setTitle($this->__('Edit your Requirement'));
            $this->renderLayout();
        } else {
            Mage::getSingleton('core/session')->addError($this->__('You are not currently logged in'));
            $this->_redirectUrl(Mage::helper('customer')->getLoginUrl());
        }
    }
    
    public function deleteAction() {
        //owner permission 
        $entity_id = (int)$this->getRequest()->getParam('id');
        $customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
        $collection = Mage::getModel('listroom/listroom')->load($entity_id);
        $Customer_id = $collection->getUserId();

        if ($customerId != $Customer_id) {
            Mage::getSingleton('core/session')->addError($this->__("Access denied"));
            $this->_redirect('*/property/show', array('trigger' => 'r'));
            return;
        }

		try {
			$model = Mage::getModel('listroom/listroom');
			$model->setId($entity_id)
				->delete();
			Mage::getSingleton('core/session')->addSuccess($this->__("Requirement Deleted Successfully"));
			$this->_redirect('*/property/show', array('trigger' => 'r'));
        	return;
		} catch (Exception $e) {
      		Mage::getSingleton('core/session')->addError('Item cannot be deleted.');
      		$this->_redirect('*/property/show', array('trigger' => 'r'));
        	return;
        }
    }
    
	public function contactAction() {
		$this->loadLayout();
		$this->renderLayout();
	}
	
	public function saveinboxAction() {
		if ($data = $this->getRequest()->getPost()) {
			$room = Mage::getModel('listroom/listroom')->load($this->getRequest()->getParam('room_id'));
			
			$model = Mage::getModel('customerextend/cuspropmsg');		
			$model->setData($data);
			
			try {
				$model->setCreatedAt(now());
				$model->setUpdateAt(now());
				$model->setSenderId(Mage::getSingleton('customer/session')->getCustomer()->getId());
				$model->setProductId($this->getRequest()->getParam('room_id'));
				$model->setMessageType(3);
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
			        $guest = Mage::getModel('customer/customer')->load($model->getRecieverId());
			        $storeId = Mage::app()->getStore()->getStoreId();
			        //Mage::log('Check: ' . $host . ' -- ' . $storeId);
			        $product = Mage::getModel('listroom/listroom')->load($model->getProductId());
			            	        
			            ////**** mail function to guest starts ****/
				        // Retrieve corresponding email template id
			            $templateId = Mage::getStoreConfig('property_section/custom_email/roomguestcontact_template', $storeId);
						$callcofirm=false;
			            if($model->getCanCall()==1){
						 $callcofirm=true;
						}
			            $emailTemplate = $mailTemplate->loadDefault($templateId);
			            $emailTemplateVariables = array(
						            'host_name' => $senderName,
									'guest_name' => $guest->getName(),
					                'room_name'	   => $product->getTitle(),
					                'checkin' => date('l, F d, Y', strtotime($model->getCheckin())),
					                'checkout'  => date('l, F d, Y', strtotime($model->getCheckout())),
									'message'   => $model->getMessage(),
									'contact_number' => $model->getContactNumber(),
									'timezone' => $model->getTimezone(),
									'can_call' => $callcofirm
				        );
				        $processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
			        
						$receiverName = $guest->getName();
						$receiverEmail = $guest->getEmail();
			            $mailSubject = "New Enquiry for Requirement";
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

				$this->_redirect('listroom/search/view', array('id' => $this->getRequest()->getParam('room_id')));
				return;
            } catch (Exception $e) {
                Mage::getSingleton('core/session')->addError($e->getMessage());
                $this->_redirect('listroom/search/view', array('id' => $this->getRequest()->getParam('room_id')));
                return;
            }
		}
	}
}