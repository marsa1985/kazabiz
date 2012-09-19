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

jimport('joomla.application.component.controller');
JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_enmasse'.DS.'tables');
require_once( JPATH_ADMINISTRATOR . DS ."components". DS ."com_enmasse". DS ."helpers". DS ."EnmasseHelper.class.php");
require_once( JPATH_ADMINISTRATOR . DS ."components". DS ."com_enmasse". DS ."helpers". DS ."DatetimeWrapper.class.php");

class EnmasseControllerDeliverer extends JController
{
	public function display($cachable = false, $urlparams = false)
	{
		$this->checkAccess();
		JRequest::setVar('view', 'deliverer');
		//JRequest::setVar('layout', 'show');
		parent::display();
		
	}
	
	public function show()
	{
		$this->checkAccess();
		JRequest::setVar('view', 'deliverer');
		JRequest::setVar('task', 'show');
		parent::display();
		
	}
	
	public function edit()
	{
		$this->checkAccess();
		JRequest::setVar('view', 'deliverer');
		JRequest::setVar('task', 'edit');
		parent::display();
		
	}
	
	public function updateOrder()
	{
		$orderId = JRequest::getVar('id', 0, 'post','int');
		$sComment = JRequest::getVar('description');
		$sDlvStatus = JRequest::getVar('delivery_status', 'undelivered');
		$oOrderTbl = JTable::getInstance('order', 'Table');
		$oOrderTbl->load($orderId);
		$oOrderTbl->description = $sComment;
		$oOrderTbl->updated_at = DatetimeWrapper::getDatetimeOfNow();
		if($sDlvStatus == "delivered")
		{
			$oOrderTbl->status = EnmasseHelper::$ORDER_STATUS_LIST['Holding_By_Deliverer'];
		}
		
		$oOrderTbl->store();
		
		JModel::getInstance('orderDeliverer', 'enmasseModel')->updateStatus($orderId, $sDlvStatus);
		
		$msg = JText::_('ORDER_DELIVERER_UPDATE_SUCCESS_MSG');
		$link  = JRoute::_("index.php?option=com_enmasse&controller=deliverer&task=show");
		JFactory::getApplication()->redirect($link, $msg);
	}
	
	private function checkAccess()
	{
		$userGroup = JFactory::getUser()->groups;
		$delivererGroup = EnmasseHelper::getSetting()->delivery_group;
		if(!in_array($delivererGroup, $userGroup))
		{
			$msg = JText::_('NO_PRIVILEDGE_FOR_ACCESSING');
			JFactory::getApplication()->redirect(JURI::base(), $msg, 'error');
		}else 
		{
			return true;
		}
	}
}