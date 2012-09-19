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

$rows = $this->dealList; 
$option = 'com_enmasse';

function getUpIcon()
{
		return "<img src='" . JURI::base() . "components/com_enmasse/images/uparrow.png' />";
}
function getDownIcon()
{
		return "<img src='" . JURI::base() . "components/com_enmasse/images/downarrow.png' />";
}
 
$filter = $this->filter;
$emptyJOpt = JHTML::_('select.option', '', JText::_('') );

$publishedJOptList = array();
array_push($publishedJOptList, JHTML::_('select.option', null, JText::_('') ));
array_push($publishedJOptList, JHTML::_('select.option', 1, JText::_('PUBLISHED') ));
array_push($publishedJOptList, JHTML::_('select.option', 0, JText::_('NOT_PUBLISHED') ));


// create list status for combobox
$statusJOptList = array();
array_push($statusJOptList, $emptyJOpt);
foreach ($this->statusList as $key=>$name)
{
	$var = JHTML::_('select.option', $key, JText::_('DEAL_'.strtoupper(str_replace(' ','_',$name))) );
	array_push($statusJOptList, $var);
}

// create list sale person for combobox
$salePersonJOptList = array();
array_push($salePersonJOptList, $emptyJOpt);
foreach ($this->salePersonList as $item)
{
	$var = JHTML::_('select.option', $item->id, JText::_($item->name) );
	array_push($salePersonJOptList, $var);
}

// create list merchant for combobox
$merchantJOptList = array();
array_push($merchantJOptList, $emptyJOpt);
foreach ($this->merchantList as $item)
{
	$var = JHTML::_('select.option', $item->id, JText::_($item->name) );
	array_push($merchantJOptList, $var);
}

?>
<head >
<script type="text/javascript">
function setTask(id,position)
{
      var pos = "updatePosition"+position;
      var link = 'index.php?option=com_enmasse&controller=deal&task=updatePosition&id='+id+'&updatePosition='+document.adminForm.elements[pos].value;
	  location.href=link;
}
</script>
</head>
<body>
<form action="index.php">
<table>
	<tr>
		<td>
            <b><?php echo JText::_('DEAL_CODE');?>: </b>
			<input type="text" name="filter[code]" value="<?php echo $filter['code']; ?>" />
			<b><?php echo JText::_('DEAL_NAME');?>: </b>
			<input type="text" name="filter[name]" value="<?php echo $filter['name']; ?>" />
			<b><?php echo JText::_('DEAL_SALE_PERSON');?>: </b>
			<?php echo JHTML::_('select.genericList', $salePersonJOptList, 'filter[saleperson_id]', null , 'value', 'text', $filter['saleperson_id']);?>
			<b><?php echo JText::_('DEAL_MERCHANT');?>: </b>
			<?php echo JHTML::_('select.genericList', $merchantJOptList, 'filter[merchant_id]', null , 'value', 'text', $filter['merchant_id']);?>
			
			<b><?php echo JText::_('FROM_DATE');?>: </b>
			<?php echo JHTML::_('calendar', '', "filter[fromdate]" , "filter[fromdate]", '%Y-%m-%d');?>
			<b><?php echo JText::_('TO_DATE');?>: </b>
			<?php echo JHTML::_('calendar', '', "filter[todate]" , "filter[todate]", '%Y-%m-%d');?>

			<input type="submit" value="<?php echo JText::_('DEAL_SEARCH');?>" /> 
			<input type="button" value="<?php echo JText::_('DEAL_RESET');?>" onClick="location.href='index.php?option=com_enmasse&controller=salereports&filter[name]=filter[location_id]=filter[category_id]=filter[published]=filter[status]='" />
		</td>
	</tr>
</table>
<input type="hidden" name="option" value="com_enmasse" />
<input type="hidden" name="controller" value="salereports" />
</form>

<form action="index.php" method="post" name="adminForm"><br />

<div id="editcell"><?php echo JText::_('DEAL_NOTE');?>

<table class="adminlist" width="100%">
	<thead>
		<tr>
			<th width="90"><?php echo JText::_( 'DEAL_CODE' ); ?></th>
			<th width="110"><?php echo JText::_( 'DEAL_NAME' ); ?></th>
			<th width="100"><?php echo JText::_( 'DEAL_SALE_PERSON' ); ?></th>
			<th width="100"><?php echo JText::_( 'DEAL_MERCHANT' ); ?></th>
			<th width="50"><?php  echo JText::_('DEAL_QUANTITY_SOLD'); ?></th>
			<th width="50"><?php echo JText::_( 'DEAL_UNIT_PRICE' ); ?></th>
			<th width="50"><?php echo JText::_( 'COMMISSION_TOTAL_SALES' ); ?></th>
			<th width="80"><?php  echo JText::_('COMMISSION_PERCENT'); ?></th>
			<th width="80"><?php echo JText::_( 'COMMISSION_TOTAL_AMOUNT' ); ?></th>		</tr>
	</thead>
	<?php
	$total_commission_amount = 0;
	for ($i=0; $i < $n=count( $rows ); $i++)
	{
		$k = $i % 2;
		
		$row = &$rows[$i];
		$checked = JHTML::_('grid.id', $i, $row->id );
		
		$total_sales = $row->price * $row->cur_sold_qty;
		$total_commission = ($total_sales * $row->commission_percent) / 100;
	
	?>

	<tr class="<?php echo "row$k"; ?>">
        <td align="center"><?php echo $row->deal_code; ?></td>
		<td ><?php echo $row->name; ?></td>
		<td align="center" ><?php  echo $row->sales_person_name;?></td>
		<td ><?php echo $row->merchant_name; ?></td>
		<td align="center"><?php echo $row->cur_sold_qty; ?></td>
		<td align="center"><?php echo $this->currency_prefix.$row->price; ?></td>
		<td align="center"><?php echo $this->currency_prefix.$total_sales; ?></td>
		<td align="center" ><?php echo $row->commission_percent; ?>%</td>
		<td align="center"><?php echo $this->currency_prefix.$total_commission; ?></td>
		
	</tr>
	<?php
		$total_commission_amount += $total_commission;
	}
	?>
	<tr>
		<td colspan="8" align="right"><b><?php echo JText::_( 'COMMISSION_TOTAL' ); ?></b></td>
		<td align="center"><?php echo $this->currency_prefix.$total_commission_amount; ?></td>
	</tr>
	
	<tfoot>
    <tr>
      <td colspan="16"><?php echo $this->pagination->getListFooter(); ?></td>
    </tr>
  </tfoot>
</table>
</div>
<input type="hidden" name="option" value="<?php echo $option;?>" /> 
<input type="hidden" name="controller" value="salereports" /> 
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="filter_order" value="<?php echo $this->order['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->order['order_dir']; ?>" />
<input type="hidden" name="filter[name]" value="<?php echo $filter['name']; ?>"/>
<input type="hidden" name="filter[saleperson_id]" value="<?php echo $filter['saleperson_id']; ?>"/>
<input type="hidden" name="filter[merchant_id]" value="<?php echo $filter['merchant_id']; ?>"/>
<input type="hidden" name="filter[published]" value="<?php echo $filter['published']; ?>"/>
<input type="hidden" name="filter[status]" value="<?php echo $filter['status']; ?>"/>
</form>
</body>