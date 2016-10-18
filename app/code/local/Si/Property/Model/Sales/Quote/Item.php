<?php
class Si_Property_Model_Sales_Quote_Item extends Mage_Sales_Model_Quote_Item
{
	public function calcRowTotal() {
        $total_price = Mage::getSingleton('core/session')->getSubTotal();
        $totalPriceCurrency = Mage::helper('directory')->currencyConvert($total_price, Mage::app()->getStore()->getBaseCurrencyCode(), Mage::app()->getStore()->getCurrentCurrencyCode());
        $this->setRowTotal( $totalPriceCurrency );
        $this->setBaseRowTotal( $total_price );
        return $this;
    }
}