<?php
class Si_Booking_IndexController extends Mage_Core_Controller_Front_Action{
	public function preDispatch() {
	    parent::preDispatch();
	    if (!Mage::getSingleton('customer/session')->authenticate($this)) {
	    	Mage::getSingleton('core/session')->addNotice($this->__('Login to access this section.'));
	        $this->setFlag('', 'no-dispatch', true);
	    }
    }
    
    public function manageAction() {
		$this->loadLayout();
		$this->renderLayout();
	}
    public function listAction() {
		$this->loadLayout();
		$this->renderLayout();
	}
    public function propertyAction() {
		$this->loadLayout();
		$this->renderLayout();
	}
    public function viewAction() {
		$this->loadLayout();
		if(!$this->getRequest()->getParam('id')) {
			Mage::getSingleton('core/session')->addError($this->__('Item not found.'));
            $this->_redirect('*/*/manage');
            return;
		}
		//owner permission 
        $customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
        $id = (int)$this->getRequest()->getParam('id');
        $item = Mage::getModel('booking/booking')->load($id);
        $customer_id = $item->getHostId();
        if ($customerId != $customer_id) {
            Mage::getSingleton('core/session')->addError($this->__("Access denied"));
            $this->_redirect('*/*/manage');
            return;
        }
        
		$this->renderLayout();
	}
    public function guestviewAction() {
		$this->loadLayout();
		if(!$this->getRequest()->getParam('id')) {
			Mage::getSingleton('core/session')->addError($this->__('Item not found.'));
            $this->_redirect('*/*/list');
            return;
		}
		//owner permission 
        $customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
        $id = (int)$this->getRequest()->getParam('id');
        $item = Mage::getModel('booking/booking')->load($id);
        $customer_id = $item->getGuestId();
        if ($customerId != $customer_id) {
            Mage::getSingleton('core/session')->addError($this->__("Access denied"));
            $this->_redirect('*/*/list');
            return;
        }
        
		$this->renderLayout();
	}
	
    public function secureAction() {
		if(!$this->getRequest()->getParam('id')) {
			Mage::getSingleton('core/session')->addError($this->__('Item not found.'));
            $this->_redirect('*/*/manage');
            return;
		}
		//owner permission 
        $customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
        $id = (int)$this->getRequest()->getParam('id');
        $item = Mage::getModel('booking/booking')->load($id);
        $customer_id = $item->getHostId();
        if ($customerId != $customer_id) {
            Mage::getSingleton('core/session')->addError($this->__("Access denied"));
            $this->_redirect('*/*/manage');
            return;
        }
        if($item->getKeyStatus() == 3 || $item->getHostTryCount() >= 3) {
			Mage::getSingleton('core/session')->addError($this->__('The key is already blocked.'));
            $this->_redirect('*/*/view', array('id' => $id));
            return;        	
        } elseif($item->getKeyStatus() == 2) {
			Mage::getSingleton('core/session')->addError($this->__('The key is already confirmed.'));
            $this->_redirect('*/*/view', array('id' => $id));
            return;        
        }
        
        $secureCode = $this->getRequest()->getParam('secure_key');
        $data['host_try_count'] = ($item->getHostTryCount() + 1);
        if($secureCode == $item->getSecureKey()) {
        	$data['key_status'] = 2;
        	Mage::getSingleton('core/session')->addSuccess($this->__('The key is successfully confirmed.'));
        } else {
        	if(($item->getHostTryCount() + 1) >= 3) {
        		$data['key_status'] = 3;
        	}
			Mage::getSingleton('core/session')->addError($this->__("The key mismatched. You have %d tries left.", (3-($item->getHostTryCount() + 1))));
        }
        $model = Mage::getModel('booking/booking');
        $model->setData($data)->setId($item->getId());
        $model->save();
        $this->_redirect('*/*/view', array('id' => $id));
        return;
	}
	
