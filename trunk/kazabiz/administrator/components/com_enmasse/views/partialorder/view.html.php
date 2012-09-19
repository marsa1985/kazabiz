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

class EnmasseViewPartialOrder extends JView
{
	public function display($tpl = null)
	{
			
		$task = JRequest::getWord('task');

		TOOLBAR_enmasse::_SMENU();
		TOOLBAR_enmasse::_PARTIAL_ORDER();
			
		$filter 	= JRequest::getVar('filter', array());

		// Weird that only this will caused warning...
		if(!isset($filter['deal_name']))
		$filter['deal_name'] = "";
		if(!isset($filter['deal_code']))
		$filter['deal_code'] = "";
		if(!isset($filter['status']))
		$filter['status'] = "";
		if(!isset($filter['year']))
		$filter['year'] = "";
		if(!isset($filter['month']))
		$filter['month'] = "";
		
		//filter partial order
		
		$filter['partial'] = true;
		JRequest::setVar('filter', $filter);
			
		$oOrderModel = JModel::getInstance('order','enmasseModel');
		$orderList 	= $oOrderModel->search($filter['status'], $filter['deal_code'], $filter['deal_name'], "created_at", "DESC", true);
		$pagination = $oOrderModel->getPagination();

		$this->statusList = EnmasseHelper::$ORDER_STATUS_LIST;
		$this->filter = $filter;
		$this->orderList = $orderList ;
		$this->pagination = $pagination;
		$this->deliveryPersons = EnmasseHelper::getDeliveryPersons();
		parent::display($tpl);
	}

}
?>