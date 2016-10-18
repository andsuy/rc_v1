<?php
/**
  * Table 'property/propertyavailablity' values  for `booking_type`
  * array('1' => 'not available', '2' => 'booked')
  *
  */

$installer = $this;
$installer->startSetup();
$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('property/propertyavailablity')};
CREATE TABLE {$this->getTable('property/propertyavailablity')} (
  `available_id` int(11) unsigned NOT NULL auto_increment,
  `product_id` int(10) NOT NULL default '0',
  `booking_type` smallint(6) NULL,
  `booking_year` smallint(6) NULL,
  `booking_month` smallint(6) NULL,
  `block_date` varchar(255) NOT NULL default '',
  `booking_price` decimal(12,4) NOT NULL default '0.0000',
  `created_at` datetime NULL,
  `update_at` datetime NULL,
  PRIMARY KEY (`available_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup();