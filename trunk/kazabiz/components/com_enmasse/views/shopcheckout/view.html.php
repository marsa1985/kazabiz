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
require_once( JPATH_SITE . DS ."components". DS ."com_enmasse". DS ."helpers". DS ."CartHelper.class.php");

class EnmasseViewShopCheckout extends JView
{
	function display($tpl = null)
	{
		$cart = unserialize(JFactory::getSession()->get('cart'));
		CartHelper::checkCart($cart);

		$setting = JModel::getInstance('setting','enmasseModel')->getSetting();
		$arData = array();
		
		$this->payGtyList = JModel::getInstance('payGty','enmasseModel')->listAll();
		$this->assignRef( 'termArticleId', $setting->article_id);
		$this->assignRef( 'theme', $setting->theme);
		$this->user = JModel::getInstance('user','enmasseModel')->getUser();
		$this->assignRef( 'cart', $cart );
		//get user data was save in the session
		$arData = JFactory::getApplication()->getUserState('com_enmasse.checkout.data');
		if(empty($arData))
		{
			//contruct default value for the inputs
			$arData['name'] = $this->user->name;
			$arData['email'] = $this->user->email;
			$arData['receiver_name'] = "";
			$arData['receiver_email'] = "";
			$arData['receiver_msg'] = "";
			$arData['receiver_address'] = "";
			$arData['receiver_phone'] = "";
		}
		
		$this->arData = $arData;
		
		$this->_setPath('template', JPATH_SITE . DS ."components". DS ."com_enmasse". DS ."theme". DS .EnmasseHelper::getThemeFromSetting(). DS ."tmpl". DS);
		$this->_layout="shop_checkout";
		parent::display($tpl);
	}

}
?>