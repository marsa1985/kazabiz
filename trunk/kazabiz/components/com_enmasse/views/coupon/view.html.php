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

class EnmasseViewCoupon extends JView
{
	function display($tpl = null)
	{
		$token = JRequest::getVar('token', null);
		$invtyName = JRequest::getVar('invtyName', null);
		
		$elementList 	= JModel::getInstance('couponElement','enmasseModel')->listAll();
		$bgImageUrl		= JModel::getInstance('setting','enmasseModel')->getCouponBg();
			
		if($invtyName!="")
		{
			$invty 			= JModel::getInstance('invty','enmasseModel')->getByName($invtyName);
			$orderItem 		= JModel::getInstance('orderItem','enmasseModel')->getById($invty->order_item_id ? $invty->order_item_id : 0);
			$order 			= JModel::getInstance('order','enmasseModel')->getById($orderItem->order_id ? $orderItem->order_id : 0);
			$deal 			= JModel::getInstance('deal','enmasseModel')->getById($invty->pdt_id ? $invty->pdt_id : 0);
			$merchant		= JModel::getInstance('merchant','enmasseModel')->getById($deal->merchant_id ? $deal->merchant_id : 0);
            $merchant->branches = json_decode($merchant->branches);
           
			$deliveryDetail = json_decode($order->delivery_detail);

			$varList = array();
			$varList['dealName'] 		= $deal->name;
			$varList['serial'] 			= $invty->name;
            /** phuocndt
             * Get status value of Coupon
             */
            $varList['qr_code'] 		= $invty->name;
			$varList['detail'] 			= $deal->short_desc;
            $varList['merchantName']    = '';
			if($merchant!= null)
			{
                if(isset($merchant->branches->branch1))
                {                    
                    $varList['merchantName'] .= $merchant->branches->branch1->name;
                    $varList['merchantName'] .= '<br />' . $merchant->branches->branch1->address;
                    $varList['merchantName'] .= '<br />' . $merchant->branches->branch1->telephone;
                }
			}
			$varList['highlight'] 		= $deal->highlight;
			$varList['personName'] 		= $deliveryDetail->name;
			$varList['term'] 			= $deal->terms;
		} 
		else
        {
			$varList['serial'] 		= "";
            $varList['qr_code'] 	= "";
		}
        
        /** phuocndt
         * Debug QR code
         */
        //echo '<pre>'; print_r($varList); echo '</pre>'; die;
        	
		if($token != EnmasseHelper::generateCouponToken($varList['serial']))
		{
			$msg 	= JText::_(INVALID_COUPON_TOKEN);
			$link 	= JRoute::_("/", false);

			JFactory::getApplication()->redirect($link, $msg);
		}
		else
		{
			$this->assignRef('varList', $varList );
			$this->assignRef('elementList', $elementList );
			$this->assignRef('bgImageUrl', $bgImageUrl );
			
			parent::display($tpl);
			exit(0);
		}
	}

}
?>