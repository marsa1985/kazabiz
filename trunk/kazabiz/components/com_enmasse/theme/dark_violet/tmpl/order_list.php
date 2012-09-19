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
require_once( JPATH_ADMINISTRATOR . DS ."components". DS ."com_enmasse".DS."helpers". DS ."EnmasseHelper.class.php");
$theme =  EnmasseHelper::getThemeFromSetting();
JFactory::getDocument()->addStyleSheet('components/com_enmasse/theme/' . $theme . '/css/screen.css');
?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<div class="maincol_full_cont">
	<h3><?php echo JText::_("LIST_YOUR_ORDER")?></h3>
	<hr />
	<br/>
	<div id="OrderList">
	  <div class="top">
			<table width="100%" class="oderList">
				<tr class="oderTitle">
					<th><?php echo JText::_('ORDER_ID');?></th>
					<th width="160"><?php echo JText::_('ORDER_DEAL');?></th>
					<th><?php echo JText::_('ORDER_QTY');?></th>
					<th><?php echo JText::_('ORDER_TOTAL');?></th>
					<th><?php echo JText::_('ORDER_DATE');?></th>
					<th><?php echo JText::_('ORDER_DELIVERY');?></th>
					<th><?php echo JText::_('ORDER_STATUS');?></th>
					<th><?php echo JText::_('ORDER_COMMENT');?></th>
					<th></th>
				</tr>
				<?php $count = 0;?>
				<?php foreach($this->orderList as $orderRow):?>
				<tr  <?php if($count % 2 == 0) echo "class=\"highlight\""?>>
	
					<td><?php echo $orderRow->display_id?></td>
					<td><?php echo $orderRow->orderItem->description?></td>
					<td><?php echo $orderRow->orderItem->qty?></td>
					<td><?php echo EnmasseHelper::displayCurrency($orderRow->total_buyer_paid);?></td>
					<td><?php echo JHTML::_('date', $orderRow->created_at, JText::_('DATE_FORMAT_LC1'));//DatetimeWrapper::getDisplayDatetime($orderRow->created_at);?></td>
				    <td><?php
				    	$deliveryObj = json_decode($orderRow->delivery_detail);
				    	echo $deliveryObj->name ."<br/>(".$deliveryObj->email.")";?></td>
				    <td><?php echo JTEXT::_('ORDER_'.strtoupper($orderRow->status));?></td>
				    <td><?php echo $orderRow->description?></td>
				    <td>
				    <?php 
				    	if($orderRow->orderItem->status=="Delivered")
				    	{
				    		$token=EnmasseHelper::generateOrderItemToken($orderRow->orderItem->id, $orderRow->orderItem->created_at);
				    ?>
						<a href='index.php?option=com_enmasse&controller=coupon&task=listing&orderItemId=<?php echo $orderRow->orderItem->id ?>&token=<?php echo $token?>'>
							<?php echo JText::_('ORDER_LIST_COUPON');?>
						</a>
					<?php 
				    	}
				    	elseif($orderRow->orderItem->status=="Refunded")
				    	{
							$pointPaid = EnmasseHelper::getPointPaidByOrderId($orderRow->orderItem->order_id);
							$refundedAmount = EnmasseHelper::getRefundedAmountByOrderId($orderRow->orderItem->order_id);
							if($pointPaid>0)
							{
								if($refundedAmount!=0)
								{
									echo JText::_('REFUNDED');
								}
								else
								{
								$buyerId = EnmasseHelper::getUserIdByOrderId($orderRow->orderItem->order_id);
					?>
						<a href='<?php echo JURI::base();?>index.php?option=com_enmasse&controller=point&task=refundForm&orderid=<?php echo $orderRow->orderItem->id; ?>&buyerid=<?php echo $buyerId; ?>'>
							<?php echo JText::_('REFUND_POINT');?>
						</a>				
								
					<?php
								}
							}	    		
				    	}
					?>
				    </td>
	
				</tr>
				<?php $count++?>
				<?php endforeach;?>
			</table>
		</div>
		<div class="bottom"></div>
	</div>

</div>
