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

class EnmasseViewSaleReports extends JView
{
	function display($tpl = null)
	{ 
		$task = JRequest::getWord('task');
		switch ($task) 
		{ 
    		case 'dealReport':
			$salesPersonId = JFactory::getSession()->get('salesPersonId');
			$filter = JRequest::getVar('filter', array('name' => "", 'code' => "", 'merchant_id' => "", 'fromdate' => "", 'todate' => ""));
			$this->assignRef('filter',$filter);
			$dealList 			= JModel::getInstance('deal','enmasseModel')->searchBySaleReports($salesPersonId, $filter['name'], $filter['merchant_id'], $filter['fromdate'], $filter['todate'], $filter['code']);
			$currency_prefix 	= JModel::getInstance('setting','enmasseModel')->getCurrencyPrefix(); 
			$this->dealList = $dealList;
			$this->assignRef('currency_prefix',$currency_prefix);
			$this->statusList = EnmasseHelper::$DEAL_STATUS_LIST;
			$this->merchantList = JModel::getInstance('merchant','enmasseModel')->listAllPublished();
			$this->_setPath('template',JPATH_SITE . DS ."components". DS ."com_enmasse". DS ."theme". DS .EnmasseHelper::getThemeFromSetting(). DS ."tmpl". DS);
			$this->_layout="sales_person_deal_report";
			parent::display($tpl);
			break;
			default:
				$link = JRoute::_("index.php?option=com_enmasse&controller=salereports&task=dealReport", false);
				JFactory::getApplication()->redirect($link, $null);
		}		
				
	}
}
?>