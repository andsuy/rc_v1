<?php
$installer = $this;
$installer->startSetup();

$installer->run("

	ALTER TABLE  `".$this->getTable('sales/order')."` ADD  `cleaningfee_amount` DECIMAL( 10, 2 ) NOT NULL;
	ALTER TABLE  `".$this->getTable('sales/order')."` ADD  `base_cleaningfee_amount` DECIMAL( 10, 2 ) NOT NULL;

	ALTER TABLE  `".$this->getTable('sales/quote_address')."` ADD  `cleaningfee_amount` DECIMAL( 10, 2 ) NOT NULL;
	ALTER TABLE  `".$this->getTable('sales/quote_address')."` ADD  `base_cleaningfee_amount` DECIMAL( 10, 2 ) NOT NULL;

	ALTER TABLE  `".$this->getTable('sales/order')."` ADD  `cleaningfee_amount_invoiced` DECIMAL( 10, 2 ) NOT NULL;
	ALTER TABLE  `".$this->getTable('sales/order')."` ADD  `base_cleaningfee_amount_invoiced` DECIMAL( 10, 2 ) NOT NULL;
	
	ALTER TABLE  `".$this->getTable('sales/invoice')."` ADD  `cleaningfee_amount` DECIMAL( 10, 2 ) NOT NULL;
	ALTER TABLE  `".$this->getTable('sales/invoice')."` ADD  `base_cleaningfee_amount` DECIMAL( 10, 2 ) NOT NULL;
	
	ALTER TABLE  `".$this->getTable('sales/order')."` ADD  `cleaningfee_amount_refunded` DECIMAL( 10, 2 ) NOT NULL;
	ALTER TABLE  `".$this->getTable('sales/order')."` ADD  `base_cleaningfee_amount_refunded` DECIMAL( 10, 2 ) NOT NULL;
	
	ALTER TABLE  `".$this->getTable('sales/creditmemo')."` ADD  `cleaningfee_amount` DECIMAL( 10, 2 ) NOT NULL;
	ALTER TABLE  `".$this->getTable('sales/creditmemo')."` ADD  `base_cleaningfee_amount` DECIMAL( 10, 2 ) NOT NULL;

");

$installer->endSetup();