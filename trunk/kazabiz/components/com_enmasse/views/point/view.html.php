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

class EnmasseViewPoint extends JView
{
	function display($tpl = null)
	{
		$orderId = JRequest::getVar('orderid', null);
		$buyerId = JRequest::getVar('buyerid', null);
		
		//Get Id of current user
        $user = JFactory::getUser();
        $userId = $user->get('id');	

        $pointPaid = EnmasseHelper::getPointPaidByOrderId($orderId);
        $totalPrice = EnmasseHelper::getTotalPriceByOrderId($orderId);
        $orderStatus = EnmasseHelper::getOrderStatusByOrderId($orderId);
        
        //If current user is owner of the order and the order was paid with point
		if($buyerId==$userId && $pointPaid>0 && $orderStatus=='Refunded')
		{		
			$dealName = EnmasseHelper::getDealNameByOrderId($orderId);
			$this->assignRef( 'dealName', $dealName );
			$this->assignRef( 'orderId', $orderId );
			$this->assignRef( 'totalPrice', $totalPrice );
			$this->assignRef( 'pointPaid', $pointPaid );
			$this->assignRef( 'buyerId', $buyerId );
			
	        $this->_setPath('template',JPATH_SITE . DS ."components". DS ."com_enmasse". DS ."theme". DS .EnmasseHelper::getThemeFromSetting(). DS ."tmpl". DS);
	    	$this->_layout="point_refund";
	        parent::display($tpl);
		}
		else
		{
			$link = JRoute::_("index.php?option=com_enmasse&view=deallisting", false);    
			JFactory::getApplication()->redirect($link);
		}
	}

}
?>