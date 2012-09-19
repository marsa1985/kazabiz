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
require_once( JPATH_ADMINISTRATOR . DS ."components". DS ."com_enmasse". DS ."helpers". DS ."EnmasseHelper.class.php");
jimport( 'joomla.application.component.view');


class EnmasseViewPayGty extends JView
{
	function display($tpl = null)
	{
		
		// -------------------to re define server link
		$server = JURI::base();

		//-----------------------------------
		$cart 		= unserialize(JFactory::getSession()->get('cart'));
		$payGty 	= unserialize(JFactory::getSession()->get('payGty'));
		if(empty($payGty))
		{
		        $link = JRoute::_("index.php?option=com_enmasse&controller=deal", false);
		        JFactory::getApplication()->redirect($link);
		}
		$orderId = JRequest::getVar('orderId');
		
		$returnUrl= $server."/index.php?option=com_enmasse&controller=payment&task=returnUrl&orderId=$orderId";
		$notifyUrl= $server."/index.php?option=com_enmasse&controller=payment&task=notifyUrl&orderId=$orderId";
		$cancelUrl= $server."/index.php?option=com_enmasse&controller=payment&task=cancelUrl";
		
		$returnUrl .= "&payClass=" . $payGty->class_name;
		$notifyUrl .= "&payClass=" . $payGty->class_name;
		
		$setting = new JObject();
		$setting->currency = JModel::getInstance('setting','enmasseModel')->getCurrency();
		$setting->country = JModel::getInstance('setting','enmasseModel')->getCountry();
		
		$this->returnUrl = $returnUrl;
		$this->notifyUrl = $notifyUrl;
		$this->cancelUrl = $cancelUrl;
		
		$this->cart = $cart;
		$this->user = JModel::getInstance('user','enmasseModel')->getUser();
		$this->systemName = JModel::getInstance('setting','enmasseModel')->getCompanyName();
		$this->attributeConfig = json_decode($payGty->attribute_config);
		$this->orderDisplayId = EnmasseHelper::displayOrderDisplayId($orderId);
		$this->setting = $setting ;
		$this->orderId = $orderId;
		$this->_setPath('template',JPATH_SITE . DS ."components". DS ."com_enmasse". DS ."helpers". DS ."payGty". DS .$payGty->class_name. DS);
    	$this->_layout="pay_gty";
        parent::display($tpl);
		
	}

}
?>