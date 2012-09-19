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

class CartHelper
{
	public static function checkGty($payGty)
	{
		$payClass 	= $payGty->class_name;
		$className 	= 'PayGty'.ucfirst($payClass);
		require_once JPATH_SITE . DS ."components". DS ."com_enmasse". DS ."helpers". DS ."payGty". DS .$payClass. DS .$className. ".class.php";

		if (call_user_func_array(array($className, "checkConfig"), array($payGty)) )
			return true;	
		else
		{
			return false;
		}
	}

	public static function checkCart($cart)
	{
		if ( empty($cart) || $cart->getTotalItem() == 0 )
		{
			$msg = JText::_( "CART_IS_EMPTY");
			$link = JRoute::_("index.php?option=com_enmasse&controller=deal", false);
			JFactory::getApplication()->redirect($link, $msg);
		}
	}

	public static function saveOrder($cart, $user, $payGty, $payGtyDetail, $deliveryGty, $deliveryDetail, $status, $guest_buying='0')
	{
		$db = JFactory::getDBO();
		$cartItem = array_pop($cart->getAll());
		
		$__data =new stdClass();
		$__data->total_buyer_paid 	= $cart->getTotalPrice();
		$__data->point_used_to_pay 	= $cart->getPoint();
		$__data->status 			= $status;
		$__data->session_id 		= JFactory::getSession()->getId();
		
		$__data->buyer_id 			= $user['id'];
		$__data->buyer_detail 		= json_encode($user);				
		
		$__data->referral_id 		= $cart->getReferralId();
		$__data->pay_gty_id 		= $payGty->id;
		$__data->delivery_gty_id 	= $deliveryGty->id;
		$__data->delivery_detail 	= json_encode($deliveryDetail);
		
		$__data->created_at = DatetimeWrapper::getDatetimeOfNow();
		$__data->updated_at = DatetimeWrapper::getDatetimeOfNow();
		
		$__data->paid_amount = number_format($cartItem->getCount() * $cartItem->item->price * $cartItem->item->prepay_percent / 100, 2);
		
		$db->insertObject( '#__enmasse_order', $__data, 'id');
				
		if ($db->getErrorNum())
		{
			echo $db->stderr();
			return false;
		}
		else
			return $__data;
	}

	public static function saveOrderItem($cart, $order, $status)
	{
		$orderItemList = array();
		$db = JFactory::getDBO();
		foreach ($cart->getAll() as $id=>$cartItem)
		{
			$__data 				= new stdClass();
			$__data->signature 		= $id;
			$__data->description 	= $cartItem->getItem()->name;
			$__data->unit_price 	= $cartItem->getItem()->price;
			$__data->qty 			= $cartItem->getCount();
			$__data->total_price 	= $cartItem->getTotalPrice();
			$__data->pdt_id 		= $cartItem->getItem()->id;
			$__data->order_id 		= $order->id;
			$__data->status 		= $status;
			$__data->created_at = $order->created_at;
			$__data->updated_at = $order->updated_at;
			$db->insertObject( '#__enmasse_order_item', $__data, 'id');
			if ($db->getErrorNum())
			{
				echo $db->stderr();
				return false;
			}
			array_push($orderItemList, $__data);
		}
		return $orderItemList;
	}
	// to update the coupon in inventorry to located to an order
	public static function allocatedInvty($orderItemList,$deallocated_at,$status)
	{
		$db = JFactory::getDBO();
		$freeCouponList = JModel::getInstance('invty','enmasseModel')->getCouponFreeByPdtID($orderItemList[0]->pdt_id);
		for($i=0 ; $i<$orderItemList[0]->qty ; $i++)
		{
		 $sequent = $i+1;
		 $query = 'UPDATE 
		 				#__enmasse_invty
		           SET
		                order_item_id = '.$orderItemList[0]->id.',name ="'.$orderItemList[0]->pdt_id.'-'.$orderItemList[0]->id.'-'.$sequent.'",deallocated_at="'.$deallocated_at.'" , status="'.$status.'"
		           WHERE
		                id = '.$freeCouponList[$i]->id;
		
		$db->setQuery( $query );
		$db->query();
		}
		return true;
		
	}

}