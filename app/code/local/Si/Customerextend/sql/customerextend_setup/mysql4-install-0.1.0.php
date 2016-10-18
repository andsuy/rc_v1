<?php
/*
 * message_type = 1 (property contact form), message_type = 2 (pmb), 3 (room contact form)
 *
 *
 */
$installer = $this;
$installer->startSetup();
$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('customerextend/customerinfo')};
CREATE TABLE {$this->getTable('customerextend/customerinfo')} (
  `pc_id` int(11) unsigned NOT NULL auto_increment,
  `customer_id` int(10) NULL,
  `image_name` varchar(255) NOT NULL default '',
  `response_time` varchar(255) NOT NULL default '',
  `host_details` text NOT NULL default '',
  `created_at` datetime NULL,
  `update_at` datetime NULL,
  PRIMARY KEY (`pc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS {$this->getTable('customerextend/cuspropmsg')};
CREATE TABLE {$this->getTable('customerextend/cuspropmsg')} (
  `prop_msg_id` int(11) unsigned NOT NULL auto_increment,
  `sender_id` int(10) NULL,
  `reciever_id` int(10) NULL,
  `product_id` int(10) NULL,
  `checkin` date NULL,
  `checkout` date NULL,
  `guest` smallint(6) NULL,
  `message_type` smallint(6) NULL,
  `message` text NOT NULL default '',
  `can_call` smallint(6) NULL,
  `contact_number` varchar(32) NOT NULL default '',
  `timezone` text NOT NULL default '',
  `sender_read` smallint(6) NOT NULL default '0',
  `reciever_read` smallint(6) NOT NULL default '0',
  `sender_delete` smallint(6) NOT NULL default '0',
  `reciever_delete` smallint(6) NOT NULL default '0',  
  `created_at` datetime NULL,
  `update_at` datetime NULL,
  PRIMARY KEY (`prop_msg_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 