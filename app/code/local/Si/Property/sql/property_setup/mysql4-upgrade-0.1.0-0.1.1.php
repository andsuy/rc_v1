<?php
$installer = $this;
$installer->startSetup();
$installer->run("


-- DROP TABLE IF EXISTS {$this->getTable('property/propertylocation')};
CREATE TABLE {$this->getTable('property/propertylocation')} (
  `location_id` int(11) unsigned NOT NULL auto_increment,
  `product_id` int(10) NOT NULL default '0',
  `latitude` varchar(255) NOT NULL default '',
  `longitude` varchar(255) NOT NULL default '',
  `created_at` datetime NULL,
  `update_at` datetime NULL,
  PRIMARY KEY (`location_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

$installer->endSetup(); 