<?php   
class Si_Property_Block_Index extends Mage_Core_Block_Template{   
    public function getHomepagePropertyBanner()
    {
        $banner_count = (int) Mage::getStoreConfig('property_section/custom_banner/banner_count');
        if (!$banner_count) { $banner_count = 10; }
        $homepage_banner = Mage::getStoreConfig('property_section/custom_banner/homepage_banner');
        switch($homepage_banner) {
        case 1:
               $_productCollection = Mage::getModel('property/property')->getPropertyCollection()
                        ->addAttributeToSelect('*')
                        ->addAttributeToFilter('status', array('eq' => 1))
                        ->addAttributeToFilter('property_approved',array('eq' => 1))
                        ->setOrder('created_at', 'desc')        
                        ->setPageSize($banner_count);
         break;
       case 2:
               $_productCollection = Mage::getModel('property/property')->getPropertyCollection()
                        ->addAttributeToSelect('*')
                        ->addAttributeToFilter('status', array('eq' => 1))
                        ->addAttributeToFilter('banner', array('eq' => 1))
                         ->addAttributeToFilter('property_approved',array('eq' => 1)) 
                        ->setOrder('created_at', 'desc')        
                        ->setPageSize($banner_count);
         break;
        case 3:
                $_productCollection = Mage::getResourceModel('reports/product_collection')
                     ->addAttributeToSelect('*')
                     ->addOrderedQty()
                     ->addAttributeToFilter('status', array('eq' => 1))
                     ->addAttributeToFilter('type_id', array('eq' => 'virtual'))
                     ->addAttributeToFilter('attribute_set_id', array('eq' => 9))
                     ->addAttributeToFilter('name', array('neq' => ''))
                      ->addAttributeToFilter('property_approved',array('eq' => 1))
                      ->setOrder('ordered_qty', 'DESC')
                     ->setPageSize($banner_count);
          break;
        }
        if(!empty($_productCollection->count()) && $_productCollection->count() > 0) {
            return $_productCollection;
        } else {
        	return 0;
        }
    }
}