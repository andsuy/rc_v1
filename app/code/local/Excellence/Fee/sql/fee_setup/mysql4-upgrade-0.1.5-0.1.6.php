<?php
$installer = $this;
$installer->startSetup();

$installer->run("

	ALTER TABLE  `".$this->getTable('sales/order')."` ADD  `extrafee_amount` DECIMAL( 10, 2 ) NOT NULL;
	ALTER TABLE  `".$this->getTable('sales/order')."` ADD  `base_extrafee_amount` DECIMAL( 10, 2 ) NOT NULL;

	ALTER TABLE  `".$this->getTable('sales/quote_address')."` ADD  `extrafee_amount` DECIMAL( 10, 2 ) NOT NULL;
	ALTER TABLE  `".$this->getTable('sales/quote_address')."` ADD  `base_extrafee_amount` DECIMAL( 10, 2 ) NOT NULL;

	ALTER TABLE  `".$this->getTable('sales/order')."` ADD  `extrafee_amount_invoiced` DECIMAL( 10, 2 ) NOT NULL;
	ALTER TABLE  `".$this->getTable('sales/order')."` ADD  `base_extrafee_amount_invoiced` DECIMAL( 10, 2 ) NOT NULL;
	
	ALTER TABLE  `".$this->getTable('sales/invoice')."` ADD  `extrafee_amount` DECIMAL( 10, 2 ) NOT NULL;
	ALTER TABLE  `".$this->getTable('sales/invoice')."` ADD  `base_extrafee_amount` DECIMAL( 10, 2 ) NOT NULL;
	
	ALTER TABLE  `".$this->getTable('sales/order')."` ADD  `extrafee_amount_refunded` DECIMAL( 10, 2 ) NOT NULL;
	ALTER TABLE  `".$this->getTable('sales/order')."` ADD  `base_extrafee_amount_refunded` DECIMAL( 10, 2 ) NOT NULL;
	
	ALTER TABLE  `".$this->getTable('sales/creditmemo')."` ADD  `extrafee_amount` DECIMAL( 10, 2 ) NOT NULL;
	ALTER TABLE  `".$this->getTable('sales/creditmemo')."` ADD  `base_extrafee_amount` DECIMAL( 10, 2 ) NOT NULL;

");

$installer->endSetup();