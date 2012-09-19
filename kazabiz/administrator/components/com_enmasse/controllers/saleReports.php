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

class EnmasseControllerSaleReports extends JController
{  
	function display($cachable = false, $urlparams = false)
	{
		JRequest::setVar('view', 'salereports');
		JRequest::setVar('layout', 'show');
		parent::display();
	}

	function save()
	{
		$data = JRequest::get( 'post' );
                
		$data['name'] = trim($data['name']);		
		$data['slug_name'] 		= EnmasseHelper::seoUrl($data['name']);
		$data['description'] 	= JRequest::getVar( 'description', '', 'post', 'string', JREQUEST_ALLOWRAW ); 
		$data['highlight'] 		= JRequest::getVar( 'highlight', '', 'post', 'string', JREQUEST_ALLOWRAW ); 
		$data['terms'] 			= JRequest::getVar( 'terms', '', 'post', 'string', JREQUEST_ALLOWRAW ); 
		
        if($data['slug_name']=='_' || $data['slug_name'] =='')
        {
           $now = str_replace(":"," ",DatetimeWrapper::getDatetimeOfNow());
           $data['slug_name'] = EnmasseHelper::seoUrl($now);
        }
        
		$model = JModel::getInstance('deal','enmasseModel');
		
		//---------------------------------------------------------------
		// if edit deal
		if($data['id'] > 0)
		{
			//---get deal data
			$deal = JModel::getInstance('deal','enmasseModel')->getById($data['id']);
			
			// get sold coupon qty for deal
			$soldCouponList = JModel::getInstance('invty','enmasseModel')->getSoldCouponByPdtId($deal->id);
			
		    //if from unlimited to limited
			if($deal->max_coupon_qty < 0  ) 
			{
				if($data['max_coupon_qty'] > 0)
				{
				    if($data['max_coupon_qty'] <= count($soldCouponList) )
					{
						$msg = JText::_('MSG_CURRENT_SOLD_GRATER_THAN_MODIFIED_COUPON');
						JFactory::getApplication()->redirect('index.php?option=com_enmasse&controller=deal&task=edit&cid='.$data['id'],$msg);
					}
					else
					{
						$numOfAddCoupon  = $data['max_coupon_qty']- count($soldCouponList);
					}
				}
			}
			else 
			{   //---------------- if change from limited to unlimited
				if($data['max_coupon_qty'] < 0 )
				 	$unlimit = true;
				 //-------------------------change the coupon qty to less
				else if($data['max_coupon_qty'] < $deal->max_coupon_qty)
				{
					//---------------------- if new coupon qty <= the sold coupon qty
					if( $data['max_coupon_qty'] <= count($soldCouponList) )
					{
						$msg = JText::_('MSG_CURRENT_SOLD_GRATER_THAN_MODIFIED_COUPON');
						JFactory::getApplication()->redirect('index.php?option=com_enmasse&controller=deal&task=edit&cid='.$data['id'],$msg);
					}
					else
					{
						$numOfRemoveCoupon = $deal->max_coupon_qty -  $data['max_coupon_qty'];
					}
				} //------------------ if change to coupon qty to greater qty
				else if($data['max_coupon_qty'] > $deal->max_coupon_qty)
					$numOfAddCoupon = $data['max_coupon_qty'] - $deal->max_coupon_qty;
			}
			
		}
		
			//------------------------
		//gemerate integration class
		 $integrateFileName = EnmasseHelper::getSubscriptionClassFromSetting().'.class.php';
		 $integrationClass = EnmasseHelper::getSubscriptionClassFromSetting();
		 require_once (JPATH_SITE . DS ."components". DS ."com_enmasse". DS ."helpers". DS ."subscription". DS .$integrationClass. DS.$integrateFileName);
		 $integrationObject = new $integrationClass();
		 
		// store data
		$row = $model->store($data);
		
		if ($row->success)
		{ 
			if($data['id'] == 0)
				$integrationObject->integration($row,'newDeal');
			//--------------------------------------
			// store location and category
			JModel::getInstance('dealCategory','enmasseModel')->store($row->id,$data['pdt_cat_id']);
			JModel::getInstance('dealLocation','enmasseModel')->store($row->id,$data['location_id']);
			
			// if is new deal and limited the coupon then create coupon in invty
			if($data['id']==0 && $row->max_coupon_qty > 0)
			{
				for($i=0 ; $i < $row->max_coupon_qty; $i++)
				{
					
					$name = $i+1;
					JModel::getInstance('invty','enmasseModel')->generateCouponFreeStatus($row->id,$name,'Free');
				}
				
			}
			else if($data['id']!=0)
			{
				if(!empty($numOfRemoveCoupon))
				{
					$freeCouponList = JModel::getInstance('invty','enmasseModel')->getCouponFreeByPdtID($data['id']);
					// removed the coupons from invty
					for($i=0; $i < $numOfRemoveCoupon ; $i++)
					{
						JModel::getInstance('invty','enmasseModel')->removeById($freeCouponList[$i]->id);
					}
				}
				else if(!empty($numOfAddCoupon))
				{
					// add more coupon to invty
					for($i=0; $i < $numOfAddCoupon ; $i++)
					{
						$name = $i+1;
						JModel::getInstance('invty','enmasseModel')->generateCouponFreeStatus($data['id'],$name,'Free');
					}
				}
				else if($unlimit)
				{
					//remove all free coupon 
					JModel::getInstance('invty','enmasseModel')->removeCouponByPdtIdAndStatus($data['id'],'Free');
					
				}
				
			}
			$msg = JText::_('SAVE_SUCCESS_MSG');
			$this->setRedirect('index.php?option=com_enmasse&controller='.JRequest::getVar('controller'), $msg);
		}
		else
		{
			$msg = JText::_('SAVE_ERROR_MSG') .": " . $model->getError();
			if($data['id'] == null)
				$this->setRedirect('index.php?option=com_enmasse&controller='.JRequest::getVar('controller').'&task=add', $msg, 'error');
			else
				$this->setRedirect('index.php?option=com_enmasse&controller='.JRequest::getVar('controller').'&task=edit&cid[0]='. $data['id'], $msg, 'error');
		}
	}

