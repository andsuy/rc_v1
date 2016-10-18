<?php
class Si_Property_Block_Blockcalendar extends Mage_Core_Block_Template
{
    public function _prepareLayout()
    {  
    	$bundleBlock = $this->getLayout()->getBlock('property/blockcalendar');
    	if ($bundleBlock) {
            $this->setTemplate('property/calendar.phtml');
        }
    }
}