	public function confirmAction() {
		if(!$this->getRequest()->getParam('id')) {
			Mage::getSingleton('core/session')->addError($this->__('Item not found.'));
            $this->_redirect('*/*/manage');
            return;
		}
		//owner permission 
        $customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
        $id = (int)$this->getRequest()->getParam('id');
        $item = Mage::getModel('booking/booking')->load($id);
        $customer_id = $item->getHostId();
        if ($customerId != $customer_id) {
            Mage::getSingleton('core/session')->addError($this->__("Access denied"));
            $this->_redirect('*/*/manage');
            return;
        }
        
        if($item->getBookingStatus() == 1) {
        	Mage::getSingleton('core/session')->addError($this->__('The booking was confirmed already.'));
            $this->_redirect('*/*/manage');
            return;
        } elseif($item->getBookingStatus() == 2) {
         	Mage::getSingleton('core/session')->addError($this->__('The booking was cancelled already. It cannot be confirmed now.'));
            $this->_redirect('*/*/manage');
            return;
        }
        
        $order = Mage::getModel('sales/order')->load($item->getOrderId());
        if (!$order->getId()) {
            $this->_getSession()->addError($this->__('This booking no longer exists.'));
            $this->_redirect('*/*/manage');
            return;
        }
        if($order) {
        	// create 5 digits code
        	//$secureCode = Mage::helper('booking')->getSecureCode();
        	
        	//$item->setSecureKey($secureCode);
        	//$item->setKeyStatus(1);
	        $item->setBookingStatus(1);
	        $item->getUpdatedAt(now());
	        $item->save();
	        
	        //**** mails to guest/host starts ****/
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

	        //get guest details
	        $guest = Mage::getModel('customer/customer')->load($item->getGuestId());
	        $host = Mage::getModel('customer/customer')->load($item->getHostId());
	        $storeId = Mage::app()->getStore()->getStoreId();
	        //Mage::log('Check: ' . $host . ' -- ' . $storeId);
	        $product = Mage::getModel('catalog/product')->load($item->getProductId());
	        
	        $senderName = Mage::getStoreConfig('trans_email/ident_sales/name', $storeId);
			$senderEmail = Mage::getStoreConfig('trans_email/ident_sales/email', $storeId);
					
	        $mailTemplate = Mage::getModel('core/email_template');
	        
	        //if (Mage::helper('sales')->canSendNewOrderEmail($storeId)) {
	            //Mage::log('test: ');
		        // Get the destination email addresses to send copies to
		        //$copyTo = Mage::helper('booking')->_getEmails('sales_email/order/copy_to');
		        //$copyMethod = Mage::getStoreConfig('sales_email/order/copy_method', $storeId);
	
	            $customerName = $order->getCustomerName();
	            	        
	            ////**** mail function to guest starts ****/
		        // Retrieve corresponding email template id
	            $templateId = Mage::getStoreConfig('property_section/custom_email/guestbookingconfirm_template', $storeId);
		            $emailTemplate = $mailTemplate->loadDefault($templateId);
		            $emailTemplateVariables = array(
			                'order'        => $order,
			                'room_name'	   => $product->getName(),
			                'property_address' => nl2br($product->getAddress()) . '<br>' . $product->getCountry(),
			            );
			        $processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
			        
				$receiverName = $guest->getName();
				$receiverEmail = $guest->getEmail();
				$mailSubject = "Confirmation of Booking # " . $order->getIncrementId();
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

				  	
				/*try {
			        $mailer = Mage::getModel('core/email_template_mailer');
			        $emailInfo = Mage::getModel('core/email_info');
			        $emailInfo->addTo($guest->getEmail(), $guest->getName());
			        if ($copyTo && $copyMethod == 'bcc') {
			            // Add bcc to customer email
			            foreach ($copyTo as $email) {
			                $emailInfo->addBcc($email);
			            }
			        }
			        $mailer->addEmailInfo($emailInfo);
			
			        // Email copies are sent as separated emails if their copy method is 'copy'
			        if ($copyTo && $copyMethod == 'copy') {
			            foreach ($copyTo as $email) {
			                $emailInfo = Mage::getModel('core/email_info');
			                $emailInfo->addTo($email);
			                $mailer->addEmailInfo($emailInfo);
			            }
			        }
			
			        // Set all required params and send emails
			        $mailer->setSender(Mage::getStoreConfig('sales_email/order/identity', $storeId));
			        $mailer->setStoreId($storeId);
			        $mailer->setTemplateId($templateId);
			        $mailer->setTemplateParams(array(
			                'order'        => $order,
			                'room_name'	   => $product->getName(),
			                //'secure_code'  => $secureCode
			            )
			        );
			        $mailer->send();
	    	    } catch (Exception $e) {
				    throw $e;
				}*/
				
				////**** mail function to guest ends ****/
				
				////**** mail function to host starts ****/
		        // Retrieve corresponding email template id
	            $templateId = Mage::getStoreConfig('property_section/custom_email/hostbookingconfirm_template', $storeId);
		            $emailTemplate = $mailTemplate->loadDefault($templateId);
		            $emailTemplateVariables = array(
			                'order'        => $order,
			                'host_name'    => $host->getFirstname(),
			                'room_name'	   => $product->getName()
			            );
			        $processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
			        
				$receiverName = $host->getName();
				$receiverEmail = $host->getEmail();
				$mailSubject = "Confirmation of Booking # " . $order->getIncrementId();
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
					
				/*try {
			        $mailer = Mage::getModel('core/email_template_mailer');
			        $emailInfo = Mage::getModel('core/email_info');
			        $emailInfo->addTo($host->getEmail(), $host->getName());
			        if ($copyTo && $copyMethod == 'bcc') {
			            // Add bcc to customer email
			            foreach ($copyTo as $email) {
			                $emailInfo->addBcc($email);
			            }
			        }
			        $mailer->addEmailInfo($emailInfo);
			
			        // Email copies are sent as separated emails if their copy method is 'copy'
			        if ($copyTo && $copyMethod == 'copy') {
			            foreach ($copyTo as $email) {
			                $emailInfo = Mage::getModel('core/email_info');
			                $emailInfo->addTo($email);
			                $mailer->addEmailInfo($emailInfo);
			            }
			        }
			
			        // Set all required params and send emails
			        $mailer->setSender(Mage::getStoreConfig('sales_email/order/identity', $storeId));
			        $mailer->setStoreId($storeId);
			        $mailer->setTemplateId($templateId);
			        $mailer->setTemplateParams(array(
			                'order'        => $order,
			                'host_name'    => $host->getFirstname(),
			                'room_name'	   => $product->getName()
			            )
			        );
			        $mailer->send();
	    	    } catch (Exception $e) {
				    throw $e;
				}*/
				////**** mail function to host ends ****/
				
	        //}
			//**** mails to guest/host ends ****/

			Mage::getSingleton('core/session')->addSuccess($this->__('The booking has been confirmed successfully. The guest has been updated by mail.'));
	        $this->_redirect('*/*/manage');
	        return;
        }     
	}
	