	function control()
	{
		$this->setRedirect('index.php?option=com_enmasse');
	}

	function publish()
	{
		EnmasseHelper::changePublishState(1,'enmasse_deal','deal','deal');
	}
	function unpublish()
	{
		EnmasseHelper::changePublishState(0,'enmasse_deal','deal','deal');
	}

	function refreshOrder($by=null)
	{
		JModel::getInstance('deal','enmasseModel')->refreshOrder($by=null);
	}
	function upPosition()
	{
		EnmasseControllerDeal::changePosition('com_enmasse', true);
	}
	function downPosition()
	{
		EnmasseControllerDeal::changePosition('com_enmasse', false);
	}
	
	function changePosition($option, $up)
	{
		// get current item
		$id = JRequest::getVar('id');
		$cur = JModel::getInstance('deal','enmasseModel')->getCurrentPosition($id);
		
		// get other item
		if ($up)
			$temp = $cur->position - 1;
		else
			$temp = $cur->position + 1;
		$other = JModel::getInstance('deal','enmasseModel')->getNextPosition($temp);

		// change position
		if ($up)
		{
			$cur->position --;
			$other->position ++;
		}
		else
		{
			$cur->position ++;
			$other->position --;
		}

		JModel::getInstance('deal','enmasseModel')->store($cur);
		if ($other->id)
			JModel::getInstance('deal','enmasseModel')->store($other);

		// redirect
		$msg = JText::_( "ORDER_POSITION_UPDATED");
		JFactory::getApplication()->redirect("index.php?option=com_enmasse&controller=deal", $msg);

	}
	
	function updatePosition()
	{
		$id = JRequest::getVar('id');
		$updatePosition = JRequest::getVar('updatePosition');
		$listPosition = JModel::getInstance('deal','enmasseModel')->getListPosition($id);
		$cur = JModel::getInstance('deal','enmasseModel')->getCurrentPosition($id);
		$other = JModel::getInstance('deal','enmasseModel')->getOtherPosition($id);
       
		// Check value of the position was updated, max value of it shouldn`t greater than total deals
		$nTotal = count($other) + 1;
		if($updatePosition >$nTotal) $updatePosition = $nTotal;
		
		if($updatePosition > $cur->position)
		{
			for($i=0; $i<count($other);$i++)
			{
				if($other[$i]->position <= $updatePosition && $other[$i]->position > $cur->position)
				{
					$other[$i]->position -=1;
					JModel::getInstance('deal','enmasseModel')->store($other[$i]);
				}
			}
		}elseif ($updatePosition < $cur->position)
		{
			for($z=0; $z<count($other);$z++)
			{
			if($other[$z]->position >= $updatePosition && $other[$z]->position < $cur->position)
				{
					$other[$z]->position +=1;
					JModel::getInstance('deal','enmasseModel')->store($other[$z]);
				}
			}
		}
		
		$cur->position = $updatePosition;
		JModel::getInstance('deal','enmasseModel')->store($cur);
		
		// redirect
		$msg = JText::_( "ORDER_POSITION_UPDATED");
		JFactory::getApplication()->redirect("index.php?option=com_enmasse&controller=deal", $msg);
	}
	
