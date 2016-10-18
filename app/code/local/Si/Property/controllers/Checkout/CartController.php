<?php
require_once 'Mage/Checkout/controllers/CartController.php';
class Si_Property_Checkout_CartController extends Mage_Checkout_CartController {

	public function addAction()
    {
        if (!$this->_validateFormKey()) {
            $this->_goBack();
            return;
        }
        $cart   = $this->_getCart();
        $params = $this->getRequest()->getParams();
        
        try {
            $product = $this->_initProduct();
            $related = $this->getRequest()->getParam('related_product');
            
        	if(Mage::getStoreConfig('property_section/general/enable')) {
				$cartItems = Mage::helper('checkout/cart')->getCart()->getItemsCount();
				if ($cartItems > 0) {
					//clear old product
                    Mage::getSingleton('checkout/cart')->truncate();
				}
		
        		//if (isset($params['qty'])) {
	                $filter = new Zend_Filter_LocalizedToNormalized(
	                    array('locale' => Mage::app()->getLocale()->getLocaleCode())
	                );
                    
	                $start = strtotime($params['from']);
                    $end = strtotime($params['to']);
                    /*$block = Mage::helper('property')->checkavailAction($start, $end, $product->getId());
                    if($block == 0) {
                    	$this->_goBack();
            			return;
                    }*/
                    $daysBetween = ceil(abs($end - $start) / 86400);
	                $params['qty'] = $filter->filter($daysBetween);
	            //}
        	} else {
	            if (isset($params['qty'])) {
	                $filter = new Zend_Filter_LocalizedToNormalized(
	                    array('locale' => Mage::app()->getLocale()->getLocaleCode())
	                );
	                $params['qty'] = $filter->filter($params['qty']);
	            }
        	}

            
            if(Mage::getStoreConfig('property_section/general/enable')) {
            	$price = $product->getPriceModel()->getFinalPrice($params['qty'], $product);
            	$serviceFeePercentage = Mage::getStoreConfig('property_section/general/processing_fee');
            	//echo '<pre>'; print_r($params); echo '</pre>';
            	$from = $params["from"];
        		$to = $params["to"];
            	$start = strtotime($from);
		        $end = strtotime($to);
		        $daysBetween = ceil(abs($end - $start) / 86400);
				$subtotal = 0.00;
		        for($i=0; $i<$daysBetween; $i++) {
					$day = date('d',strtotime($from . "+$i days"));
					$month = date('n',strtotime($from . "+$i days"));
					$year = date('Y',strtotime($from . "+$i days"));
					$checkSpCollection = Mage::getModel('property/propertyspecial')->getCollection()
						->addFieldToFilter('product_id', $product->getId())
						->addFieldToFilter('special_year', $year)
						->addFieldToFilter('special_month', $month)
						->addFieldToFilter('special_date', $day);
						//echo $checkSpCollection->getSelect();
						//echo '<br>';
					if($checkSpCollection->count() > 0) {
						foreach ($checkSpCollection as $_sp) {
							$price = $_sp->getSpecialPrice();
							break;
						}
					} else {
						$price = $product->getPriceModel()->getFinalPrice($params['qty'], $product);
					}
			
					$subtotal += $price;
		        }
		        //echo $subtotal; exit;
        		Mage::getSingleton('core/session')->setSubTotal($subtotal);
            	if(!empty($serviceFeePercentage)) {
	            	$serviceFee = (($subtotal * $serviceFeePercentage) / 100);
	            	Mage::getSingleton('core/session')->setServiceFee($serviceFee);
            	}
            	if($product->getCleaningFee()) {
            		Mage::getSingleton('core/session')->setCleaningFee($product->getCleaningFee());
            	} else {
            		Mage::getSingleton('core/session')->setCleaningFee(0.00);
            	}
            	if($product->getExtraFees()) {
            		Mage::getSingleton('core/session')->setExtraFee($product->getExtraFees());
            	} else {
            		Mage::getSingleton('core/session')->setExtraFee(0.00);
            	}
            	if($product->getDepositAmount()) {
            		Mage::getSingleton('core/session')->setDeposit($product->getDepositAmount());
            	} else {
            		Mage::getSingleton('core/session')->setDeposit(0.00);
            	}
            }
            /**
             * Check product availability
             */
            if (!$product) {
                $this->_goBack();
                return;
            }

            $cart->addProduct($product, $params);
            if (!empty($related)) {
                $cart->addProductsByIds(explode(',', $related));
            }

            $cart->save();

            $this->_getSession()->setCartWasUpdated(true);

            /**
             * @todo remove wishlist observer processAddToCart
             */
            Mage::dispatchEvent('checkout_cart_add_product_complete',
                array('product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse())
            );

            if (!$this->_getSession()->getNoCartRedirect(true)) {
                if (!$cart->getQuote()->getHasError()) {
                    $message = $this->__('%s was added to your shopping cart.', Mage::helper('core')->escapeHtml($product->getName()));
                    $this->_getSession()->addSuccess($message);
                }
                $this->_goBack();
            }
        } catch (Mage_Core_Exception $e) {
            if ($this->_getSession()->getUseNotice(true)) {
                $this->_getSession()->addNotice(Mage::helper('core')->escapeHtml($e->getMessage()));
            } else {
                $messages = array_unique(explode("\n", $e->getMessage()));
                foreach ($messages as $message) {
                    $this->_getSession()->addError(Mage::helper('core')->escapeHtml($message));
                }
            }

            $url = $this->_getSession()->getRedirectUrl(true);
            if ($url) {
                $this->getResponse()->setRedirect($url);
            } else {
                $this->_redirectReferer(Mage::helper('checkout/cart')->getCartUrl());
            }
        } catch (Exception $e) {
            $this->_getSession()->addException($e, $this->__('Cannot add the item to shopping cart.'));
            Mage::logException($e);
            $this->_goBack();
        }
    }
}
