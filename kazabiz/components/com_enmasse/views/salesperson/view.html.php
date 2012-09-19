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

require_once( JPATH_ADMINISTRATOR . DS ."components". DS ."com_enmasse". DS ."helpers". DS ."EnmasseHelper.class.php");

class EnmasseViewSalesPerson extends JView
{
	function display($tpl = null)
	{
		$task = JRequest::getWord('task');
		switch ($task) 
		{
    		case 'dealEdit':
    			
				$cid 	= JRequest::getVar( 'cid', array(0), '', 'array' );
				// define deal attributes
				$row = new JObject();
				$row->id = null;
				$row->name = null;
				$row->description = null;
				$row->short_desc = null;
				$row->origin_price = null;
				$row->price = null;
				$row->pic_dir = null;
				$row->start_at = null;
				$row->end_at = null;
				$row->min_needed_qty = null;
				$row->highlight = null;
				$row->terms = null;
				$row->created_at =null;
				$row->updated_at = null;
				$row->merchant_id = null;
				$row->prepay_percent = null;
				$row->commission_percent = null;
				
				if($cid[0]!=0)
					$row = JModel::getInstance('deal','enmasseModel')->getById($cid[0]);
				
					
				$this->assignRef( 'deal', $row );
				
				$this->currencyPrefix = JModel::getInstance('setting','enmasseModel')->getCurrencyPrefix();
				$this->currencyPostfix = JModel::getInstance('setting','enmasseModel')->getCurrencyPostfix();
				$this->statusList = EnmasseHelper::$DEAL_STATUS_LIST;
				$this->locationList = JModel::getInstance('location','enmasseModel')->listAllPublished();
				$this->categoryList = JModel::getInstance('category','enmasseModel')->listAllPublished();
				$this->merchantList = JModel::getInstance('merchant','enmasseModel')->listAllPublished();
				
				if($cid[0] != null)
				{
					$dealCategoryIdList = JModel::getInstance('dealcategory','enmasseModel')->getCategoryByDealId($cid[0]);
				    $this->dealCategoryList = JModel::getInstance('category','enmasseModel')->getCategoryListInArrId($dealCategoryIdList);
				    $dealLocationIdList = JModel::getInstance('deallocation','enmasseModel')->getLocationByDealId($cid[0]);
				    $this->dealLocationList = JModel::getInstance('location','enmasseModel')->getLocationListInArrId($dealLocationIdList);
				}
				else
				{
					$dealCategoryList = array();
					$this->assignRef('dealCategoryList',$dealCategoryList);
					$dealLocationList = array();
					$this->assignRef('dealLocationList',$dealLocationList);
				}		
			
				$this->_setPath('template',JPATH_SITE . DS ."components". DS ."com_enmasse". DS ."theme". DS .EnmasseHelper::getThemeFromSetting(). DS ."tmpl". DS);
				$this->_layout="sales_person_deal_edit";
				parent::display($tpl);
				
				break;
				
			case 'dealShow':
				$salesPersonId = JFactory::getSession()->get('salesPersonId');
				
				$filter = JRequest::getVar('filter', array('name' => "", 'code' => "", 'status' => "", 'published' => ""));
				$this->assignRef('filter',$filter);
		
				$dealList = JModel::getInstance('deal','enmasseModel')->searchBySalesPerson($salesPersonId, $filter['name'], $filter['published'], $filter['status'], $filter['code']);
				$this->assignRef('dealList',$dealList);
				
				$this->statusList = EnmasseHelper::$DEAL_STATUS_LIST;
				$this->locationList = JModel::getInstance('location','enmasseModel')->listAllPublished();
				$this->categoryList = JModel::getInstance('category','enmasseModel')->listAllPublished();
						
				$this->_setPath('template',JPATH_SITE . DS ."components". DS ."com_enmasse". DS ."theme". DS .EnmasseHelper::getThemeFromSetting(). DS ."tmpl". DS);
				$this->_layout="sales_person_deal_show";
				parent::display($tpl);
				
				break;
				
			case 'merchantList':
				$oMerMdl = JModel::getInstance('merchant', 'EnmasseModel');
		
				$arMerchant = $oMerMdl->search();
				$oPgn = $oMerMdl->getPagination();
				
				$filter = JRequest::getVar('filter');
			
				$state = $oMerMdl->get( 'state' );
				// get order values
				$order['order_dir'] = $state->get( 'filter_order_dir' );
				$order['order']     = $state->get( 'filter_order' );

				$this->filter = $filter ;
				$this->merchantList = $arMerchant ;
				$this->pagination = $oPgn ;
				$this->order = $order ;
								
				$this->_layout="sales_person_merchant_show";
				$this->_setPath('template',JPATH_SITE . DS ."components". DS ."com_enmasse". DS ."theme". DS .EnmasseHelper::getThemeFromSetting(). DS ."tmpl". DS);
				parent::display($tpl);
				break;
				
			case 'merchantEdit':
				$cid 	= JRequest::getVar( 'cid', array(0), '', 'array' );
				
				if($cid[0] > 0)
				{
					// implicit adding id filter condition for merchant search action(sale person id alway filter by default)
					$filter = array();
					$filter['id'] = $cid[0];
					JRequest::setVar('filter', $filter);
					$arContact = JModel::getInstance('merchant', 'EnmasseModel')->search();
					if(!$arContact || count($arContact) == 0)
					{
						$msg = JText::_("SALES_PERSON_NO_PERMISSION_ON_MERCHANT");
						$link = JRoute::_("index.php?option=com_enmasse&controller=salesPerson&task=merchantList", false);
						JFactory::getApplication()->redirect($link, $msg, 'error');
					}else 
					{
						$oContact = array_shift($arContact);//get the first merchant that was found
					}
				}else
				{
					JTable::addIncludePath(JPATH_ADMINISTRATOR . DS ."components". DS ."com_enmasse". DS ."tables");
					$oContact = JTable::getInstance('merchant', 'Table');
				}
				
				$this->oContact = $oContact;
								
				$this->_layout="sales_person_merchant_edit";
				$this->_setPath('template',JPATH_SITE . DS ."components". DS ."com_enmasse". DS ."theme". DS .EnmasseHelper::getThemeFromSetting(). DS ."tmpl". DS);
				parent::display($tpl);
				break;
				
			default:
				$link = JRoute::_("index.php?option=com_enmasse&controller=salesPerson&task=dealShow", false);
				JFactory::getApplication()->redirect($link, $null);
		}
	}

}
?>