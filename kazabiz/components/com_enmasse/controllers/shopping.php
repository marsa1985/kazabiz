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

jimport('joomla.application.component.controller');
require_once( JPATH_ADMINISTRATOR . DS ."components". DS ."com_enmasse". DS ."helpers". DS ."DatetimeWrapper.class.php");
require_once( JPATH_ADMINISTRATOR . DS ."components". DS ."com_enmasse". DS ."helpers". DS ."EnmasseHelper.class.php");


require_once( JPATH_SITE . DS ."components". DS ."com_enmasse". DS ."helpers". DS ."Cart.class.php");
require_once( JPATH_SITE . DS ."components". DS ."com_enmasse". DS ."helpers". DS ."CartHelper.class.php");

class EnmasseControllerShopping extends JController
{
	function __construct()
	{
		parent::__construct();
	}

	function display($cachable = false, $urlparams = false) {
	}

	// Shopping Cart
	function viewCart()
	{
		JRequest::setVar('view', 'shopcart');
		parent::display();
	}

	// Shopping Cart
	function reCheckout()
	{
		JRequest::setVar('view', 'shopcheckout');
		parent::display();
	}

	function addToCart()
	{
		$dealId = JRequest::getVar('dealId');
		$referralId = JRequest::getVar('referralid');
		$bBuy4friend = JRequest::getVar('buy4friend',0);
		$deal = JModel::getInstance('deal','enmasseModel')->getById($dealId);


		//*************************************************************************************
		// check Deal
		$now = DatetimeWrapper::getDatetimeOfNow();
		if ($now < $deal->start_at)
		{
			$msg = JText::_( "DEAL_NOT_READY");
			$link = JRoute::_("index.php?option=com_enmasse&controller=deal&task=view&id=".$deal->id, false);
			JFactory::getApplication()->redirect($link, $msg);
		}
		elseif ( time() > (strtotime($deal->end_at) + 24*60*60))
		{
			$msg = JText::_( "DEAL_END_MSG");
			$link = JRoute::_("index.php?option=com_enmasse&controller=deal&task=view&id=".$deal->id, false);
			JFactory::getApplication()->redirect($link, $msg);
		}
		elseif($deal->published == false)
		{
			$msg = JText::_( "DEAL_NOLONGER_PUBLISH");
			$link = JRoute::_("index.php?option=com_enmasse&controller=deal&task=view&id=".$deal->id, false);
			JFactory::getApplication()->redirect($link, $msg);
		}
		elseif($deal->status == "Voided")
		{
			$msg = JText::_( "DEAL_HAVE_BEEN_VOID");
			$link = JRoute::_("index.php?option=com_enmasse&controller=deal&task=view&id=".$deal->id, false);
			JFactory::getApplication()->redirect($link, $msg);
		}
		else
		{
			// add to cart
			/*
			$cart = unserialize(JFactory::getSession()->get('cart'));
			if (empty($cart))
			$cart = new Cart();
			*/
			// We only allow 1 item per cart from now one...
			$cart = new Cart();
			$cart->addItem($deal);
				
			//Set sesstion for referral ID
			if($referralId!='')
			{
				$cart->setReferralId($referralId);
			}

			JFactory::getSession()->set('cart', serialize($cart));
			$dealName = $deal->name;
			$cartItemCount = $cart->getItem($dealId)->getCount();

			$msg = $dealName . " ". JText::_( "DEAL_ADD_TO_CART");
			$link = JRoute::_("index.php?option=com_enmasse&controller=shopping&task=checkout&buy4friend=$bBuy4friend", false);
			JFactory::getApplication()->redirect($link, $msg);
		}
	}

	function emptyCart()
	{
		$cart = unserialize(JFactory::getSession()->get('cart'));

		if ( !empty($cart) || $cart->getTotalItem() != 0 )
		{
			$cart->deleteAll();
			JFactory::getSession()->set('cart', serialize($cart));
		}

		$msg = JText::_( "EMPTY_CART");
		$link = JRoute::_("index.php?option=com_enmasse&controller=deal&task=listing", false);
		JFactory::getApplication()->redirect($link, $msg);
	}

	function removeItem()
	{
		$itemId = JRequest::getVar('itemId');

		$cart = unserialize(JFactory::getSession()->get('cart'));
		CartHelper::checkCart($cart);
		$cart->deleteItem($itemId);
		JFactory::getSession()->set('cart', serialize($cart));

		$msg = JText::_( "ITEM_REMOVE_MSG");
		$link = JRoute::_("index.php?option=com_enmasse&controller=shopping&task=viewCart", false);
		JFactory::getApplication()->redirect($link, $msg);
	}

