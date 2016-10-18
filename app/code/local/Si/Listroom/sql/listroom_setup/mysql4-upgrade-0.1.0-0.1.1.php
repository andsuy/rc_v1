<?php
$installer = $this;
$installer->startSetup();
$installer->run("


ALTER TABLE {$this->getTable('listroom')} ADD `accommodates` SMALLINT(6) NOT NULL default '0', 
ADD `from` date default NULL, 
ADD `to` date default NULL;

");

$installer->endSetup();