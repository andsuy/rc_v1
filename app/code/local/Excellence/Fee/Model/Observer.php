<?php
class Excellence_Fee_Model_Observer{
	public function invoiceSaveAfter(Varien_Event_Observer $observer)
	{
		$invoice = $observer->getEvent()->getInvoice();
		if ($invoice->getBaseFeeAmount()) {
			$order = $invoice->getOrder();
			$order->setFeeAmountInvoiced($order->getFeeAmountInvoiced() + $invoice->getFeeAmount());
			$order->setBaseFeeAmountInvoiced($order->getBaseFeeAmountInvoiced() + $invoice->getBaseFeeAmount());
		}
		
		if ($invoice->getBaseCleaningfeeAmount()) {
			$order = $invoice->getOrder();
			$order->setCleaningfeeAmountInvoiced($order->getCleaningfeeAmountInvoiced() + $invoice->getCleaningfeeAmount());
			$order->setBaseCleaningfeeAmountInvoiced($order->getBaseCleaningfeeAmountInvoiced() + $invoice->getBaseCleaningfeeAmount());
		}
		
		if ($invoice->getBaseExtrafeeAmount()) {
			$order = $invoice->getOrder();
			$order->setExtrafeeAmountInvoiced($order->getExtrafeeAmountInvoiced() + $invoice->getExtrafeeAmount());
			$order->setBaseExtrafeeAmountInvoiced($order->getBaseExtrafeeAmountInvoiced() + $invoice->getBaseExtrafeeAmount());
		}
		
		/*if ($invoice->getBaseDepositAmount()) {
			$order = $invoice->getOrder();
			$order->setDepositAmountInvoiced($order->getDepositAmountInvoiced() + $invoice->getDepositAmount());
			$order->setBaseDepositAmountInvoiced($order->getBaseDepositAmountInvoiced() + $invoice->getBaseDepositAmount());
		}*/
		
		return $this;
	}
	public function creditmemoSaveAfter(Varien_Event_Observer $observer)
	{
		/* @var $creditmemo Mage_Sales_Model_Order_Creditmemo */
		$creditmemo = $observer->getEvent()->getCreditmemo();
		if ($creditmemo->getFeeAmount()) {
			$order = $creditmemo->getOrder();
			$order->setFeeAmountRefunded($order->getFeeAmountRefunded() + $creditmemo->getFeeAmount());
			$order->setBaseFeeAmountRefunded($order->getBaseFeeAmountRefunded() + $creditmemo->getBaseFeeAmount());
		}
		if ($creditmemo->getCleaningfeeAmount()) {
			$order = $creditmemo->getOrder();
			$order->setCleaningfeeAmountRefunded($order->getCleaningfeeAmountRefunded() + $creditmemo->getCleaningfeeAmount());
			$order->setBaseCleaningfeeAmountRefunded($order->getBaseCleaningfeeAmountRefunded() + $creditmemo->getBaseCleaningfeeAmount());
		}
		if ($creditmemo->getExtrafeeAmount()) {
			$order = $creditmemo->getOrder();
			$order->setExtrafeeAmountRefunded($order->getExtrafeeAmountRefunded() + $creditmemo->getExtrafeeAmount());
			$order->setBaseExtrafeeAmountRefunded($order->getBaseExtrafeeAmountRefunded() + $creditmemo->getBaseExtrafeeAmount());
		}
		/*if ($creditmemo->getDepositAmount()) {
			$order = $creditmemo->getOrder();
			$order->setDepositAmountRefunded($order->getDepositAmountRefunded() + $creditmemo->getDepositAmount());
			$order->setBaseDepositAmountRefunded($order->getBaseDepositAmountRefunded() + $creditmemo->getBaseDepositAmount());
		}*/
		return $this;
	}
	public function updatePaypalTotal($evt){
		$cart = $evt->getPaypalCart();
		//echo '<pre>'; print_r($cart->getSalesEntity()->getData()); echo '</pre>';
		/*$totalfeeAmount = 0.00;
		$totalfeeAmount += $cart->getSalesEntity()->getFeeAmount();
		$totalfeeAmount += $cart->getSalesEntity()->getCleaningfeeAmount();
		$totalfeeAmount += $cart->getSalesEntity()->getExtrafeeAmount();
		$totalfeeAmount += $cart->getSalesEntity()->getDepositAmount();*/
		
		//echo 'Total Fee = ' . $totalfeeAmount; exit;
		//$cart->updateTotal(Mage_Paypal_Model_Cart::TOTAL_SUBTOTAL, $totalfeeAmount);
		
		$baseTotalfeeAmount = 0.00;
		$baseTotalfeeAmount += $cart->getSalesEntity()->getBaseFeeAmount();
		$baseTotalfeeAmount += $cart->getSalesEntity()->getBaseCleaningfeeAmount();
		$baseTotalfeeAmount += $cart->getSalesEntity()->getBaseExtrafeeAmount();
		//$baseTotalfeeAmount += $cart->getSalesEntity()->getBaseDepositAmount();
		$cart->updateTotal('base_subtotal', $baseTotalfeeAmount);
		
		$cart->updateTotal(Mage_Paypal_Model_Cart::TOTAL_SUBTOTAL, $baseTotalfeeAmount);
	}
}