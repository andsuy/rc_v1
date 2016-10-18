<?php
class Excellence_Fee_Model_Sales_Order_Total_Invoice_Deposit extends Mage_Sales_Model_Order_Invoice_Total_Abstract
{
	public function collect(Mage_Sales_Model_Order_Invoice $invoice)
	{
		$order = $invoice->getOrder();	
		
		/*$depositAmountLeft = $order->getDepositAmount() - $order->getDepositAmountInvoiced();
		$baseDepositAmountLeft = $order->getBaseDepositAmount() - $order->getBaseDepositAmountInvoiced();
			$invoice->setGrandTotal($invoice->getGrandTotal() + $depositAmountLeft);
			$invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $baseDepositAmountLeft);

			
		$invoice->setDepositAmount($depositAmountLeft);
		$invoice->setBaseDepositAmount($baseDepositAmountLeft);*/
		
		return $this;
	}
}