	function changeItem()
	{
		$itemId 	= JRequest::getVar('itemId');
		$value 		= JRequest::getVar('value', 1, 'method', 'int');

		if(empty($value))
		{
			$value = 1;
				
		}
		$buy4friend = JRequest::getVar('buy4friend', 0, 'method', 'int');
		//-----------------------------
		// get max purchase per user allowed and check if the update qty is > or not
		$maxBuyQtyOfDeal = EnmasseHelper::getMaxBuyQtyOfDeal($itemId);
		if($maxBuyQtyOfDeal >= 0 && $value > $maxBuyQtyOfDeal)
		{
				
			$msg = JText::_("ITEM_UPDATE_GREATER_THAN_MAX");
			JFactory::getApplication()->redirect("index.php?option=com_enmasse&controller=shopping&task=checkout",$msg);
		}

		$cart = unserialize(JFactory::getSession()->get('cart'));
		CartHelper::checkCart($cart);
		$cart->changeItem($itemId, $value);
		JFactory::getSession()->set('cart', serialize($cart));

		$msg = JText::_( "ITEM_UPDATE_MSG");
		$link = JRoute::_("index.php?option=com_enmasse&controller=shopping&task=checkout&buy4friend=$buy4friend", false);
		JFactory::getApplication()->redirect($link, $msg);
	}

	function changePoint()
	{
		$value = JRequest::getVar('value');
		$buy4friend = JRequest::getVar('buy4friend', 0, 'method', 'int');

		$cart = unserialize(JFactory::getSession()->get('cart'));
		CartHelper::checkCart($cart);
		$currentPoint = $cart->getPoint();
		//$point = $value + $currentPoint;
		$totalPrice = $cart->getTotalPrice();

		//------------------------
		//gemerate integration class
		$isPointSystemEnabled = EnmasseHelper::isPointSystemEnabled();
		if($isPointSystemEnabled==true)
		{
			$integrationClass = EnmasseHelper::getPointSystemClassFromSetting();
			$integrateFileName = $integrationClass.'.class.php';
			require_once (JPATH_SITE . DS ."components". DS ."com_enmasse". DS ."helpers". DS ."pointsystem". DS .$integrationClass. DS.$integrateFileName);
			 
			$integrationObject = new $integrationClass();
			$user = JFactory::getUser();
			$user_id = $user->get('id');

				
			if($value < 0 || !is_numeric($value))
			{
				$msg = JText::_("INVALID_POINT");
				JFactory::getApplication()->redirect(JRoute::_("index.php?option=com_enmasse&controller=shopping&task=checkout&buy4friend=$buy4friend"),$msg);
			}

			if(!$integrationObject->checkEnoughPoint($user_id, $value))
			{
				$msg = JText::_("NOT_ENOUGH_POINT");
				JFactory::getApplication()->redirect(JRoute::_("index.php?option=com_enmasse&controller=shopping&task=checkout&buy4friend=$buy4friend"),$msg);

			}

			if($value < 0 || $value > $totalPrice)
			{
				$msg = JText::_("POINT_GREATER_THAN_TOTAL_PRICE");
				JFactory::getApplication()->redirect(JRoute::_("index.php?option=com_enmasse&controller=shopping&task=checkout&buy4friend=$buy4friend"),$msg);
			}
				
			$cart->changePoint($value);

			JFactory::getSession()->set('cart', serialize($cart));
			$msg = JText::_( "ITEM_UPDATE_MSG");
			$link = JRoute::_("index.php?option=com_enmasse&controller=shopping&task=checkout&buy4friend=$buy4friend", false);
			JFactory::getApplication()->redirect($link, $msg);
		}
		else
		{
			$msg = JText::_( "NO_POINT_SYSTEM");
			$link = JRoute::_("index.php?option=com_enmasse&controller=shopping&task=checkout&buy4friend=$buy4friend", false);
			JFactory::getApplication()->redirect($link, $msg);
		}
	}

	//--------------------------------------------------------------------

