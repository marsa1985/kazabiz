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
require_once(JPATH_COMPONENT.DS."models".DS."order.php");
// load language pack
$language = JFactory::getLanguage();
$base_dir = JPATH_SITE.DS.'components'.DS.'com_enmasse';
$version = new JVersion;
$joomla = $version->getShortVersion();
if(substr($joomla,0,3) >= '1.6'){
    $extension = 'com_enmasse16';
}else{
    $extension = 'com_enmasse';
}
if($language->load($extension, $base_dir, $language->getTag(), true) == false)
{
	$language->load($extension, $base_dir, 'en-GB', true);
}

class EnmasseViewOrderList extends JView
{
	function display($tpl = null)
	{
		if (JFactory::getUser()->get('guest'))
		{   
			$msg = JText::_( "ORDER_PLEASE_LOGIN_BEFORE");
			$redirectUrl = base64_encode("index.php?option=com_enmasse&view=orderList");
            $version = new JVersion;
            $joomla = $version->getShortVersion();
            if(substr($joomla,0,3) >= '1.6'){
                $link = JRoute::_("index.php?option=com_users&view=login&return=".$redirectUrl, false);
            }else{
                $link = JRoute::_("index.php?option=com_user&view=login&return=".$redirectUrl, false);    
            }
			JFactory::getApplication()->redirect($link, $msg);
		}

		$orderList 	= JModel::getInstance('order','enmasseModel')->listForBuyer(JFactory::getUser()->id);

		for($count =0; $count < count($orderList); $count++)
		{
			$orderItemList = JModel::getInstance('orderItem','enmasseModel')->listByOrderId($orderList[$count]->id);
			$orderList[$count]->orderItem 	= $orderItemList[0];
			$orderList[$count]->display_id 	= EnmasseHelper::displayOrderDisplayId($orderList[$count]->id);
		}
		$this->assignRef( 'orderList', $orderList );

		$this->_setPath('template',JPATH_SITE . DS ."components". DS ."com_enmasse". DS ."theme". DS .EnmasseHelper::getThemeFromSetting(). DS ."tmpl". DS);
		$this->_layout="order_list";
		parent::display($tpl);

	}

}
?>