	public function guestcancelAction() {
		if(!$this->getRequest()->getParam('id')) {
			Mage::getSingleton('core/session')->addError($this->__('Item not found.'));
            $this->_redirect('*/*/list');
            return;
		}
		//owner permission 
        $customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
        $id = (int)$this->getRequest()->getParam('id');
        $item = Mage::getModel('booking/booking')->load($id);
        $customer_id = $item->getGuestId();
        if ($customerId != $customer_id) {
            Mage::getSingleton('core/session')->addError($this->__("Access denied"));
            $this->_redirect('*/*/list');
            return;
        }
        if($item->getBookingStatus() == 2) {
        	Mage::getSingleton('core/session')->addError($this->__('The booking was already cancelled by Host.'));
            $this->_redirect('*/*/list');
            return;
        } elseif($item->getBookingStatus() == 4) {
         	Mage::getSingleton('core/session')->addError($this->__('The booking was already cancelled by you.'));
            $this->_redirect('*/*/list');
            return;
        }
        
        $product = Mage::getModel('catalog/product')->load($item->getProductId());
        $order = Mage::getModel('sales/order')->load($item->getOrderId());
        $baseSubtotal = $order->getBaseSubtotal();
        $today = date('Y-m-d');
		$cancelFlag = 0;
        //echo date('Y-m-d', strtotime('-5 days', strtotime($item->getCheckin())));
				$storeId = Mage::app()->getStore()->getStoreId();
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
			$guest = Mage::getModel('customer/customer')->load($item->getGuestId());
	        $host = Mage::getModel('customer/customer')->load($item->getHostId());
			
        	$senderName = Mage::getStoreConfig('trans_email/ident_sales/name', $storeId);
			$senderEmail = Mage::getStoreConfig('trans_email/ident_sales/email', $storeId);
			
        $cancellationPolicy = $product->getCancellationPolicy();
        if($cancellationPolicy == 25) {
        	// Moderate: Full refund 5 days prior to arrival, except fees
			$endDate = date('Y-m-d', strtotime('-2 days', strtotime($item->getCheckin())));
	        if(strtotime($today) < strtotime($endDate)) {
        		$data = array();
    		} else {
        		$data = array(
        					'comment_text' => '',
        					'adjustment_negative' => $baseSubtotal
        				);
    		}
		} elseif($cancellationPolicy == 26) {
        	// Flexible: Full refund 1 day prior to arrival, except fees
			$endDate = date('Y-m-d', strtotime('-1 days', strtotime($item->getCheckin())));
	        if(strtotime($today) < strtotime($endDate)) {
        		$data = array();
    		} else {
        		$data = array(
        					'comment_text' => '',
        					'adjustment_negative' => $baseSubtotal
        				);
    		}
    	} elseif($cancellationPolicy == 27) {
            // Strict: 50% refund up until 1 week prior to arrival, except fees
			$endDate = date('Y-m-d', strtotime('-1 week', strtotime($item->getCheckin())));
	        $deduction = number_format(($baseSubtotal / 2), 2);
    		if(strtotime($today) < strtotime($endDate)) {
        		$data = array(
        					'comment_text' => '',
        					'adjustment_negative' => $deduction
        				);	            		
    		} else {
        		$data = array(
        					'comment_text' => '',
        					'adjustment_negative' => $baseSubtotal
        				);
    		}
    	}
    	
			if($order->canCreditmemo()) {
				try {
            		if ($order->hasInvoices()) {
					    foreach ($order->getInvoiceCollection() as $inv) {
					        $invoiceId = $inv->getId();
						   break;
					    }
					    $invoice = Mage::getModel('sales/order_invoice')->load($invoiceId);
            		}
            		
		            $service = Mage::getModel('sales/service_order', $order);
		            if ($invoice) {
		                $creditmemo = $service->prepareInvoiceCreditmemo($invoice, $data);
		            } else {
		                $creditmemo = $service->prepareCreditmemo($data);
		            }
		            $creditmemo->setState(2);
		            
			        $transactionSave = Mage::getModel('core/resource_transaction')
			            ->addObject($creditmemo)
			            ->addObject($creditmemo->getOrder());
			        if ($creditmemo->getInvoice()) {
			            $transactionSave->addObject($creditmemo->getInvoice());
			        }
			        $transactionSave->save();
			        $order->setStatus('refunded')->save();
			        //$creditmemo->sendEmail(1, '');
			        
		// credit memo magento email
		
        // Start store emulation process
        $appEmulation = Mage::getSingleton('core/app_emulation');
        $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($storeId);

        try {
            // Retrieve specified view block from appropriate design package (depends on emulated store)
            $paymentBlock = Mage::helper('payment')->getInfoBlock($order->getPayment())
                ->setIsSecureMode(true);
            $paymentBlock->getMethod()->setStore($storeId);
            $paymentBlockHtml = $paymentBlock->toHtml();
        } catch (Exception $exception) {
            // Stop store emulation process
            $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);
            throw $exception;
        }

        // Stop store emulation process
        $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);

        // Retrieve corresponding email template id and customer name
        if ($order->getCustomerIsGuest()) {
            $templateId = Mage::getStoreConfig('sales_email/creditmemo/guest_template', $storeId);
            $customerName = $order->getBillingAddress()->getName();
        } else {
            $templateId = Mage::getStoreConfig('sales_email/creditmemo/template', $storeId);
            $customerName = $order->getCustomerName();
        }

	            $emailTemplate = $mailTemplate->load($templateId);
	            $emailTemplateVariables = array(
	                'order'        => $order,
	                'creditmemo'   => $creditmemo,
	                //'comment'      => $comment,
	                'billing'      => $order->getBillingAddress(),
	                'payment_html' => $paymentBlockHtml
		            );
		        $processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
			        
				$receiverName = $guest->getName();
				$receiverEmail = $guest->getEmail();
				$mailSubject = "Credit Memo # " . $creditmemo->getIncrementId() . " for Booking # " . $order->getIncrementId();
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

        ////////////////////
		        	$cancelFlag = 1;
		        } catch (Exception $e) {
		            
		        }
			}
        
        		if($cancelFlag) {
		            $item->setBookingStatus(4);
			        $item->getUpdatedAt(now());
			        $item->save();
			        
			        /**** cancel booked date of this booking ****/
			        //echo '<pre>'; print_r($item->getData()); echo '</pre>';
			        $start = strtotime($item->getCheckin());
	                $end = strtotime($item->getCheckout());
	                $daysBetween = ceil(abs($end - $start) / 86400);
		            for($i=0; $i<$daysBetween; $i++) {
						$day = date('d',strtotime($item->getCheckin() . "+$i days"));
						$month = date('n',strtotime($item->getCheckin() . "+$i days"));
						$year = date('Y',strtotime($item->getCheckin() . "+$i days"));
						
						$checkCollection = Mage::getModel('property/propertyavailablity')->getCollection()
										->addFieldToFilter('product_id', $item->getProductId())
										->addFieldToFilter('booking_year', $year)
										->addFieldToFilter('booking_month', $month);
						if($checkCollection->count() > 0) {
							foreach ($checkCollection as $_check) {
								//Mage::log('Check: ' . $_check->getAvailableId());
								$availableId = $_check->getAvailableId();
								break;
							}
							
							$available = Mage::getModel('property/propertyavailablity')->load($availableId);
							$preBlockDate = str_replace(','.$day, '', $available->getBlockDate());
							//Mage::log('Block date: ' . $preBlockDate);
							//echo $availableId . ' = ' . $available->getBlockDate() . ' = ' . $day . ' = ' . $preBlockDate . '<br>';
							$model = Mage::getModel('property/propertyavailablity');		
							$model->setData($data)
								->setId($availableId);
							$model->setBlockDate($preBlockDate);
							$model->save();
						}
	                }
	                //exit;
	                
		        //**** mails to guest/host/admin starts ****/
		        
		        //get guest details

		        
		        //Mage::log('Check: ' . $host . ' -- ' . $storeId);
		        $product = Mage::getModel('catalog/product')->load($item->getProductId());
		        
		        //if (Mage::helper('sales')->canSendNewOrderEmail($storeId)) {
		            //Mage::log('test: ');
			        // Get the destination email addresses to send copies to
			        //$copyTo = Mage::helper('booking')->_getEmails('sales_email/creditmemo/copy_to');
			        //$copyMethod = Mage::getStoreConfig('sales_email/creditmemo/copy_method', $storeId);
		
		            $customerName = $order->getCustomerName();
		            	        
		            ////**** mail function to guest starts ****/
			        // Retrieve corresponding email template id
		        	$templateId = Mage::getStoreConfig('property_section/custom_email/guestbookingcreditmemo_byguest_template', $storeId);
		            $emailTemplate = $mailTemplate->loadDefault($templateId);
		            $emailTemplateVariables = array(
				                'order'        => $order,
				                'room_name'	   => $product->getName()
			            );
			        $processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
			        
				$receiverName = $guest->getName();
				$receiverEmail = $guest->getEmail();
				$mailSubject = "Credit Memo of Booking # " . $order->getIncrementId();
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
				
				
					/*try {
				        $mailer = Mage::getModel('core/email_template_mailer');
				        $emailInfo = Mage::getModel('core/email_info');
				        $emailInfo->addTo($guest->getEmail(), $guest->getName());
				        if ($copyTo && $copyMethod == 'bcc') {
				            // Add bcc to customer email
				            foreach ($copyTo as $email) {
				                $emailInfo->addBcc($email);
				            }
				        }
				        $mailer->addEmailInfo($emailInfo);
				
				        // Email copies are sent as separated emails if their copy method is 'copy'
				        if ($copyTo && $copyMethod == 'copy') {
				            foreach ($copyTo as $email) {
				                $emailInfo = Mage::getModel('core/email_info');
				                $emailInfo->addTo($email);
				                $mailer->addEmailInfo($emailInfo);
				            }
				        }
				
				        // Set all required params and send emails
				        $mailer->setSender(Mage::getStoreConfig('sales_email/order/identity', $storeId));
				        $mailer->setStoreId($storeId);
				        $mailer->setTemplateId($templateId);
				        $mailer->setTemplateParams(array(
				                'order'        => $order,
				                'room_name'	   => $product->getName()
				            )
				        );
				        $mailer->send();
		    	    } catch (Exception $e) {
					    throw $e;
					}*/
					////**** mail function to guest ends ****/
					
					////**** mail function to host starts ****/
			        // Retrieve corresponding email template id
			        $templateId = Mage::getStoreConfig('property_section/custom_email/hostbookingcreditmemo_byguest_template', $storeId);
			            $emailTemplate = $mailTemplate->loadDefault($templateId);
			            $emailTemplateVariables = array(
				                'order'        => $order,
				                'host_name'    => $host->getFirstname(),
				                'room_name'	   => $product->getName()
				            );
				        $processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
			        
						$receiverName = $host->getName();
						$receiverEmail = $host->getEmail();
						$mailSubject = "Credit Memo of Booking # " . $order->getIncrementId();
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
					/*try {
				        $mailer = Mage::getModel('core/email_template_mailer');
				        $emailInfo = Mage::getModel('core/email_info');
				        $emailInfo->addTo($host->getEmail(), $host->getName());
				        if ($copyTo && $copyMethod == 'bcc') {
				            // Add bcc to customer email
				            foreach ($copyTo as $email) {
				                $emailInfo->addBcc($email);
				            }
				        }
				        $mailer->addEmailInfo($emailInfo);
				
				        // Email copies are sent as separated emails if their copy method is 'copy'
				        if ($copyTo && $copyMethod == 'copy') {
				            foreach ($copyTo as $email) {
				                $emailInfo = Mage::getModel('core/email_info');
				                $emailInfo->addTo($email);
				                $mailer->addEmailInfo($emailInfo);
				            }
				        }
				
				        // Set all required params and send emails
				        $mailer->setSender(Mage::getStoreConfig('sales_email/order/identity', $storeId));
				        $mailer->setStoreId($storeId);
				        $mailer->setTemplateId($templateId);
				        $mailer->setTemplateParams(array(
				                'order'        => $order,
				                'host_name'    => $host->getFirstname(),
				                'room_name'	   => $product->getName()
				            )
				        );
				        $mailer->send();
		    	    } catch (Exception $e) {
					    throw $e;
					}*/
					////**** mail function to host ends ****/
					
					////**** mail function to admin starts ****/
			        $templateId = Mage::getStoreConfig('property_section/custom_email/adminbookingcreditmemo_byguest_template', $storeId);
			            $emailTemplate = $mailTemplate->loadDefault($templateId);
			            $emailTemplateVariables = array(
				                'order'        => $order,
				                'host_name'    => $host->getFirstname(),
				                'room_name'	   => $product->getName()
				            );
				        $processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
			        
						$receiverName = Mage::getStoreConfig('trans_email/ident_general/name', $storeId);
						$receiverEmail = Mage::getStoreConfig('trans_email/ident_general/email', $storeId);
						$mailSubject = "Credit Memo of Booking # " . $order->getIncrementId();
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
									        
					/*try {
				        $mailer = Mage::getModel('core/email_template_mailer');
				        $emailInfo = Mage::getModel('core/email_info');
				        $emailInfo->addTo(Mage::getStoreConfig('trans_email/ident_general/email', $storeId), Mage::getStoreConfig('trans_email/ident_general/name', $storeId));
				        if ($copyTo && $copyMethod == 'bcc') {
				            // Add bcc to customer email
				            foreach ($copyTo as $email) {
				                $emailInfo->addBcc($email);
				            }
				        }
				        $mailer->addEmailInfo($emailInfo);
				
				        // Email copies are sent as separated emails if their copy method is 'copy'
				        if ($copyTo && $copyMethod == 'copy') {
				            foreach ($copyTo as $email) {
				                $emailInfo = Mage::getModel('core/email_info');
				                $emailInfo->addTo($email);
				                $mailer->addEmailInfo($emailInfo);
				            }
				        }
				
				        // Set all required params and send emails
				        $mailer->setSender(Mage::getStoreConfig('sales_email/order/identity', $storeId));
				        $mailer->setStoreId($storeId);
				        $mailer->setTemplateId($templateId);
				        $mailer->setTemplateParams(array(
				                'order'        => $order,
				                'host_name'    => $host->getFirstname(),
				                'room_name'	   => $product->getName()
				            )
				        );
				        $mailer->send();
		    	    } catch (Exception $e) {
					    throw $e;
					}*/
					////**** mail function to admin ends ****/
					
		        //}
				//**** mails to guest/host/admin ends ****/
					Mage::getSingleton('core/session')->addSuccess($this->__('The booking has been cancelled successfully. The refund request has been initiated to Admin. The guest has been updated by mail.'));

                } else {
                	Mage::getSingleton('core/session')->addError($this->__('The booking can not be cancelled now.'));
                }
            $this->_redirect('*/*/list');
            return;
	}
	
	public function cancelAction() {
		if(!$this->getRequest()->getParam('id')) {
			Mage::getSingleton('core/session')->addError($this->__('Item not found.'));
            $this->_redirect('*/*/manage');
            return;
		}
		//owner permission 
        $customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
        $id = (int)$this->getRequest()->getParam('id');
        $item = Mage::getModel('booking/booking')->load($id);
        $customer_id = $item->getHostId();
        if ($customerId != $customer_id) {
            Mage::getSingleton('core/session')->addError($this->__("Access denied"));
            $this->_redirect('*/*/manage');
            return;
        }
        
        if($item->getBookingStatus() == 2) {
        	Mage::getSingleton('core/session')->addError($this->__('The booking was cancelled already.'));
            $this->_redirect('*/*/manage');
            return;
        } elseif($item->getBookingStatus() == 1) {
         	Mage::getSingleton('core/session')->addError($this->__('The booking was confirmed already. It cannot be cancelled now.'));
            $this->_redirect('*/*/manage');
            return;
        }
        
        $order = Mage::getModel('sales/order')->load($item->getOrderId());
        if (!$order->getId()) {
            $this->_getSession()->addError($this->__('This booking no longer exists.'));
            $this->_redirect('*/*/manage');
            return;
        }
        
        if ($order) {
        		$storeId = Mage::app()->getStore()->getStoreId();
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
			$guest = Mage::getModel('customer/customer')->load($item->getGuestId());
	        $host = Mage::getModel('customer/customer')->load($item->getHostId());
			
        	$senderName = Mage::getStoreConfig('trans_email/ident_sales/name', $storeId);
			$senderEmail = Mage::getStoreConfig('trans_email/ident_sales/email', $storeId);
			
            try {
            	$cancelFlag = 0;
            	$stateFlag = 0;
            	if($order->canCancel()) {
            		try {
		                $order->cancel()
		                    ->save();
		                $cancelFlag = 1;
		                $stateFlag = 1;
	                } catch (Exception $e) {
		            }
            	} elseif($order->canCreditmemo()) {
        		    $data = array();
			        try {
	            		if ($order->hasInvoices()) {
						    foreach ($order->getInvoiceCollection() as $inv) {
						        $invoiceId = $inv->getId();
							   break;
						    }
						    $invoice = Mage::getModel('sales/order_invoice')->load($invoiceId);
	            		}
	            		
			            $service = Mage::getModel('sales/service_order', $order);
			            if ($invoice) {
			                $creditmemo = $service->prepareInvoiceCreditmemo($invoice, $data);
			            } else {
			                $creditmemo = $service->prepareCreditmemo($data);
			            }
			            $creditmemo->setState(2);
			            
				        $transactionSave = Mage::getModel('core/resource_transaction')
				            ->addObject($creditmemo)
				            ->addObject($creditmemo->getOrder());
				        if ($creditmemo->getInvoice()) {
				            $transactionSave->addObject($creditmemo->getInvoice());
				        }
				        $transactionSave->save();
				        $order->setStatus('refunded')->save();
				        //$creditmemo->sendEmail(1, '');
				        
		// credit memo magento email
		
        // Start store emulation process
        $appEmulation = Mage::getSingleton('core/app_emulation');
        $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($storeId);

        try {
            // Retrieve specified view block from appropriate design package (depends on emulated store)
            $paymentBlock = Mage::helper('payment')->getInfoBlock($order->getPayment())
                ->setIsSecureMode(true);
            $paymentBlock->getMethod()->setStore($storeId);
            $paymentBlockHtml = $paymentBlock->toHtml();
        } catch (Exception $exception) {
            // Stop store emulation process
            $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);
            throw $exception;
        }

        // Stop store emulation process
        $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);

        // Retrieve corresponding email template id and customer name
        if ($order->getCustomerIsGuest()) {
            $templateId = Mage::getStoreConfig('sales_email/creditmemo/guest_template', $storeId);
            $customerName = $order->getBillingAddress()->getName();
        } else {
            $templateId = Mage::getStoreConfig('sales_email/creditmemo/template', $storeId);
            $customerName = $order->getCustomerName();
        }

	            $emailTemplate = $mailTemplate->load($templateId);
	            $emailTemplateVariables = array(
	                'order'        => $order,
	                'creditmemo'   => $creditmemo,
	                //'comment'      => $comment,
	                'billing'      => $order->getBillingAddress(),
	                'payment_html' => $paymentBlockHtml
		            );
		        $processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
			        
				$receiverName = $guest->getName();
				$receiverEmail = $guest->getEmail();
				$mailSubject = "Credit Memo # " . $creditmemo->getIncrementId() . " for Booking # " . $order->getIncrementId();
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

        ////////////////////
        
			        	$cancelFlag = 1;
			        	$stateFlag = 2;
			        } catch (Exception $e) {
			            
			        }
            	}
            	
                if($cancelFlag) {
			        if($stateFlag == 1) {
		            	$item->setBookingStatus(2);
			        } elseif($stateFlag == 2) {
			        	$item->setBookingStatus(3);
			        }
			        $item->getUpdatedAt(now());
			        $item->save();
			        
			        /**** cancel booked date of this booking ****/
			        //echo '<pre>'; print_r($item->getData()); echo '</pre>';
			        $start = strtotime($item->getCheckin());
	                $end = strtotime($item->getCheckout());
	                $daysBetween = ceil(abs($end - $start) / 86400);
		            for($i=0; $i<$daysBetween; $i++) {
						$day = date('d',strtotime($item->getCheckin() . "+$i days"));
						$month = date('n',strtotime($item->getCheckin() . "+$i days"));
						$year = date('Y',strtotime($item->getCheckin() . "+$i days"));
						
						$checkCollection = Mage::getModel('property/propertyavailablity')->getCollection()
										->addFieldToFilter('product_id', $item->getProductId())
										->addFieldToFilter('booking_year', $year)
										->addFieldToFilter('booking_month', $month);
						if($checkCollection->count() > 0) {
							foreach ($checkCollection as $_check) {
								//Mage::log('Check: ' . $_check->getAvailableId());
								$availableId = $_check->getAvailableId();
								break;
							}
							
							$available = Mage::getModel('property/propertyavailablity')->load($availableId);
							$preBlockDate = str_replace(','.$day, '', $available->getBlockDate());
							//Mage::log('Block date: ' . $preBlockDate);
							//echo $availableId . ' = ' . $available->getBlockDate() . ' = ' . $day . ' = ' . $preBlockDate . '<br>';
							$model = Mage::getModel('property/propertyavailablity');		
							$model->setData($data)
								->setId($availableId);
							$model->setBlockDate($preBlockDate);
							$model->save();
						}
	                }
	                //exit;
		        //**** mails to guest/host/admin starts ****/
		        //get guest details
		        $guest = Mage::getModel('customer/customer')->load($item->getGuestId());
		        $host = Mage::getModel('customer/customer')->load($item->getHostId());
		        $storeId = Mage::app()->getStore()->getStoreId();
		        //Mage::log('Check: ' . $host . ' -- ' . $storeId);
		        $product = Mage::getModel('catalog/product')->load($item->getProductId());
		        
		        //if (Mage::helper('sales')->canSendNewOrderEmail($storeId)) {
		            //Mage::log('test: ');
			        // Get the destination email addresses to send copies to
			        //$copyTo = Mage::helper('booking')->_getEmails('sales_email/creditmemo/copy_to');
			        //$copyMethod = Mage::getStoreConfig('sales_email/creditmemo/copy_method', $storeId);
		
		            $customerName = $order->getCustomerName();
		            	        
		            ////**** mail function to guest starts ****/
			        // Retrieve corresponding email template id
			        if($stateFlag == 1) {
		            	$templateId = Mage::getStoreConfig('property_section/custom_email/guestbookingcancel_template', $storeId);
			        } elseif($stateFlag == 2) {
			        	$templateId = Mage::getStoreConfig('property_section/custom_email/guestbookingcreditmemo_template', $storeId);
			        }

			            $emailTemplate = $mailTemplate->loadDefault($templateId);
			            $emailTemplateVariables = array(
				                'order'        => $order,
				                'room_name'	   => $product->getName()
				            );
				        $processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
			        
						$receiverName = $guest->getName();
						$receiverEmail = $guest->getEmail();
				        if($stateFlag == 1) {
			            	$mailSubject = "Canceled Booking # " . $order->getIncrementId();
				        } elseif($stateFlag == 2) {
				        	$mailSubject = "Credit Memo of Booking # " . $order->getIncrementId();
				        }
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
						
					/*try {
				        $mailer = Mage::getModel('core/email_template_mailer');
				        $emailInfo = Mage::getModel('core/email_info');
				        $emailInfo->addTo($guest->getEmail(), $guest->getName());
				        if ($copyTo && $copyMethod == 'bcc') {
				            // Add bcc to customer email
				            foreach ($copyTo as $email) {
				                $emailInfo->addBcc($email);
				            }
				        }
				        $mailer->addEmailInfo($emailInfo);
				
				        // Email copies are sent as separated emails if their copy method is 'copy'
				        if ($copyTo && $copyMethod == 'copy') {
				            foreach ($copyTo as $email) {
				                $emailInfo = Mage::getModel('core/email_info');
				                $emailInfo->addTo($email);
				                $mailer->addEmailInfo($emailInfo);
				            }
				        }
				
				        // Set all required params and send emails
				        $mailer->setSender(Mage::getStoreConfig('sales_email/order/identity', $storeId));
				        $mailer->setStoreId($storeId);
				        $mailer->setTemplateId($templateId);
				        $mailer->setTemplateParams(array(
				                'order'        => $order,
				                'room_name'	   => $product->getName()
				            )
				        );
				        $mailer->send();
		    	    } catch (Exception $e) {
					    throw $e;
					}*/
					////**** mail function to guest ends ****/
					
					////**** mail function to host starts ****/
			        // Retrieve corresponding email template id
			        if($stateFlag == 1) {
		            	$templateId = Mage::getStoreConfig('property_section/custom_email/hostbookingcancel_template', $storeId);
			        } elseif($stateFlag == 2) {
			        	$templateId = Mage::getStoreConfig('property_section/custom_email/hostbookingcreditmemo_template', $storeId);
			        }

			        	$emailTemplate = $mailTemplate->loadDefault($templateId);
			            $emailTemplateVariables = array(
				                'order'        => $order,
				                'host_name'    => $host->getFirstname(),
				                'room_name'	   => $product->getName()
				            );
				        $processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
			        
						$receiverName = $host->getName();
						$receiverEmail = $host->getEmail();
			        if($stateFlag == 1) {
		            	$mailSubject = "Canceled Booking # " . $order->getIncrementId();
			        } elseif($stateFlag == 2) {
			        	$mailSubject = "Credit Memo of Booking # " . $order->getIncrementId();
			        }
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
						
					/*try {
				        $mailer = Mage::getModel('core/email_template_mailer');
				        $emailInfo = Mage::getModel('core/email_info');
				        $emailInfo->addTo($host->getEmail(), $host->getName());
				        if ($copyTo && $copyMethod == 'bcc') {
				            // Add bcc to customer email
				            foreach ($copyTo as $email) {
				                $emailInfo->addBcc($email);
				            }
				        }
				        $mailer->addEmailInfo($emailInfo);
				
				        // Email copies are sent as separated emails if their copy method is 'copy'
				        if ($copyTo && $copyMethod == 'copy') {
				            foreach ($copyTo as $email) {
				                $emailInfo = Mage::getModel('core/email_info');
				                $emailInfo->addTo($email);
				                $mailer->addEmailInfo($emailInfo);
				            }
				        }
				
				        // Set all required params and send emails
				        $mailer->setSender(Mage::getStoreConfig('sales_email/order/identity', $storeId));
				        $mailer->setStoreId($storeId);
				        $mailer->setTemplateId($templateId);
				        $mailer->setTemplateParams(array(
				                'order'        => $order,
				                'host_name'    => $host->getFirstname(),
				                'room_name'	   => $product->getName()
				            )
				        );
				        $mailer->send();
		    	    } catch (Exception $e) {
					    throw $e;
					}*/
					////**** mail function to host ends ****/
					
					////**** mail function to admin starts ****/
					if($stateFlag == 2) {
			        	$templateId = Mage::getStoreConfig('property_section/custom_email/adminbookingcreditmemo_template', $storeId);
			        }

			        	$emailTemplate = $mailTemplate->loadDefault($templateId);
			            $emailTemplateVariables = array(
				                'order'        => $order,
				                'host_name'    => $host->getFirstname(),
				                'room_name'	   => $product->getName()
				            );
				        $processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
			        
						$receiverName = Mage::getStoreConfig('trans_email/ident_general/name', $storeId);
						$receiverEmail = Mage::getStoreConfig('trans_email/ident_general/email', $storeId);
			        if($stateFlag == 1) {
		            	$mailSubject = "Canceled Booking # " . $order->getIncrementId();
			        } elseif($stateFlag == 2) {
			        	$mailSubject = "Credit Memo of Booking # " . $order->getIncrementId();
			        }
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
						
					/*try {
				        $mailer = Mage::getModel('core/email_template_mailer');
				        $emailInfo = Mage::getModel('core/email_info');
				        $emailInfo->addTo(Mage::getStoreConfig('trans_email/ident_general/email', $storeId), Mage::getStoreConfig('trans_email/ident_general/name', $storeId));
				        if ($copyTo && $copyMethod == 'bcc') {
				            // Add bcc to customer email
				            foreach ($copyTo as $email) {
				                $emailInfo->addBcc($email);
				            }
				        }
				        $mailer->addEmailInfo($emailInfo);
				
				        // Email copies are sent as separated emails if their copy method is 'copy'
				        if ($copyTo && $copyMethod == 'copy') {
				            foreach ($copyTo as $email) {
				                $emailInfo = Mage::getModel('core/email_info');
				                $emailInfo->addTo($email);
				                $mailer->addEmailInfo($emailInfo);
				            }
				        }
				
				        // Set all required params and send emails
				        $mailer->setSender(Mage::getStoreConfig('sales_email/order/identity', $storeId));
				        $mailer->setStoreId($storeId);
				        $mailer->setTemplateId($templateId);
				        $mailer->setTemplateParams(array(
				                'order'        => $order,
				                'host_name'    => $host->getFirstname(),
				                'room_name'	   => $product->getName()
				            )
				        );
				        $mailer->send();
		    	    } catch (Exception $e) {
					    throw $e;
					}*/
					////**** mail function to admin ends ****/
					
		        //}
				//**** mails to guest/host/admin ends ****/
			        if($stateFlag == 1) {
						Mage::getSingleton('core/session')->addSuccess($this->__('The booking has been cancelled successfully. The guest has been updated by mail.'));
			        } elseif($stateFlag == 2) {
						Mage::getSingleton('core/session')->addSuccess($this->__('The booking has been cancelled successfully. The refund request has been initiated to Admin. The guest has been updated by mail.'));
			        }

                } else {
                	Mage::getSingleton('core/session')->addError($this->__('The booking can not be cancelled now.'));
                }
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('core/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('core/session')->addError($this->__('The booking can not be cancelled now.'));
                Mage::logException($e);
            }
            $this->_redirect('*/*/manage');
            return;
        } 
	}
}