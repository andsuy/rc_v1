<?php
class Excellence_Fee_Model_Sales_Order_Total_Creditmemo_Deposit extends Mage_Sales_Model_Order_Creditmemo_Total_Abstract
{
	public function collect(Mage_Sales_Model_Order_Creditmemo $creditmemo)
	{
		$order = $creditmemo->getOrder();

		/*$depositAmountLeft = $order->getDepositAmount() - $order->getDepositAmountRefunded();
		$baseDepositAmountLeft = $order->getBaseDepositAmount() - $order->getBaseDepositAmountRefunded();
		if ($baseDepositAmountLeft > 0) {
			$creditmemo->setGrandTotal($creditmemo->getGrandTotal() + $depositAmountLeft);
			$creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() + $baseDepositAmountLeft);
			$creditmemo->setDepositAmount($depositAmountLeft);
			$creditmemo->setBaseDepositAmount($baseDepositAmountLeft);
		}*/
		
		return $this;
	}
}
