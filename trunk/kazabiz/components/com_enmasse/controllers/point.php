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
require_once( JPATH_ADMINISTRATOR . DS ."components". DS ."com_enmasse".DS."helpers". DS ."EnmasseHelper.class.php");

class EnmasseControllerPoint extends JController
{
  function __construct()
  {
    parent::__construct();
  }
  
  function refundForm() 
  {
    JRequest::setVar('view', 'point');
    parent::display();
  } 
  
  function doRefund()
  {
	$post = JRequest::get('post');
	$orderId = $post['orderid'];	
	$pointRefund = $post['point'];
	$buyerId = $post['buyerid'];
	
	//Get Id of current user
	$user = JFactory::getUser();
	$userId = $user->get('id');	

	$pointPaid = EnmasseHelper::getPointPaidByOrderId($orderId);
	$totalPrice = EnmasseHelper::getTotalPriceByOrderId($orderId);
	$orderStatus = EnmasseHelper::getOrderStatusByOrderId($orderId);
	$refundedAmount = EnmasseHelper::getRefundedAmountByOrderId($orderId);
	//Check the last time to be sure we do for right buyer and do on a right order
	$pass = true;
	if($pointRefund == '')
	{
		$msg = JText::_('INVALID_POINT');
		$pass = false;
	}
	if($pointPaid<0)
	{
		$msg = JText::_('NOT_PAID_WITH_POINT');
		$pass = false;
	}
	if($pointRefund>$totalPrice)
	{
		$msg = JText::_('POINT_NOT_GREATE_TOTAL_PRICE');
		$pass = false;
	}
	if($buyerId!=$userId)
	{
		$msg = JText::_('NOT_OWNER');
		$pass = false;	
	}
	if($refundedAmount!=0)
	{
		$msg = JText::_('ALREADY_REQUESTED');
		$pass = false;
	}
	if ($pass)
	{
		if(JModel::getInstance('point','enmasseModel')->doRefund($userId, $orderId,$pointRefund))
		{
			$msg = JText::_('REFUND_SUCCESSFULLY');
		}
		else
		{
			$msg = JText::_('REFUND_FAILED');
		}				
		$link = JRoute::_("index.php?option=com_enmasse&controller=point&task=refundForm&orderid=".$orderId."&buyerid=".$buyerid, false);
		JFactory::getApplication()->redirect($link, $msg);  		
  	}
  	else
  	{	
		$link = JRoute::_("index.php?option=com_enmasse&controller=point&task=refundForm&orderid=".$orderId."&buyerid=".$buyerid, false);
		JFactory::getApplication()->redirect($link, $msg);    		
  	}
  }

}
?>