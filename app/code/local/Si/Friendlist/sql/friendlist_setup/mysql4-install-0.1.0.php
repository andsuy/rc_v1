<?php

$installer = $this;
$installer->startSetup();
$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('friendlist/friendlist')};
CREATE TABLE {$this->getTable('friendlist/friendlist')} (
  `friend_id` int(11) unsigned NOT NULL auto_increment,
  `friend_fb_id` int(10) default NULL,
  `customer_id` int(10) default NULL,
  `friend_name` varchar(255) NOT NULL default '',
  `created_at` datetime NULL,
  `update_at` datetime NULL,
  PRIMARY KEY (`friend_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup();