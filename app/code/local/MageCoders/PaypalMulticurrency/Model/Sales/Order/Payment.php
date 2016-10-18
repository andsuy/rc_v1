<?php
/**
 * @author   MageCoders
 * @package    MageCoders_PaypalMulticurrency
 */
class MageCoders_PaypalMulticurrency_Model_Sales_Order_Payment extends Mage_Sales_Model_Order_Payment{

 /**
     * Decide whether authorization transaction may close (if the amount to capture will cover entire order)
     * @param float $amountToCapture
     * @return bool
     */
	protected $_helper; 
	 
    protected function _isCaptureFinal($amountToCapture)
    {
		$this->_helper = Mage::helper('paypalmulticurrency');
		
		$amountToCapture = $this->_helper->convertCurrency($amountToCapture);
		
        $amountToCapture = $this->_formatAmount($amountToCapture, true);
		
		$currency = $this->getOrder()->getOrderCurrencyCode();
		$grandTotal = $this->_helper->convertCurrency($this->getOrder()->getGrandTotal(),$currency);
		
        $orderGrandTotal = $this->_formatAmount($grandTotal, true);
     	if ($orderGrandTotal == $this->_formatAmount($this->_helper->convertCurrency($this->getBaseAmountPaid()), true) + $amountToCapture) {
	        if (false !== $this->getShouldCloseParentTransaction()) {
                $this->setShouldCloseParentTransaction(true);
            }
            return true;
        }
        return false;
    }

}