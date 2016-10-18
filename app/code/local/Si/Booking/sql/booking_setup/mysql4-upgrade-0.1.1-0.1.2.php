<?php
$installer = $this;
$installer->startSetup();
$installer->run("

	ALTER TABLE {$this->getTable('booking/booking')}
	ADD `host_try_count` smallint(6) NOT NULL default '0';

");

$installer->endSetup(); 