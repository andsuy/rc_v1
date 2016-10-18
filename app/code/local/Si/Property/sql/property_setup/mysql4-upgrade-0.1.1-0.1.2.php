<?php
$installer = $this;
$installer->startSetup();
$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('property/propertyspecial')};
CREATE TABLE {$this->getTable('property/propertyspecial')} (
  `special_id` int(11) unsigned NOT NULL auto_increment,
  `product_id` int(10) NOT NULL default '0',
  `special_year` smallint(6) NULL,
  `special_month` smallint(6) NULL,
  `special_date` varchar(255) NOT NULL default '',
  `special_price` decimal(12,4) NOT NULL default '0.0000',
  `created_at` datetime NULL,
  `update_at` datetime NULL,
  PRIMARY KEY (`special_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

$installer->endSetup(); 