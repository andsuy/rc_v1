<?php
class Si_Booking_CronController extends Mage_Core_Controller_Front_Action {   

	public function sendcodeAction() {
		$checkinDate = date('Y-m-d', strtotime("+1 days") );
		//echo $checkinDate; // 2014-05-11
		$collection = Mage::getModel('booking/booking')->getCollection()
						->addFieldToFilter('checkin', array('eq' => $checkinDate));
						//->addFieldToFilter('booking_status', 1)
						//->addFieldToFilter('key_status', 0);

		if($collection->count() > 0) {
			foreach($collection as $_booking) {
				$item = Mage::getModel('booking/booking')->load($_booking->getBookingId());
		//echo '<pre>'; print_r($item->getData()); echo '</pre>';
		//echo date('l, F d, Y', strtotime($item->getCheckin()));
		//exit;
				$order = Mage::getModel('sales/order')->load($item->getOrderId());
				if($order) {
		        	// create 5 digits code
		        	$secureCode = Mage::helper('booking')->getSecureCode();
		        	
		        	$item->setSecureKey($secureCode);
		        	$item->setKeyStatus(1);
			        $item->setUpdateAt(now());
			        $item->save();
			        
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
			
        	$senderName = Mage::getStoreConfig('trans_email/ident_sales/name', $storeId);
			$senderEmail = Mage::getStoreConfig('trans_email/ident_sales/email', $storeId);
			
			        //**** mails to guest starts ****/
			        //get guest details
			        $guest = Mage::getModel('customer/customer')->load($item->getGuestId());
			        $host = Mage::getModel('customer/customer')->load($item->getHostId());
			        $storeId = Mage::app()->getStore()->getStoreId();
			        //Mage::log('Check: ' . $host . ' -- ' . $storeId);
			        $product = Mage::getModel('catalog/product')->load($item->getProductId());
			        
			        if (Mage::helper('sales')->canSendNewOrderEmail($storeId)) {
			            //Mage::log('test: ');
				        // Get the destination email addresses to send copies to
				        $copyTo = Mage::helper('booking')->_getEmails('sales_email/order/copy_to');
				        $copyMethod = Mage::getStoreConfig('sales_email/order/copy_method', $storeId);
			
			            $customerName = $order->getCustomerName();
			            	        
			            ////**** mail function to guest starts ****/
				        // Retrieve corresponding email template id
			            $templateId = Mage::getStoreConfig('property_section/custom_email/guestbookingsecurecode_template', $storeId);
			            
			            $emailTemplate = $mailTemplate->loadDefault($templateId);
			            $emailTemplateVariables = array(
					                'order'        => $order,
					                'room_name'	   => $product->getName(),
					                'checkin_date' => date('l, F d, Y', strtotime($item->getCheckin())),
					                'secure_code'  => $secureCode
				            );
				        $processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
			        
						$receiverName = $guest->getName();
						$receiverEmail = $guest->getEmail();
			            $mailSubject = "Details of Booking # " . $order->getIncrementId();
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
					                'checkin_date' => date('l, F d, Y', strtotime($item->getCheckin())),
					                'secure_code'  => $secureCode
					            )
					        );
					        $mailer->send();
					        //echo 'mail sent';
			    	    } catch (Exception $e) {
						    throw $e;
						}*/
						////**** mail function to guest ends ****/
						
			        }
					//**** mails to guest ends ****/
		
		        }
			}
		}
		     
	}
	
	public function sendreminderAction() {

		$dueDate = date('Y-m-d H:i:s', strtotime("-24 hours") );
		$collection = Mage::getModel('booking/booking')->getCollection()
				->addFieldToFilter('booking_status', 0);
				//->addFieldToFilter('created_at', array('gteq' => $dueDate));
				//->addFieldToFilter('key_status', 0);
		//echo $collection->getSelect();
		if($collection->count() > 0) {
			foreach($collection as $_booking) {
				//echo '<pre>'; print_r($_booking->getData()); echo '</pre>';
				$item = Mage::getModel('booking/booking')->load($_booking->getBookingId());
				
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
				$storeId = Mage::app()->getStore()->getStoreId();
	        	$senderName = Mage::getStoreConfig('trans_email/ident_sales/name', $storeId);
				$senderEmail = Mage::getStoreConfig('trans_email/ident_sales/email', $storeId);
							
				if(strtotime($item->getCreatedAt()) >= strtotime($dueDate)) {
					$order = Mage::getModel('sales/order')->load($item->getOrderId());
					if($order) {
						if($item->getOrderStatus() == 'complete') {
					        //**** mails to host starts ****/
					        //get host details
					        $host = Mage::getModel('customer/customer')->load($item->getHostId());
					        $guest = Mage::getModel('customer/customer')->load($item->getGuestId());
					        $storeId = Mage::app()->getStore()->getStoreId();
					        //Mage::log('Check: ' . $host . ' -- ' . $storeId);
					        $product = Mage::getModel('catalog/product')->load($item->getProductId());
					        		
					            //Mage::log('test: ');
						        // Get the destination email addresses to send copies to
						        //$copyTo = Mage::helper('booking')->_getEmails('sales_email/order/copy_to');
						        //$copyMethod = Mage::getStoreConfig('sales_email/order/copy_method', $storeId);
					
					            $customerName = $order->getCustomerName();
		
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
					        
					            ////**** mail function to guest starts ****/
						        // Retrieve corresponding email template id
					            $templateId = Mage::getStoreConfig('property_section/custom_email/hostbookingreminder_template', $storeId);
					            $emailTemplate = $mailTemplate->loadDefault($templateId);
					            $emailTemplateVariables = array(
						                'order'        => $order,
						                'billing'      => $order->getBillingAddress(),
						                'payment_html' => $paymentBlockHtml,
						                'host_name'	   => $host->getFirstname()
						            );
						        $processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
					        
								$receiverName = $host->getName();
								$receiverEmail = $host->getEmail();
					            $mailSubject = "New Booking # " . $order->getIncrementId();
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
						                'billing'      => $order->getBillingAddress(),
						                'payment_html' => $paymentBlockHtml,
						                'host_name'	   => $host->getFirstname()
							            )
							        );
							        $mailer->send();
							        //echo 'mail sent';
					    	    } catch (Exception $e) {
								    throw $e;
								}*/
								////**** mail function to host ends ****/
								
							//**** mails to host ends ****/
						}
			        }
				} else {
					$order = Mage::getModel('sales/order')->load($item->getOrderId());
					if ($order) {
						$host = Mage::getModel('customer/customer')->load($item->getHostId());
				        $guest = Mage::getModel('customer/customer')->load($item->getGuestId());
				        $storeId = Mage::app()->getStore()->getStoreId();
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
			            	if($item->getOrderStatus() == 'canceled' && $item->getBookingStatus() == 0) {
			            		$item->setBookingStatus(2);
			            		$item->setUpdateAt(now());
						        $item->save();
			            	}
			                if($cancelFlag) {
						        if($stateFlag == 1) {
					            	$item->setBookingStatus(2);
						        } elseif($stateFlag == 2) {
						        	$item->setBookingStatus(3);
						        }
						        $item->setUpdateAt(now());
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
				                
				            if($item->getOrderStatus() == 'complete') {   
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
							        $copyTo = Mage::helper('booking')->_getEmails('sales_email/creditmemo/copy_to');
							        $copyMethod = Mage::getStoreConfig('sales_email/creditmemo/copy_method', $storeId);
						
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
				            }
						        if($stateFlag == 1) {
						        	
						        } elseif($stateFlag == 2) {
						        	
						        }
			
			                } else {
			                	
			                }
			            } catch (Mage_Core_Exception $e) {
			            	
			            } catch (Exception $e) {
			                Mage::logException($e);
			            }

			        }
				}   
			}
		}
	}
	
	public function cancelorderAction() {
	    $orderCollection = Mage::getResourceModel('sales/order_collection');
        $orderCollection->addFieldToFilter('state', 'pending_payment')
                ->getSelect();

        foreach($orderCollection->getItems() as $order)
        {
          $order = Mage::getModel('sales/order')->load($order['entity_id']);
          //echo $order->getId(); echo '<br>';
	    	if($order->canCancel()) {
	    		try {
	                $order->cancel()
	                    ->save();
	                $cancelFlag = 1;
	                $stateFlag = 1;
	            } catch (Exception $e) {
	            }
	    	}
        }
	}
	
}