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

$rows = $this -> orderList;
$option = 'com_enmasse';

require_once( JPATH_ADMINISTRATOR . DS ."components". DS ."com_enmasse".DS."helpers". DS ."EnmasseHelper.class.php");

$filter = $this->filter;

$emptyJOpt = JHTML::_('select.option', '', '--- All ---' );

$statusJOptList = array();
array_push($statusJOptList, $emptyJOpt);
foreach ($this->statusList as $key=>$name)
{
	$var = JHTML::_('select.option', $name, JText::_('ORDER_'.strtoupper($key)) );
	array_push($statusJOptList, $var);
}

$oEmpty = new JObject();
$oEmpty->id = 0;
$oEmpty->username = "";

array_unshift($this->deliveryPersons, $oEmpty);

?>
<form action="index.php">
<table style="float:left; width:70%">
	<tr>
		<td>
			<div style="float: left; margin-right: 10px;">
			<b><?php echo JText::_('ORDER_STATUS')?>: </b>
			<?php echo JHTML::_('select.genericList', $statusJOptList, 'filter[status]', null , 'value', 'text', $filter['status']);?>
			</div>

			<div style="float: left; margin-right: 10px;">
				<b><?php echo JText::_('ORDER_DEAL_CODE')?></b>
				<input type="text" name="filter[deal_code]"	value="<?php echo $filter['deal_code']; ?>" />
			</div>
			<div style="float: left; margin-right: 10px;">
				<b><?php echo JText::_('ORDER_DEAL_NAME')?></b>
				<input type="text" name="filter[deal_name]"	value="<?php echo $filter['deal_name']; ?>" />
			</div>
			<input type="hidden" name="filter[partial]" value="1"/>
			<input type="submit" value="<?php echo JText::_('ORDER_SEARCH')?>" />
			<input type="button" value="<?php echo JText::_('ORDER_RESET')?>" onClick="location.href='index.php?option=com_enmasse&controller=partialOrder'" />
		</td>
		
	</tr>
</table>

<input type="hidden" name="controller" value="partialOrder" />
<input type="hidden" name="option" value="com_enmasse" />
</form>

<form action="index.php" method="post" id="adminForm" name="adminForm">
<table style="float:right">
	<tr>
		<td>
			<div style="float: left; margin-right: 10px;">
			<b><?php echo JText::_('ORDER_ASSIGNED_DELIVERER')?>: </b>
			<?php echo JHTML::_('select.genericList', $this->deliveryPersons, 'deliverer_id', null , 'id', 'username', '');?>
			</div>
		</td>
	</tr>
</table>
<div style="clear:both;"></div>
<table class="adminlist">
	<thead>
		<tr>
			<th width="20">
				<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows ); ?>);" />
			</th>
			<th class="title" width='5px'><?php echo JText::_('ORDER_ID')?></th>
			<th width='80px'><?php echo JText::_('ORDER_TOTAL_PAID')?> </th>
			<th width='80px'><?php echo JText::_('ORDER_PAID_AMOUNT')?> </th>
			<th width='80px'><?php echo JText::_('ORDER_REMAIN_AMOUNT')?> </th>
            <th><?php echo JText::_('ORDER_DEAL_CODE')?></th>
			<th><?php echo JText::_('ORDER_DEAL_NAME')?></th>
			<th><?php echo JText::_('ORDER_QUANTITY')?></th>
			<th><?php echo JText::_('ORDER_BUYER_DETAIL')?></th>
			<th><?php echo JText::_('ORDER_PAYMENT_DETAIL')?></th>
			<th width="5" nowrap="nowrap"><?php echo JText::_('ORDER_STATUS')?></th>
			<th><?php echo JText::_('ORDER_COMMENT')?></th>
			<th><?php echo JText::_('ORDER_DELIVERER')?></th>
			<th><?php echo JText::_('UPDATED_AT')?></th>
		</tr>
	</thead>
	<?php
	for ($i=0; $i < count( $rows ); $i++)
	{
		$k = $i % 2;		
		$row = &$rows[$i];
		$checked = JHTML::_('grid.id', $i, $row->id );
		$link =  JRoute::_('index.php?option=' . $option .'&controller=order'.'&task=edit&orderId='. $row->id .'&partial=1') ;
		$deliverer = EnmasseHelper::getDelivererName($row->id);
	?>
	<tr class="<?php echo "row$k"; ?>">
		<td align="center"><?php echo $checked?></td>
		<td align="center">
			<a href="<?php echo $link?>"><?php echo EnmasseHelper::displayOrderDisplayId($row->id) ; ?></a>
		</td>
		<td align="right">
			<?php echo EnmasseHelper::displayCurrency($row->total_buyer_paid); ?>
		</td>
		<td align="right">
			<?php echo EnmasseHelper::displayCurrency($row->paid_amount); ?>
		</td>
		<td align="right">
			<?php echo EnmasseHelper::displayCurrency($row->total_buyer_paid - $row->paid_amount); ?>
		</td>
        <td>
			<?php echo $row->deal_code;?>
		</td>
		<td>
			<?php echo $row->deal_name;?>
		</td>
		<td>
			<?php echo $row->qty;?>
		</td>
		<td>
			<?php echo EnmasseHelper::displayBuyer( json_decode($row->buyer_detail) ); ?>
		</td>
		<td nowrap="true">
			<?php 
			if(json_decode($row->pay_detail)!='')
			{
				echo EnmasseHelper::displayJson($row->pay_detail);
				echo "<br />";
			}
			if($row->point_used_to_pay>0)
			{
				echo '<strong>'.JText::_('POINTS').':</strong> ' . $row->point_used_to_pay;
			}		
			?>	
		</td>
		<td align="center">
			<?php 
				if($row->status == "Paid" && $row->total_buyer_paid > $row->paid_amount)
				{
					echo JText::_('ORDER_PARTIAL_PAID');
				}else if($row->status == "Paid" && $row->total_buyer_paid == $row->paid_amount)
				{
					echo JText::_('ORDER_FULL_PAID');
				}else
				{
					echo JTEXT::_('ORDER_'.strtoupper($row->status));
				}
				 
			?>
		</td>
		<td align="left">
			<?php echo $row->description; ?>
		</td>
		<td align="center"><?php echo $deliverer; ?></td>
		<td align="center"><?php echo DatetimeWrapper::getDisplayDatetime($row->updated_at); ?></td>
	</tr>
	<?php
	} 
	?>
	<tfoot>
    <tr>
      <td colspan="12"><?php echo $this->pagination->getListFooter(); ?></td>
    </tr>
  </tfoot>
</table>
<input type="hidden" name="option" value="<?php echo $option;?>" />
<input type="hidden" name="controller" value="partialOrder" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="filter[status]" value="<?php echo $filter['status']; ?>" />
<input type="hidden" name="filter[deal_name]" value="<?php echo $filter['deal_name']; ?>" />
</form>
