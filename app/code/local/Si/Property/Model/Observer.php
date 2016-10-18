<?php
class Si_Property_Model_Observer
{
	public function catalogProductLoadAfter(Varien_Event_Observer $observer)
	{
		$action = Mage::app()->getFrontController()->getAction();
		if (!empty($action) && $action->getFullActionName() == 'checkout_cart_add')
		{
			$product = $observer->getProduct();
			if($product->getAttributeSetId() == 9) {
				$additionalOptions = array();
				// assuming you are posting your custom form values in an array called extra_options...
				if ($options = $action->getRequest()->getParam('checkin')) {
					// add to the additional options array
					if ($additionalOption = $product->getCustomOption('checkin')) {
						$additionalOptions = (array) unserialize($additionalOption->getValue());
					}
					foreach ($options as $key => $value) {
						$additionalOptions[] = array(
							'label' => $key,
							'value' => $value,
						);
					}
				}
				if ($options = $action->getRequest()->getParam('checkout')) {
					// add to the additional options array
					if ($additionalOption = $product->getCustomOption('checkout')) {
						$additionalOptions = (array) unserialize($additionalOption->getValue());
					}
					foreach ($options as $key => $value) {
						$additionalOptions[] = array(
							'label' => $key,
							'value' => $value,
						);
					}
				}
				if ($options = $action->getRequest()->getParam('accommodate')) {
					// add to the additional options array
					if ($additionalOption = $product->getCustomOption('accommodate')) {
						$additionalOptions = (array) unserialize($additionalOption->getValue());
					}
					foreach ($options as $key => $value) {
						$additionalOptions[] = array(
							'label' => $key,
							'value' => $value,
						);
					}
				}
				// add the additional options array with the option code additional_options
				$product->addCustomOption('additional_options', serialize($additionalOptions));
			}
		}
	}
	
	public function salesConvertQuoteItemToOrderItem(Varien_Event_Observer $observer)
	{
		$quoteItem = $observer->getItem();
		if ($additionalOptions = $quoteItem->getOptionByCode('additional_options')) {
			$orderItem = $observer->getOrderItem();
			$options = $orderItem->getProductOptions();
			$options['additional_options'] = unserialize($additionalOptions->getValue());
			$orderItem->setProductOptions($options);
		}
	}

	public function checkoutCartProductAddAfter(Varien_Event_Observer $observer)
	{
		$action = Mage::app()->getFrontController()->getAction();
	    if ($action->getFullActionName() == 'sales_order_reorder')
	    {
	        $item = $observer->getQuoteItem();
	        $buyInfo = $item->getBuyRequest();
	        if ($options = $buyInfo->getMchart())
	        {
	            $additionalOptions = array();
	            if ($additionalOption = $item->getOptionByCode('additional_options'))
	            {
	                $additionalOptions = (array) unserialize($additionalOption->getValue());
	            }
	            foreach ($options as $key => $value)
	            {
	                $additionalOptions[] = array(
	                    'label' => $key,
	                    'value' => $value,
	                );
	            }
	            $item->addOption(array(
	                'code' => 'additional_options',
	                'value' => serialize($additionalOptions)
	            ));
	        }
	    }
	}
	public function checkoutCartSaveBefore(Varien_Event_Observer $observer)
	{
		//echo '<pre>'; print_r($observer); echo '</pre>'; exit;
		$cart = $observer->getEvent()->getCart();
		foreach($cart->getQuote()->getAllItems() as $item) {
        	$options = $item->getProduct()->getCustomOptions();
			$data = array();
			if($item->getProduct()->getAttributeSetId() == 9) {
				$additionalOptions = array();
				if (isset($options['checkin'])) {
					$data['checkin'] = $options['checkin']->getValue();	
			        $additionalOptions[] = array(
						 'code' => 'additional_options',
						 'label' => 'Checkin',
						 'value' => $data['checkin']
						);
				}
				if (isset($options['checkout'])) {
					$data['checkout'] = $options['checkout']->getValue();	
			        $additionalOptions[] = array(
						 'code' => 'additional_options',
						 'label' => 'Checkout',
						 'value' => $data['checkout']
						);
				}
				if (isset($options['accommodate'])) {
					$data['accommodate'] = $options['accommodate']->getValue();	
			        $additionalOptions[] = array(
						 'code' => 'additional_options',
						 'label' => 'Accommodate',
						 'value' => $data['accommodate']
						);
				}
				$item->addOption(array(
				  'code' => 'additional_options',
				  'value' => serialize($additionalOptions),
				));
			}
        }
	}
	
