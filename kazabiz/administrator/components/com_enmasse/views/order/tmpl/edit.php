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
require_once( JPATH_ADMINISTRATOR . DS ."components". DS ."com_enmasse". DS ."helpers". DS ."EnmasseHelper.class.php");

$order_row = $this->order;
$option = 'com_enmasse';
//-------------------
// to re-define the link of server root

$temp_uri_arr =explode ('/',$_SERVER['REQUEST_URI'])  ;
$link_server = "";
 for($count = 0; $count < count($temp_uri_arr); $count++)
 {
 	if($temp_uri_arr[$count]== '')
 	{ }
 	else if($temp_uri_arr[$count] == 'administrator' )
 	{
 		break ;
 	}
 	else
 	{
 	$link_server.= '/';
 	$link_server.=$temp_uri_arr[$count];	
 	}
 }
?>
<script language="javascript" type="text/javascript">
 function setTask()
 {
    document.adminForm.task.value = 'save';
 }
 function submitForm()
 {
	 var sOrderStatus = document.adminForm.status.value;
	 var sMsg = "";
	 switch (sOrderStatus)
	 {
	 	case '<?php echo EnmasseHelper::$ORDER_STATUS_LIST['Paid']?>':
		 	sMsg = "<?php echo JTEXT::_('ORDER_PAID');?>";
		 	break;
	 	case '<?php echo EnmasseHelper::$ORDER_STATUS_LIST['Cancelled']?>':
		 	sMsg = "<?php echo JTEXT::_('ORDER_CANCELLED');?>";
		 	break;            
	 	case '<?php echo EnmasseHelper::$ORDER_STATUS_LIST['Refunded']?>':
	 		sMsg = "<?php echo JTEXT::_('ORDER_REFUNDED');?>";
		 	break;
	 	case '<?php echo EnmasseHelper::$ORDER_STATUS_LIST['Waiting_For_Refund']?>':
	 		sMsg = "<?php echo JTEXT::_('ORDER_WAITING_FOR_REFUND');?>";
		 	break;
	 	case '<?php echo EnmasseHelper::$ORDER_STATUS_LIST['Delivered']?>':
	 		sMsg = "<?php echo JTEXT::_('ORDER_DELIVERED');?>";
		 	break;
	 	
	 }
	 var confirmmation = confirm("<?php echo JTEXT::_('CHANGE_STATUS_CONFIRM_MSG');?> " +'"'+ sMsg + '" ?');
	 if (confirmmation == true)
	 {
		 document.adminForm.submit();
	 }else{
		 document.adminForm.status.value = "";
	 }
		 
	
 }
	function setOrderStatus(orderStatus)
	{ 
		 document.adminForm.status.value = orderStatus;
	}

</script>

