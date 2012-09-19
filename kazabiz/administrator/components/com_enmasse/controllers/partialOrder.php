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
jimport('joomla.application.component.controller');
JTable::addIncludePath('components'.DS.'com_enmasse'.DS.'tables');
require_once( JPATH_ADMINISTRATOR . DS ."components". DS ."com_enmasse".DS."helpers". DS ."EnmasseHelper.class.php");

class EnmasseControllerPartialOrder extends JController
{
	public function display($cachable = false, $urlparams = false)
	{
		JRequest::setVar('view', 'partialorder');
		JRequest::setVar('layout', 'show');
		parent::display();
	}
	
	function control()
	{
		$this->setRedirect('index.php?option=com_enmasse');
	}
	
	public function assignOrder()
	{
		$cid = JRequest::getVar('cid', null, 'post', 'array');
		$nDeliverer = JRequest::getVar('deliverer_id', null);
		if(empty($cid))
		{
			$msg = JText::_('ORDER_ASSIGN_NO_ORDER_CHOSEN_MSG');
			$link = 'index.php?option=com_enmasse&controller=partialOrder';
			JFactory::getApplication()->redirect($link, $msg, 'error');
		}
		
		if(empty($nDeliverer))
		{
			$msg = JText::_('ORDER_NO_DELIVERER_CHOSEN_MSG');
			$link = 'index.php?option=com_enmasse&controller=partialOrder';
			JFactory::getApplication()->redirect($link, $msg, 'error');
		}
		
		//check order already delivered
		$arUndelivered = JModel::getInstance('Order', 'EnmasseModel')->getListIdUndelivered($cid);
		
		$arDelivered = array_diff($cid, $arUndelivered);
		
		if(count($arDelivered) > 0)
		{
			$waningMsg = JText::sprintf("ORDER_PARTIAL_ALREADY_DELIVERY", implode(', ', $arDelivered));
			JFactory::getApplication()->enqueueMessage($waningMsg, 'warning');
			if(empty($arUndelivered))
			{
				$this->setRedirect('index.php?option=com_enmasse&controller=partialOrder');
				return;
			}
		}
		$oOrderDeliverer = JModel::getInstance('OrderDeliverer', 'EnmasseModel');
				
		if($oOrderDeliverer->save(array('user_id' => $nDeliverer, 'orders' =>$arUndelivered)))
		{
			$msg = JText::_('ORDER_DELIVERER_ASSIGN_SUCCESS_MSG');
			JFactory::getApplication()->enqueueMessage($msg);
			$link = 'index.php?option=com_enmasse&controller=partialOrder';
			$this->setRedirect($link);
		}else 
		{
			$msg = $oOrderDeliverer->getError();
			$link = 'index.php?option=com_enmasse&controller=partialOrder';
			JFactory::getApplication()->redirect($link, $msg, 'error');
		}
		
		
	}
}