	public function processBookingAfterCheckout(Varien_Event_Observer $observer)
	{
		$_store = Mage::app()->getStore();
		$resource = Mage::getSingleton('core/resource');
		$write = $resource->getConnection('core_write');
     	$table = $resource->getTableName('sales_flat_quote_item_option');
     	$table2 = $resource->getTableName('property_availability');
     	
		$order = $observer->getEvent()->getOrder();
		$quote = $observer->getEvent()->getQuote();
		
		//Mage::log('My log message: '.$order->getId() . ' == ' . $order->getStatus());
		
		$cartItems = $quote->getAllVisibleItems();
		foreach ($cartItems as $item) {
		    //Mage::log('QuoteId: ' .$quote->getId() . '  QuoteItem: ' .$item->getItemId());
		    $getCheckinSql = "SELECT value FROM ".$table." WHERE item_id = '".$item->getItemId()."' AND code = 'checkin'";
		    $checkin = $write->fetchOne($getCheckinSql);
		    $getCheckoutSql = "SELECT value FROM ".$table." WHERE item_id = '".$item->getItemId()."' AND code = 'checkout'";
		    $checkout = $write->fetchOne($getCheckoutSql);
		    
		    if(Mage::getStoreConfig('property_section/general/enable')) { // check property function enable
		    	
		    	$start = strtotime($checkin);
                $end = strtotime($checkout);
                $daysBetween = ceil(abs($end - $start) / 86400);

                /**** update property booking details start ****/
                
                $getAccommodateSql = "SELECT value FROM ".$table." WHERE item_id = '".$item->getItemId()."' AND code = 'accommodate'";
		    	$accommodate = $write->fetchOne($getAccommodateSql);
		    	$product = Mage::getModel('catalog/product')->load($item->getProductId());
		    	$commissionFeePercentage = Mage::getStoreConfig('property_section/general/commission_fee');
            	if(!empty($commissionFeePercentage)) {
	            	$commissionFee = (($order->getSubtotal() * $commissionFeePercentage) / 100);
	            	$baseCommissionFee = (($order->getBaseSubtotal() * $commissionFeePercentage) / 100);
            	}
            	
            	$ordered_items = $order->getAllItems();
            	foreach($ordered_items as $item) {
	            	$orderItemId = $item->getItemId();
	            	break;
            	}
            	
            	$bookingData = array(
							'product_id' => $item->getProductId(),
							'host_id' => $product->getUserId(),
							'guest_id' => $order->getCustomerId(),
							'checkin' => $checkin,
							'checkout' => $checkout,
							'accomodates' => $accommodate,
							'subtotal' => $_store->roundPrice($order->getSubtotal()),
							'host_fee' => $_store->roundPrice($commissionFee),
							'service_fee' => $_store->roundPrice($order->getFeeAmount()),
							'total' => $_store->roundPrice($order->getGrandTotal()),
							'order_id' => $order->getId(),
							'order_item_id' => $orderItemId,
							'order_status' => $order->getStatus(),
							'booking_status' => 0,
							'secure_key' => '',
							'key_status' => '',
							'base_currency_code' => $order->getBaseCurrencyCode(),							
							'order_currency_code' => $order->getOrderCurrencyCode(),
							'created_at' => now(),
							'update_at' => now(),
							'base_subtotal' => $_store->roundPrice($order->getBaseSubtotal()),
							'base_host_fee' => $_store->roundPrice($baseCommissionFee),
							'base_service_fee' => $_store->roundPrice($order->getBaseFeeAmount()),
							'base_total' => $_store->roundPrice($order->getBaseGrandTotal())
						);
						
				$orderId = $order->getId();
		    	if(!empty($orderId)) {
			    	$booking = Mage::getModel('booking/booking')->load($orderId, 'order_id');
			    	$model = Mage::getModel('booking/booking');
					$model->setData($bookingData)->setId($booking->getId());
					$model->save();
		    	}
		    	
                /**** update property booking details end ****/

		    }
		}
	}
	
	// Email helper methods
	protected function _getEmails($configPath)
    {
        $data = Mage::getStoreConfig($configPath, Mage::app()->getStore()->getStoreId());
        if (!empty($data)) {
            return explode(',', $data);
        }
        return false;
    }
    
