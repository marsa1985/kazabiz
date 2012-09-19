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

class EnmasseViewMerchant extends JView
{
	function display($tpl = null)
	{
		$task = JRequest::getWord('task');
		switch ($task) 
		{
    		case 'dealCouponMgmt':
				$merchantId = JFactory::getSession()->get('merchantId');		
				// To list deal by merchant
				$dealList = JModel::getInstance('deal','enmasseModel')->listConfirmedByMerchantId($merchantId);
				$this->assignRef('dealList',$dealList);
				$dealId ='';
				$orderItemList = null;
				$filter = JRequest::getVar('filter');
				$this->assignRef('filter',$filter);
				$dealId = $filter['deal_id'];
			
				if(!empty($dealId))
				{
					$deal =  JModel::getInstance('deal','enmasseModel')->getById($dealId);
					$this->assignRef('deal',$deal);	
					
					$orderItemList = JModel::getInstance('orderItem','enmasseModel')->listByPdtIdAndStatus($dealId, "Delivered");
					
					for($count =0; $count < count($orderItemList); $count++)
					{
						$orderItemList[$count]->invtyList 	= JModel::getInstance('invty','enmasseModel')->listByOrderItemId($orderItemList[$count]->id);
						$orderItemList[$count]->order 		= JModel::getInstance('order','enmasseModel')->getById($orderItemList[$count]->order_id);
					}
					
				}
				$this->assignRef('orderItemList',$orderItemList);
				$this->_setPath('template',JPATH_SITE . DS ."components". DS ."com_enmasse". DS ."theme". DS .EnmasseHelper::getThemeFromSetting(). DS ."tmpl". DS);
				$this->_layout="merchant_deal_coupon_mgmt";
				parent::display($tpl);
				break;
    		default:
				$link = JRoute::_("index.php?option=com_enmasse&controller=merchant&task=dealCouponMgmt", false);
				JFactory::getApplication()->redirect($link, $null);
		}
	}

}
?>