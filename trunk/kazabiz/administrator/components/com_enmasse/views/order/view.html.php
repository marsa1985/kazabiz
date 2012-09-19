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

class EnmasseViewOrder extends JView
{
	function display($tpl = null)
	{
			
		$task = JRequest::getWord('task');

		if($task == 'edit') // display order with its item
		{
			TOOLBAR_enmasse::_ORDER_NEW();
		
			$orderId = JRequest::getVar('orderId');

			//$modelOrder 	= JModel::getInstance('order','enmasseModel');
			//$modelOrderItem = JModel::getInstance('orderItem','enmasseModel');
            $modelInvty = JModel::getInstance('invty','enmasseModel');
			
			$order 				= JModel::getInstance('order','enmasseModel')->getById($orderId);
			$orderItemList		= JModel::getInstance('orderItem','enmasseModel')->listByOrderId($orderId);
			
			$order->orderItem 	= $orderItemList[0];
			$order->orderItem->invtyList = $modelInvty->listByOrderItemId($order->orderItem->id);
			
			$oDeal = JModel::getInstance('deal','enmasseModel')->getById($order->orderItem->pdt_id);
			$this->sDealStatus = $oDeal->status;
			
			$this->assignRef( 'statusList', EnmasseHelper::$ORDER_STATUS_LIST);
			$this->assignRef( 'order', $order );
			$this->partial = JRequest::getVar('partial', 0, 'method', 'int');
		}
		elseif($task == 'exportExcel')
		{
			$filter 	= JRequest::getVar('filter');
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
				
			$oOrderModel = JModel::getInstance('order','enmasseModel');
			$orderList 	= $oOrderModel->search($filter['status'], $filter['deal_code'], $filter['deal_name'], "created_at", "DESC");
			$this->filter = $filter;
			$this->orderList = $orderList ;
		}
		else // display list of orders
		{
			TOOLBAR_enmasse::_SMENU();
			TOOLBAR_enmasse::_ORDER();
			
			$filter 	= JRequest::getVar('filter');
            
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
			
			$oOrderModel = JModel::getInstance('order','enmasseModel');
			$orderList 	= $oOrderModel->search($filter['status'], $filter['deal_code'], $filter['deal_name'], "created_at", "DESC");
		    $pagination = $oOrderModel->getPagination();
		    
		    $this->statusList = EnmasseHelper::$ORDER_STATUS_LIST;
			$this->filter = $filter;
			$this->orderList = $orderList ;
			$this->pagination = $pagination;
		}
		parent::display($tpl);
	}

}
?>