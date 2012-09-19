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


defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.application.component.view');
require_once( JPATH_ADMINISTRATOR . DS ."components". DS ."com_enmasse". DS ."toolbar.enmasse.html.php");

class EnmasseViewSaleReports extends JView
{
	function display($tpl = null)
	{ 
		$task = JRequest::getWord('task');

			TOOLBAR_enmasse::_SMENU();
			$nNumberOfDeals = JModel::getInstance('deal','enmasseModel')->countAll();
			if($nNumberOfDeals==0)
			{
				TOOLBAR_enmasse::_DEAL_EMPTY();
			}
			else
			{
				TOOLBAR_enmasse::_SALE_REPORTS();
			}
			
            $filter 	= JRequest::getVar('filter');
            $filter['code'] = isset($filter['code']) ? $filter['code'] : '';
            $filter['name'] = isset($filter['name']) ? $filter['name'] : '';
            $filter['saleperson_id'] = isset($filter['saleperson_id']) ? $filter['saleperson_id'] : '';
            $filter['merchant_id'] = isset($filter['merchant_id']) ? $filter['merchant_id'] : '';
            
            $filter['fromdate'] = isset($filter['fromdate']) ? $filter['fromdate'] : '';
            $filter['todate'] = isset($filter['todate']) ? $filter['todate'] : '';
            
            $currency_prefix	= JModel::getInstance('setting','enmasseModel')->getCurrencyPrefix();
            
			$dealList 		= JModel::getInstance('salereports','enmasseModel')->search($filter['code'], $filter['name'],$filter['saleperson_id'],$filter['merchant_id'],$filter['fromdate'], $filter['todate']);
			
			/// load pagination
			$pagination =JModel::getInstance('salereports','enmasseModel')->getPagination($filter['code'], $filter['name'],$filter['saleperson_id'],$filter['merchant_id'],$filter['fromdate'], $filter['todate']);
			$state = $this->get( 'state' );

			for($i=0; $i < count($dealList); $i++)
			{
				$dealCategoryIdList = JModel::getInstance('dealcategory','enmasseModel')->getCategoryByDealId($dealList[$i]->id);
				$dealLocationIdList = JModel::getInstance('deallocation','enmasseModel')->getLocationByDealId($dealList[$i]->id);
				
				//----------------------------------------------
				// get list of category name
				if(count($dealCategoryIdList)!=0)
					$categoryList = JModel::getInstance('category','enmasseModel')->getCategoryListInArrId($dealCategoryIdList);
				else
				   $categoryList = null;

				   
				 //----------------------------------------------
				// get list of location name
				if(count($dealLocationIdList)!=0)
			    	$locationList = JModel::getInstance('location','enmasseModel')->getLocationListInArrId($dealLocationIdList);
				else
				   $locationList = null;
				   
				   
				if(count($locationList)!=0 && $locationList!=null)
					$dealList[$i]->location_name 		= $locationList;
				else 
					$dealList[$i]->location_name 		= null;
					
				if(count($categoryList)!=0 && $categoryList!=null)
					$dealList[$i]->category_name 		= $categoryList;
				else
				    $dealList[$i]->category_name 		= null;
					$dealList[$i]->sales_person_name 	= JModel::getInstance('salesPerson','enmasseModel')->retrieveName($dealList[$i]->sales_person_id);
					$dealList[$i]->merchant_name 		= JModel::getInstance('merchant','enmasseModel')->retrieveName($dealList[$i]->merchant_id);
			}

			$this->assignRef( 'filter', $filter);
			$this->statusList = EnmasseHelper::$DEAL_STATUS_LIST;
			
			$this->salePersonList = JModel::getInstance('salesPerson','enmasseModel')->listAllPublished();
			$this->merchantList = JModel::getInstance('merchant','enmasseModel')->listAllPublished();
		
			$this->assignRef( 'dealList', $dealList );
			$this->assignRef('pagination', $pagination);
			$this->assignRef( 'order', $order );
			$this->assignRef( 'currency_prefix', $currency_prefix );
		
		
		parent::display($tpl);
	}

}
?>