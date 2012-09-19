<?php

/* ------------------------------------------------------------------------
  # En Masse - Social Buying Extension 2010
  # ------------------------------------------------------------------------
  # By Matamko.com
  # Copyright (C) 2010 Matamko.com. All Rights Reserved.
  # @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
  # Websites: http://www.matamko.com
  # Technical Support:  Visit our forum at www.matamko.com
  ------------------------------------------------------------------------- */

require_once(dirname(__FILE__) . DS . 'DatetimeWrapper.class.php');
require_once(dirname(__FILE__) . DS . 'BillHelper.class.php');
class EnmasseHelper {

    //-------------------------
    // CONSTANT Values
    public static $DEAL_STATUS_LIST = array(
        "Pending" => "Pending",
        "On Sales" => "On Sales",
        "Confirmed" => "Confirmed",
        "Voided" => "Voided"
    );
    public static $ORDER_STATUS_LIST = array(
        "Cancelled" => "Cancelled",
        "Pending" => "Pending",
        "Unpaid" => "Unpaid",
        "Paid" => "Paid",
        "Delivered" => "Delivered",
        "Refunded" => "Refunded",
    	"Waiting_For_Refund" => "Waiting_For_Refund",
    	"Cancel" => "Cancel",
    	"Holding_By_Deliverer" => "Holding_By_Deliverer"
    );
    public static $ORDER_ITEM_STATUS_LIST = array(
        "Cancelled" => "Cancelled",
        "Unpaid" => "Unpaid",
        "Paid" => "Paid",
        "Delivered" => "Delivered",
        "Refunded" => "Refunded",
    	"Waiting_For_Refund" => "Waiting_For_Refund",
    	"Cancel" => "Cancel"
    );
    public static $INVTY_STATUS_LIST = array(
        "Cancelled" => "Cancelled",
    	"Pending" => "Pending",
    	"Free" => "Free",
        "Hold" => "Hold",
        "Taken" => "Taken",
        "Used" => "Used"
    );
        
    public static $MERCHANT_SETTLEMENT_STATUS_LIST = array(
    	"Not_Paid_Out" => "Not_Paid_Out",
    	"Should_Be_Paid_Out" => "Should_Be_Paid_Out",
    	"Paid_Out" => "Paid_Out",
    );

    //-------------------------
    // Theme
    private static function themeList() {
        $list = scandir(JPATH_SITE . DS . 'components' . DS . 'com_enmasse' . DS . 'theme', 0);
        //		return array ('default', 'extended');
        $returnList = array();
        $count = 0;
        for ($i = 0; $i < count($list); $i++) {
            if ($list[$i] != ".." && $list[$i] != "." && $list[$i] != "css" && $list[$i] != "js") {
                $returnList[$count] = $list[$i];
                $count+=1;
            }
        }
        return $returnList;
    }
    
    public static function webThemeList() {
    	$list = EnmasseHelper::themeList();
    	$returnList = array();
    	$count = 0;
        for ($i = 0; $i < count($list); $i++) {
	        $pos = strrpos($list[$i], "mobile");
			if ($pos === false) { 
				$returnList[$count] = $list[$i];
                $count+=1;
			}
        }
        return $returnList;
    }
    
	public static function mobileThemeList() {
    	$list = EnmasseHelper::themeList();
    	$webThemeList = EnmasseHelper::webThemeList();
    	$returnList = array();
    	$count = 0;
        for ($i = 0; $i < count($list); $i++) {
			if (! in_array($list[$i], $webThemeList)) { 
				$returnList[$count] = $list[$i];
                $count+=1;
			}
        }
        return $returnList;
    }
    
    public static function  getSetting()
    {
    	static $oSetting;
    	if(empty($oSetting))
    	{
    		$db = JFactory::getDBO();
        	$query = "SELECT * FROM #__enmasse_setting limit 1";
       		$db->setQuery($query);
       		$oSetting = $db->loadObject();
    		if ($db->getErrorNum())
    		{
				JError::raiseError( 500, $db->getErrorMsg() );
				return false;
			}
    	}
    	
    	return $oSetting;
    }

    public static function getThemeFromSetting()
    {
    	$oSetting = self::getSetting();
    	$device = JRequest::getVar('is_mobile');
    	return $device?$oSetting->mobile_theme:$oSetting->theme;
    }
    
    public static function getMobileThemeFromSetting()
    {
    	$oSetting = self::getSetting();
    	return $oSetting->mobile_theme;
    }

    public static function getLocationPopUpActiveFromSetting() {
    	$oSetting = self::getSetting();
        return $oSetting->active_popup_location;
    }

    public static function getMinuteReleaseInvtyFromSetting($payGty = null) {
        $oSetting = self::getSetting();
        
        // Minutes to release coupon for cash payment
        if($payGty != null && isset($payGty->class_name))
        {
            if($payGty->class_name == 'cash')
            {
                return $oSetting->cash_minute_release_invty;
            }        
        }
        // Minutes to release coupon for other payments
        return $oSetting->minute_release_invty;          

    }

