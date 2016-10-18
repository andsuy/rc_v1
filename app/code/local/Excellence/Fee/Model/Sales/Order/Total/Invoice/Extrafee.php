<?php
class Excellence_Fee_Model_Sales_Order_Total_Invoice_Extrafee extends Mage_Sales_Model_Order_Invoice_Total_Abstract
{
	public function collect(Mage_Sales_Model_Order_Invoice $invoice)
	{
		$order = $invoice->getOrder();	
		
		$extrafeeAmountLeft = $order->getExtrafeeAmount() - $order->getExtrafeeAmountInvoiced();
		$baseExtrafeeAmountLeft = $order->getBaseExtrafeeAmount() - $order->getBaseExtrafeeAmountInvoiced();
		//if (abs($baseExtrafeeAmountLeft) < $invoice->getBaseGrandTotal()) {
			$invoice->setGrandTotal($invoice->getGrandTotal() + $extrafeeAmountLeft);
			$invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $baseExtrafeeAmountLeft);
		/*} else {
			$extrafeeAmountLeft = $invoice->getGrandTotal() * -1;
			$baseExtrafeeAmountLeft = $invoice->getBaseGrandTotal() * -1;

			$invoice->setGrandTotal(0);
			$invoice->setBaseGrandTotal(0);
		}*/
			
		$invoice->setExtrafeeAmount($extrafeeAmountLeft);
		$invoice->setBaseExtrafeeAmount($baseExtrafeeAmountLeft);
		
		return $this;
	}
}
