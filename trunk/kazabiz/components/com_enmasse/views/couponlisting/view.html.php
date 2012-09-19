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

class EnmasseViewCouponListing extends JView
{
	function display($tpl = null)
	{
		$token = JRequest::getVar('token', null);
		$orderItemId = JRequest::getVar('orderItemId', null);
		
		$orderItem = JModel::getInstance('orderItem','enmasseModel')->getById($orderItemId);
		
		$ourToken = EnmasseHelper::generateOrderItemToken($orderItemId, $orderItem->created_at);

		if($ourToken != $token)
		{
			$link = JRoute::_("/", false);
			$msg = JText::_( "INVALID_COUPON_TOKEN");
			JFactory::getApplication()->redirect($link, $msg, "error");
		}
		
		$invtyList = JModel::getInstance('invty','enmasseModel')->listByOrderItemId($orderItem -> id);
		$deal      = JModel::getInstance('deal','enmasseModel')->getById($orderItem -> pdt_id);

		$this->assignRef( 'invtyList', $invtyList );
		$this->assignRef( 'deal', $deal );
		
        $this->_setPath('template',JPATH_SITE . DS ."components". DS ."com_enmasse". DS ."theme". DS .EnmasseHelper::getThemeFromSetting(). DS ."tmpl". DS);
    	$this->_layout="coupon_listing";
        parent::display($tpl);
	}

}
?>