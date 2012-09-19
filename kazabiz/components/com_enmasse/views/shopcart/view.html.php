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
require_once( JPATH_SITE . DS ."components". DS ."com_enmasse". DS ."helpers". DS ."Cart.class.php");
require_once( JPATH_SITE . DS ."components". DS ."com_enmasse". DS ."helpers". DS ."CartHelper.class.php");
require_once( JPATH_ADMINISTRATOR . DS ."components". DS ."com_enmasse". DS ."helpers". DS ."EnmasseHelper.class.php");

class EnmasseViewShopCart extends JView
{
	function display($tpl = null)
	{
		$cart = unserialize(JFactory::getSession()->get('cart'));
		if($cart == null)
		{
			$msg = JText::_( "CART_IS_EMPTY");
			$link = JRoute::_("index.php?option=com_enmasse&controller=deal&task=listing", false);

			JFactory::getApplication()->redirect($link, $msg);
		}
		else if($cart->getTotalItem() == 0)
		{
			$msg = JText::_( "CART_IS_EMPTY");
			$link = JRoute::_("index.php?option=com_enmasse&controller=deal&task=listing", false);

			JFactory::getApplication()->redirect($link, $msg);			
		}
		$this->assignRef( 'cart', $cart );
		
		$this->_setPath('template',JPATH_SITE . DS ."components". DS ."com_enmasse". DS ."theme". DS .EnmasseHelper::getThemeFromSetting(). DS ."tmpl". DS);
		$this->_layout="shop_cart";
		parent::display($tpl);
	}

}
?>
