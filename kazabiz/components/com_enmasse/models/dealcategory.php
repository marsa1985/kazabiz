<?php
/*------------------------------------------------------------------------
# En Masse - Social Buying Extension 2010
# ------------------------------------------------------------------------
# By Matamko.com
# Copyright (C) 2010 Matamko.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.matamko.com
# Technical Support:  Visit our forum at www.matamko.com
-------------------------------------------------------------------------*/


// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();
jimport( 'joomla.application.component.model' );
jimport( 'joomla.html.pagination' );
class EnmasseModelDealCategory extends JModel
{
    function getCategoryByDealId($id)
	{
		$db 	= JFactory::getDBO();
		$query = ' SELECT 
		              category_id
		           FROM 
		              #__enmasse_deal_category 
		           WHERE 
		             deal_id = '.$id;
		$db->setQuery($query);
		$category = $db->loadResultArray();
		
		if ($this->_db->getErrorNum()) {
			JError::raiseError( 500, $this->_db->stderr() );
			return false;
		}
		return $category;
	}
	
    function getDealByCategoryId($id)
	{
		$db 	= JFactory::getDBO();
		$query = ' SELECT 
		              deal_id
		           FROM 
		              #__enmasse_deal_category 
		           WHERE 
		             category_id = '.$id;
		$db->setQuery($query);
		$dealIdArr = $db->loadResultArray();
		
		if ($this->_db->getErrorNum()) {
			JError::raiseError( 500, $this->_db->stderr() );
			return false;
		}
		return $dealIdArr;
	}
	
	function store($dealId,$categoryIdArr)
	{
		$currentCategorys = EnmasseModelDealCategory::getCategoryByDealId($dealId);
		if(count($currentCategorys) == 0)
		{
			for($i=0 ; $i<count($categoryIdArr); $i++)
			{
				EnmasseModelDealCategory::save($dealId,$categoryIdArr[$i]);
			}
		}
		else
		{
			for($z=0 ; $z < count($currentCategorys); $z++)
			{
				EnmasseModelDealCategory::delete($dealId,$currentCategorys[$z]);
			}
		    for($i=0 ; $i<count($categoryIdArr); $i++)
			{
				EnmasseModelDealCategory::save($dealId,$categoryIdArr[$i]);
			}
//			for($i=0 ; $i < count($categoryIdArr); $i++)
//			{
//				$available = false;
//				for($x=0 ; $x < count($currentCategorys); $x++)
//				{
//					if($currentCategorys[$x] == $categoryIdArr[$i])
//					{
//						$availabe = true;
//						break;
//					}
//				}
//				if(!$available)
//					EnmasseModelDealCategory::save($dealId,$categoryIdArr[$i]);
//			}
//			
//			for($z=0 ; $z < count($currentCategorys); $z++)
//			{
//				$available = false;
//				for($y=0; $y < count($categoryIdArr); $y++)
//				{
//					if($currentLocations[$z] == $categoryIdArr[$y])
//					{
//						$available = true;
//					}
//				}
//				
//				if(!$available)
//				{
//					EnmasseModelDealCategory::delete($dealId,$currentCategorys[$z]);
//				}
//			}
			
			
		}
		
	}
	
	function delete($dealId,$categoryId)
	{
		$db 	= JFactory::getDBO();
		$query = " DELETE FROM
		              #__enmasse_deal_category
		           WHERE 
		            deal_id=$dealId AND category_id=$categoryId ";
		$db->setQuery($query);
		$db->query();
	}
	
	function save($dealId,$categoryId)
	{
		$db 	= JFactory::getDBO();
		$query = " INSERT INTO 
		              #__enmasse_deal_category
		           VALUES 
		            ('null',$dealId,$categoryId) ";
		$db->setQuery($query);
		$db->query();
	}
}
?>