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

$rows = $this->arBillTmpl;
$option = 'com_enmasse';
?>
<div>
<form action="index.php" method="post" name="adminForm">
<table class="adminlist">
	<thead>
		<tr>
			<th width="5"><input type="checkbox" name="toggle" value=""
				onclick="checkAll(<?php echo count( $rows ); ?>);" /></th>
			<th><?php echo JText::_('BILL_TEMPLATE_NAME');?></th>
			<th><?php echo JText::_('BILL_TEMPLATE_AVAILABLE_ATTRIBUTES');?></th>
			<th></th>
		</tr>
	</thead>
	<?php
	for ($i=0; $i < count( $rows ); $i++)
	{
		$k = $i % 2;
		
		$row = &$rows[$i];
		$checked = JHTML::_('grid.id', $i, $row->id );
		$link =  JRoute::_('index.php?option=' . $option .'&controller=billTemplate'.'&task=edit&cid[]='. $row->id) ;
	?>
	<tr class="<?php echo "row$k"; ?>">
		<td><?php echo $checked; ?></td>
		<td><a href="<?php echo $link?>"><?php echo $row->slug_name; ?></a></td>
		<td><?php echo $row->avail_attribute; ?></td>
		<td>
			<a href="index.php?option=com_enmasse&controller=billTemplate&task=preview" target="_blank"><?php echo JText::_("BILL_TEMPLATE_PREVIEW")?></a>
		</td>
	</tr>
	<?php
	} 
	?>
</table>
<input type="hidden" name="option" value="<?php echo $option;?>" />
<input type="hidden" name="controller" value="billTemplate" />
<input type="hidden" name="task" value="edit" />
<input type="hidden" name="boxchecked" value="0" />
</form>
</div>
