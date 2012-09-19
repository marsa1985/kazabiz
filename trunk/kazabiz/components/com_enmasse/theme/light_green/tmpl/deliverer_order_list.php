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
	<h3><?php echo JText::_("LIST_YOUR_DELIVERY_ORDER")?></h3>
	<hr />
	<br/>
	<div id="OrderList">
	  <div class="top">
			<table width="100%" class="oderList">
				<tr class="oderTitle">
					<th><?php echo JText::_('ORDER_ID');?></th>
					<th><?php echo JText::_('ORDER_QTY');?></th>
					<th><?php echo JText::_('ORDER_TOTAL');?></th>
					<th><?php echo JText::_('ORDER_PAID_AMOUNT');?></th>
					<th><?php echo JText::_('ORDER_REMAIN_AMOUNT');?></th>
					<th><?php echo JText::_('ORDER_DATE');?></th>
					<th><?php echo JText::_('ORDER_DELIVERY');?></th>
					<th><?php echo JText::_('ORDER_STATUS');?></th>
					<th><?php echo JText::_('ORDER_COMMENT');?></th>
					<th></th>
				</tr>
				<?php $count = 0;?>
				<?php	foreach($this->orderList as $orderRow):?>
					<?php $link = JRoute::_('index.php?option=com_enmasse&controller=deliverer&task=edit&id=' .$orderRow->id)?>
				<tr  <?php if($count % 2 == 0) echo "class=\"highlight\""?>>
	
					<td>
						<a href="<?php echo $link?>"><?php echo $orderRow->display_id?></a>
			
					</td>
					<td><?php echo $orderRow->orderItem->qty?></td>
					<td><?php echo EnmasseHelper::displayCurrency($orderRow->total_buyer_paid);?></td>
					<td><?php echo EnmasseHelper::displayCurrency($orderRow->paid_amount);?></td>
					<td><?php echo EnmasseHelper::displayCurrency($orderRow->total_buyer_paid - $orderRow->paid_amount);?></td>
					<td><?php echo JHTML::_('date', $orderRow->created_at, JText::_('DATE_FORMAT_LC1'));//DatetimeWrapper::getDisplayDatetime($orderRow->created_at);?></td>
				    <td><?php
				    	   	echo EnmasseHelper::displayJson($orderRow->delivery_detail)?>
				   	</td>
				    <td><?php echo JTEXT::_('ORDER_'.strtoupper($orderRow->status));?></td>
				    <td><?php echo $orderRow->description?></td>
				    <td>
					    <?php 
					    	$token=EnmasseHelper::generateOrderItemToken($orderRow->orderItem->id, $orderRow->orderItem->created_at);
					    ?>
						<a href='index.php?option=com_enmasse&controller=coupon&task=listing&orderItemId=<?php echo $orderRow->orderItem->id ?>&token=<?php echo $token?>'>
							<?php echo JText::_('ORDER_LIST_COUPON');?>
						</a>
				    </td>
	
				</tr>
				<?php $count++?>
				<?php endforeach;?>
			</table>
		</div>
		<div class="bottom"></div>
	</div>
</div>
