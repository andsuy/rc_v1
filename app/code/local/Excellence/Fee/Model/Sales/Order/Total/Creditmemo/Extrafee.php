<?php
class Excellence_Fee_Model_Sales_Order_Total_Creditmemo_Extrafee extends Mage_Sales_Model_Order_Creditmemo_Total_Abstract
{
	public function collect(Mage_Sales_Model_Order_Creditmemo $creditmemo)
	{
		$order = $creditmemo->getOrder();

		$extrafeeAmountLeft = $order->getExtrafeeAmount() - $order->getExtrafeeAmountRefunded();
		$baseExtrafeeAmountLeft = $order->getBaseExtrafeeAmount() - $order->getBaseExtrafeeAmountRefunded();
		if ($baseExtrafeeAmountLeft > 0) {
			$creditmemo->setGrandTotal($creditmemo->getGrandTotal() + $extrafeeAmountLeft);
			$creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() + $baseExtrafeeAmountLeft);
			$creditmemo->setExtrafeeAmount($extrafeeAmountLeft);
			$creditmemo->setBaseExtrafeeAmount($baseExtrafeeAmountLeft);
		}
		
		return $this;
	}
}
