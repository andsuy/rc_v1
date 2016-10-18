<?php
class Excellence_Fee_Model_Sales_Order_Total_Creditmemo_Cleaningfee extends Mage_Sales_Model_Order_Creditmemo_Total_Abstract
{
	public function collect(Mage_Sales_Model_Order_Creditmemo $creditmemo)
	{
		$order = $creditmemo->getOrder();

		$cleaningfeeAmountLeft = $order->getCleaningfeeAmount() - $order->getCleaningfeeAmountRefunded();
		$baseCleaningfeeAmountLeft = $order->getBaseCleaningfeeAmount() - $order->getBaseCleaningfeeAmountRefunded();
		if ($baseCleaningfeeAmountLeft > 0) {
			$creditmemo->setGrandTotal($creditmemo->getGrandTotal() + $cleaningfeeAmountLeft);
			$creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() + $baseCleaningfeeAmountLeft);
			$creditmemo->setCleaningfeeAmount($cleaningfeeAmountLeft);
			$creditmemo->setBaseCleaningfeeAmount($baseCleaningfeeAmountLeft);
		}
		
		return $this;
	}
}
