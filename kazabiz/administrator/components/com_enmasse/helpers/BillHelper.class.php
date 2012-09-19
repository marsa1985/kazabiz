<?php
class BillHelper
{
	public static $BUYER_RECEIPT_TMPL_NAME = "buyer_receipt";
	
	
	/**
	 * 
	 * Create bill with PDF format, return file location.
	 * @param integer $nOrderId order id that need to print bill.
	 * @param string $dest whether save to the file or echo direct to web browser (for preview).
	 * @return string name of created file with absolute path. 
	 */
	public static function createPDF($nOrderId, $dest = 'F')
	{
		$oOrder = JModel::getInstance('order', 'enmasseModel')->getById($nOrderId);
		if(empty($oOrder)) return null;
		
		$buyer = json_decode($oOrder->buyer_detail);
		$arOrderItem = JModel::getInstance('orderItem', 'enmasseModel')->listByOrderId($nOrderId);
		$sOderDetail = '<table border="1"><tr valign="middle"><th align="center" style="width:30px;">'
						.JText::_("BILL_TEMPLATE_ORDER_DETAIL_NO").'</th><th style="width:60px;">'
						.JText::_("BILL_TEMPLATE_ORDER_DETAIL_QUANTITY").'</th><th style="width:50px;">'
						.JText::_("BILL_TEMPLATE_ORDER_DETAIL_DEAL_ID").'</th><th align="center" style="width:320px; ">'
						.JText::_("BILL_TEMPLATE_ORDER_DETAIL_DEAL_DESC").'</th><th style="width:50px;">'
						.JText::_("BILL_TEMPLATE_ORDER_DETAIL_UNIT_PRICE").'</th><th style="width:50px;">'
						.JText::_("BILL_TEMPLATE_ORDER_DETAIL_TAX").'</th><th style="width:80px;">'
						.JText::_("BILL_TEMPLATE_ORDER_DETAIL_TOTAL").'</th></tr>';
		
		$oPayGty = JModel::getInstance('payGty', 'enmasseModel')->getById($oOrder->pay_gty_id);
		if(empty($oPayGty))
		{
			$oPayGty = new JObject();
			$oPayGty->name = "";
			
		}				
		$count = 1;				
		foreach ($arOrderItem as $oItem)
		{
			$sOderDetail .= '<tr valign="middle"><td >'
							.$count++  .'</td><td >'
							.$oItem->qty .'</td><td >'
							.$oItem->pdt_id .'</td><td style="width:300px;text-align: left">'
							.$oItem->description .'</td><td >'
							.$oItem->unit_price .'</td><td >'
							. '</td><td >'
							.$oItem->total_price .'</td></tr>';
		}
		
		$sOderDetail .= '<tr><td colspan="7" style="text-align:right" >Total Amount: ' .$oOrder->total_buyer_paid . '</td></tr></table>';
		
		
		$db = JFactory::getDbo();
		$query = "SELECT *
						FROM #__enmasse_bill_template
						WHERE slug_name = " .$db->quote(self::$BUYER_RECEIPT_TMPL_NAME);
		$db->setQuery($query);
		$oBillTmpl = $db->loadObject();
		$sTmpl = $oBillTmpl->content;
		
		$arParam = array();
		$arParam['[BUYER_NAME]'] = $buyer->name;
		$arParam['[BUYER_EMAIL]'] = $buyer->email;
		$arParam['[BILL_NUMBER]'] = EnmasseHelper::displayOrderDisplayId($oOrder->id);
		$arParam['[BILL_DATE]'] = DatetimeWrapper::getDatetimeOfNow();
		$arParam['[PAYMENT_METHOD]'] = $oPayGty->name;
		$arParam['[BILL_DETAIL]'] = $sOderDetail;
		$arParam['[BILL_DESCRIPTION]'] = $oOrder->description;
		
		//ADD style for table
		
		$sTmpl = '<style>p{margin: 8px 0px }table {border-collapse:collapse;} td, th{text-align: center} th{height: 40px}</style>' .$sTmpl;
		$arSearch = array_keys($arParam);
		
		$sTmpl = str_replace($arSearch, $arParam, $sTmpl);
		//-----------------------------
		//process for image tag because there have the difference source path between 
		//html2pdf and richtext editor(using for edit bill template),image tag
		//was wrong source path and it cause html2pdf error
		$sPattern = '/(<img\s+src=")(.*)(")/i';
		if(strtoupper(substr(PHP_OS, 0, 3)) == "WIN")
		{
			$sReplace = '$1' .JPATH_SITE .DS.'\\$2$3';
		}else {
			$sReplace = '$1' .JPATH_SITE .DS.'$2$3';
		}
		
		$sTmpl = preg_replace($sPattern, $sReplace, $sTmpl);
		
		require_once(dirname(__FILE__).'/html2pdf/html2pdf.class.php');
		try
		{
			$sOutFileName = "bill_preview.pdf";
			if($dest == 'F')
			{
				$sOutFileName = JPATH_ADMINISTRATOR . DS ."components". DS ."com_enmasse". DS ."bills" .DS .EnmasseHelper::displayOrderDisplayId($oOrder->id) .".pdf";
				if(file_exists($sOutFileName))
				{
					unlink($sOutFileName);
				}
			}
			
			$html2pdf = new HTML2PDF('P', 'A4', 'en');
			$html2pdf->setDefaultFont('Arial');
			$html2pdf->writeHTML($sTmpl);
			$html2pdf->Output($sOutFileName,$dest);
		}
		catch(HTML2PDF_exception $e) {
			return;
		}
		
		return $sOutFileName;
	}
}