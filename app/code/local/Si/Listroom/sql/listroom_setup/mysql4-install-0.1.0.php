<?php
$installer = $this;
$installer->startSetup();
$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('listroom')};
CREATE TABLE {$this->getTable('listroom')} (
  `listroom_id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `user_id` int(11) unsigned NULL,
  `property_type` int(11) unsigned NULL,
  `room_type` int(11) unsigned NULL,
  `amenity` text NOT NULL default '',
  `budget_min` decimal(12,4) NOT NULL default '0.0000',
  `budget_max` decimal(12,4) NOT NULL default '0.0000',
  `description` text NOT NULL default '',
  `keywords` text NOT NULL default '',
  `locality` text NOT NULL default '',
  `city` varchar(255) NOT NULL default '',
  `state` varchar(255) NOT NULL default '',
  `country` varchar(255) NOT NULL default '',
  `status` smallint(6) NOT NULL default '0',
  `room_lat` varchar(255) NOT NULL default '',
  `room_lnt` varchar(255) NOT NULL default '',
  `created_at` datetime NULL,
  `update_at` datetime NULL,
  PRIMARY KEY (`listroom_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 