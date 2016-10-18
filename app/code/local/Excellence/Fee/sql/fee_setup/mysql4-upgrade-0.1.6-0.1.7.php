<?php
$installer = $this;
$installer->startSetup();

$installer->run("

	ALTER TABLE  `".$this->getTable('sales/order')."` ADD  `deposit_amount` DECIMAL( 10, 2 ) NOT NULL;
	ALTER TABLE  `".$this->getTable('sales/order')."` ADD  `base_deposit_amount` DECIMAL( 10, 2 ) NOT NULL;

	ALTER TABLE  `".$this->getTable('sales/quote_address')."` ADD  `deposit_amount` DECIMAL( 10, 2 ) NOT NULL;
	ALTER TABLE  `".$this->getTable('sales/quote_address')."` ADD  `base_deposit_amount` DECIMAL( 10, 2 ) NOT NULL;

	ALTER TABLE  `".$this->getTable('sales/order')."` ADD  `deposit_amount_invoiced` DECIMAL( 10, 2 ) NOT NULL;
	ALTER TABLE  `".$this->getTable('sales/order')."` ADD  `base_deposit_amount_invoiced` DECIMAL( 10, 2 ) NOT NULL;
	
	ALTER TABLE  `".$this->getTable('sales/invoice')."` ADD  `deposit_amount` DECIMAL( 10, 2 ) NOT NULL;
	ALTER TABLE  `".$this->getTable('sales/invoice')."` ADD  `base_deposit_amount` DECIMAL( 10, 2 ) NOT NULL;
	
	ALTER TABLE  `".$this->getTable('sales/order')."` ADD  `deposit_amount_refunded` DECIMAL( 10, 2 ) NOT NULL;
	ALTER TABLE  `".$this->getTable('sales/order')."` ADD  `base_deposit_amount_refunded` DECIMAL( 10, 2 ) NOT NULL;
	
	ALTER TABLE  `".$this->getTable('sales/creditmemo')."` ADD  `deposit_amount` DECIMAL( 10, 2 ) NOT NULL;
	ALTER TABLE  `".$this->getTable('sales/creditmemo')."` ADD  `base_deposit_amount` DECIMAL( 10, 2 ) NOT NULL;

");

$installer->endSetup();