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
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.framework');

$rows = $this->arMerStm;
$option = 'com_enmasse';
$filter = $this->filter;

$oEmpty = new JObject();
$oEmpty->name = "";
$oEmpty->id   = "";
//add empty option for search select list
array_unshift($this->arMer, $oEmpty);
array_unshift($this->arDeal, $oEmpty);

//contruct merchant status list
$arMerStmStatus = array();
$arMerStmStatus[" "] = "";//must be set array key as space otherwise it will get 0 as dfault
foreach ($this->arMerStmStatus as $key => $value)
{
	$arMerStmStatus[$key] = "MERCHANT_SETTLEMENT_" .strtoupper($value);
}

?>
<table width="100%">
	<tr>
		<td>
			<form action="index.php" name="filterForm">
				<input type="hidden" name="option" value="com_enmasse" />
				<input type="hidden" name="controller" value="merchantSettlement" />
				<b><?php echo JText::_('MERCHANT_SETTLEMENT_MERCHANT_NAME');?> : </b> <?php echo JHtml::_('select.genericList', $this->arMer, 'filter[merchant_id]', null , 'id', 'name', isset($filter['merchant_id']) ? $filter['merchant_id']: '')?>
				<b><?php echo JText::_('MERCHANT_SETTLEMENT_DEAL_NAME');?> : </b> <?php echo JHtml::_('select.genericList', $this->arDeal, 'filter[deal_id]', "style=\"max-width:300px\"" , 'id', 'name', isset($filter['deal_id']) ? $filter['deal_id']: '')?>
				<b><?php echo JText::_('MERCHANT_SETTLEMENT_STATUS');?> : </b> <?php echo JHtml::_('select.genericList', $arMerStmStatus, 'filter[status]', null , 'value', 'text', isset($filter['status']) ? $filter['status']: '', false, true)?>
				<input type="submit" value="<?php echo JText::_('MERCHANT_SETTLEMENT_SEARCH');?>" />
				<input type="button" value="<?php echo JText::_('MERCHANT_SETTLEMENT_RESET');?>" onClick="location.href='index.php?option=com_enmasse&controller=merchantSettlement&filter[merchant_id]=&filter[deal_id]='" />
			</form>
		</td>
	</tr>
</table>

<form action="index.php" method="post" name="adminForm" >
<table class="adminlist">
	<thead>
		<tr>
			<th width="5"><input type="checkbox" name="toggle" value=""
				onclick="checkAll(<?php echo count( $rows ); ?>);" /></th>
			<th width="50"><?php echo JText::_('MERCHANT_SETTLEMENT_COUPON_ID');?></th>
			<th width="50"><?php echo JText::_('MERCHANT_SETTLEMENT_DEAL_CODE');?></th>
			<th width="50"><?php echo JTEXT::_('MERCHANT_SETTLEMENT_BUYER_NAME');?></th>
			<th width="100"><?php echo JTEXT::_('MERCHANT_SETTLEMENT_BUYER_EMAIL');?></th>
			<th width="50"><?php echo JTEXT::_('MERCHANT_SETTLEMENT_DELIVERY_NAME');?></th>
			<th width="100"><?php echo JTEXT::_('MERCHANT_SETTLEMENT_DELIVERY_EMAIL');?></th>
			<th width="150"><?php echo JTEXT::_('MERCHANT_SETTLEMENT_ORDER_COMMENT');?></th>
			<th width="100"><?php  echo JHTML::_( 'grid.sort', JText::_('MERCHANT_SETTLEMENT_PURCHASE_DATE'), 'oi.created_at', $this->order['order_dir'], $this->order['order']); ?></th>
			<th width="50"><?php echo JText::_('MERCHANT_SETTLEMENT_COUPON_PRICE');?></th>
			<th width="100"><?php echo JText::_('MERCHANT_SETTLEMENT_COUPON_SERIAL');?></th>
			<th width="100"><?php echo JText::_('MERCHANT_SETTLEMENT_COUPON_STATUS');?></th>
			<th width="100"><?php echo JText::_('MERCHANT_SETTLEMENT_SETTLEMENT_STATUS');?></th>
			
		</tr>
	</thead>
	<?php
	$i = 0;
	foreach ($rows  as $row)
	{
		$k = $i % 2;
		$checked = JHTML::_('grid.id', $i, $row->coupon_id );
		$arBuyer = json_decode($row->order_buyer_detail);
        $arReceiver = json_decode($row->order_delivery_detail);
		$i++;
	?>
	<tr class="<?php echo "row$k"; ?>" style="text-align: center">
		<td><?php echo $checked; ?></td>
		<td><?php echo $row->coupon_id; ?></td>
		<td><?php echo $row->deal_code; ?></td>
		<td><?php echo $arBuyer->name; ?></td>
		<td><?php echo $arBuyer->email; ?></td>
		<td><?php echo $arReceiver->name; ?></td>
		<td><?php echo $arReceiver->email; ?></td>
		<td><?php echo $row->order_description; ?></td>
		<td><?php echo  DatetimeWrapper::getDisplayDatetime($row->created_at); ?></td>
		<td><?php echo EnmasseHelper::displayCurrency($row->unit_price); ?></td>
		<td><?php echo '# ' .$row->coupon_serial; ?></td>
		<td><?php echo JText::_('COUPON_' .strtoupper($row->coupon_status)); ?></td>
		<td><?php echo JText::_('MERCHANT_SETTLEMENT_' .strtoupper($row->coupon_settlement_status)); ?></td>
		
	</tr>
	<?php
	} 
	?>
	<tfoot>
    <tr>
      <td colspan="15"><?php echo $this->pagination->getListFooter(); ?></td>
    </tr>
  </tfoot>
</table>
<input type="hidden" name="option" value="<?php echo $option;?>" />
<input type="hidden" name="controller" value="merchantSettlement" />
<input type="hidden" name="task" value="" /> 
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="filter_order" value="<?php echo $this->order['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->order['order_dir']; ?>" />
<input type="hidden" name="filter[merchant_id]" value="<?php echo empty($filter['merchant_id'])? "" :  $filter['merchant_id']; ?>" />
<input type="hidden" name="filter[status]" value="<?php echo empty($filter['merchant_id'])? "" : $filter['status']; ?>" />
<input type="hidden" name="filter[deal_id]" value="<?php echo empty($filter['deal_id'])? "" :  $filter['deal_id']; ?>" />
<input type="button" value="<?php echo JText::_('MERCHANT_SETTLEMENT_PAY_OUT')?>" 
	onclick="submitForm('payOut')"/>
<input type="button" value="<?php echo JText::_('MERCHANT_SETTLEMENT_DO_NOT_PAY_OUT')?>" 
	onclick="submitForm('doNotPayOut')"/>
</form>
<script type="text/javascript">
	function submitForm(task)
	{
		if(document.adminForm.boxchecked.value == 0)
		{
			alert('<?php echo JText::_('MERCHANT_SETTLEMENT_NO_COUPON_SELECTED')?>');
			return;
		}
		var agree = false;
		switch(task)
		{
			case 'payOut':
				agree = confirm('<?php echo JText::_('MERCHANT_SETTLEMENT_PAY_OUT_CONFIRM_MSG')?>')
				break;
			case 'doNotPayOut':
				agree = confirm('<?php echo JText::_('MERCHANT_SETTLEMENT_DO_NOT_PAY_OUT_CONFIRM_MSG')?>')
				break;
		}
		if(agree)
		{
			document.adminForm.task.value = task;
			document.adminForm.submit();
		}
		
	}

</script>
