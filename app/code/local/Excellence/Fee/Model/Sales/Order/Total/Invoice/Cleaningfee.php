<?php
class Excellence_Fee_Model_Sales_Order_Total_Invoice_Cleaningfee extends Mage_Sales_Model_Order_Invoice_Total_Abstract
{
	public function collect(Mage_Sales_Model_Order_Invoice $invoice)
	{
		$order = $invoice->getOrder();	
		
		$cleaningfeeAmountLeft = $order->getCleaningfeeAmount() - $order->getCleaningfeeAmountInvoiced();
		$baseCleaningfeeAmountLeft = $order->getBaseCleaningfeeAmount() - $order->getBaseCleaningfeeAmountInvoiced();
		//if (abs($baseCleaningfeeAmountLeft) < $invoice->getBaseGrandTotal()) {
			$invoice->setGrandTotal($invoice->getGrandTotal() + $cleaningfeeAmountLeft);
			$invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $baseCleaningfeeAmountLeft);
		/*} else {
			$cleaningfeeAmountLeft = $invoice->getGrandTotal() * -1;
			$baseCleaningfeeAmountLeft = $invoice->getBaseGrandTotal() * -1;

			$invoice->setGrandTotal(0);
			$invoice->setBaseGrandTotal(0);
		}*/
			
		$invoice->setCleaningfeeAmount($cleaningfeeAmountLeft);
		$invoice->setBaseCleaningfeeAmount($baseCleaningfeeAmountLeft);
		
		return $this;
	}
}
