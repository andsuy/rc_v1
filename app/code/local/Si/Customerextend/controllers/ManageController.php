<?php
class Si_Customerextend_ManageController extends Mage_Core_Controller_Front_Action{
	public function preDispatch() {
	    parent::preDispatch();
	    if (!Mage::getSingleton('customer/session')->authenticate($this)) {
	    	Mage::getSingleton('core/session')->addNotice($this->__('Login to access this section.'));
	        $this->setFlag('', 'no-dispatch', true);
	    }
    }
    
    public function updateAction() {
    	if ($data = $this->getRequest()->getPost()) {
    		$customer_id = Mage::getSingleton('customer/session')->getId();
        	$customerProfile = Mage::getModel('customerextend/customerinfo')->load($customer_id, 'customer_id');
        	//echo '<pre>'; print_r($customerProfile); echo '</pre>'; exit;
        
    		$model = Mage::getModel('customerextend/customerinfo');		
			$model->setData($data)
				->setId($customerProfile->getPcId());
			
			try {
				if ($model->getCreatedAt == NULL || $model->getUpdateAt() == NULL) {
					$model->setCreatedAt(now())
						->setUpdateAt(now());
				} else {
					$model->setUpdateAt(now());
				}	
				$model->setCustomerId($customer_id);
				$model->save();
				Mage::getSingleton('core/session')->addSuccess($this->__('Profile was successfully saved.'));

				$this->_redirect('customer/account/edit');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('core/session')->addError($e->getMessage());
                $this->_redirect('customer/account/edit');
                return;
            }
    	}
    	Mage::getSingleton('core/session')->addError($this->__('Unable to find item to save.'));
        $this->_redirect('customer/account/edit');
    }
    
    public function updatepictureAction() {
    	if(isset($_FILES['image_name']['name']) && $_FILES['image_name']['name'] != '') {
    		$data = array();
    		$customer_id = Mage::getSingleton('customer/session')->getId();
        	$customerProfile = Mage::getModel('customerextend/customerinfo')->load($customer_id, 'customer_id');
        	//echo '<pre>'; print_r($customerProfile); echo '</pre>'; exit;
        	
        	if(isset($_FILES['image_name']['name']) && $_FILES['image_name']['name'] != '') {
				try {	
					/* Starting upload */	
					$uploader = new Varien_File_Uploader('image_name');
					
					// Any extention would work
	           		$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
					$uploader->setAllowRenameFiles(true);
					
					// Set the file upload mode 
					// false -> get the file directly in the specified folder
					// true -> get the file in the product like folders 
					//	(file.jpg will go in something like /media/f/i/file.jpg)
					$uploader->setFilesDispersion(false);
							
					// We set media as the upload dir
					$path = Mage::getBaseDir('media') . DS . 'catalog' . DS . 'customer' . DS;
					$image = $uploader->save($path, $_FILES['image_name']['name'] );
					//echo '<pre>'; print_r($image); echo '</pre>'; exit;
				} catch (Exception $e) {
		      		Mage::getSingleton('core/session')->addError($e->getMessage());
	                $this->_redirect('customer/account');
	                return;
		        }
	        
		        //this way the name is saved in DB
	  			$data['image_name'] = $image['name'];
			}
			
    		$model = Mage::getModel('customerextend/customerinfo');		
			$model->setData($data)
				->setId($customerProfile->getPcId());
			
			try {
				if ($model->getCreatedAt == NULL || $model->getUpdateAt() == NULL) {
					$model->setCreatedAt(now())
						->setUpdateAt(now());
				} else {
					$model->setUpdateAt(now());
				}	
				$model->setCustomerId($customer_id);
				$model->save();
				Mage::getSingleton('core/session')->addSuccess($this->__('Profile was successfully saved.'));

				$this->_redirect('customer/account');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('core/session')->addError($e->getMessage());
                $this->_redirect('customer/account');
                return;
            }
    	}
    	Mage::getSingleton('core/session')->addError($this->__('Unable to find item to save.'));
        $this->_redirect('customer/account');
    }
    
    public function deletepictureAction() {
    	if($this->getRequest()->getParam('delete')) {
    		$customer_id = Mage::getSingleton('customer/session')->getId();
        	$customerProfile = Mage::getModel('customerextend/customerinfo')->load($customer_id, 'customer_id');
        	$imagePath = Mage::getBaseDir("media") . "catalog/customer/" . $customerProfile->getImageName();
        	$reszImagePath = Mage::getBaseDir("media") . "catalog/customer/resz_120_" . $customerProfile->getImageName();
        	$resz50ImagePath = Mage::getBaseDir("media") . "catalog/customer/resz_50_" . $customerProfile->getImageName();
        	unlink($imagePath);
        	unlink($reszImagePath);
        	unlink($resz50ImagePath);
        	$data = array(
        			'image_name' => '',
        			'update_at'	 => now()
        			);
        	$model = Mage::getModel('customerextend/customerinfo');		
			$model->setData($data)
				->setId($customerProfile->getPcId());
			$model->save();
    	} else {
    		Mage::getSingleton('core/session')->addError($this->__('Unable to find item.'));
        	$this->_redirect('customer/account');
    	}
    }
    
    public function inboxAction() {
    	$this->loadLayout();
        $this->renderLayout();
    }
    public function pmbAction() {
       $this->loadLayout();
       $this->renderLayout();
    }
    
	public function replypmbAction() {
       $receiver_id = $this->getRequest()->getParam('rid');
       $message = $this->getRequest()->getParam('message');

        $customer = Mage::getSingleton('customer/session')->getCustomer();
        $cusId = $customer->getId();
        
        $data = array(
        			'sender_id' => $cusId,
        			'reciever_id' => $receiver_id,
        			'message_type' => 2,
        			'message' => $message,
        			'created_at' => now(),
        			'update_at' => now()
        		);
    	$model = Mage::getModel('customerextend/cuspropmsg');		
		$model->setData($data);
		
		try {
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
			        $reciver = Mage::getModel('customer/customer')->load($model->getRecieverId());
			        $storeId = Mage::app()->getStore()->getStoreId();
			        //Mage::log('Check: ' . $host . ' -- ' . $storeId);
			            	        
			            ////**** mail function to guest starts ****/
				        // Retrieve corresponding email template id
			            $templateId = Mage::getStoreConfig('property_section/custom_email/guestcontact_template', $storeId);
						$callcofirm=false;
			            if($model->getCanCall()==1){
						 $callcofirm=true;
						}
			            $emailTemplate = $mailTemplate->loadDefault($templateId);
			            $emailTemplateVariables = array(
						            'reciever_name' => $reciver->getName(),
									'sender_name' => $senderName,
									'message'   => $message,
				        );
				        $processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
			        
						$receiverName = $reciver->getName();
						$receiverEmail = $reciver->getEmail();
			            $mailSubject = "New massage";
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
			echo 1;
        } catch (Exception $e) {
            echo 0;
        }
	}
}