    //-----------------------------------
    // subscription list
    public static function subscriptionList() {
        $list = scandir(JPATH_SITE . DS . 'components' . DS . 'com_enmasse' . DS . 'helpers' . DS . 'subscription', 0);
        //		return array ('default', 'extended');
        $returnList = array();
        $count = 0;
        for ($i = 0; $i < count($list); $i++) {
            if ($list[$i] == ".." || $list[$i] == ".") {
                
            } else {
                $returnList[$count] = $list[$i];
                $count+=1;
            }
        }
        return $returnList;
    }

    //-----------------------------------
    // point system list
    public static function pointSystemList() {
        $list = scandir(JPATH_SITE . DS . 'components' . DS . 'com_enmasse' . DS . 'helpers' . DS . 'pointsystem', 0);
        //		return array ('default', 'extended');
        $returnList = array();
        $count = 0;
        for ($i = 0; $i < count($list); $i++) {
            if ($list[$i] == ".." || $list[$i] == ".") {
                
            } else {
                $returnList[$count] = $list[$i];
                $count+=1;
            }
        }
        return $returnList;
    }

    //-------------------------
    // Token

    public static function generateOrderItemToken($orderItemId, $orderItemCreatedAt) {
        return md5($orderItemId . $orderItemCreatedAt . $orderItemId);
    }

    public static function generateCouponToken($name) {
        return md5($name);
    }

    //-------------------------
    // Display Control

    public static function displayOrderDisplayId($id) {
        return str_pad($id, 10, '0', STR_PAD_LEFT);
    }

    public static function displayCurrency($num) {
    	$setting = self::getSetting();
    	    	
        $var = floatval($num);
        $dec_point = '.';
        $thousand_sep = ',';
        if ($setting->currency_separator != "")
            $thousand_sep = $setting->currency_separator;

        if ($setting->currency_decimal_separator != "")
            $dec_point = $setting->currency_decimal_separator;

        $dec_num = 2;
        if ($setting->currency_decimal != "")
            $dec_num = intval($setting->currency_decimal);

        $var = number_format($var, $dec_num, $dec_point, $thousand_sep);

        $prex = '';
        $post = '';
        if ($setting->currency_prefix)
            $prex = $setting->currency_prefix;
        if ($setting->currency_postfix)
            $post = $setting->currency_postfix;
        return $prex . '' . $var . ' ' . $post;
    }

    public static function displayJson($string) {
        $arr = json_decode($string);
        if ($arr) {
            $result = '<table>';
            foreach ($arr as $key => $value) {
                $result .= '<tr><td>';
                $result .= '<div align="left"><b>' . ucWords($key) . '</b> : ' . $value . '</div>';
                $result .= '</td></tr>';
            }
            $result .= '</table>';
            return $result;
        }
        return '';
    }

    public static function getBuyerId($buyer) {
        if ($buyer) {
            return $buyer->id;
        }
        return '';
    }

    public static function displayBuyer($buyer) {
        if ($buyer) {
            $result = $buyer->name . '<br/>(' . $buyer->email . ')';
            return $result;
        }
        return '';
    }

    //-------------------------
    // System

    public static function sendMailByTemplate($mailto, $templateName, $params=array(), $attachFileName = "") {
        $app = JFactory::getApplication();

        // get Template
        $db =  JFactory::getDBO();
        $query = "SELECT * FROM #__enmasse_email_template WHERE ";
        $query .= "slug_name='$templateName'";
        $db->setQuery($query);

        $templateObj = $db->loadObject();
        if (empty($templateObj)) {
            $app->enqueueMessage("Template $templateName not found !!", 'error');
            return;
        }
        // replace params
        foreach ($params as $key => $value) {
            $templateObj->subject = str_replace($key, $value, $templateObj->subject);
            $templateObj->content = str_replace($key, $value, $templateObj->content);
        }

        self::sendMail($mailto, $templateObj->subject, $templateObj->content, $attachFileName);
    }

    public static function sendMail($mailto, $subject, $body, $attachFileName = "") {
        $app = JFactory::getApplication();
        $mailer = JFactory::getMailer();
        $config = JFactory::getConfig();

        $sender = array(
            $config->getValue('config.mailfrom'),
            $config->getValue('config.fromname')
        );

        $recipient = array($mailto); //$user->email;

        $mailer->setSubject($subject);
        $mailer->isHTML(true);
        $mailer->setBody($body);
		if(!empty($attachFileName))
		{
			$mailer->addAttachment($attachFileName);
		}
        $mailer->addRecipient($recipient);
        $mailer->setSender($sender);
        $send = &$mailer->Send();

        if ($send !== true) {
            //$app->enqueueMessage('Error Sending email:<br/>'.$send->message,'error');
            return false;
        } else {
            return true;
        }
    }