	/*public static function printPdf(){
		?>
			<script type="text/javascript">
			<!--
				window.location.href = "index.php?option=com_enmasse&view=salereports";
				window.print();
			//-->
			</script>
		<?php
	}*/
	
	public static function pdf(){	
		$filter = JRequest::getVar('filter', array('name' => "", 'code' => "", 'merchant_id' => "", 'fromdate' => "", 'todate' => ""));
		$dealList 			= JModel::getInstance('salereports','enmasseModel')->search($filter['code'], $filter['name'],$filter['saleperson_id'],$filter['merchant_id'],$filter['fromdate'], $filter['todate']);
		
		$currency = JModel::getInstance('setting','enmasseModel')->getCurrencyPrefix();
		
		if(empty($dealList)){
			$msg = JText::_( "NO_SALE_REPORT");
			JFactory::getApplication()->redirect("index.php?option=com_enmasse&controller=saleReports", $msg,'notice');
			return false;
		}
		
		$result = '<table style="border:1px dotted #D5D5D5; border-collapse: collapse;"><tr valign="middle"><th style="border:1px dotted #D5D5D5;" align="center" width="30">'
						.JText::_("No").'</th><th style="border:1px dotted #D5D5D5;" align="center" width="80">'
						.JText::_("Deal Code").'</th><th style="border:1px dotted #D5D5D5;" align="center" width="80">'
						.JText::_("Deal Name").'</th><th style="border:1px dotted #D5D5D5;" align="center" width="60">'
						.JText::_("Merchant").'</th><th style="border:1px dotted #D5D5D5;" align="center" width="60">'
						.JText::_("Qty Sold").'</th><th style="border:1px dotted #D5D5D5;" align="center" width="80">'
						.JText::_("Unit Price").'</th><th style="border:1px dotted #D5D5D5;" align="center" width="80">'
						.JText::_("Total Sales").'</th><th style="border:1px dotted #D5D5D5;" align="center" width="80">'
						.JText::_("Commission Percentage").'</th><th style="border:1px dotted #D5D5D5;" align="left" width="150">'
						.JText::_("Total Commission Amount").'</th></tr>';
		$i = 0;
		$total_commission_amount = 0;
		foreach ($dealList as $row)
		{
			
			$i++;
			$merchant_name 	= JModel::getInstance('merchant','enmasseModel')->retrieveName($row->merchant_id);
			$total_sales = $row->price * $row->cur_sold_qty;
			$total_amount = ($total_sales * $row->commission_percent) / 100;
			$result .= '<tr>
				<td style="border:1px dotted #D5D5D5;" align="center">'.$i.'</td>
				<td style="border:1px dotted #D5D5D5;">'.$row->deal_code.'</td>
				<td style="border:1px dotted #D5D5D5;">'.$row->name.'</td>
				<td style="border:1px dotted #D5D5D5;">'.$merchant_name.'</td>
				<td style="border:1px dotted #D5D5D5;" align="center">'.$row->cur_sold_qty.'</td>
				<td style="border:1px dotted #D5D5D5;" align="center">'.$currency.$row->price.'</td>
				<td style="border:1px dotted #D5D5D5;" align="center">'.$currency.$total_sales.'</td>
				<td style="border:1px dotted #D5D5D5;" align="center">'.$row->commission_percent.' % </td>
				<td style="border:1px dotted #D5D5D5;" align="center">'.$currency.$total_amount.'</td></tr>';
			;
			
			$total_commission_amount += $total_amount;
		}
		$result .= '<tr><td style="border-right:1px dotted #D5D5D5; text-align:right" colspan="8"   >Total: </td>
					<td style="border:1px dotted #D5D5D5;" align="center" align="center">' .$currency.$total_commission_amount. '</td></tr></table>';
		//todo
	
		require_once(JPATH_ADMINISTRATOR. DS .'components' . DS .'com_enmasse' .DS .'helpers' .DS .'html2pdf' . DS .'html2pdf.class.php');
		
		$html2pdf = new HTML2PDF('P', 'A4', 'en');
		$html2pdf->setDefaultFont('Arial');
		$html2pdf->writeHTML($result);
		$outFileName = 'report-' .DatetimeWrapper::getDateOfNow() .'.pdf';
		$html2pdf->Output($outFileName,'I');
		
		header("Content-type:application/pdf");
		// It will be called downloaded.pdf
		header("Content-Disposition:attachment;filename=downloaded.pdf");
		
		die();
	}
	public function cancel(){
		$this->setRedirect('index.php?option=com_enmasse');
	}
}
?>