	function checkout()
	{
		JRequest::setVar('view', 'shopcheckout');
		$activeGuestBuying = EnmasseHelper::isGuestBuyingEnable();
		if (JFactory::getUser()->get('guest') && $activeGuestBuying==false)
		{
			$msg = JText::_('ORDER_PLEASE_LOGIN_BEFORE');
			$redirectUrl = base64_encode(JRoute::_("index.php?option=com_enmasse&controller=shopping&task=checkout"));
			$link = JRoute::_("index.php?option=com_users&view=login&return=".$redirectUrl, false);

			JFactory::getApplication()->redirect($link, $msg);
		}
		else
		{
			$cart = unserialize(JFactory::getSession()->get('cart'));
			if(empty($cart) || $cart->getTotalItem() == 0)
			{
				$msg = JText::_( "CART_IS_EMPTY");
				$link = JRoute::_("index.php?option=com_enmasse&controller=deal&task=listing", false);

				JFactory::getApplication()->redirect($link, $msg);
			}
				
			$arTotal = array();

			if(JFactory::getUser()->get('id'))
			{
				$arDealId =array();

				foreach ($cart->getAll() as $cartItem)
				{
					$arDealId[] = $cartItem->getItem()->id;
				}

				//get array total quantity of deals
				$arTotal = EnmasseHelper::getTotalBoughtQtyOfUser(JFactory::getUser()->get('id'), $arDealId);

			}
			
			// check cart item
			$this->checkMaxCartItem($cart);
			
			parent::display();

		}

	}

