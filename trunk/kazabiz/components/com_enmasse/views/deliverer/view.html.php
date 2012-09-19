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

class EnmasseViewDeliverer extends JView
{
	function display($tpl = null)
	{
		$task = JRequest::getWord('task');
		
		$userId = JFactory::getUser()->id;
		switch ($task) 
		{
    		case 'show':
    			$arOrderId = JModel::getInstance('deliverer', 'EnmasseModel')->getOrdersByUserId($userId);
    			$orderList = JModel::getInstance('order', 'EnmasseModel')->getOrdersByIds(empty($arOrderId)? array(0) : $arOrderId);
    			for($count =0; $count < count($orderList); $count++)
    			{
    				$orderItemList = JModel::getInstance('orderItem','enmasseModel')->listByOrderId($orderList[$count]->id);
    				$oOrderDeliverer = JModel::getInstance('OrderDeliverer', 'enmasseModel')->getByOrderId($orderList[$count]->id);
    				$orderList[$count]->orderItem 	= $orderItemList[0];
    				$orderList[$count]->status 	= $oOrderDeliverer->status;
    				$orderList[$count]->display_id 	= EnmasseHelper::displayOrderDisplayId($orderList[$count]->id);
    			}
    			$this->assignRef( 'orderList', $orderList );

    			$this->_setPath('template',JPATH_SITE . DS ."components". DS ."com_enmasse". DS ."theme". DS .EnmasseHelper::getThemeFromSetting(). DS ."tmpl". DS);
    			$this->_layout="deliverer_order_list";
    			parent::display($tpl);
    			break;
    			
    		case 'edit':
    			$orderId = JRequest::getVar('id', 0, 'method', 'int');
    			$oOrderDeliverer = JModel::getInstance('OrderDeliverer', 'enmasseModel')->getByOrderId($orderId);
    			if(!$oOrderDeliverer)
    			{
    				$link = JRoute::_("index.php?option=com_enmasse&controller=deliverer&task=show");
    				$msg  = JText::_('INVALID_ORDER_ID_MSG');
					JFactory::getApplication()->redirect($link, $msg, 'error');
    			}
    			$oOrder = JModel::getInstance('order', 'EnmasseModel')->getById($orderId);
    			$oOrder->delivery_status = $oOrderDeliverer->status;
    			
    			$this->oOrder = $oOrder;
    			$this->oOrderItemList = JModel::getInstance('orderItem','enmasseModel')->listByOrderId($oOrder->id);
    			$this->_setPath('template',JPATH_SITE . DS ."components". DS ."com_enmasse". DS ."theme". DS .EnmasseHelper::getThemeFromSetting(). DS ."tmpl". DS);
    			$this->_layout="deliverer_order_edit";
    			
    			parent::display($tpl);
    			break;
    			
			default:
				
				$link = JRoute::_("index.php?option=com_enmasse&controller=deliverer&task=show");
				JFactory::getApplication()->redirect($link);
		}
	}

}
?>