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
JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_enmasse'.DS.'tables');
require_once( JPATH_ADMINISTRATOR . DS ."components". DS ."com_enmasse". DS ."helpers". DS ."EnmasseHelper.class.php");
require_once( JPATH_ADMINISTRATOR . DS ."components". DS ."com_enmasse". DS ."helpers". DS ."DatetimeWrapper.class.php");

class EnmasseControllerSaleReports extends JController
{		
	function dealReport()
	{
		$this->checkAccess();
		
		JRequest::setVar('view', 'salereports');
		JRequest::setVar('task', 'dealReport');
		parent::display();
	}
	
	private function checkAccess()
	{
	    if (JFactory::getUser()->get('guest'))
		{			
			$msg = JText::_( "SALE_PLEASE_LOGIN_BEFORE");
			$redirectUrl = base64_encode("index.php?option=com_enmasse&controller=saleReports&task=dealReport");  
		    $version = new JVersion;
            $joomla = $version->getShortVersion();
            if(substr($joomla,0,3) >= '1.6'){
                $link = JRoute::_("index.php?option=com_users&view=login&return=".$redirectUrl, false);  
            }else{
                $link = JRoute::_("index.php?option=com_user&view=login&return=".$redirectUrl, false);    
            }
			JFactory::getApplication()->redirect($link, $msg);
		}
		
		$salesPersonId = JFactory::getSession()->get('salesPersonId');

		if($salesPersonId == null)
		{
			$salesPerson = JModel::getInstance('salesPerson','enmasseModel')->getByUserName(JFactory::getUser()->get('username'));
			if ($salesPerson != null)
				JFactory::getSession()->set('salesPersonId', $salesPerson->id);
			else
			{
	         	$msg = JText::_('SALE_NO_ACCESS');
				$link = JRoute::_("index.php?option=com_enmasse&controller=deal&task=listing", false);
				JFactory::getApplication()->redirect($link, $msg);
			}
		}
		return $salesPersonId;
	}
	
	public function createPdf(){
		$salesPersonId = JFactory::getSession()->get('salesPersonId');
		//$filter = JRequest::getVar('filter', array('name' => "", 'code' => "", 'merchant_id' => "", 'fromdate' => "", 'todate' => ""));
		//$filter = JRequest::getVar('filter',array(),'post','array');
        $currency_prefix 	= JModel::getInstance('setting','enmasseModel')->getCurrencyPrefix(); 
		$dealList 			= JModel::getInstance('deal','enmasseModel')->searchBySaleReports($salesPersonId, JRequest::getVar('name'), JRequest::getVar('merchant_id'), JRequest::getVar('fromdate'), JRequest::getVar('todate'), JRequest::getVar('code'));
		if(empty($dealList)) return null;
		$result = '<table style="border:1px dotted #D5D5D5; border-collapse: collapse;"><tr valign="middle"><th style="border:1px dotted #D5D5D5;" align="center" width="50">'
						.JText::_("No").'</th><th style="border:1px dotted #D5D5D5;" width="150">'
						.JText::_("Deal Code").'</th><th style="border:1px dotted #D5D5D5;" width="150">'
						.JText::_("Deal Name").'</th><th style="border:1px dotted #D5D5D5;" width="100">'
						.JText::_("Merchant").'</th><th style="border:1px dotted #D5D5D5;" align="center" width="80">'
						.JText::_("Qty Sold").'</th><th style="border:1px dotted #D5D5D5;" align="center" width="80">'
						.JText::_("Unit Price").'</th><th style="border:1px dotted #D5D5D5;" align="center" width="80">'
						.JText::_("Total Sales").'</th></tr>';
		$i = 0;
		foreach ($dealList as $row)
		{
			$i++;
			$merchant_name 	= JModel::getInstance('merchant','enmasseModel')->retrieveName($row->merchant_id);
			$total_sales = $row->price * $row->cur_sold_qty;
			$result .= '<tr>
				<td style="border:1px dotted #D5D5D5;" align="center">'.$i.'</td>
				<td style="border:1px dotted #D5D5D5;">'.$row->deal_code.'</td>
				<td style="border:1px dotted #D5D5D5;">'.$row->name.'</td>
				<td style="border:1px dotted #D5D5D5;">'.$merchant_name.'</td>
				<td style="border:1px dotted #D5D5D5;" align="center">'.$row->cur_sold_qty.'</td>
				<td style="border:1px dotted #D5D5D5;" align="center">'.$currency_prefix.$row->price.'</td>
				<td style="border:1px dotted #D5D5D5;" align="center">'.$currency_prefix.$total_sales.'</td></tr>';
			
			$total_amount += $total_sales;
		}
		$result .= '<tr><td style="border:1px dotted #D5D5D5;" colspan="6" style="text-align:right" >Total Amount: </td>
					<td style="border:1px dotted #D5D5D5;" align="center">' .$currency_prefix.$total_amount. '</td></tr></table>';
		//todo
		
		require_once(JPATH_ADMINISTRATOR. DS .'components' . DS .'com_enmasse' .DS .'helpers' .DS .'html2pdf' . DS .'html2pdf.class.php');
		
		$html2pdf = new HTML2PDF('P', 'A4', 'en');
		$html2pdf->setDefaultFont('Arial');
		$html2pdf->writeHTML($result);
		$outFileName = 'report-' .DatetimeWrapper::getDateOfNow() .'.pdf';
		$html2pdf->Output($outFileName,'I');
		
		die;
	}
	
}
?>