	function submitCheckOut()
	{
		$activeGuestBuying = EnmasseHelper::isGuestBuyingEnable();
		$bBuy4friend = JRequest::getVar('buy4friend', 0);
		$sEmailPt = "/^([0-9a-zA-Z]([-.\w]*[0-9a-zA-Z])*@([0-9a-zA-Z][-\w]*[0-9a-zA-Z]\.)+[a-zA-Z]{2,9})$/";

		//save user input data into the session
		if(JRequest::getMethod() == "POST")
		{
			$arData = JRequest::get('post');
			JFactory::getApplication()->setUserState("com_enmasse.checkout.data", $arData);
		}

		//check the permission for checkout action
		if (JFactory::getUser()->get('guest') && !$activeGuestBuying)
		{
			$msg = JText::_( "MERCHANT_PLEASE_LOGIN_BEFORE");
			$redirectUrl = base64_encode("index.php?option=com_enmasse&controller=shopping&task=checkout&buy4friend=$bBuy4friend");
			$link = JRoute::_("index.php?option=com_users&view=login&return=".$redirectUrl, false);
			JFactory::getApplication()->redirect($link, $msg, 'error');
		}

		//validate the cart
		$cart = unserialize(JFactory::getSession()->get('cart'));
		CartHelper::checkCart($cart);
		foreach($cart->getAll() as $cartItem)
		{
			$item = $cartItem->getItem();
		}
		//get enmasse setting
		$setting = JModel::getInstance('setting','enmasseModel')->getSetting();
		
		// check max cart Item
		$this->checkMaxCartItem($cart);

		//validate Buyer information
		$buyerName 	= JRequest::getVar('name');
		$buyerEmail 	= JRequest::getVar('email');
		if(empty($buyerName) || empty($buyerEmail))
		{
			$msg = JText::_("SHOP_CARD_CHECKOUT_BUYER_INFORMATION_REQUIRED_MSG");
			$link = JRoute::_("index.php?option=com_enmasse&controller=shopping&task=checkout&buy4friend=$bBuy4friend", false);
			JFactory::getApplication()->redirect($link, $msg, 'error');
		}
		elseif (!preg_match($sEmailPt, $buyerEmail))
		{
			$msg = JText::_("SHOP_CARD_CHECKOUT_BUYER_EMAIL_INVALID_MSG");
			$link = JRoute::_("index.php?option=com_enmasse&controller=shopping&task=checkout&buy4friend=$bBuy4friend", false);
			JFactory::getApplication()->redirect($link, $msg, 'error');
		}

		//----- If the deal permit partial payment, it mean the coupon was delivery by directly, so we need to validate address and phone number of receiver
		if($item->prepay_percent <100)
		{
			$receiverAddress = JRequest::getVar('receiver_address');
			$receiverPhone = JRequest::getVar('receiver_phone');
				
			if(empty($receiverPhone) || empty($receiverAddress))
			{
				$msg = JText::_( "SHOP_CARD_CHECKOUT_RECEIVER_INFORMATION_REQUIRED_MSG");
				$link = JRoute::_("index.php?option=com_enmasse&controller=shopping&task=checkout&buy4friend=$bBuy4friend", false);
				JFactory::getApplication()->redirect($link, $msg, 'error');
			}else if(!preg_match('/^[0-9 \.,\-\(\)\+]*$/', $receiverPhone))
			{
				$msg = JText::_( "SHOP_CARD_CHECKOUT_RECEIVER_PHONE_INVALID");
				$link = JRoute::_("index.php?option=com_enmasse&controller=shopping&task=checkout&buy4friend=$bBuy4friend", false);
				JFactory::getApplication()->redirect($link, $msg, 'error');
			}
		}


		if($bBuy4friend)
		{
			$receiverName = JRequest::getVar('receiver_name');
			$receiverEmail = JRequest::getVar('receiver_email');
			$receiverMsg 	= JRequest::getVar('receiver_msg');
			if(empty($receiverName) || empty($receiverEmail))
			{
				$msg = JText::_( "SHOP_CARD_CHECKOUT_RECEIVER_INFORMATION_REQUIRED_MSG");
				$link = JRoute::_("index.php?option=com_enmasse&controller=shopping&task=checkout&buy4friend=$bBuy4friend", false);
				JFactory::getApplication()->redirect($link, $msg, 'error');
			}
			elseif (!preg_match($sEmailPt, $receiverEmail))
			{
				$msg = JText::_("SHOP_CARD_CHECKOUT_RECEIVER_EMAIL_INVALID_MSG");
				$link = JRoute::_("index.php?option=com_enmasse&controller=shopping&task=checkout&buy4friend=$bBuy4friend", false);
				JFactory::getApplication()->redirect($link, $msg, 'error');
			}

		}

		//------------------------------------------------------
		// to check it this deal is free for customer
		if($cart->getTotalPrice() > 0)
		{
			//deal is not free
			$payGtyId 	= JRequest::getVar('payGtyId');
				
			if($payGtyId == null )
			{
				$msg = JText::_( "SELECT_PAYMENT_MSG");
				$link = JRoute::_("index.php?option=com_enmasse&controller=shopping&task=checkout&buy4friend=$bBuy4friend", false);
				JFactory::getApplication()->redirect($link, $msg, 'error');
			}

			if($setting->article_id != 0 && JRequest::getVar('terms')==false)
			{
				$msg = JText::_( "AGREE_TERM_CONDITION_MSG");
				$link = JRoute::_("index.php?option=com_enmasse&controller=shopping&task=checkout&buy4friend=$bBuy4friend", false);
				JFactory::getApplication()->redirect($link, $msg, 'error');
			}
				
			$payGty = JModel::getInstance('payGty','enmasseModel')->getById($payGtyId);
			// checking gateway configuration
			if(CartHelper::checkGty($payGty)==false)
			{
				$msg = JText::_( "PAYMENT_INCOMPLETE_MSG");
				$link = JRoute::_("index.php?option=com_enmasse&controller=shopping&task=checkout&buy4friend=$bBuy4friend", false);
				JFactory::getApplication()->redirect($link, $msg);
			}
				
			// save gty info into session
			JFactory::getSession()->set('payGty', serialize($payGty));
			JFactory::getSession()->set('attribute_config', json_encode($payGty->attribute_config));
				
			//--------If admin set the prepay_percent of the deal to 0.00, set the order status to 'Paid' (with paid_amount is 0.00)
			if($item->prepay_percent == 0.00)
			{
				$status = EnmasseHelper::$ORDER_STATUS_LIST['Paid'];
				$couponStatus = EnmasseHelper::$INVTY_STATUS_LIST['Hold'];
			}else
			{
				//------------------------------------
				// generate name of payment gateway file and class
				$payGtyFile = 'PayGty'.ucfirst($payGty->class_name).'.class.php';
				$className = 'PayGty'.ucfirst($payGty->class_name);
				//---------------------------------------------------
				// get payment gateway object
				require_once (JPATH_SITE . DS ."components". DS ."com_enmasse". DS ."helpers". DS ."payGty". DS .$payGty->class_name. DS.$payGtyFile);
				$paymentClassObj = new $className();
				$paymentReturnStatusObj = $paymentClassObj->returnStatus();

				$status = $paymentReturnStatusObj->order;
				$couponStatus = $paymentReturnStatusObj->coupon;
			}
				
		}
		else
		{
			//deal is free
			$payGty = "Free";
			$status = 'Unpaid';
			$couponStatus = 'Pending';
				
			//save the payGty as free
			JFactory::getSession()->set('payGty', 'Free');
		}
		//----------------------------------------
		//determine information of coupon receiver
		if($bBuy4friend)
		{
			$deliveryDetail = array ('name' => $receiverName, 'email' => $receiverEmail, 'msg' => $receiverMsg, 'address' => $receiverAddress, 'phone' => $receiverPhone);
		}
		else
		{
			$deliveryDetail = array ('name' => $buyerName, 'email' => $buyerEmail, 'msg' => '', 'address' => $receiverAddress, 'phone' => $receiverPhone);
		}

		//--------------------------
		//generate order
		$dvrGty = ($item->prepay_percent < 100)? 2: 1;
		$deliveryGty 	= JModel::getInstance('deliveryGty','enmasseModel')->getById($dvrGty);

		$user = array();
		$user['id'] = JFactory::getUser()->get('id',0);
		$user['name'] = $buyerName;
		$user['email'] = $buyerEmail;

		$order 			= CartHelper::saveOrder($cart, $user, $payGty, null, $deliveryGty, $deliveryDetail,$status);

		$session =& JFactory::getSession();
		$session->set( 'newOrderId', $order->id );

		$orderItemList 	= CartHelper::saveOrderItem($cart, $order,$status);

		//-----------------------------
		// if this deal is set limited the coupon to sold out, go to invty and allocate coupons for this order
		// if not create coupons for that order
		if($item->max_coupon_qty > 0)
		{
			$now = DatetimeWrapper::getDatetimeOfNow();
			$nunOfSecondtoAdd = (EnmasseHelper::getMinuteReleaseInvtyFromSetting($payGty))*60;
			$intvy             = CartHelper::allocatedInvty($orderItemList,DatetimeWrapper::mkFutureDatetimeSecFromNow($now,$nunOfSecondtoAdd),$couponStatus);
		}
		else
		{
			JModel::getInstance('invty','enmasseModel')->generateForOrderItem($orderItemList[0]->pdt_id, $orderItemList[0]->id, $orderItemList[0]->qty, $couponStatus);
		}

		//------------------------
		//generate integration class
		$isPointSystemEnabled = EnmasseHelper::isPointSystemEnabled();
		if($isPointSystemEnabled)
		{
			$integrationClass = EnmasseHelper::getPointSystemClassFromSetting();
			$integrateFileName = $integrationClass.'.class.php';
			require_once (JPATH_SITE . DS ."components". DS ."com_enmasse". DS ."helpers". DS ."pointsystem". DS .$integrationClass. DS.$integrateFileName);
			$user = JFactory::getUser();
			$user_id = $user->get('id');
			$point = $cart->getPoint();
			if($point>0) //If user buys with point, point will be greater than zero
			{
				$integrationObject = new $integrationClass();
				$integrationObject->integration($user_id,'paybypoint',$point);
			}
		}

		//validating is ok, flush user data
		JFactory::getApplication()->setUserState("com_enmasse.checkout.data", null);
			
		// --------------------------------
		// if deal is free then directly do the notify
		if($cart->getTotalPrice() > 0)
		{
			//deal is not free, check if buyer must prepay a specific amount
			if($item->prepay_percent > 0)
			{
				$link = JRoute::_("index.php?option=com_enmasse&controller=payment&task=gateway&orderId=" . $order->id, false);
			}else
			{
				//do notify for the order that not to prepay
				EnmasseHelper::doNotify($order->id);

				$link = JRoute::_("index.php?option=com_enmasse&controller=deal&task=listing");
				$msg = JText::_("PARTIAL_PAYMENT_NO_PREPAY_CHECKOUT_MSG");
				JFactory::getApplication()->redirect($link, $msg);
			}
				
				
		}
		else
		{
			//deal is free
			$link = JRoute::_("index.php?option=com_enmasse&controller=payment&task=doNotify&orderId=$order->id", false);
		}

		JFactory::getApplication()->redirect($link);
			
	}

