<?php
class Si_Property_Model_Sales_Order extends Mage_Sales_Model_Order
{
	protected function _setState($state, $status = false, $comment = '',
        $isCustomerNotified = null, $shouldProtectState = false)
    {
    	// call parent parent to process default flow
        parent::_setState($state,$status,$comment,$isCustomerNotified,$shouldProtectState);
		// trigger event sales_order_status_save_after
        Mage::dispatchEvent('sales_order_status_save_after', array('order' => $this, 'state' => $state, 'status' => $status, 'comment' => $comment, 'isCustomerNotified' => $isCustomerNotified, 'shouldProtectState' => $shouldProtectState));
        
        return $this;
    }
}