    public static function changePublishState($state=0, $table, $R_action, $jtable) {
        global $mainframe;

        $version = new JVersion;
        $joomla = $version->getShortVersion();
        if (substr($joomla, 0, 3) >= 1.6) {
            $mainframe = JFactory::getApplication();
        }

        // Initialize variables
        $db =  JFactory::getDBO();

        // define variable $cid from GET
        $cid = JRequest::getVar('cid', array(), '', 'array');
        JArrayHelper::toInteger($cid);

        // Check there is/are item that will be changed.
        //If not, show the error.
        if (count($cid) < 1) {
            $action = $state ? 'publish' : 'unpublish';
            JError::raiseError(500, JText::_('NO_ITEM_SELECTED', true));
        }

        // Prepare sql statement, if cid more than one,
        // it will be "cid1, cid2, cid3, ..."
        $cids = implode(',', $cid);

        $query = 'UPDATE #__' . $table .
                ' 	SET published = ' . (int) $state .
                ' 	WHERE id IN ( ' . $cids . ' )'
        ;
        // Execute query
        $db->setQuery($query);
        if (!$db->query()) {
            JError::raiseError(500, $db->getErrorMsg());
        }

        if (count($cid) == 1) {
            $row = & JTable::getInstance($jtable, 'Table');
            //		    $row->checkin( intval( $cid[0] ) );
        }

        // After all, redirect to front page
        $mainframe->redirect('index.php?option=com_enmasse&controller=' . $R_action);
    }

    public static function seoUrl($string) {
        //Unwanted:  {UPPERCASE} ; / ? : @ & = + $ , . ! ~ * ' ( )
        $string = strtolower($string);
        //Strip any unwanted characters
        $string = preg_replace("/[^a-z0-9_\s-]/", "", $string);
        //Clean multiple dashes or whitespaces
        $string = preg_replace("/[\s-]+/", " ", $string);
        //Convert whitespaces and underscore to dash
        $string = preg_replace("/[\s_]/", "_", $string);
        return $string;
    }

    //-------------------------------
    // Deal
    public static function orderItemDelivered($orderItem) {
       
        $deal = JModel::getInstance('deal', 'enmasseModel')->getById($orderItem->pdt_id);
		
        //-------------------------------------------------------------------------
        // update sold coupon status to taken
        JModel::getInstance('invty', 'enmasseModel')->updateStatusByPdtIdAndStatus($orderItem->pdt_id, "Taken", "Sold");

        //----------------------------------------------------------------------
        // Update Order Item status to delivered
        JModel::getInstance('orderItem', 'enmasseModel')->updateStatus($orderItem->id, "Delivered");
        JModel::getInstance('orderItem', 'enmasseModel')->updateIsDelivered($orderItem->id, 1);

        //--------------------------------------------------------------------
        // Update Order, check if all order item are in the status first
        $order = JModel::getInstance('order', 'enmasseModel')->getById($orderItem->order_id);

        $tempOrderItemList = JModel::getInstance('orderItem', 'enmasseModel')->listByOrderId($orderItem->order_id);
        $checkFlag = true;
        foreach ($tempOrderItemList as $tempOrderItem) {
            if ($tempOrderItem->status != "Delivered") {
                $checkFlag = false;
                break;
            }
        }
        //---------------------------------------------------------
        // update order status to delivered
        if ($checkFlag)
            JModel::getInstance('order', 'enmasseModel')->updateStatus($order->id, "Delivered");
    	//------ check whether deal is partial payment or not, if the deal is partial, dont email the coupon becuase we will delivery directly and get the remain amount
        if($deal->prepay_percent < 100)
		{
			return;
		}
        //--------------------
        // Email the Buyer
        $buyerDetail = json_decode($order->buyer_detail);
        $deliveryDetail = json_decode($order->delivery_detail);

        $params = array();
        $params['$orderId'] = self::displayOrderDisplayId($order->id);
        $params['$dealName'] = $orderItem->description;
        $params['$buyerName'] = $buyerDetail->name;
        $params['$deliveryName'] = $deliveryDetail->name;
        $params['$deliveryEmail'] = $deliveryDetail->email;
        
        self::sendMailByTemplate($buyerDetail->email, 'confirm_deal_buyer', $params);
       
        //---------------------------
        // Email the Receiver

        $token = self::generateOrderItemToken($orderItem->id, $orderItem->created_at);
        $link = JURI::root() . '/index.php?option=com_enmasse&controller=coupon&task=listing&orderItemId=' . $orderItem->id . '&token=' . $token . '&buffer=';
        $params = array();
        $params['$orderId'] = self::displayOrderDisplayId($order->id);
        $params['$dealName'] = $orderItem->description;
        $params['$buyerName'] = $buyerDetail->name;
        $params['$deliveryName'] = $deliveryDetail->name;
        $params['$deliveryEmail'] = $deliveryDetail->email;
        $params['$deliveryMsg'] = $deliveryDetail->msg;
        $params['$linkToCoupon'] = $link;
        
        self::sendMailByTemplate($deliveryDetail->email, 'confirm_deal_receiver', $params);
    }
    
	public static function orderItemRefunded($orderItem) {
        //--------------------
        // Update Order Item to refunded status
        JModel::getInstance('orderItem', 'enmasseModel')->updateStatus($orderItem->id, self::$ORDER_ITEM_STATUS_LIST["Refunded"]);
       
        //--------------------
        // Update Order, check if all order item are in the status first
        $order = JModel::getInstance('order', 'enmasseModel')->getById($orderItem->order_id);

        $tempOrderItemList = JModel::getInstance('orderItem', 'enmasseModel')->listByOrderId($orderItem->order_id);
        $checkFlag = true;
        
        foreach ($tempOrderItemList as $tempOrderItem)
        {
            if ($tempOrderItem->status != self::$ORDER_ITEM_STATUS_LIST["Refunded"])
            {
                $checkFlag = false;
                break;
            }
        }
        
        if ($checkFlag)
            JModel::getInstance('order', 'enmasseModel')->updateStatus($order->id, self::$ORDER_STATUS_LIST["Refunded"]);
        
    }