	private function checkMaxCartItem($cart) {
		
		foreach($cart->getAll() as $cartItem)
		{
			$item = $cartItem->getItem();
			$max_buy_qty = $item->max_buy_qty;
			$currentBuyQty = $cartItem->getCount();
			$boughtQty = empty($arTotal)? 0 : $arTotal[$item->id]->total;
			// to check total bought if it is over allowed qty.
			if($max_buy_qty >=0 && ($boughtQty+$currentBuyQty) > $max_buy_qty)
			{
				$msg = JText::_("QUANTITY_GREATER_THAN_MAX");
				JFactory::getApplication()->redirect(JRoute::_("index.php?option=com_enmasse&controller=deal&task=listing"), $msg, 'error');
			}
			//----------------------------
			// to check if this deal is set with limited coupon to solve out
			if($item->max_coupon_qty > 0)
			{
				// get the coupon which is free from inventory
				$freeCouponArr = JModel::getInstance('invty','enmasseModel')->getCouponFreeByPdtID($item->id);
				//check if the free coupon enough for this order
				if($currentBuyQty > count($freeCouponArr))
				{
					$msg = JText::_("LIMIT_COUPON_QTY").' '.count($freeCouponArr);
					JFactory::getApplication()->redirect(JRoute::_("index.php?option=com_enmasse&controller=shopping&task=reCheckout"), $msg, 'error');
				}

			}
		}
	}
}
?>