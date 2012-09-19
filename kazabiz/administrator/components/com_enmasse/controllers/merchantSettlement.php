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
jimport('joomla.application.component.controller');
JTable::addIncludePath('components'.DS.'com_enmasse'.DS.'tables');
require_once( JPATH_ADMINISTRATOR . DS ."components". DS ."com_enmasse".DS."helpers". DS ."EnmasseHelper.class.php");

class EnmasseControllerMerchantSettlement extends JController
{

	public function display($cachable = false, $urlparams = false)
	{
		JRequest::setVar('view', 'merchantsettlement');
		JRequest::setVar('layout', 'show');
		parent::display();
	}
	
	public function payOut()
	{
		$cid = JRequest::getVar('cid', array(), 'post', 'array');
		$arFilter = JRequest::getVar('filter', array(), 'post', 'array');
		
		$status = array();
		$status[] = EnmasseHelper::$MERCHANT_SETTLEMENT_STATUS_LIST['Not_Paid_Out'];
		$status[] = EnmasseHelper::$MERCHANT_SETTLEMENT_STATUS_LIST['Should_Be_Paid_Out'];
		
		$oCouponModel = JModel::getInstance('invty', 'EnmasseModel');
			
		$arId = $oCouponModel->getBySettlementStatus($status, $arFilter);
		
		$arProcessId = array_intersect($cid, $arId);
		
		if(count($arProcessId) != count($cid))
		{
			$msg = JText::sprintf("MERCHANT_SETTLEMENT_ALREADY_PAID_OUT", implode(', ', array_diff($cid, $arProcessId)));
			$link = "index.php?option=com_enmasse&controller=merchantSettlement";
			JFactory::getApplication()->redirect($link, $msg, 'warning');
		}
		
		//validation is passed
		if(!empty($arProcessId))
		{
			//update coupon settlement status to 'Paid Out'
			$oCouponModel->payOutCoupons($arProcessId);
			
			//export information of selected coupon to excel
			$arData = JModel::getInstance('MerchantSettlement', 'EnmasseModel')->getReportData($cid);
			$filename = "Report" . date('Ymd') . ".xls";
			header("Content-Disposition: attachment; filename=\"$filename\"");
			header("Content-Type: application/vnd.ms-excel");
			echo $this->generateReport($arData);
			die;
		}
	}
	
	public function doNotPayOut()
	{
		$cid = JRequest::getVar('cid', array(), 'post', 'array');
		$arFilter = JRequest::getVar('filter', array(), 'post', 'array');
		
		$status = array();
		$status[] = EnmasseHelper::$MERCHANT_SETTLEMENT_STATUS_LIST['Paid_Out'];
		$status[] = EnmasseHelper::$MERCHANT_SETTLEMENT_STATUS_LIST['Should_Be_Paid_Out'];
		
		$oCouponModel = JModel::getInstance('invty', 'EnmasseModel');
			
		$arId = $oCouponModel->getBySettlementStatus($status, $arFilter);
		
		$arProcessId = array_intersect($cid, $arId);
		
		if(count($arProcessId) != count($cid))
		{
			$msg = JText::sprintf("MERCHANT_SETTLEMENT_ALREADY_NOT_PAID_OUT", implode(', ', array_diff($cid, $arProcessId)));
			$link = "index.php?option=com_enmasse&controller=merchantSettlement";
			JFactory::getApplication()->redirect($link, $msg, 'warning'); 
		}
		
		//validation passed
		if(!empty($arProcessId))
		{
			$oCouponModel->doNotPayOutCoupons($arProcessId);
		}
		
		$msg = JText::sprintf("MERCHANT_SETTLEMENT_DO_NOT_PAY_OUT_SUCCESS_MSG", implode(', ', $cid));
		$link = "index.php?option=com_enmasse&controller=merchantSettlement";
		JFactory::getApplication()->redirect($link,$msg);
	}

	public function control()
	{
		$this->setRedirect('index.php?option=com_enmasse');
	}
	
	private function generateReport($arData)
	{
		ob_start();
		echo '<table border="1">';
        echo "<thead>
				         <tr><td colspan='11' style='font-size:16px; color:#0000FF; text-align:center;'> Deal Coupon Report </td> </tr>
						<tr>
							<th width=\"5%\">" . JTEXT::_('MERCHANT_SETTLEMENT_SERIAL') . "</th>
							<th width=\"10%\">" . JTEXT::_('MERCHANT_SETTLEMENT_DEAL_CODE') . "</th>
							<th width=\"15%\">" . JTEXT::_('MERCHANT_SETTLEMENT_BUYER_NAME') . "</th>
							<th width=\"15%\">" . JTEXT::_('MERCHANT_SETTLEMENT_BUYER_EMAIL') . "</th>
							<th width=\"15%\">" . JTEXT::_('MERCHANT_SETTLEMENT_DELIVERY_NAME') . "</th>
							<th width=\"15%\">" . JTEXT::_('MERCHANT_SETTLEMENT_DELIVERY_EMAIL') . "</th>
							<th width=\"15%\">" . JTEXT::_('MERCHANT_SETTLEMENT_ORDER_COMMENT') . "</th>
							<th width=\"10%\">" . JTEXT::_('MERCHANT_SETTLEMENT_PURCHASE_DATE') . "</th>
							<th width=\"10%\">" . JTEXT::_('MERCHANT_SETTLEMENT_PRICE') . "</th>
							<th width=\"5%\">" . JTEXT::_('MERCHANT_SETTLEMENT_COUPON_SERIAL') . "</th>
							<th width=\"5%\">" . JTEXT::_('MERCHANT_SETTLEMENT_COUPON_STATUS') . "</th>
							<th width=\"5%\">" . JTEXT::_('MERCHANT_SETTLEMENT_SETTLEMENT_STATUS') . "</th>
						</tr>
					</thead>";
        $count = 0;
        foreach ($arData as $item) {
        	$count++;
        	$arBuyer = json_decode($item->order_buyer_detail);
        	$arReceiver = json_decode($item->order_delivery_detail);
            echo '<tr>';
            echo '<td>' . $count . '</td>';
            echo '<td>' . $item->deal_code . '</td>';
            echo '<td>' . $arBuyer->name . '</td>';
            echo '<td>' . $arBuyer->email . '</td>';
            echo '<td>' . $arReceiver->name . '</td>';
            echo '<td>' . $arReceiver->email . '</td>';
            echo '<td>' . $item->order_description . '</td>';
            echo '<td>' . $item->created_at . '</td>';
            echo '<td>' . $item->unit_price . '</td>';
            echo '<td style="text-align:center;"># ' . $item->coupon_serial . '</td>';
            echo '<td style="text-align:center;">' . $item->coupon_status . '</td>';
            echo '<td>' .JText::_('MERCHANT_SETTLEMENT_' .strtoupper($item->coupon_settlement_status)) . '</td>';
            echo '</tr>';
        }
        echo '</table>';
		$contents = ob_get_contents();
		ob_end_clean();
		return $contents;
	}
	
}
?>
