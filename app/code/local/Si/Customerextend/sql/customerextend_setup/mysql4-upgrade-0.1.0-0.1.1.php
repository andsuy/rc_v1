<?php
$installer = $this;
$installer->startSetup();
$installer->run("

	ALTER TABLE {$this->getTable('customerextend/customerinfo')} ADD `currency` varchar(16) NOT NULL default '';

");

$installer->endSetup(); 