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

class EnmasseControllerOrder extends JController
{

	function display($cachable = false, $urlparams = false)
	{
		JRequest::setVar('view', 'order');
		JRequest::setVar('layout', 'show');
		parent::display();
	}
	function edit()
	{
		JRequest::setVar('view', 'order');
		JRequest::setVar('layout', 'edit');
		parent::display();
	}

	function control()
	{
		$this->setRedirect('index.php?option=com_enmasse');
	}
	
	public function exportExcel()
	{
		JRequest::setVar('view', 'order');
		JRequest::setVar('layout', 'excel_export');
		parent::display();
	}
	
	function save()
	{
		$status = JRequest::getVar( 'status', '', 'post');
		$partial = JRequest::getVar('partial', 0, 'method', 'int');
		$orderData = new JObject();
		$orderData->id 			= JRequest::getInt( 'id', '', 'post');
		$orderData->description = JRequest::getVar( 'description', '', 'post', 'text', JREQUEST_ALLOWRAW );
		//
		$orderData->buyerid 		= JRequest::getInt( 'buyerid', '', 'post');
		
		$model = JModel::getInstance('order','enmasseModel');
		
		if(empty($status))//admin dont update order status, so we just save order information
		{
			if ($model->store($orderData))
			{
				$msg = JText::_('SAVE_SUCCESS_MSG');
				if($partial)
				{
					$this->setRedirect('index.php?option=com_enmasse&controller=partialOrder', $msg);
				}else 
				{
					$this->setRedirect('index.php?option=com_enmasse&controller='.JRequest::getVar('controller'), $msg);
				}
				
			}
			else
			{
				$msg = JText::_('SAVE_ERROR_MSG') .": " . $model->getError();
				if($orderData->id == null)
					$this->setRedirect('index.php?option=com_enmasse&controller='.JRequest::getVar('controller').'&task=add', $msg, 'error');
				else
					$this->setRedirect('index.php?option=com_enmasse&controller='.JRequest::getVar('controller').'&task=edit&cid[0]='. $orderData->id .'&partial=' .$partial, $msg, 'error');
			}
		}
		else
		{
			//------------------------------
			//  send email and update total sold qty for each case of when change status of order
			
		    if( $status == EnmasseHelper::$ORDER_STATUS_LIST["Paid"])
		    {
	    		EnmasseHelper::doNotify($orderData->id); 	
	  	    	$msg = JTEXT::_('SAVE_SUCCESS_MSG_AND_SEND_RECEIPT');
		    }
		    else if( $status == EnmasseHelper::$ORDER_STATUS_LIST["Cancelled"])
		    {
				$orderId = $orderData->id;
	    		$order = JModel::getInstance('order', 'enmasseModel')->getById($orderId);
				JModel::getInstance('order', 'enmasseModel')->updateStatus($order->id, 'Cancelled');
				$orderItemList = JModel::getInstance('orderItem', 'enmasseModel')->listByOrderId($orderId);
				for ($count = 0; $count < count($orderItemList); $count++)
				{
            		$orderItem = $orderItemList[$count];
					JModel::getInstance('orderItem', 'enmasseModel')->updateStatus($orderItem->id, 'Unpaid');
            		JModel::getInstance('invty', 'enmasseModel')->freeCouponByOrderItemId($orderItem->id, 'Free');
				}
	  	    	$msg = JTEXT::_('SAVE_SUCCESS_MSG');
		    }            
		    else if($status == EnmasseHelper::$ORDER_STATUS_LIST["Waiting_For_Refund"])
		    {
		    	$orderItemList = JModel::getInstance('orderItem','enmasseModel')->listByOrderId($orderData->id);
		   		foreach($orderItemList as $orderItem)
				{
					EnmasseHelper::orderItemWaitingForRefund($orderItem);
					sleep(1);
				}
				$msg = JTEXT::_('SAVE_SUCCESS_MSG_AND_SEND_REFUND');	    
	
		    }
		    
			else if($status == EnmasseHelper::$ORDER_STATUS_LIST["Refunded"])
		    {
		    	$orderItemList = JModel::getInstance('orderItem','enmasseModel')->listByOrderId($orderData->id);
		   		foreach($orderItemList as $orderItem)
				{
					// reduce deal quality sold
					// only reduce when order item was delivered
					$isDelivered = intVal($orderItem->is_delivered);
					if ($isDelivered) {
						JModel::getInstance('deal', 'enmasseModel')->reduceQtySold($orderItem->pdt_id, $orderItem->qty);
					}
					
					EnmasseHelper::orderItemRefunded($orderItem);
					sleep(1);
				}
				$msg = JTEXT::_('SAVE_SUCCESS_MSG');	    
	
		    }
		    else if($status == 'Delivered')
		    {
		    	$orderItemList = JModel::getInstance('orderItem','enmasseModel')->listByOrderId($orderData->id);
		    	//update paid_amount of coupon to 100%
		    	$model->updateToFullPaid($orderData->id);
		    	
		    	JModel::getInstance("orderDeliverer", 'EnmasseModel')->updateStatus($orderData->id, "Delivered");
		    	
		   		foreach($orderItemList as $orderItem)
				{
					// update deal quality sold
					JModel::getInstance('deal', 'enmasseModel')->addQtySold($orderItem->pdt_id, $orderItem->qty);
					
					EnmasseHelper::orderItemDelivered($orderItem);
					sleep(1);
				}
				$msg = JTEXT::_('SAVE_SUCCESS_MSG_AND_SEND_DELIVERY');
		    }
		    
		    if($partial)
		    {
		    	$this->setRedirect('index.php?option=com_enmasse&controller=partialOrder', $msg);
		    }else
		    {
		    	$this->setRedirect('index.php?option=com_enmasse&controller=order', $msg);
		    }
			
		}
	}
}
?>