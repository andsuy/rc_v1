<?php
class Si_Property_Model_Source_Banner extends Varien_Object
{
     public function toOptionArray()
    {
        return array(
            array('value' => 1, 'label'=>'Recent Property'),
            array('value' => 2, 'label'=>'Selected Property'),
            array('value' => 3, 'label'=>'Popular Property'),
        );
    }
}