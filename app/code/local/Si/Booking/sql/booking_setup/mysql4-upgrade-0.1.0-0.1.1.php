<?php
$installer = $this;
$installer->startSetup();
$installer->run("

	ALTER TABLE {$this->getTable('booking/booking')}
	ADD `base_subtotal` decimal(12,4) NOT NULL default '0.0000',
	ADD `base_host_fee` decimal(12,4) NOT NULL default '0.0000',
	ADD `base_service_fee` decimal(12,4) NOT NULL default '0.0000',
	ADD `base_total` decimal(12,4) NOT NULL default '0.0000';

");

$installer->endSetup(); 