    public static function orderItemWaitingForRefund($orderItem) {
        //--------------------
        // Update Order Item
        JModel::getInstance('orderItem', 'enmasseModel')->updateStatus($orderItem->id, self::$ORDER_ITEM_STATUS_LIST["Waiting_For_Refund"]);
        
        // only reduce quality sold when the order is at status "refunded"
        //JModel::getInstance('deal', 'enmasseModel')->reduceQtySold($orderItem->pdt_id, $orderItem->qty);
        //--------------------
        // Update Order, check if all order item are in the status first
        $order = JModel::getInstance('order', 'enmasseModel')->getById($orderItem->order_id);

        $tempOrderItemList = JModel::getInstance('orderItem', 'enmasseModel')->listByOrderId($orderItem->order_id);
        $checkFlag = true;
        foreach ($tempOrderItemList as $tempOrderItem) {
            if ($tempOrderItem->status != self::$ORDER_ITEM_STATUS_LIST["Waiting_For_Refund"]) {
                $checkFlag = false;
                break;
            }
        }
        if ($checkFlag)
            JModel::getInstance('order', 'enmasseModel')->updateStatus($order->id, self::$ORDER_STATUS_LIST["Waiting_For_Refund"]);

        $buyer = json_decode($order->buyer_detail);
        $delivery = json_decode($order->delivery_detail);

        $pointUsedToPay = $order->point_used_to_pay;

        //--------------------
        // Get Adin's mail
        
        $setting = self::getSetting();

        $params = array();
        $params['$dealName'] = $deal->name;
        $params['$buyerName'] = $buyer->name;
        $params['$buyerEmail'] = $buyer->email;
        $params['$deliveryName'] = $delivery->name;
        $params['$deliveryEmail'] = $delivery->email;
        $params['$orderId'] = self::displayOrderDisplayId($order->id);
        $buyerEmail = $buyer->email;
        
        if ($pointUsedToPay > 0) { //If buyer paid with point, send with a different mail template
            $moneyUsedToPay = $orderItem->total_price - $pointUsedToPay;
            $params['$refundAmt'] = self::displayCurrency($moneyUsedToPay);
            $params['$refundPoint'] = $pointUsedToPay;
            //--------------------
            // Email the Buyer
            self::sendMailByTemplate($buyerEmail, 'void_deal_with_point', $params);
            //--------------------
            // Email the Admin
            self::sendMailByTemplate($setting->customer_support_email, 'void_deal_with_point', $params);
        } else { // If buyer didn't pay with point, send with normal template
            $params['$refundAmt'] = self::displayCurrency($orderItem->total_price);
            //--------------------
            // Email the Buyer
            self::sendMailByTemplate($buyerEmail, 'void_deal', $params);
            //--------------------
            // Email the Admin
            self::sendMailByTemplate($setting->customer_support_email, 'void_deal', $params);
        }
    }