    public function updateBookingOrderStatus(Varien_Event_Observer $observer) {
    	$order = $observer->getEvent()->getOrder();
    	$orderId = $order->getId();
    	if(!empty($orderId)) {
	    	$booking = Mage::getModel('booking/booking')->load($orderId, 'order_id');
	    	$model = Mage::getModel('booking/booking');
	    	$data = array('order_status' => $order->getStatus(), 'order_id' => $orderId);
			$model->setData($data)->setId($booking->getId());
			$model->save();
			
			if($order->getStatus() == 'complete') {
				$checkin = $booking->getCheckin();
				$checkout = $booking->getCheckout();
				$start = strtotime($checkin);
                $end = strtotime($checkout);
                $daysBetween = ceil(abs($end - $start) / 86400);
                
                /***** add/update block/availability date starts *****/
                for($i=0; $i<$daysBetween; $i++) {
					$day = date('d',strtotime($checkin . "+$i days"));
					$month = date('n',strtotime($checkin . "+$i days"));
					$year = date('Y',strtotime($checkin . "+$i days"));
					
					$data = array(
								'product_id' => $booking->getProductId(),
								'booking_type' => 2,
								'booking_year' => $year,
								'booking_month' => $month,
								'created_at' => now(),
								'update_at' => now(),
							);
					$checkCollection = Mage::getModel('property/propertyavailablity')->getCollection()
										->addFieldToFilter('product_id', $booking->getProductId())
										->addFieldToFilter('booking_type', 2)
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
                }
                /***** add/update block/availability date ends *****/
                
                
				/********** save mails data to EC2 server ********/
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
                /**** send host the booking notification mail starts ****/
                $product = Mage::getModel('catalog/product')->load($booking->getProductId());
                //get host details
                $host = Mage::getModel('customer/customer')->load($product->getUserId());
                $guest = Mage::getModel('customer/customer')->load($order->getCustomerId());
		        $storeId = Mage::app()->getStore()->getStoreId();
		        $mailTemplate = Mage::getModel('core/email_template');
		        
		        //Mage::log('Check: ' . $host . ' -- ' . $storeId);
		        
		        //if (Mage::helper('sales')->canSendNewOrderEmail($storeId)) {
		            //Mage::log('test: ');
			        // Get the destination email addresses to send copies to
			        //$copyTo = $this->_getEmails('sales_email/order/copy_to');
			        //$copyMethod = Mage::getStoreConfig('sales_email/order/copy_method', $storeId);
			
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
			        
			        $senderName = Mage::getStoreConfig('trans_email/ident_sales/name', $storeId);
					$senderEmail = Mage::getStoreConfig('trans_email/ident_sales/email', $storeId);
				
			        // Retrieve corresponding email template id and customer name
		            $customerName = $order->getCustomerName();
		            
		            $templateId = Mage::getStoreConfig('property_section/custom_email/hostbookinginfo_template', $storeId);
		            $emailTemplate = $mailTemplate->loadDefault($templateId);
		            $emailTemplateVariables = array(
			                'order'        => $order,
			                'billing'      => $order->getBillingAddress(),
			                'payment_html' => $paymentBlockHtml,
			                'host_name'	   => $host->getFirstname()
			            );
			        $processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
			        
		        //}

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
				
                /**** send host the booking notification mail ends ****/
                
                /**** send order confirmation mail starts ****/
                
                $orderCurrencyCode = Mage::app()->getLocale()->currency( $order->getOrderCurrencyCode() )->getSymbol();
                $itemsBlockHtml = '
                	<table cellspacing="0" cellpadding="0" width="650" border="0" style="border:1px solid #eaeaea">
				    <thead>
				        <tr>
				            <th bgcolor="#EAEAEA" align="left" style="font-size:13px;padding:3px 9px">Property</th>
				            <th bgcolor="#EAEAEA" align="left" style="font-size:13px;padding:3px 9px">&nbsp;</th>
				            <th bgcolor="#EAEAEA" align="center" style="font-size:13px;padding:3px 9px">Night</th>
				            <th bgcolor="#EAEAEA" align="right" style="font-size:13px;padding:3px 9px">Subtotal</th>
				        </tr>
				    </thead>
				    <tbody bgcolor="#F6F6F6">
                ';
				 $ordered_items = $order->getAllItems();
				 foreach($ordered_items as $item){
				 	$itemOptionArray = $item->getProductOptions();
				 	$itemsBlockHtml .= '
				 		<tr>
				    <td valign="top" align="left" style="font-size:11px;padding:3px 9px;border-bottom:1px dotted #cccccc">
				        <strong style="font-size:11px">'. $item->getName() .'</strong>
				                <dl style="margin:0;padding:0">
				                        <dt><strong><em>Checkin</em></strong></dt>
				            <dd style="margin:0;padding:0 0 0 9px">
				                '. $itemOptionArray['info_buyRequest']['from'] .'            </dd>
				                        <dt><strong><em>Checkout</em></strong></dt>
				            <dd style="margin:0;padding:0 0 0 9px">
				                '. $itemOptionArray['info_buyRequest']['to'] .'            </dd>
				                        <dt><strong><em>Accommodate</em></strong></dt>
				            <dd style="margin:0;padding:0 0 0 9px">
				                '. $itemOptionArray['info_buyRequest']['number_of_guests'] .'            </dd>
				                    </dl>
				                                                        </td>
				    <td valign="top" align="left" style="font-size:11px;padding:3px 9px;border-bottom:1px dotted #cccccc">&nbsp;</td>
				    <td valign="top" align="center" style="font-size:11px;padding:3px 9px;border-bottom:1px dotted #cccccc">'. $item->getQtyOrdered() .'</td>
				    <td valign="top" align="right" style="font-size:11px;padding:3px 9px;border-bottom:1px dotted #cccccc">
				                                                <span>'. $orderCurrencyCode . number_format($item->getPrice(), 2) . '</span>            
				
				                    
				
				            </td>
				</tr>
				 	';
				 }
				 $itemsBlockHtml .= '
				 	</tbody>
				    
				    <tbody>
				                <tr>
				        <td align="right" style="padding:3px 9px" colspan="3">
				                        Subtotal                    </td>
				        <td align="right" style="padding:3px 9px">
				                        <span>'. $orderCurrencyCode . number_format($order->getSubtotal(), 2) . '</span>                    </td>
				    </tr>';
				 
				 if($order->getFeeAmount() > 0) {
				    $itemsBlockHtml .= '<tr>
				        <td align="right" style="padding:3px 9px" colspan="3">
				                        Processing Fee                    </td>
				        <td align="right" style="padding:3px 9px">
				                        <span>'. $orderCurrencyCode . number_format($order->getFeeAmount(), 2) . '</span>                    </td>
				    </tr>';
				 }
				 if($order->getCleaningfeeAmount() > 0) {
				    $itemsBlockHtml .= '<tr>
				        <td align="right" style="padding:3px 9px" colspan="3">
				                        Cleaning Fee                    </td>
				        <td align="right" style="padding:3px 9px">
				                        <span>'. $orderCurrencyCode . number_format($order->getCleaningfeeAmount(), 2) . '</span>                    </td>
				    </tr>';
				 }
				 if($order->getExtrafeeAmount() > 0) {
				    $itemsBlockHtml .= '<tr>
				        <td align="right" style="padding:3px 9px" colspan="3">
				                        Extra Fees                    </td>
				        <td align="right" style="padding:3px 9px">
				                        <span>'. $orderCurrencyCode . number_format($order->getExtrafeeAmount(), 2) . '</span>                    </td>
				    </tr>';
				 }
				    $itemsBlockHtml .= '<tr>
				        <td align="right" style="padding:3px 9px" colspan="3">
				                        <strong>Grand Total</strong>
				                    </td>
				        <td align="right" style="padding:3px 9px">
				                        <strong><span>'. $orderCurrencyCode . number_format($order->getGrandTotal(), 2) . '</span></strong>
				                    </td>
				    </tr>
				        </tbody>
				</table>
				 ';
                
                $templateId = Mage::getStoreConfig('sales_email/order/template', $storeId);
		            $emailTemplate = $mailTemplate->load($templateId);
		            $emailTemplateVariables = array(
			                'order'        => $order,
			                'billing'      => $order->getBillingAddress(),
			                'payment_html' => $paymentBlockHtml,
			                'item_html'	   => $itemsBlockHtml
			            );
			        $processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
			        
				$receiverName = $guest->getName();
				$receiverEmail = $guest->getEmail();
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
                /**** send order confirmation mail ends ****/
                mysql_close($conn);
                /********** save mails data to EC2 server ********/
                
			}
    	}
    }
    
    public function updateRefunds(Varien_Event_Observer $observer) {
    	//echo '<pre>'; print_r($observer->getEvent()); echo '</pre>'; exit;
    }
}