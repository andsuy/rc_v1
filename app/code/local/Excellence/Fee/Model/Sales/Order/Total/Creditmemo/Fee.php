<?php
class Excellence_Fee_Model_Sales_Order_Total_Creditmemo_Fee extends Mage_Sales_Model_Order_Creditmemo_Total_Abstract
{
	public function collect(Mage_Sales_Model_Order_Creditmemo $creditmemo)
	{
		$order = $creditmemo->getOrder();
		$feeAmountLeft = $order->getFeeAmount() - $order->getFeeAmountRefunded();
		$basefeeAmountLeft = $order->getBaseFeeAmount() - $order->getBaseFeeAmountRefunded();
		if ($basefeeAmountLeft > 0) {
			$creditmemo->setGrandTotal($creditmemo->getGrandTotal() + $feeAmountLeft);
			$creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() + $basefeeAmountLeft);
			$creditmemo->setFeeAmount($feeAmountLeft);
			$creditmemo->setBaseFeeAmount($basefeeAmountLeft);
		}
		
		return $this;
	}
}