    //--------------------
    // CURL
    public static function post($postUrl, $postDataStr) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $postUrl); // URL to post
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postDataStr);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $result = curl_exec($ch); // runs the post
        if (curl_errno($ch)) {
            curl_close($ch);
            return false;
        } else {
            curl_close($ch);
            return true;
        }
    }

    //--------------------
    // json decode from string
    public static function jsonDecoder($postdata) {
        $first_arr = explode("&", $postdata);
        $attribute_array = array();
        for ($i = 0; $i < count($first_arr); $i++) {
            $second_arr = explode('=', $first_arr[$i]);
            $key = $second_arr[0];
            $attribute_array[$key] = $second_arr[1];
        }
        return $attribute_array;
    }

    //--------------------
    // to generate html table of report for exporting
    public static function reportGenerator($itemList) {
        echo '<table border="1">';
        echo "<thead>
				         <tr><td colspan='9' style='font-size:16px; color:#0000FF; text-align:center;'>" . JTEXT::_('REPORT_TITLE') . "</td> </tr>
						<tr>
							<th width=\"5%\">" . JTEXT::_('REPORT_SERIAL') . "</th>
							<th width=\"15%\">" . JTEXT::_('REPORT_BUYER_NAME') . "</th>
							<th width=\"15%\">" . JTEXT::_('REPORT_BUYER_MAIL') . "</th>
							<th width=\"15%\">" . JTEXT::_('REPORT_DELIVERY_NAME') . "</th>
							<th width=\"15%\">" . JTEXT::_('REPORT_DELIVERY_MAIL') . "</th>
							<th width=\"15%\">" . JTEXT::_('REPORT_ORDER_COMMENT') . "</th>
							<th width=\"10%\">" . JTEXT::_('REPORT_PURCHASE_DATE') . "</th>
							<th width=\"5%\">" . JTEXT::_('REPORT_COUPON_SERIAL') . "</th>
							<th width=\"5%\">" . JTEXT::_('REPORT_COUPON_STATUS') . "</th>
						</tr>
					</thead>";
        for ($i = 0; $i < count($itemList); $i++) {
            $itemOrder = $itemList[$i];
            echo '<tr>';
            echo '<td>' . $itemOrder['Serial No.'] . '</td>';
            echo '<td>' . $itemOrder['Buyer Name'] . '</td>';
            echo '<td>' . $itemOrder['Buyer Email'] . '</td>';
            echo '<td>' . $itemOrder['Delivery Name'] . '</td>';
            echo '<td>' . $itemOrder['Delivery Email'] . '</td>';
            echo '<td>' . $itemOrder['Order Comment'] . '</td>';
            echo '<td>' . $itemOrder['Purchase Date'] . '</td>';
            echo '<td style="text-align:center;"># ' . $itemOrder['Coupon Serial'] . '</td>';
            echo '<td style="text-align:center;">' . $itemOrder['Coupon Status'] . '</td>';
            echo '</tr>';
        }
        echo '</table>';
    }

    //--------------------
    // to get article title for setting
    public static function getArticleTitleById($id) {
        $db =  JFactory::getDBO();
        $query = "SELECT title FROM #__content Where id=" . $id;
        $db->setQuery($query);
        $content = $db->loadObject();
        return $content->title;
    }

    //-----------------------
    public static function setLastAction($option, $view) {
        $session = JFactory::getSession();
        if ($option == "com_user" && $view == 'register')
            $session->set('lastAction', 'registation');
        else
            $session->set('lastAction', 'none');
    }

    //--------------------
    // to do order notify

    public static function doNotify($orderId) {
        $order = JModel::getInstance('order', 'enmasseModel')->getById($orderId);
        JModel::getInstance('order', 'enmasseModel')->updateStatus($order->id, 'Paid');

        $orderItemList = JModel::getInstance('orderItem', 'enmasseModel')->listByOrderId($orderId);
        $totalQty = 0;

        for ($count = 0; $count < count($orderItemList); $count++) {
            $orderItem = $orderItemList[$count];
            // only add total sold when order has status of delivered
            //JModel::getInstance('deal', 'enmasseModel')->addQtySold($orderItem->pdt_id, $orderItem->qty);
            JModel::getInstance('orderItem', 'enmasseModel')->updateStatus($orderItem->id, 'Paid');
            JModel::getInstance('invty', 'enmasseModel')->updateStatusByOrderItemId($orderItem->id, 'Sold');

            if ($count == 0)
                $dealName = $orderItem->description;
            elseif ($count == (count($orderItemList) - 1))
                $dealName .= " & " . $orderItem->description;
            else
                $dealName .= " , " . $orderItem->description;

            $totalQty += $orderItem->qty;
        }
        
        //---------------------------
        //Create the receipt with pdf format
		//just create bill if the order is not free and buyer have full paid
		//remember full paid = order->status = "paid" and order->paid_amount == 0.0
		if($order->total_buyer_paid > 0 && $order->paid_amount == 0.0)
		{
			$sBillName = BillHelper::createPDF($orderId);
		}else
		{
			$sBillName = "";
		}
		
        
        //--------------------------
        // Sending email
        $payment = json_decode($order->pay_detail);
        $buyer = json_decode($order->buyer_detail);
        $delivery = json_decode($order->delivery_detail);

        $params = array();
        $params['$buyerName'] = $buyer->name;
        $params['$buyerEmail'] = $buyer->email;
        $buyerEmail = $buyer->email;
        
        $params['$deliveryName'] = $delivery->name;
        $params['$deliveryEmail'] = $delivery->email;
        $params['$orderId'] = self::displayOrderDisplayId($order->id);
        $params['$dealName'] = $dealName;
        $params['$totalPrice'] = self::displayCurrency($order->paid_amount);
        $params['$totalQty'] = $totalQty;
        $params['$createdAt'] = DatetimeWrapper::getDisplayDatetime($order->created_at);

		if(self::getSetting()->sending_bill_auto == 1)
        {
        	self::sendMailByTemplate($buyerEmail, 'receipt', $params, $sBillName);//send mail with bill attachment
        }else
        {
        	self::sendMailByTemplate($buyerEmail, 'receipt', $params);//send mail with no attachment
        }
        foreach ($orderItemList as $orderItem) {
            $deal = JModel::getInstance('deal', 'enmasseModel')->getById($orderItem->pdt_id);
            if ($deal->status == "Confirmed" && $deal->prepay_percent == 100.0)
                self::orderItemDelivered($orderItem);
                
            //--- auto confirm deal
            if($deal->auto_confirm && $deal->status != "Confirmed" && $deal->min_needed_qty <= $deal->cur_sold_qty)
            {
	            //------------------------
				//generate integration class			
				$isPointSystemEnabled = EnmasseHelper::isPointSystemEnabled();
				if($isPointSystemEnabled==true)
				{
					$integrationClass = EnmasseHelper::getPointSystemClassFromSetting();
					$integrateFileName = $integrationClass.'.class.php';			
					require_once (JPATH_SITE . DS ."components". DS ."com_enmasse". DS ."helpers". DS ."pointsystem". DS .$integrationClass. DS.$integrateFileName);
				}
				//--- update deal status to "Confirmed"
				JModel::getInstance('deal','enmasseModel')->updateStatus($deal->id, 'Confirmed');
				//--- delivery coupon by email if the deal is not partial payment
				if($deal->prepay_percent == 100.0)
				{
					$arOrderItemDelivery = JModel::getInstance('orderItem','enmasseModel')->listByPdtIdAndStatus($deal->id, "Paid");
						
					foreach($arOrderItemDelivery as $oOrderItem)
					{
						EnmasseHelper::orderItemDelivered($oOrderItem);

						//------------------------
						//generate integration class
						if($isPointSystemEnabled==true)
						{
							$buyerId = EnmasseHelper::getUserIdByOrderId($oOrderItem->order_id);
							$referralId = EnmasseHelper::getReferralIdByOrderId($oOrderItem->order_id);
							$integrationObject = new $integrationClass();
							$integrationObject->integration($buyerId,'confirmdeal');
								
							if(!empty($referralId))
							{
								$integrationObject->integration($referralId,'referralbonus');
							}
						}
							
						sleep(1);
					}
				}
				//--- Refund point to who paid with point but without paying by cash
				if($isPointSystemEnabled==true)
				{
					$buyerList = EnmasseHelper::getBuyerRefundConfirmDeal($deal->id);
					$integrationObject = new $integrationClass();
					foreach($buyerList as $buyer)
					{
						$integrationObject->integration($buyer['buyer_id'],'refunddeal',$buyer['point_used_to_pay']);
					}
				}
            }
        }
                
    }

    //------------------------------------
    //-- to get deal image height & width
    public static function getDealImageSize() {
        $db =  JFactory::getDBO();
        $query = "SELECT image_height,image_width FROM #__enmasse_setting limit 1";
        $db->setQuery($query);
        $setting = $db->loadObject();
        return $setting;
    }

    /* Get max value */

    public static function getMaxBuyQtyOfDeal($id) {
        $db = &JFactory::getDBO();
        $query = "SELECT max_buy_qty FROM #__enmasse_deal where id=$id";
        $db->setQuery($query);
        return $db->loadResult();
    }

    //------------------------------------------------
    // to get the total bought quantity on a deal of one user
    
    /**
     * 
     * to get the total bought quantity on  deals of one user
     * @param int $nUserId 
     * @param array $arDealId list of deal id that need to find the total bought
     * @return array associate array with key is the deal id
     */

    public static function getTotalBoughtQtyOfUser($nUserId, $arDealId) {
                
        $db = JFactory::getDbo();
        $query = "SELECT oi.pdt_id, SUM(oi.qty) AS total
        			FROM #__enmasse_order_item oi
        			INNER JOIN #__enmasse_order o ON oi.order_id = o.id
        			WHERE oi.pdt_id IN (" .implode(", ", $arDealId).")
        			AND o.buyer_id = $nUserId
        			AND o.status IN ('Paid', 'Delivered')
        			GROUP BY oi.pdt_id";
       
        $db->setQuery($query);
        $arTotal = $db->loadObjectList('pdt_id');
        
        return $arTotal;
        
    }
    
    

    public static function is_urlEncoded($string) {
        $test_string = $string;
        while (urldecode($test_string) != $test_string) {
            $test_string = urldecode($test_string);
        }
        return (urlencode($test_string) == $string) ? True : False;
    }

    public static function getModuleById($id) {
        $db =  JFactory::getDBO();
        $query = "SELECT * FROM #__modules where id = $id";
        $db->setQuery($query);
        $module = $db->loadObject();
        return $module;
    }

    public static function getSubscriptionClassFromSetting() {
        $db =  JFactory::getDBO();
        $query = "SELECT subscription_class FROM #__enmasse_setting limit 1";
        $db->setQuery($query);
        $setting = $db->loadObject();
        return $setting->subscription_class;
    }

    public static function getPointSystemClassFromSetting() {
        $db =  JFactory::getDBO();
        $query = "SELECT point_system_class FROM #__enmasse_setting limit 1";
        $db->setQuery($query);
        $setting = $db->loadObject();
        return $setting->point_system_class;
    }

    public static function isPointSystemEnabled() {
        $db =  JFactory::getDBO();
        $query = "SELECT point_system_class FROM #__enmasse_setting limit 1";
        $db->setQuery($query);
        $setting = $db->loadObject();
        if ($setting->point_system_class == 'no') {
            return false;
        } else {
            return true;
        }
    }

    public static function getUserByName($name) {
    	static $instance = array();
    	if(!empty($instance[$name])) return $instance[$name];
    	else
    	{
	        $db =  JFactory::getDBO();
	        $query = "SELECT * FROM #__users WHERE username = '" . $name . "'";
	        $db->setQuery($query);
	        $user = $db->loadObject();
	        if($user)
	        {
	        	$instance[$name] = $user;
	        	return $user;
	        }else
	        {
	        	return null;
	        }
    	}
    }

    public static function getUserIdByOrderId($orderId) {
        $db =  JFactory::getDBO();
        $query = "SELECT buyer_id FROM #__enmasse_order WHERE id = '" . $orderId . "'";
        $db->setQuery($query);
        $buyer_id = $db->loadResult();
        return $buyer_id;
    }

    public static function getReferralIdByOrderId($orderId) {
        $db =  JFactory::getDBO();
        $query = "SELECT referral_id FROM #__enmasse_order WHERE id = '" . $orderId . "'";
        $db->setQuery($query);
        $referralId = $db->loadResult();
        return $referralId;
    }

    public static function getPointPaidByOrderId($orderId) {
        $db =  JFactory::getDBO();
        $query = "SELECT point_used_to_pay FROM #__enmasse_order WHERE id = '" . $orderId . "'";
        $db->setQuery($query);
        $pointUsedToPay = $db->loadResult();
        return $pointUsedToPay;
    }

    public static function getTotalPriceByOrderId($orderId) {
        $db =  JFactory::getDBO();
        $query = "SELECT total_buyer_paid FROM #__enmasse_order WHERE id = '" . $orderId . "'";
        $db->setQuery($query);
        $totalPrice = $db->loadResult();
        return $totalPrice;
    }

    public static function getOrderStatusByOrderId($orderId) {
        $db =  JFactory::getDBO();
        $query = "SELECT status FROM #__enmasse_order WHERE id = '" . $orderId . "'";
        $db->setQuery($query);
        $orderStatus = $db->loadResult();
        return $orderStatus;
    }

    public static function getDealNameByOrderId($orderId) {
        $db =  JFactory::getDBO();
        $query = "SELECT name FROM #__enmasse_deal WHERE id IN (SELECT signature FROM #__enmasse_order_item WHERE order_id = '" . $orderId . "')";
        $db->setQuery($query);
        $dealName = $db->loadResult();
        return $dealName;
    }

    public static function getRefundedAmountByOrderId($orderId) {
        $db =  JFactory::getDBO();
        $query = "SELECT refunded_amount FROM #__enmasse_order WHERE id = '" . $orderId . "' AND status = 'Refunded'";
        $db->setQuery($query);
        $refundedAmount = $db->loadResult();
        return $refundedAmount;
    }

    public static function setPaidStatusByOrderId($orderId) {
        $db =  JFactory::getDBO();
        $query = "UPDATE #__enmasse_order SET status = 'Paid' WHERE id = '" . $orderId . "';";
        $db->setQuery($query);
        $db->query();
        $query = "UPDATE #__enmasse_order_item SET status = 'Paid' WHERE order_id = '" . $orderId . "';";
        $db->setQuery($query);
    }

    public static function setRefundedStatusByOrderId($orderId) {
        $query = "UPDATE #__enmasse_order SET status = 'Refunded' WHERE id = '" . $orderId . "';";
        $db->setQuery($query);
        $db->query();
        $query = "UPDATE #__enmasse_order_item SET status = 'Refunded' WHERE order_id = '" . $orderId . "';";
        $db->setQuery($query);
        $db->query();
    }

    public static function setPendingStatusByOrderId($orderId) {
        $query = "UPDATE #__enmasse_order SET status = 'Pending' WHERE id = '" . $orderId . "';";
        $db->setQuery($query);
        $db->query();
        $query = "UPDATE #__enmasse_order_item SET status = 'Pending' WHERE order_id = '" . $orderId . "';";
        $db->setQuery($query);
        $db->query();
    }

    public static function getBuyerRefundVoidDeal($dealId) {
        $db =  JFactory::getDBO();
        $query = "SELECT buyer_id, point_used_to_pay FROM #__enmasse_order WHERE id IN (SELECT order_id FROM #__enmasse_order_item WHERE pdt_id = '" . $dealId . "') AND point_used_to_pay>0;";
        $db->setQuery($query);
        return $db->loadAssocList();
    }

    public static function getBuyerRefundConfirmDeal($dealId) {
        $db =  JFactory::getDBO();
        $query = "SELECT buyer_id, point_used_to_pay FROM #__enmasse_order WHERE id IN (SELECT order_id FROM #__enmasse_order_item WHERE pdt_id = '" . $dealId . "' AND status='Pending' OR status='Unpaid') AND point_used_to_pay>0;";
        $db->setQuery($query);
        return $db->loadAssocList();
    }

    public static function isGuestBuyingEnable() {
        $db =  JFactory::getDBO();
        $query = "SELECT active_guest_buying FROM #__enmasse_setting limit 1";
        $db->setQuery($query);
        $activeGuestBuying = $db->loadResult();
        if ($activeGuestBuying == '1') {
            return true;
        } else {
            return false;
        }
    }
    
	/**
	 * 
	 * Get locations name by deal id
	 * @param $dDealId
	 * @return array location name
	 */
    public static function getDealLocationNames($dDealId)
    {
    	$db = JFactory::getDBO();
    	$query = "SELECT
    	                loc.name
    	          FROM 
    	               `#__enmasse_location` loc 
    	          		INNER JOIN
    	          		`#__enmasse_deal_location` d_loc
    	          		ON loc.id = d_loc.location_id
    	          WHERE 
    	               d_loc.deal_id = $dDealId";
    	$db->setQuery($query);
    	$names = $db->loadResultArray();
    	return $names;
    }

	public static function checkCupponOfMerchant($couName, $dAuthorId)
	{
		$db =& JFactory::getDBO();
		$couName = $db->getEscaped($couName);
		$query = "	SELECT
						d.id
					FROM 
						#__enmasse_deal d
					INNER JOIN #__enmasse_invty i
					ON d.id = i.pdt_id
					WHERE
	              		i.name = '$couName'
	              	AND 
	              		d.merchant_id = $dAuthorId ";
		
		$db->setQuery( $query );
		
		return $db->loadResult();
	}
	
	public static function escapeSqlLikeSpecialChar($keyword)
	{
		$sPattern = '/([%_])/';
		$sReplace = '\\\\$1';
		return preg_replace($sPattern, $sReplace, $keyword);
	}
    
	public static function getJoomlaUserGroups()
	{
		$db =  JFactory::getDBO();
		$query = "SELECT id, title AS name FROM #__usergroups";
		
		$db->setQuery($query);
		
		return $db->loadObjectList();
	}
    
    public static function isAllowedPayByPoint($dealId) {
        $db =  JFactory::getDBO();
        $query = "SELECT pay_by_point FROM #__enmasse_deal WHERE id = " . $dealId;
        $db->setQuery($query);
        $payByPoint = $db->loadResult();
        if($payByPoint==1)
        {
        	return true;
        }
        else
        {
        	return false;
        }
    }
    
	public static function countDealRatingByDealId($nDealId)
	{
		$db = JFactory::getDBO();
		$query = "	SELECT 
						rating 
					FROM 
						#__enmasse_comment 
					WHERE
                        status = 1
                    AND
						deal_id = $nDealId";
		$db->setQuery( $query );
		$aResult = $db->loadResultArray();       
        $nRating = 0;
        $nCount = 0;
        
		if(!empty($aResult))
		{
			foreach($aResult as $nResult)
			{
				$nRating += $nResult;
				$nCount++;
			}
		}

        if($nCount == 0)
        {
            return 0;
        }
        else
        {
            return $nRating/$nCount;
        }
	}

    public static function markUserAsSpammer($nCommentId)
    {     
        $comment = JModel::getInstance('Comment', 'EnmasseModel')->getCommentById($nCommentId);
        $user = JFactory::getUser($comment->user_id);
        if(EnmasseHelper::checkSpammer($comment->user_id))
        {
            $sMessage = JText::sprintf('COMMENT_USER_ALREADY_SPAMMER', $user->name);
        }
        else
        {
            $db = JFactory::getDBO();
            $query = "SELECT id FROM #__enmasse_comment WHERE user_id = " . $comment->user_id;
            $db->setQuery($query);
            $aIDs = $db->loadResultArray();
            $sCommentIds = implode(',', $aIDs);
            $bResult = JModel::getInstance('Comment', 'EnmasseModel')->changeCommentStatus($sCommentIds, 3);
            if($bResult)
            {
                $sMessage = JText::_('COMMENT_SPAMMED_SUCCESSFULLY');            
            }
            else
            {
                $sMessage = JText::_('COMMENT_SPAMMED_FAILED'); 
            }
       
            $query = "INSERT INTO #__enmasse_comment_spammer VALUES ('', $comment->user_id) ";
            $db->setQuery($query);
            $db->query();
            if ($db->getErrorNum()) {
                $sMessage = JText::sprintf('COMMENT_USER_SPAMMED_FAILED', $user->name);
                JJFactory::getApplication()->redirect('index.php?option=com_enmasse&controller=comment', $sMessage, 'error');
            }
            $sMessage = JText::sprintf('COMMENT_MARK_SPAMMER_SUCCESSFULLY', $user->name);   
        }
        JFactory::getApplication()->redirect('index.php?option=com_enmasse&controller=comment', $sMessage);
    }
    
    public static function checkSpammer($nUserId)
    {
        $db = JFactory::getDBO();
        $query = "SELECT COUNT(user_id) FROM #__enmasse_comment_spammer WHERE user_id = " . $nUserId;
        $db->setQuery($query);
		$count = $db->loadResult();
        if($count == 0)
        {
            return false;
        }
        else
        {
            return true;
        }
    }
	public static function getDeliveryPersons()
	{
		$dlvGroupId = self::getSetting()->delivery_group;
		if(empty($dlvGroupId)) return array();
		$db = JFactory::getDBO();
		$query = " SELECT u.id, u.username
					FROM #__users u
					LEFT JOIN #__user_usergroup_map m ON u.id = m.user_id 
					WHERE m.group_id = " .self::getSetting()->delivery_group;
		
		$db->setQuery( $query );
		$result = $db->loadObjectList();
		if(empty($result))
		{
			return array();
		}else 
		{
			return $result;
		}
		
	}
	
	public static function getDelivererName($orderId)
	{
		$db = JFactory::getDBO();
		$query = "SELECT u.username
					FROM #__users u
					LEFT JOIN #__enmasse_order_deliverer d ON u.id = d.user_id
					WHERE d.order_id = $orderId";
		
		$db->setQuery( $query );
		
		return $db->loadResult();
	}
	public static function strip_only($str, $tags = array('>','<','<"','">',"<'","'>",'"','/','\\',"'")) {
		$str = strip_tags($str);
		foreach ($tags as $id => $tag)
		{
			$str = str_replace($tag,'', $str);
		}
		return $str;
	}
	public  static  function checkSpecialCharacter($str = null, $chars = array('>','<','<"','">',"<'","'>",'"','/','\\',"'"))
	{
		foreach ($chars as $id => $char)
		{
			if(similar_text($str,$char) >0)
			{
				return true;
			}
		}
		return  false;
	}
		
}

?>