<form action="index.php" method="post" name="adminForm" id="adminForm">
<div class="width-100 fltrt">
<fieldset class="adminform"><legend><?php echo JText::_('ORDER_DETAIL');?></legend>
<table class="admintable">
	<tr>
		<td width="100" align="right" class="key" valign="top"><?php echo JText::_('ORDER_ID');?></td>
		<td><?php echo EnmasseHelper::displayOrderDisplayId($order_row->id);?></td>
	</tr>
	<tr>
		<td width="100" align="right" class="key" valign="top"><?php echo JText::_('ORDER_COMMENT');?></td>
		<td><textarea name="description" cols=40 rows=3><?php echo $order_row->description;?></textarea>
		</td>
	</tr>
	<tr>
		<td width="100" align="right" class="key" valign="top"><?php echo JText::_('ORDER_DEAL_NAME');?></td>
		<td align="left"><?php 
		echo $order_row->orderItem->description;
		?></td>
	</tr>
	<tr>
		<td width="100" align="right" class="key" valign="top"><?php echo JText::_('ORDER_QUANTITY');?></td>
		<td align="left"><?php 
		echo $order_row->orderItem->qty;
		?></td>
	</tr>
	<tr>
		<td width="100" align="right" class="key" valign="top"><?php echo JText::_('ORDER_BUYER_DETAIL');?></td>
		<td align="left"><?php 
		echo EnmasseHelper::displayBuyer(json_decode($order_row->buyer_detail));
		?></td>
	</tr>
	<tr>
		<td width="100" align="right" class="key" valign="top"><?php echo JText::_('ORDER_PAYMENT_DETAIL');?></td>
		<td align="left">
		<?php
		if(json_decode($order_row->pay_detail)!='')
		{
			echo EnmasseHelper::displayJson($order_row->pay_detail);
			echo "<br />";
		}
		if($order_row->point_used_to_pay>0)
		{
			echo '<strong>'.JText::_('POINTS').':</strong> ' . $order_row->point_used_to_pay;
		}
		?>		
		</td>
	</tr>
	<tr>
		<td width="100" align="right" class="key" valign="top"><?php echo JText::_('ORDER_DELIVERY_DETAIL');?></td>
		<td align="left"><?php 
		echo EnmasseHelper::displayJson($order_row->delivery_detail);
		?></td>
	</tr>
	<tr>
		<td width="100" align="right" class="key" valign="top"><?php echo JText::_('ORDER_STATUS');?></td>
		<td>
			<input type="hidden" name="status" value="" />
			<div style="width: 80px;  line-height: 15px;">
				<b>
				<?php echo JText::_('ORDER_'.strtoupper($order_row->status));?>
				</b>
			</div>
			<div class="button_list">
				<?php if($this->sDealStatus == EnmasseHelper::$DEAL_STATUS_LIST['Voided'] && ($order_row->status == 'Paid' || $order_row->status == 'Delivered')):?>
					<input type="button" class="button" value="<?php echo JTEXT::_('ORDER_REFUNDED')?>" onclick="setTask();setOrderStatus('<?php echo EnmasseHelper::$ORDER_STATUS_LIST['Refunded']?>');submitForm();">
				<?php elseif ($order_row->status == EnmasseHelper::$ORDER_STATUS_LIST['Pending'] || $order_row->status == EnmasseHelper::$ORDER_STATUS_LIST['Unpaid'] ):?>
					<input type="button" class="button" value="<?php echo JTEXT::_('ORDER_PAID')?>" onclick="setTask();setOrderStatus('<?php echo EnmasseHelper::$ORDER_STATUS_LIST['Paid']?>');submitForm();">
                    <input type="button" class="button" value="<?php echo JTEXT::_('ORDER_CANCELLED')?>" onclick="setTask();setOrderStatus('<?php echo EnmasseHelper::$ORDER_STATUS_LIST['Cancelled']?>');submitForm();" />                    
				<?php elseif ($order_row->status == EnmasseHelper::$ORDER_STATUS_LIST['Paid'] || $order_row->status == EnmasseHelper::$ORDER_STATUS_LIST['Holding_By_Deliverer']):?>
					<input type="button" class="button" value="<?php echo JTEXT::_('ORDER_WAITING_FOR_REFUND')?>" onclick="setTask();setOrderStatus('<?php echo EnmasseHelper::$ORDER_STATUS_LIST['Waiting_For_Refund']?>');submitForm();">
					<input type="button" class="button" value="<?php echo JTEXT::_('ORDER_REFUNDED')?>" onclick="setTask();setOrderStatus('<?php echo EnmasseHelper::$ORDER_STATUS_LIST['Refunded']?>');submitForm();">
					<input type="button" class="button" value="<?php echo JTEXT::_('ORDER_DELIVERED')?>" onclick="setTask();setOrderStatus('<?php echo EnmasseHelper::$ORDER_STATUS_LIST['Delivered']?>');submitForm();">
				<?php elseif ($order_row->status == EnmasseHelper::$ORDER_STATUS_LIST['Delivered']):?>
					<input type="button" class="button" value="<?php echo JTEXT::_('ORDER_WAITING_FOR_REFUND')?>" onclick="setTask();setOrderStatus('<?php echo EnmasseHelper::$ORDER_STATUS_LIST['Waiting_For_Refund']?>');submitForm();">
					<input type="button" class="button" value="<?php echo JTEXT::_('ORDER_REFUNDED')?>" onclick="setTask();setOrderStatus('<?php echo EnmasseHelper::$ORDER_STATUS_LIST['Refunded']?>');submitForm();">
				<?php elseif ($order_row->status == EnmasseHelper::$ORDER_STATUS_LIST['Waiting_For_Refund']):?>
					<input type="button" class="button" value="<?php echo JTEXT::_('ORDER_REFUNDED')?>" onclick="setTask();setOrderStatus('<?php echo EnmasseHelper::$ORDER_STATUS_LIST['Refunded']?>');submitForm();">
				<?php endif;?>
			</div>
		</td>
	</tr>
	<tr>
		<td width="100" align="right" class="key"><?php echo JText::_('CREATED_AT');?></td>
		<td><?php echo DatetimeWrapper::getDisplayDatetime($order_row->created_at); ?></td>
	</tr>
	<tr>
		<td width="100" align="right" class="key"><?php echo JText::_('UPDATED_AT');?></td>
		<td><?php echo DatetimeWrapper::getDisplayDatetime($order_row->updated_at); ?></td>
	</tr>
</table>

</fieldset>

<fieldset class="adminform">
	<legend><?php echo JText::_('ORDER_COUPON_DETAIL');?></legend>
<table class="adminlist">
	<thead>
		<tr>
			<th class="title" width=""><?php echo JText::_('ORDER_COUPON_SERIAL');?></th>
			<th width=""><?php echo JText::_('ORDER_COUPON_STATUS');?></th>
			<th></th>
		</tr>
	</thead>
	<?php
	
	$invtyList = $order_row->orderItem->invtyList;
	$base_url='http://';
	$base_url.= $_SERVER["SERVER_NAME"].$link_server;
	for ($i=0; $i < count( $invtyList ); $i++)
	{
		$k = $i % 2;
		$link = $base_url."/index.php?option=com_enmasse&controller=coupon&task=generate&invtyName=".$invtyList[$i]->name
	          ."&token=".EnmasseHelper::generateCouponToken($invtyList[$i]->name);
	?>
	<tr class="<?php echo "row$k"; ?>">
		<td align="center"><?php echo $invtyList[$i]->name; ?></td>
		<td align="center"><?php echo JTEXT::_('COUPON_'.strtoupper($invtyList[$i]->status));?></td>
		<td align="center"><a href='<?php echo $link ;?>' target="_blank"><?php echo JTEXT::_('REPORT_COUPON_REVIEW');?></a></td>
	</tr>
	<?php
	} 
	?>
</table>
</fieldset>
<input type="hidden" name="buyerid" value="<?php echo EnmasseHelper::getBuyerId(json_decode($order_row->buyer_detail)); ?>" />
<input type="hidden" name="id" value="<?php echo $order_row->id; ?>" />
<input type="hidden" name="option" value="<?php echo $option;?>" /> 
<input type="hidden" name="controller" value="order" />
<input type="hidden" name="partial" value="<?php echo $this->partial;?>" />
<input type="hidden" name="task" value="" />
</div>
</form>