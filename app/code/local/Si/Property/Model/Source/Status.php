<?php
class Si_Property_Model_Source_Status extends Varien_Object
{
     public function toOptionArray()
    {
        return array(
            array('value' => 0, 'label'=>Mage::helper('adminhtml')->__('No')),
            array('value' => 1, 'label'=>Mage::helper('adminhtml')->__('Yes')),
        );
    }
}