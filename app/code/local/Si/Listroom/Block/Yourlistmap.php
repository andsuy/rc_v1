<?php
class Si_Listroom_Block_Yourlistmap extends Mage_Catalog_Block_Product_Abstract
{
    protected function _prepareLayout()
    {
        return parent::_prepareLayout();
    }
    
    public function getAdvanceSearchResult(){
        $positions = $this->getRequest()->getParam('searchPostions');
		$pageno = $this->getRequest()->getParam('pageno');

		$collection = Mage::getModel('listroom/listroom')->getCollection();
		if(count($positions) > 0) {
			$sqlClause = '';
			foreach ($positions as $_pos) {
				$posArray = explode(',', $_pos);
				
				$latLimit = (strlen(substr(strrchr($posArray[0], "."), 1)) > 7) ? 7 : strlen(substr(strrchr($posArray[0], "."), 1));
				$lntLimit = (strlen(substr(strrchr($posArray[1], "."), 1)) > 7) ? 7 : strlen(substr(strrchr($posArray[1], "."), 1));
				
				$sqlClause .= "(`room_lat` = '" . (float)trim(number_format(ltrim($posArray[0],'('), $latLimit, '.', '')) . "' AND `room_lnt` = '" . (float)trim(number_format(rtrim($posArray[1],')'), $lntLimit, '.', '')) . "') OR ";
			}
        	$collection->getSelect()->where(rtrim($sqlClause, ' OR '));
		}
		//return $collection->getSelect();
		$collection->setCurPage($pageno)->setPageSize(10);
        //return $collection->getSelect();
        //return $positions;
        return $collection;
    }
    
}