<?php
/**
  * Table 'booking/booking' values  for `booking_status`
  * array('0' => 'Pending', '1' => 'Confirmed', '2' => 'Canceled', '3' => 'Refunded', '4' => Cancelled by Guest)
  *
  * Table 'booking/booking' values  for `key_status`
  * array('0' => 'Not Generatd', '1' => 'Sent to guest', '2' => 'Confirmed', '3' => 'Blocked')
  *
  */

$installer = $this;
$installer->startSetup();
$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('booking/booking')};
CREATE TABLE {$this->getTable('booking/booking')} (
  `booking_id` int(11) unsigned NOT NULL auto_increment,
  `product_id` int(10) default NULL,
  `host_id` int(10) default NULL,
  `guest_id` int(10) default NULL,
  `checkin` date NULL,
  `checkout` date NULL,
  `accomodates` smallint(6) NULL,
  `subtotal` decimal(12,4) NOT NULL default '0.0000',
  `host_fee` decimal(12,4) NOT NULL default '0.0000',
  `service_fee` decimal(12,4) NOT NULL default '0.0000',
  `total` decimal(12,4) NOT NULL default '0.0000',
  `order_id` int(10) default NULL,
  `order_item_id` int(10) default NULL,
  `order_status` varchar(64) default NULL,
  `booking_status` smallint(6) NOT NULL default '0',
  `secure_key` varchar(32) NOT NULL default '',
  `key_status` smallint(6) NOT NULL default '0',
  `base_currency_code` varchar(32) NOT NULL default '',
  `order_currency_code` varchar(32) NOT NULL default '',
  `created_at` datetime NULL,
  `update_at` datetime NULL,
  PRIMARY KEY (`booking_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup();